<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Database;
use App\Core\Session;
use App\Core\View;
use App\Core\Response;
use App\Models\AuditLog;
use App\Services\WordTemplateEngine;

/**
 * Template Controller - Word (.DOCX) Template Engine & Variable Mapping
 */
class TemplateController
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * List all Word Document Templates
     */
    public function index(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 12;
        $search = trim($_GET['search'] ?? '');
        $category = $_GET['category'] ?? '';

        $where = "1=1";
        $params = [];

        // Users see their own templates + global templates (user_id = 1)
        if (!Auth::hasRole('Super Admin')) {
            $where .= " AND (t.user_id = ? OR t.user_id = 1)";
            $params[] = Auth::id();
        }

        if ($search !== '') {
            $where .= " AND (t.name LIKE ? OR t.category LIKE ? OR t.description LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        if ($category !== '') {
            $where .= " AND t.category = ?";
            $params[] = $category;
        }

        $total = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM document_templates t WHERE {$where}", $params);
        $offset = ($page - 1) * $perPage;

        $templates = $this->db->fetchAll(
            "SELECT t.*, u.name as creator_name,
                    (SELECT COUNT(*) FROM document_template_variables WHERE template_id = t.id) as variable_count,
                    (SELECT COUNT(*) FROM forms WHERE template_id = t.id) as linked_forms_count,
                    (SELECT COUNT(*) FROM documents WHERE template_id = t.id) as generated_docs_count
             FROM document_templates t
             LEFT JOIN users u ON t.user_id = u.id
             WHERE {$where}
             ORDER BY t.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        View::page('templates.index', [
            'title'     => 'Template Surat Word (.DOCX)',
            'pageTitle' => 'Template Surat Microsoft Word',
            'templates' => $templates,
            'total'     => $total,
            'page'      => $page,
            'lastPage'  => max(1, ceil($total / $perPage)),
            'filters'   => ['search' => $search, 'category' => $category],
        ]);
    }

    /**
     * Show create/upload template view
     */
    public function create(): void
    {
        View::page('templates.create', [
            'title'     => 'Tambah Template Surat Word (.DOCX)',
            'pageTitle' => 'Upload Template Word',
        ]);
    }

    /**
     * Store new Word Template (.docx)
     */
    public function store(): void
    {
        CSRF::check();

        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? 'Surat Keterangan');
        $description = trim($_POST['description'] ?? '');
        $status = in_array($_POST['status'] ?? '', ['active', 'inactive']) ? $_POST['status'] : 'active';

        if ($name === '') {
            Session::flash('error', 'Nama template wajib diisi.');
            Session::setOld($_POST);
            Response::redirect(url('templates/create'));
            return;
        }

        // Validate File Upload
        $file = $_FILES['template_file'] ?? null;
        if (!$file || !isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            Session::flash('error', 'Berkas template Microsoft Word (.docx) wajib diunggah.');
            Session::setOld($_POST);
            Response::redirect(url('templates/create'));
            return;
        }

        // Validate Extension & MIME
        $originalFilename = $file['name'];
        $ext = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
        $mime = mime_content_type($file['tmp_name']);

        if ($ext !== 'docx') {
            Session::flash('error', 'Format berkas tidak valid. Hanya berkas Microsoft Word (.docx) yang diizinkan.');
            Response::redirect(url('templates/create'));
            return;
        }

        // Validate File Size (Max 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            Session::flash('error', 'Ukuran berkas melebihi batas maksimum (10 MB).');
            Response::redirect(url('templates/create'));
            return;
        }

        // Secure Random Storage Path
        $storageDir = BASE_PATH . '/storage/templates/documents/';
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $randomFilename = 'template_' . bin2hex(random_bytes(10)) . '.docx';
        $targetPath = $storageDir . $randomFilename;
        $relativePath = 'storage/templates/documents/' . $randomFilename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            Session::flash('error', 'Gagal menyimpan berkas template ke server.');
            Response::redirect(url('templates/create'));
            return;
        }

        try {
            // Extract Variables Automatically using WordTemplateEngine
            $detectedVariables = WordTemplateEngine::extractVariables($targetPath);

            // Insert into document_templates table
            $templateId = $this->db->insert('document_templates', [
                'user_id'           => Auth::id(),
                'name'              => $name,
                'category'          => $category,
                'description'       => $description,
                'file_path'         => $relativePath,
                'original_filename' => $originalFilename,
                'version'           => 1,
                'status'            => $status,
                'created_by'        => Auth::id(),
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);

            // Insert each detected variable into document_template_variables
            foreach ($detectedVariables as $varName) {
                $cleanVar = trim($varName, '{} ');
                if ($cleanVar === '') continue;

                // Auto-suggest source_type based on variable name
                $sourceType = 'form_response';
                $sourceKey = $cleanVar;

                if (in_array($cleanVar, ['tanggal', 'tanggal_surat', 'bulan', 'tahun', 'nomor_surat', 'nomor_dokumen', 'tanggal_angka'])) {
                    $sourceType = 'system';
                } elseif (in_array($cleanVar, ['user_name', 'user_email', 'creator_name'])) {
                    $sourceType = 'user';
                } elseif (in_array($cleanVar, ['nama_instansi', 'alamat_instansi', 'telepon_instansi', 'nama_kepala', 'nip_kepala', 'nama_pejabat', 'nip_pejabat', 'jabatan_pejabat', 'kota'])) {
                    $sourceType = 'setting';
                }

                $label = ucwords(str_replace('_', ' ', $cleanVar));

                $this->db->insert('document_template_variables', [
                    'template_id'   => $templateId,
                    'variable_name' => $cleanVar,
                    'label'         => $label,
                    'source_type'   => $sourceType,
                    'source_key'    => $sourceKey,
                    'default_value' => null,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
            }

            // Insert Version 1 record
            $this->db->insert('document_template_versions', [
                'template_id'    => $templateId,
                'version'        => 1,
                'file_path'      => $relativePath,
                'variables_json' => json_encode($detectedVariables),
                'created_by'     => Auth::id(),
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            AuditLog::log('create', 'document_templates', (int) $templateId, "Template dibuat: {$name} (Version 1, " . count($detectedVariables) . " variable terdeteksi)");

            Session::flash('success', "Template Word berhasil diunggah! " . count($detectedVariables) . " variable terdeteksi otomatis.");
            Response::redirect(url("templates/{$templateId}/mapping"));
        } catch (\Exception $e) {
            @unlink($targetPath);
            Session::flash('error', 'Gagal memproses berkas Word: ' . $e->getMessage());
            Response::redirect(url('templates/create'));
        }
    }

    /**
     * Variable Mapping View
     */
    public function mapping(string $id): void
    {
        $templateId = (int) $id;
        $template = $this->db->fetch("SELECT * FROM document_templates WHERE id = ?", [$templateId]);

        if (!$template) {
            Response::redirectWith(url('templates'), 'error', 'Template tidak ditemukan.');
            return;
        }

        if ($template->user_id !== Auth::id() && !Auth::hasRole('Super Admin')) {
            Response::redirectWith(url('templates'), 'error', 'Akses ditolak.');
            return;
        }

        // Fetch all variables for this template
        $variables = $this->db->fetchAll(
            "SELECT * FROM document_template_variables WHERE template_id = ? ORDER BY id ASC",
            [$templateId]
        );

        // Fetch user's forms for field suggestions
        $forms = $this->db->fetchAll("SELECT id, title, slug FROM forms ORDER BY title ASC");

        // Fetch settings for setting source key suggestions
        $settings = $this->db->fetchAll("SELECT `key`, `value` FROM settings ORDER BY id ASC");

        View::page('templates.mapping', [
            'title'     => 'Mapping Variable — ' . $template->name,
            'pageTitle' => 'Mapping Variable: ' . $template->name,
            'template'  => $template,
            'variables' => $variables,
            'forms'     => $forms,
            'settings'  => $settings,
        ]);
    }

    /**
     * Save Variable Mappings
     */
    public function saveMapping(string $id): void
    {
        CSRF::check();

        $templateId = (int) $id;
        $template = $this->db->fetch("SELECT * FROM document_templates WHERE id = ?", [$templateId]);

        if (!$template) {
            Response::redirectWith(url('templates'), 'error', 'Template tidak ditemukan.');
            return;
        }

        if ($template->user_id !== Auth::id() && !Auth::hasRole('Super Admin')) {
            Response::redirectWith(url('templates'), 'error', 'Akses ditolak.');
            return;
        }

        $mappings = $_POST['mappings'] ?? [];

        foreach ($mappings as $varId => $data) {
            $this->db->update('document_template_variables', [
                'label'         => trim($data['label'] ?? ''),
                'source_type'   => $data['source_type'] ?? 'form_response',
                'source_key'    => trim($data['source_key'] ?? ''),
                'default_value' => trim($data['default_value'] ?? ''),
                'updated_at'    => date('Y-m-d H:i:s'),
            ], 'id = ? AND template_id = ?', [(int)$varId, $templateId]);
        }

        AuditLog::log('update', 'document_templates', $templateId, "Mapping variable template diperbarui: {$template->name}");

        Response::redirectWith(url("templates/{$templateId}/mapping"), 'success', 'Pengaturan mapping variable berhasil disimpan!');
    }

    /**
     * Show Template Versions History & Upload New Version
     */
    public function versions(string $id): void
    {
        $templateId = (int) $id;
        $template = $this->db->fetch("SELECT * FROM document_templates WHERE id = ?", [$templateId]);

        if (!$template) {
            Response::redirectWith(url('templates'), 'error', 'Template tidak ditemukan.');
            return;
        }

        $versions = $this->db->fetchAll(
            "SELECT tv.*, u.name as creator_name 
             FROM document_template_versions tv
             LEFT JOIN users u ON tv.created_by = u.id
             WHERE tv.template_id = ?
             ORDER BY tv.version DESC",
            [$templateId]
        );

        View::page('templates.versions', [
            'title'     => 'Riwayat Versi — ' . $template->name,
            'pageTitle' => 'Riwayat Versi: ' . $template->name,
            'template'  => $template,
            'versions'  => $versions,
        ]);
    }

    /**
     * Upload new version (Version 1 -> Version 2)
     */
    public function uploadVersion(string $id): void
    {
        CSRF::check();

        $templateId = (int) $id;
        $template = $this->db->fetch("SELECT * FROM document_templates WHERE id = ?", [$templateId]);

        if (!$template) {
            Response::redirectWith(url('templates'), 'error', 'Template tidak ditemukan.');
            return;
        }

        $file = $_FILES['template_file'] ?? null;
        if (!$file || !isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            Response::redirectWith(url("templates/{$templateId}/versions"), 'error', 'Pilih berkas Word (.docx) yang valid.');
            return;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'docx') {
            Response::redirectWith(url("templates/{$templateId}/versions"), 'error', 'Hanya berkas .docx yang didukung.');
            return;
        }

        $storageDir = BASE_PATH . '/storage/templates/documents/';
        $randomFilename = 'template_' . bin2hex(random_bytes(10)) . '.docx';
        $targetPath = $storageDir . $randomFilename;
        $relativePath = 'storage/templates/documents/' . $randomFilename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            Response::redirectWith(url("templates/{$templateId}/versions"), 'error', 'Gagal menyimpan berkas baru.');
            return;
        }

        try {
            $detectedVariables = WordTemplateEngine::extractVariables($targetPath);
            $newVersion = ((int) $template->version) + 1;

            // Update main template record
            $this->db->update('document_templates', [
                'file_path'         => $relativePath,
                'original_filename' => $file['name'],
                'version'           => $newVersion,
                'updated_at'        => date('Y-m-d H:i:s'),
            ], 'id = ?', [$templateId]);

            // Insert new version history
            $this->db->insert('document_template_versions', [
                'template_id'    => $templateId,
                'version'        => $newVersion,
                'file_path'      => $relativePath,
                'variables_json' => json_encode($detectedVariables),
                'created_by'     => Auth::id(),
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            // Add any newly detected variables that didn't exist before
            $existingVars = $this->db->fetchAll("SELECT variable_name FROM document_template_variables WHERE template_id = ?", [$templateId]);
            $existingMap = array_column($existingVars, 'variable_name');

            foreach ($detectedVariables as $varName) {
                if (!in_array($varName, $existingMap)) {
                    $this->db->insert('document_template_variables', [
                        'template_id'   => $templateId,
                        'variable_name' => $varName,
                        'label'         => ucwords(str_replace('_', ' ', $varName)),
                        'source_type'   => 'form_response',
                        'source_key'    => $varName,
                        'default_value' => null,
                        'created_at'    => date('Y-m-d H:i:s'),
                        'updated_at'    => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            AuditLog::log('update', 'document_templates', $templateId, "Template diperbarui ke Version {$newVersion}: {$template->name}");

            Response::redirectWith(url("templates/{$templateId}/mapping"), 'success', "Berhasil memperbarui template ke Versi {$newVersion}!");
        } catch (\Exception $e) {
            @unlink($targetPath);
            Response::redirectWith(url("templates/{$templateId}/versions"), 'error', 'Gagal memproses versi baru: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate Template
     */
    public function duplicate(string $id): void
    {
        CSRF::check();

        $templateId = (int) $id;
        $template = $this->db->fetch("SELECT * FROM document_templates WHERE id = ?", [$templateId]);

        if (!$template) {
            Response::redirectWith(url('templates'), 'error', 'Template tidak ditemukan.');
            return;
        }

        // Copy physical .docx file
        $srcPath = BASE_PATH . '/' . $template->file_path;
        $newFilename = 'template_' . bin2hex(random_bytes(10)) . '.docx';
        $newRelativePath = 'storage/templates/documents/' . $newFilename;
        $newFullPath = BASE_PATH . '/' . $newRelativePath;

        if (file_exists($srcPath)) {
            copy($srcPath, $newFullPath);
        }

        $newTitle = $template->name . ' (Salinan)';

        $newId = $this->db->insert('document_templates', [
            'user_id'           => Auth::id(),
            'name'              => $newTitle,
            'category'          => $template->category,
            'description'       => $template->description,
            'file_path'         => $newRelativePath,
            'original_filename' => $template->original_filename,
            'version'           => 1,
            'status'            => 'active',
            'created_by'        => Auth::id(),
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);

        // Copy variable mappings
        $variables = $this->db->fetchAll("SELECT * FROM document_template_variables WHERE template_id = ?", [$templateId]);
        foreach ($variables as $v) {
            $this->db->insert('document_template_variables', [
                'template_id'   => $newId,
                'variable_name' => $v->variable_name,
                'label'         => $v->label,
                'source_type'   => $v->source_type,
                'source_key'    => $v->source_key,
                'default_value' => $v->default_value,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        // Insert Version 1 record
        $this->db->insert('document_template_versions', [
            'template_id'    => $newId,
            'version'        => 1,
            'file_path'      => $newRelativePath,
            'variables_json' => json_encode(array_column($variables, 'variable_name')),
            'created_by'     => Auth::id(),
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        AuditLog::log('create', 'document_templates', (int)$newId, "Template diduplikasi dari '{$template->name}' ke '{$newTitle}'");

        Response::redirectWith(url("templates/{$newId}/mapping"), 'success', "Template berhasil diduplikasi!");
    }

    /**
     * Download raw .docx template file
     */
    public function download(string $id): void
    {
        $templateId = (int) $id;
        $template = $this->db->fetch("SELECT * FROM document_templates WHERE id = ?", [$templateId]);

        if (!$template || empty($template->file_path)) {
            Response::redirectWith(url('templates'), 'error', 'Berkas template tidak ditemukan.');
            return;
        }

        $fullPath = BASE_PATH . '/' . $template->file_path;
        if (!file_exists($fullPath)) {
            Response::redirectWith(url('templates'), 'error', 'Berkas template fisik tidak ditemukan di server.');
            return;
        }

        $filename = $template->original_filename ?: ($template->name . '.docx');

        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fullPath));
        readfile($fullPath);
        exit;
    }

    /**
     * Delete template
     */
    public function destroy(string $id): void
    {
        CSRF::check();

        $templateId = (int) $id;
        $template = $this->db->fetch("SELECT * FROM document_templates WHERE id = ?", [$templateId]);

        if ($template) {
            if ($template->user_id !== Auth::id() && !Auth::hasRole('Super Admin')) {
                Response::redirectWith(url('templates'), 'error', 'Akses ditolak.');
                return;
            }

            // Remove physical file if exists
            if (!empty($template->file_path)) {
                @unlink(BASE_PATH . '/' . $template->file_path);
            }

            $this->db->delete('document_templates', 'id = ?', [$templateId]);
            AuditLog::log('delete', 'document_templates', $templateId, "Template dihapus: {$template->name}");
            Response::redirectWith(url('templates'), 'success', 'Template berhasil dihapus.');
        } else {
            Response::redirectWith(url('templates'), 'error', 'Template tidak ditemukan.');
        }
    }
}
