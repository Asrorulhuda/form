<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Database;
use App\Core\Session;
use App\Core\View;
use App\Core\Response;
use App\Models\AuditLog;

/**
 * Form Management & Builder Controller
 */
class FormController
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * List all forms (Clients see their own forms; Admins see all forms)
     */
    public function index(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $search = trim($_GET['search'] ?? '');
        $status = $_GET['status'] ?? '';

        $where = '1=1';
        $params = [];

        // If not Super Admin / Admin, show only the user's forms
        if (!Auth::hasRole('Super Admin', 'Admin')) {
            $where .= " AND f.user_id = ?";
            $params[] = Auth::id();
        }

        if ($search !== '') {
            $where .= " AND (f.title LIKE ? OR f.description LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        if ($status !== '') {
            $where .= " AND f.status = ?";
            $params[] = $status;
        }

        $total = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM forms f WHERE {$where}", $params);
        $offset = ($page - 1) * $perPage;

        $forms = $this->db->fetchAll(
            "SELECT f.*, u.name as creator_name,
                    (SELECT COUNT(*) FROM form_responses WHERE form_id = f.id) as response_count,
                    (SELECT COUNT(*) FROM form_fields WHERE form_id = f.id) as field_count
             FROM forms f 
             LEFT JOIN users u ON f.user_id = u.id 
             WHERE {$where} 
             ORDER BY f.created_at DESC 
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        View::page('forms.index', [
            'title'     => 'Kelola Formulir',
            'pageTitle' => 'Formulir Saya',
            'forms'     => $forms,
            'total'     => $total,
            'page'      => $page,
            'lastPage'  => max(1, ceil($total / $perPage)),
            'filters'   => ['search' => $search, 'status' => $status],
        ]);
    }

    /**
     * Show create form page
     */
    public function create(): void
    {
        View::page('forms.create', [
            'title'     => 'Buat Formulir Baru',
            'pageTitle' => 'Buat Formulir',
        ]);
    }

    /**
     * Store new form and redirect to Builder
     */
    public function store(): void
    {
        CSRF::check();

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($title === '') {
            Session::flash('error', 'Judul formulir wajib diisi.');
            Response::redirect(url('forms/create'));
            return;
        }

        // Generate unique slug
        $baseSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
        $slug = $baseSlug ?: 'form-' . time();
        $counter = 1;
        while ($this->db->fetchColumn("SELECT COUNT(*) FROM forms WHERE slug = ?", [$slug]) > 0) {
            $slug = $baseSlug . '-' . (++$counter);
        }

        $formId = $this->db->insert('forms', [
            'user_id'       => Auth::id(),
            'title'         => $title,
            'slug'          => $slug,
            'description'   => $description,
            'status'        => 'published',
            'settings_json' => json_encode(['allow_multiple' => true]),
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        // Add 2 initial default fields (Nama Lengkap, Email)
        $this->db->insert('form_fields', [
            'form_id'     => $formId,
            'field_type'  => 'text',
            'field_name'  => 'nama_lengkap',
            'label'       => 'Nama Lengkap',
            'placeholder' => 'Masukkan nama lengkap',
            'sort_order'  => 0,
            'is_required' => 1,
        ]);

        $this->db->insert('form_fields', [
            'form_id'     => $formId,
            'field_type'  => 'email',
            'field_name'  => 'email',
            'label'       => 'Alamat Email',
            'placeholder' => 'nama@email.com',
            'sort_order'  => 1,
            'is_required' => 1,
        ]);

        AuditLog::log('create', 'forms', (int) $formId, "Membuat form: {$title}");

        // Redirect directly to the interactive visual Builder!
        Response::redirect(url("forms/{$formId}/builder"));
    }

    /**
     * Visual Drag & Drop Form Builder
     */
    public function builder(string $id): void
    {
        $formId = (int) $id;
        $form = $this->db->fetch("SELECT * FROM forms WHERE id = ?", [$formId]);

        if (!$form) {
            Response::redirectWith(url('forms'), 'error', 'Formulir tidak ditemukan.');
            return;
        }

        // Authorization check: only creator or admin can edit
        if ($form->user_id !== Auth::id() && !Auth::hasRole('Super Admin', 'Admin')) {
            Response::redirectWith(url('forms'), 'error', 'Anda tidak memiliki akses untuk mengedit formulir ini.');
            return;
        }

        // Get existing fields
        $fields = $this->db->fetchAll(
            "SELECT * FROM form_fields WHERE form_id = ? ORDER BY sort_order ASC, id ASC",
            [$formId]
        );

        // Get available real Word document templates with variables
        $templates = $this->db->fetchAll(
            "SELECT t.id, t.name, t.category, t.version, t.original_filename, t.file_path,
                    (SELECT COUNT(*) FROM document_template_variables WHERE template_id = t.id) as variable_count
             FROM document_templates t 
             WHERE t.status = 'active' 
             ORDER BY t.name ASC"
        );

        // Fetch variables for each template
        $templateVariables = [];
        foreach ($templates as $tmpl) {
            $vars = $this->db->fetchAll(
                "SELECT * FROM document_template_variables WHERE template_id = ? ORDER BY id ASC",
                [$tmpl->id]
            );
            $templateVariables[$tmpl->id] = $vars;
        }

        View::page('forms.builder', [
            'title'             => 'Form Builder — ' . $form->title,
            'pageTitle'         => 'Form Builder: ' . $form->title,
            'form'              => $form,
            'fields'            => $fields,
            'templates'         => $templates,
            'templateVariables' => $templateVariables,
        ]);
    }

    /**
     * AJAX Endpoint: Save fields and template from the visual builder
     */
    public function saveFields(string $id): void
    {
        $formId = (int) $id;
        $form = $this->db->fetch("SELECT * FROM forms WHERE id = ?", [$formId]);

        if (!$form) {
            Response::error('Formulir tidak ditemukan.', null, 404);
            return;
        }

        if ($form->user_id !== Auth::id() && !Auth::hasRole('Super Admin')) {
            Response::error('Akses ditolak.', null, 403);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $fieldsData = $input['fields'] ?? [];
        $title = trim($input['title'] ?? $form->title);
        $description = trim($input['description'] ?? $form->description);
        $status = in_array($input['status'] ?? '', ['published', 'draft', 'closed']) ? $input['status'] : $form->status;
        $rawSlug = trim($input['slug'] ?? '');
        $slug = $form->slug;
        if ($rawSlug !== '') {
            $cleanSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $rawSlug), '-'));
            if ($cleanSlug !== '') {
                $exists = $this->db->fetchColumn("SELECT COUNT(*) FROM forms WHERE slug = ? AND id != ?", [$cleanSlug, $formId]);
                if ($exists == 0) {
                    $slug = $cleanSlug;
                }
            }
        }

        $templateId = !empty($input['template_id']) ? (int) $input['template_id'] : null;

        // Update form info including template_id & slug
        $this->db->update('forms', [
            'title'       => $title,
            'slug'        => $slug,
            'description' => $description,
            'template_id' => $templateId,
            'status'      => $status,
            'updated_at'  => date('Y-m-d H:i:s'),
        ], 'id = ?', [$formId]);

        // Begin transaction to replace fields
        $this->db->beginTransaction();
        try {
            $this->db->delete('form_fields', 'form_id = ?', [$formId]);

            foreach ($fieldsData as $order => $f) {
                $fieldType = $f['field_type'] ?? 'text';
                $label = trim($f['label'] ?? 'Field');
                $rawFieldName = trim($f['field_name'] ?? '');
                $fieldName = $rawFieldName ?: strtolower(preg_replace('/[^A-Za-z0-9_]+/', '_', $label));
                $fieldName = trim($fieldName, '_') ?: 'field_' . ($order + 1);

                $optionsJson = !empty($f['options']) ? json_encode($f['options']) : null;
                $settingsJson = !empty($f['settings']) ? json_encode($f['settings']) : null;

                $this->db->insert('form_fields', [
                    'form_id'       => $formId,
                    'field_type'    => $fieldType,
                    'field_name'    => $fieldName,
                    'label'         => $label,
                    'description'   => $f['description'] ?? null,
                    'placeholder'   => $f['placeholder'] ?? null,
                    'options_json'  => $optionsJson,
                    'settings_json' => $settingsJson,
                    'sort_order'    => $order,
                    'is_required'   => !empty($f['is_required']) ? 1 : 0,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
            }

            // Update variable mappings if provided
            if ($templateId && !empty($input['mappings'])) {
                foreach ($input['mappings'] as $varId => $m) {
                    $this->db->update('document_template_variables', [
                        'source_type' => $m['source_type'] ?? 'form_response',
                        'source_key'  => trim($m['source_key'] ?? ''),
                    ], 'id = ? AND template_id = ?', [(int)$varId, $templateId]);
                }
            }

            $this->db->commit();
            AuditLog::log('update', 'forms', $formId, "Menyimpan perubahan fields & template pada form: {$title}");

            Response::success('Formulir berhasil disimpan!', [
                'public_url' => url("form/{$slug}"),
                'slug'       => $slug,
            ]);
        } catch (\Exception $e) {
            $this->db->rollback();
            Response::error('Gagal menyimpan formulir: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * View responses for a specific form
     */
    public function responses(string $id): void
    {
        $formId = (int) $id;
        $form = $this->db->fetch("SELECT * FROM forms WHERE id = ?", [$formId]);

        if (!$form) {
            Response::redirectWith(url('forms'), 'error', 'Formulir tidak ditemukan.');
            return;
        }

        $fields = $this->db->fetchAll(
            "SELECT * FROM form_fields WHERE form_id = ? ORDER BY sort_order ASC",
            [$formId]
        );

        $responses = $this->db->fetchAll(
            "SELECT * FROM form_responses WHERE form_id = ? ORDER BY submitted_at DESC",
            [$formId]
        );

        // Fetch all values mapped by response_id and field_id
        $values = $this->db->fetchAll(
            "SELECT rv.* FROM form_response_values rv
             JOIN form_responses r ON rv.response_id = r.id
             WHERE r.form_id = ?",
            [$formId]
        );

        $valuesMap = [];
        foreach ($values as $v) {
            $valuesMap[$v->response_id][$v->field_id] = $v->value_text;
        }

        // Fetch all generated documents for these responses
        $documents = $this->db->fetchAll(
            "SELECT d.* FROM documents d
             JOIN form_responses r ON d.form_response_id = r.id
             WHERE r.form_id = ?",
            [$formId]
        );
        $docsMap = [];
        foreach ($documents as $d) {
            $docsMap[$d->form_response_id] = $d;
        }

        View::page('forms.responses', [
            'title'     => 'Respons — ' . $form->title,
            'pageTitle' => 'Respons: ' . $form->title,
            'form'      => $form,
            'fields'    => $fields,
            'responses' => $responses,
            'valuesMap' => $valuesMap,
            'docsMap'   => $docsMap,
        ]);
    }

    /**
     * Export form responses to Excel
     */
    public function exportResponses(string $id): void
    {
        $formId = (int) $id;
        $form = $this->db->fetch("SELECT * FROM forms WHERE id = ?", [$formId]);

        if (!$form) {
            Response::redirectWith(url('forms'), 'error', 'Formulir tidak ditemukan.');
            return;
        }

        if ($form->user_id !== Auth::id() && !Auth::hasRole('Super Admin', 'Admin')) {
            Response::redirectWith(url('forms'), 'error', 'Akses ditolak.');
            return;
        }

        $fields = $this->db->fetchAll(
            "SELECT * FROM form_fields WHERE form_id = ? AND field_type NOT IN ('heading', 'description') ORDER BY sort_order ASC",
            [$formId]
        );

        $responses = $this->db->fetchAll(
            "SELECT * FROM form_responses WHERE form_id = ? ORDER BY submitted_at ASC",
            [$formId]
        );

        $values = $this->db->fetchAll(
            "SELECT rv.* FROM form_response_values rv
             JOIN form_responses r ON rv.response_id = r.id
             WHERE r.form_id = ?",
            [$formId]
        );

        $valuesMap = [];
        foreach ($values as $v) {
            $valuesMap[$v->response_id][$v->field_id] = $v->value_text;
        }

        // Fetch all generated documents for these responses
        $documents = $this->db->fetchAll(
            "SELECT d.* FROM documents d
             JOIN form_responses r ON d.form_response_id = r.id
             WHERE r.form_id = ?",
            [$formId]
        );
        $docsMap = [];
        foreach ($documents as $d) {
            $docsMap[$d->form_response_id] = $d;
        }

        $hasTemplate = !empty($form->template_id) || !empty($docsMap);

        // Build Headers
        $headers = ['No', 'Waktu Submit'];
        foreach ($fields as $f) {
            $headers[] = $f->label;
        }
        if ($hasTemplate) {
            $headers[] = 'Nomor Surat / Dokumen';
            $headers[] = 'Token Verifikasi';
            $headers[] = 'Link Berkas Dokumen';
            $headers[] = 'Link Unduh Word (.docx)';
        }
        $headers[] = 'IP Address';

        // Build Rows
        $rows = [];
        $no = 1;
        foreach ($responses as $r) {
            $row = [
                $no++,
                date('d/m/Y H:i:s', strtotime($r->submitted_at)),
            ];
            foreach ($fields as $f) {
                $row[] = $valuesMap[$r->id][$f->id] ?? '-';
            }
            if ($hasTemplate) {
                $doc = $docsMap[$r->id] ?? null;
                if ($doc) {
                    $row[] = $doc->document_number;
                    $row[] = $doc->verification_token;
                    $row[] = url("document/{$doc->verification_token}");
                    $row[] = !empty($doc->file_path_docx) ? url("document/{$doc->verification_token}/download-docx") : '-';
                } else {
                    $row[] = '-';
                    $row[] = '-';
                    $row[] = '-';
                    $row[] = '-';
                }
            }
            $row[] = $r->ip_address ?? '127.0.0.1';
            $rows[] = $row;
        }

        AuditLog::log('export', 'form_responses', $formId, "Mengekspor data respons form: {$form->title}");

        \App\Services\ExcelExportService::exportToExcel(
            'Data_Responden_' . ($form->slug ?: 'form_' . $formId),
            $headers,
            $rows,
            'Data Responden - ' . $form->title
        );
    }

    /**
     * Delete a single response
     */
    public function deleteResponse(string $formId, string $responseId): void
    {
        CSRF::check();

        $form = $this->db->fetch("SELECT * FROM forms WHERE id = ?", [(int)$formId]);
        if (!$form || ($form->user_id !== Auth::id() && !Auth::hasRole('Super Admin', 'Admin'))) {
            Response::redirectWith(url("forms/{$formId}/responses"), 'error', 'Akses ditolak.');
            return;
        }

        $resp = $this->db->fetch("SELECT * FROM form_responses WHERE id = ? AND form_id = ?", [(int)$responseId, (int)$formId]);
        if ($resp) {
            $this->db->delete('form_response_values', 'response_id = ?', [(int)$responseId]);
            $this->db->delete('form_responses', 'id = ?', [(int)$responseId]);
            AuditLog::log('delete', 'form_responses', (int)$responseId, "Menghapus data respons ID: {$responseId}");
            Response::redirectWith(url("forms/{$formId}/responses"), 'success', 'Data responden berhasil dihapus.');
        } else {
            Response::redirectWith(url("forms/{$formId}/responses"), 'error', 'Data respons tidak ditemukan.');
        }
    }

    /**
     * Clear all responses for a form
     */
    public function clearResponses(string $formId): void
    {
        CSRF::check();

        $form = $this->db->fetch("SELECT * FROM forms WHERE id = ?", [(int)$formId]);
        if (!$form || ($form->user_id !== Auth::id() && !Auth::hasRole('Super Admin', 'Admin'))) {
            Response::redirectWith(url("forms/{$formId}/responses"), 'error', 'Akses ditolak.');
            return;
        }

        $responses = $this->db->fetchAll("SELECT id FROM form_responses WHERE form_id = ?", [(int)$formId]);
        foreach ($responses as $r) {
            $this->db->delete('form_response_values', 'response_id = ?', [$r->id]);
        }
        $this->db->delete('form_responses', 'form_id = ?', [(int)$formId]);

        AuditLog::log('delete', 'form_responses', (int)$formId, "Mengosongkan semua respons form: {$form->title}");
        Response::redirectWith(url("forms/{$formId}/responses"), 'success', 'Semua data responden berhasil dikosongkan.');
    }

    /**
     * Delete a form
     */
    public function destroy(string $id): void
    {
        CSRF::check();

        $form = $this->db->fetch("SELECT * FROM forms WHERE id = ?", [(int) $id]);
        if ($form) {
            if ($form->user_id !== Auth::id() && !Auth::hasRole('Super Admin', 'Admin')) {
                Response::redirectWith(url('forms'), 'error', 'Akses ditolak.');
                return;
            }

            $this->db->delete('forms', 'id = ?', [(int) $id]);
            AuditLog::log('delete', 'forms', (int) $id, "Menghapus form: {$form->title}");
            Response::redirectWith(url('forms'), 'success', 'Formulir berhasil dihapus.');
        } else {
            Response::redirectWith(url('forms'), 'error', 'Formulir tidak ditemukan.');
        }
    }
}
