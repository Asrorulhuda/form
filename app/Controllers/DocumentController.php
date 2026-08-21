<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Database;
use App\Core\View;
use App\Core\Response;
use App\Core\Session;
use App\Models\AuditLog;
use App\Services\WordTemplateEngine;

/**
 * Document Generator Controller
 * Manages document generation from Word templates (.docx) and PDF downloads
 */
class DocumentController
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * List all generated documents
     */
    public function index(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 15;
        $search = trim($_GET['search'] ?? '');
        $status = $_GET['status'] ?? '';

        $where = '1=1';
        $params = [];

        // Users see their own created documents, Super Admin sees all
        if (!Auth::hasRole('Super Admin')) {
            $where .= " AND d.created_by = ?";
            $params[] = Auth::id();
        }

        if ($search !== '') {
            $where .= " AND (d.title LIKE ? OR d.document_number LIKE ? OR d.verification_token LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        if ($status !== '') {
            $where .= " AND d.status = ?";
            $params[] = $status;
        }

        $total = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM documents d WHERE {$where}", $params);
        $offset = ($page - 1) * $perPage;

        $documents = $this->db->fetchAll(
            "SELECT d.*, u.name as creator_name, t.name as template_name, t.version as template_version
             FROM documents d 
             LEFT JOIN users u ON d.created_by = u.id 
             LEFT JOIN document_templates t ON d.template_id = t.id
             WHERE {$where} 
             ORDER BY d.created_at DESC 
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        View::page('documents.index', [
            'title'     => 'Data Surat & Dokumen Terbit',
            'pageTitle' => 'Generator Surat & Dokumen',
            'documents' => $documents,
            'total'     => $total,
            'page'      => $page,
            'lastPage'  => max(1, ceil($total / $perPage)),
            'filters'   => ['search' => $search, 'status' => $status],
        ]);
    }

    /**
     * Show Generate Document View
     */
    public function create(): void
    {
        // Load active Word templates
        $templates = $this->db->fetchAll("SELECT * FROM document_templates WHERE status = 'active' ORDER BY name ASC");

        // Load forms for response selection
        $forms = $this->db->fetchAll("SELECT * FROM forms ORDER BY title ASC");

        $selectedTemplateId = (int)($_GET['template_id'] ?? 0);
        $selectedResponseId = (int)($_GET['response_id'] ?? 0);

        $selectedTemplate = null;
        $variables = [];
        if ($selectedTemplateId > 0) {
            $selectedTemplate = $this->db->fetch("SELECT * FROM document_templates WHERE id = ?", [$selectedTemplateId]);
            if ($selectedTemplate) {
                $variables = $this->db->fetchAll("SELECT * FROM document_template_variables WHERE template_id = ? ORDER BY id ASC", [$selectedTemplateId]);
            }
        }

        View::page('documents.generate', [
            'title'            => 'Generate Surat dari Template Word',
            'pageTitle'        => 'Generate Surat Baru',
            'templates'        => $templates,
            'forms'            => $forms,
            'selectedTemplate' => $selectedTemplate,
            'variables'        => $variables,
            'selectedResponseId'=> $selectedResponseId,
        ]);
    }

    /**
     * Process Document Generation from Word Template
     */
    public function store(): void
    {
        CSRF::check();

        $templateId = (int)($_POST['template_id'] ?? 0);
        $template = $this->db->fetch("SELECT * FROM document_templates WHERE id = ?", [$templateId]);

        if (!$template || empty($template->file_path)) {
            Session::flash('error', 'Pilih template surat Word yang valid.');
            Response::redirect(url('documents/create'));
            return;
        }

        $fullTemplatePath = BASE_PATH . '/' . $template->file_path;
        if (!file_exists($fullTemplatePath)) {
            Session::flash('error', 'Berkas fisik template Word tidak ditemukan di server.');
            Response::redirect(url('documents/create'));
            return;
        }

        $variables = $this->db->fetchAll("SELECT * FROM document_template_variables WHERE template_id = ?", [$templateId]);
        $inputVariables = $_POST['variables'] ?? [];

        // Generate unique Document Number & Verification Token
        $seq = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM documents") + 1;
        $docNumber = 'DOC/' . date('Ym') . '/' . str_pad((string)$seq, 5, '0', STR_PAD_LEFT);
        $verificationToken = 'ASR-' . strtoupper(bin2hex(random_bytes(4)));

        // Load Settings
        $settingsRaw = $this->db->fetchAll("SELECT `key`, `value` FROM settings");
        $settingsMap = [];
        foreach ($settingsRaw as $s) {
            $settingsMap[$s->key] = $s->value;
        }

        // Resolve Variables
        $resolvedValues = WordTemplateEngine::resolveVariableValues(
            $variables,
            $inputVariables,
            Auth::user(),
            $settingsMap,
            $docNumber
        );

        // Also merge any direct input variables
        foreach ($inputVariables as $k => $v) {
            $resolvedValues[$k] = $v;
        }

        $verifyUrl = url("verify/{$verificationToken}");
        $resolvedValues['token_verifikasi'] = $verificationToken;
        $resolvedValues['token'] = $verificationToken;
        $resolvedValues['link_verifikasi'] = $verifyUrl;

        // Generate QR Code PNG file for Word template replacement
        $qrTempPath = BASE_PATH . '/storage/temp/qr_' . $verificationToken . '.png';
        \App\Services\QrCodeService::generatePngFile($verifyUrl, $qrTempPath);
        $resolvedValues['__qr_image_path'] = $qrTempPath;

        // Prepare Output Paths
        $randomFileBase = 'DOC_' . date('Ym') . '_' . bin2hex(random_bytes(8));
        $outputDocxRelative = 'storage/generated/docx/' . $randomFileBase . '.docx';
        $outputDocxFull = BASE_PATH . '/' . $outputDocxRelative;

        $outputPdfRelative = 'storage/generated/pdf/' . $randomFileBase . '.pdf';
        $outputPdfFull = BASE_PATH . '/' . $outputPdfRelative;

        try {
            // Render DOCX using PHPWord TemplateProcessor
            WordTemplateEngine::renderDocx($fullTemplatePath, $resolvedValues, $outputDocxFull);

            // Parse rendered docx to HTML for instant browser viewing and printing
            try {
                $renderedHtml = \App\Services\DocxParser::parseToHtml($outputDocxFull);
            } catch (\Exception $ex) {
                $renderedHtml = null;
            }

            // Attempt PDF Conversion via LibreOffice
            $pdfResult = WordTemplateEngine::convertToPdf($outputDocxFull, $outputPdfFull);
            $hasPdf = ($pdfResult && file_exists($outputPdfFull));

            $docTitle = $template->name . ' - ' . ($resolvedValues['nama_siswa'] ?? ($resolvedValues['nama_lengkap'] ?? 'Dokumen'));

            // Insert into documents table
            $docId = $this->db->insert('documents', [
                'template_id'         => $template->id,
                'template_version_id' => $template->version,
                'form_response_id'    => !empty($_POST['form_response_id']) ? (int)$_POST['form_response_id'] : null,
                'document_number'     => $docNumber,
                'title'               => $docTitle,
                'file_path_docx'      => $outputDocxRelative,
                'file_path_pdf'       => $hasPdf ? $outputPdfRelative : null,
                'content'             => $renderedHtml ?? null,
                'status'              => 'generated',
                'verification_token'  => $verificationToken,
                'created_by'          => Auth::id(),
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ]);

            // Clean temporary QR file
            if (file_exists($qrTempPath)) {
                @unlink($qrTempPath);
            }

            AuditLog::log('create', 'documents', (int)$docId, "Dokumen dibuat: {$docNumber} ({$docTitle})");

            $msg = "✓ Surat Word (.DOCX) berhasil dibuat!";
            if ($hasPdf) {
                $msg .= " Format PDF juga siap diunduh.";
            } else {
                $msg .= " (Catatan: PDF converter LibreOffice belum terpasang di server, file Word tetap dapat diunduh & dicetak).";
            }

            Session::flash('success', $msg);
            Response::redirect(url('documents'));
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal menghasilkan dokumen: ' . $e->getMessage());
            Response::redirect(url('documents/create'));
        }
    }

    /**
     * Secure Download DOCX
     */
    public function downloadDocx(string $id): void
    {
        $docId = (int)$id;
        $doc = $this->db->fetch("SELECT * FROM documents WHERE id = ?", [$docId]);

        if (!$doc || empty($doc->file_path_docx)) {
            Response::redirectWith(url('documents'), 'error', 'Berkas dokumen Word tidak ditemukan.');
            return;
        }

        if ($doc->created_by !== Auth::id() && !Auth::hasRole('Super Admin')) {
            Response::redirectWith(url('documents'), 'error', 'Akses ditolak.');
            return;
        }

        $fullPath = BASE_PATH . '/' . $doc->file_path_docx;
        if (!file_exists($fullPath)) {
            Response::redirectWith(url('documents'), 'error', 'Berkas fisik Word tidak ditemukan di server.');
            return;
        }

        AuditLog::log('view', 'documents', $docId, "Dokumen di-download (Word): {$doc->document_number}");

        $downloadFilename = str_replace('/', '_', $doc->document_number) . '.docx';

        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $downloadFilename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fullPath));
        readfile($fullPath);
        exit;
    }

    /**
     * Secure Download PDF
     */
    public function downloadPdf(string $id): void
    {
        $docId = (int)$id;
        $doc = $this->db->fetch("SELECT * FROM documents WHERE id = ?", [$docId]);

        if (!$doc || empty($doc->file_path_pdf)) {
            Response::redirectWith(url('documents'), 'error', 'Berkas PDF belum tersedia untuk dokumen ini.');
            return;
        }

        if ($doc->created_by !== Auth::id() && !Auth::hasRole('Super Admin')) {
            Response::redirectWith(url('documents'), 'error', 'Akses ditolak.');
            return;
        }

        $fullPath = BASE_PATH . '/' . $doc->file_path_pdf;
        if (!file_exists($fullPath)) {
            Response::redirectWith(url('documents'), 'error', 'Berkas PDF fisik tidak ditemukan di server.');
            return;
        }

        AuditLog::log('view', 'documents', $docId, "Dokumen di-download (PDF): {$doc->document_number}");

        $downloadFilename = str_replace('/', '_', $doc->document_number) . '.pdf';

        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $downloadFilename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fullPath));
        readfile($fullPath);
        exit;
    }

    /**
     * Delete document
     */
    public function destroy(string $id): void
    {
        CSRF::check();

        $docId = (int) $id;
        $doc = $this->db->fetch("SELECT * FROM documents WHERE id = ?", [$docId]);

        if ($doc) {
            if ($doc->created_by !== Auth::id() && !Auth::hasRole('Super Admin')) {
                Response::redirectWith(url('documents'), 'error', 'Akses ditolak.');
                return;
            }

            if (!empty($doc->file_path_docx)) @unlink(BASE_PATH . '/' . $doc->file_path_docx);
            if (!empty($doc->file_path_pdf)) @unlink(BASE_PATH . '/' . $doc->file_path_pdf);

            $this->db->delete('documents', 'id = ?', [$docId]);
            AuditLog::log('delete', 'documents', $docId, "Dokumen dihapus: {$doc->document_number}");
            Response::redirectWith(url('documents'), 'success', 'Dokumen berhasil dihapus.');
        } else {
            Response::redirectWith(url('documents'), 'error', 'Dokumen tidak ditemukan.');
        }
    }
}
