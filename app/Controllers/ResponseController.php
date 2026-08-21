<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;
use App\Core\Response;
use App\Core\CSRF;
use App\Models\AuditLog;
use App\Services\ExcelExportService;

/**
 * Form Responses Controller
 */
class ResponseController
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * List all responses
     */
    public function index(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 15;
        $formId = $_GET['form_id'] ?? '';

        $where = '1=1';
        $params = [];

        if ($formId !== '') {
            $where .= " AND r.form_id = ?";
            $params[] = $formId;
        }

        $total = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM form_responses r WHERE {$where}", $params);
        $offset = ($page - 1) * $perPage;

        $responses = $this->db->fetchAll(
            "SELECT r.*, f.title as form_title, f.slug as form_slug, f.template_id,
                    u.name as respondent_name,
                    d.document_number, d.verification_token, d.file_path_docx, d.file_path_pdf
             FROM form_responses r
             JOIN forms f ON r.form_id = f.id
             LEFT JOIN users u ON r.respondent_id = u.id
             LEFT JOIN documents d ON d.form_response_id = r.id
             WHERE {$where}
             ORDER BY r.submitted_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        $formsList = $this->db->fetchAll("SELECT id, title FROM forms ORDER BY title ASC");

        View::page('responses.index', [
            'title'        => 'Data Respons Formulir',
            'pageTitle'    => 'Data Respons & Responden',
            'responses'    => $responses,
            'formsList'    => $formsList,
            'total'        => $total,
            'page'         => $page,
            'lastPage'     => max(1, ceil($total / $perPage)),
            'selectedForm' => $formId,
        ]);
    }

    /**
     * Export all or filtered responses to Excel
     */
    public function export(): void
    {
        $formId = $_GET['form_id'] ?? '';

        // If specific form selected, export with question answers
        if ($formId !== '') {
            $form = $this->db->fetch("SELECT * FROM forms WHERE id = ?", [(int)$formId]);
            if ($form) {
                $ctrl = new FormController();
                $ctrl->exportResponses((string)$formId);
                return;
            }
        }

        // Export all forms summary
        $responses = $this->db->fetchAll(
            "SELECT r.*, f.title as form_title, u.name as respondent_name,
                    d.document_number, d.verification_token, d.file_path_docx
             FROM form_responses r
             JOIN forms f ON r.form_id = f.id
             LEFT JOIN users u ON r.respondent_id = u.id
             LEFT JOIN documents d ON d.form_response_id = r.id
             ORDER BY r.submitted_at DESC"
        );

        $headers = [
            'No',
            'ID Respons',
            'Formulir',
            'Responden',
            'Waktu Submit',
            'Nomor Surat / Dokumen',
            'Token Verifikasi',
            'Link Berkas Dokumen',
            'Link Unduh Word (.docx)',
            'IP Address'
        ];

        $rows = [];
        $no = 1;
        foreach ($responses as $r) {
            $hasDoc = !empty($r->document_number);
            $rows[] = [
                $no++,
                '#RESP-' . $r->id,
                $r->form_title,
                $r->respondent_name ?? 'Publik / Anonim',
                date('d/m/Y H:i:s', strtotime($r->submitted_at)),
                $hasDoc ? $r->document_number : '-',
                $hasDoc ? $r->verification_token : '-',
                $hasDoc ? url("document/{$r->verification_token}") : '-',
                ($hasDoc && !empty($r->file_path_docx)) ? url("document/{$r->verification_token}/download-docx") : '-',
                $r->ip_address ?? '127.0.0.1',
            ];
        }

        AuditLog::log('export', 'form_responses', 0, "Mengekspor semua data respons formulir");

        ExcelExportService::exportToExcel(
            'Semua_Data_Respons_Formulir',
            $headers,
            $rows,
            'Rekapitulasi Semua Respons Formulir'
        );
    }

    /**
     * Delete a single response
     */
    public function destroy(string $id): void
    {
        CSRF::check();

        $responseId = (int)$id;
        $resp = $this->db->fetch("SELECT * FROM form_responses WHERE id = ?", [$responseId]);

        if ($resp) {
            $this->db->delete('form_response_values', 'response_id = ?', [$responseId]);
            $this->db->delete('form_responses', 'id = ?', [$responseId]);
            AuditLog::log('delete', 'form_responses', $responseId, "Menghapus data respons ID: #RESP-{$responseId}");
            Response::redirectWith(url('responses'), 'success', "Respons #RESP-{$responseId} berhasil dihapus.");
        } else {
            Response::redirectWith(url('responses'), 'error', 'Data respons tidak ditemukan.');
        }
    }

    /**
     * Clear all or filtered responses
     */
    public function clear(): void
    {
        CSRF::check();

        $formId = $_POST['form_id'] ?? '';

        if ($formId !== '') {
            $responses = $this->db->fetchAll("SELECT id FROM form_responses WHERE form_id = ?", [(int)$formId]);
            foreach ($responses as $r) {
                $this->db->delete('form_response_values', 'response_id = ?', [$r->id]);
            }
            $this->db->delete('form_responses', 'form_id = ?', [(int)$formId]);
            AuditLog::log('delete', 'form_responses', (int)$formId, "Mengosongkan respons form ID: {$formId}");
            Response::redirectWith(url("responses?form_id={$formId}"), 'success', 'Data respons form berhasil dikosongkan.');
        } else {
            $this->db->query("DELETE FROM form_response_values");
            $this->db->query("DELETE FROM form_responses");
            AuditLog::log('delete', 'form_responses', 0, "Mengosongkan semua data respons formulir");
            Response::redirectWith(url('responses'), 'success', 'Semua data respons berhasil dikosongkan.');
        }
    }

    /**
     * Send Response Document Link via WhatsApp
     */
    public function sendWhatsApp(string $id): void
    {
        CSRF::check();

        $responseId = (int)$id;
        $targetPhone = trim($_POST['phone'] ?? '');

        if (empty($targetPhone)) {
            Response::redirectWith(url('responses'), 'error', 'Nomor WhatsApp tujuan harus diisi.');
            return;
        }

        $resp = $this->db->fetch(
            "SELECT r.*, f.title as form_title, d.document_number, d.verification_token
             FROM form_responses r
             JOIN forms f ON r.form_id = f.id
             LEFT JOIN documents d ON d.form_response_id = r.id
             WHERE r.id = ?",
            [$responseId]
        );

        if (!$resp) {
            Response::redirectWith(url('responses'), 'error', 'Data respons tidak ditemukan.');
            return;
        }

        $settingModel = new \App\Models\Setting();
        $siteName = $settingModel->get('site_name', 'ASR FORM');

        $msg = "Halo,\n\n"
             . "Berikut adalah informasi tanda terima pengisian formulir *{$resp->form_title}* di *{$siteName}*:\n"
             . "📌 *ID Respons:* #RESP-{$resp->id}\n"
             . "📅 *Waktu Submit:* " . date('d/m/Y H:i', strtotime($resp->submitted_at)) . " WIB\n\n";

        if (!empty($resp->verification_token)) {
            $docUrl = url("document/{$resp->verification_token}");
            $msg .= "📄 *Dokumen Resmi Telah Terbit:*\n"
                  . "Nomor Dokumen: *{$resp->document_number}*\n"
                  . "Lihat & Unduh Dokumen:\n🔗 {$docUrl}\n\n";
        }

        $msg .= "Terima kasih telah menggunakan layanan {$siteName}.";

        $wa = \App\Services\WhatsAppService::getInstance();
        $result = $wa->sendMessage($targetPhone, $msg);

        if ($result['success']) {
            Response::redirectWith(url('responses'), 'success', "Pesan WhatsApp berhasil dikirim ke {$targetPhone}.");
        } else {
            Response::redirectWith(url('responses'), 'error', "Gagal mengirim WhatsApp: " . $result['message']);
        }
    }
}
