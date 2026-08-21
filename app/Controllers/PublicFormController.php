<?php

namespace App\Controllers;

use App\Core\CSRF;
use App\Core\Database;
use App\Core\View;
use App\Core\Response;
use App\Core\Session;

/**
 * Public Form Controller
 * Renders public forms, auto-generates documents, and renders public document view
 */
class PublicFormController
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Display public form by slug
     */
    public function show(string $slug): void
    {
        $form = $this->db->fetch("SELECT * FROM forms WHERE slug = ?", [$slug]);

        if (!$form) {
            http_response_code(404);
            View::render('public.not_found', [
                'title' => 'Formulir Tidak Ditemukan',
            ]);
            return;
        }

        if ($form->status === 'closed') {
            View::render('public.closed', [
                'title' => $form->title . ' — Formulir Ditutup',
                'form'  => $form,
            ]);
            return;
        }

        // Fetch all fields for this form sorted by sort_order
        $fields = $this->db->fetchAll(
            "SELECT * FROM form_fields WHERE form_id = ? ORDER BY sort_order ASC, id ASC",
            [$form->id]
        );

        View::render('public.form', [
            'title'  => $form->title . ' — ASR FORM',
            'form'   => $form,
            'fields' => $fields,
        ]);
    }

    /**
     * Handle public form submission (No login required) & Auto-generate Document
     */
    public function submit(string $slug): void
    {
        CSRF::check();

        $form = $this->db->fetch("SELECT * FROM forms WHERE slug = ?", [$slug]);
        if (!$form || $form->status === 'closed') {
            Response::redirect(url("form/{$slug}"));
            return;
        }

        $fields = $this->db->fetchAll(
            "SELECT * FROM form_fields WHERE form_id = ? ORDER BY sort_order ASC, id ASC",
            [$form->id]
        );

        $errors = [];
        $valuesToSave = [];
        $submittedAnswersMap = [];

        foreach ($fields as $field) {
            // Skip layout fields
            if (in_array($field->field_type, ['heading', 'description', 'section'])) {
                continue;
            }

            $fieldName = $field->field_name;
            $label = $field->label;
            $isRequired = (bool) $field->is_required;

            // Check conditional logic visibility
            $settings = json_decode($field->settings_json ?? '{}', true) ?: [];
            $cond = $settings['conditional_logic'] ?? null;
            $isVisible = true;

            if (!empty($cond['enabled']) && !empty($cond['target_field'])) {
                $targetVal = $_POST[$cond['target_field']] ?? '';
                $compareVal = trim($cond['value'] ?? '');
                $isMatch = false;

                if (is_array($targetVal)) {
                    if ($cond['operator'] === 'equals') $isMatch = in_array($compareVal, $targetVal);
                    elseif ($cond['operator'] === 'not_equals') $isMatch = !in_array($compareVal, $targetVal);
                    elseif ($cond['operator'] === 'not_empty') $isMatch = !empty($targetVal);
                    elseif ($cond['operator'] === 'empty') $isMatch = empty($targetVal);
                } else {
                    $strVal = (string)$targetVal;
                    if ($cond['operator'] === 'equals') $isMatch = (strcasecmp($strVal, $compareVal) === 0);
                    elseif ($cond['operator'] === 'not_equals') $isMatch = (strcasecmp($strVal, $compareVal) !== 0);
                    elseif ($cond['operator'] === 'contains') $isMatch = (stripos($strVal, $compareVal) !== false);
                    elseif ($cond['operator'] === 'not_empty') $isMatch = (trim($strVal) !== '');
                    elseif ($cond['operator'] === 'empty') $isMatch = (trim($strVal) === '');
                }

                $isVisible = ($cond['action'] === 'show') ? $isMatch : !$isMatch;
            }

            // If field was hidden by conditional logic, skip required validation
            if (!$isVisible) {
                $isRequired = false;
            }

            // Handle file upload
            if (in_array($field->field_type, ['file', 'image'])) {
                $file = $_FILES[$fieldName] ?? null;
                $hasFile = $file && isset($file['tmp_name']) && is_uploaded_file($file['tmp_name']) && $file['error'] === UPLOAD_ERR_OK;

                if ($isRequired && !$hasFile) {
                    $errors[$fieldName] = "Berkas '{$label}' wajib diunggah.";
                } elseif ($hasFile) {
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $mime = mime_content_type($file['tmp_name']);

                    // Strict extension whitelist
                    $allowedImageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                    $allowedFileExts  = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'zip', 'rar', 'jpg', 'jpeg', 'png'];

                    $isImageField = ($field->field_type === 'image');
                    $allowedExts = $isImageField ? $allowedImageExts : $allowedFileExts;

                    // Dangerous extensions blocking
                    $dangerousExts = ['php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phar', 'exe', 'sh', 'bat', 'cmd', 'js', 'vbs', 'cgi', 'pl', 'htaccess'];
                    if (in_array($ext, $dangerousExts) || !in_array($ext, $allowedExts)) {
                        $errors[$fieldName] = "Format berkas '{$label}' (.{$ext}) tidak diizinkan untuk keamanan.";
                        continue;
                    }

                    // Max size limit (Default 10MB)
                    $maxBytes = 10 * 1024 * 1024;
                    if ($file['size'] > $maxBytes) {
                        $errors[$fieldName] = "Ukuran berkas '{$label}' melebihi batas maksimum 10MB.";
                        continue;
                    }

                    $uploadDir = BASE_PATH . '/public/uploads/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    $filename = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
                    $targetPath = $uploadDir . $filename;

                    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                        $fileUrl = url('uploads/' . $filename);
                        $valuesToSave[$field->id] = [
                            'text' => $fileUrl,
                            'json' => json_encode([
                                'original_name' => $file['name'],
                                'size'          => $file['size'],
                                'mime'          => $mime,
                                'url'           => $fileUrl,
                            ]),
                        ];
                        $submittedAnswersMap[$fieldName] = $fileUrl;
                    } else {
                        $errors[$fieldName] = "Gagal menyimpan berkas '{$label}' ke server.";
                    }
                }
                continue;
            }

            // Handle normal inputs / checkboxes
            $val = $_POST[$fieldName] ?? null;

            if ($field->field_type === 'checkbox') {
                $valArray = is_array($val) ? $val : ($val ? [$val] : []);
                if ($isRequired && empty($valArray)) {
                    $errors[$fieldName] = "Pilihan '{$label}' wajib dipilih.";
                } else {
                    $textVal = implode(', ', $valArray);
                    $valuesToSave[$field->id] = [
                        'text' => $textVal,
                        'json' => json_encode($valArray),
                    ];
                    $submittedAnswersMap[$fieldName] = $textVal;
                }
                continue;
            }

            // Text, textarea, number, email, phone, date, time, dropdown, radio, signature, rating, scale
            $valStr = is_string($val) ? trim($val) : '';

            if ($isRequired && $valStr === '') {
                $errors[$fieldName] = "'{$label}' wajib diisi.";
            } else {
                $valuesToSave[$field->id] = [
                    'text' => $valStr,
                    'json' => null,
                ];
                $submittedAnswersMap[$fieldName] = $valStr;
            }
        }

        if (!empty($errors)) {
            Session::flash('form_errors', $errors);
            Session::setOld($_POST);
            Response::redirect(url("form/{$slug}"));
            return;
        }

        // Save Response
        $responseId = $this->db->insert('form_responses', [
            'form_id'       => $form->id,
            'respondent_id' => null,
            'submitted_at'  => date('Y-m-d H:i:s'),
            'ip_address'    => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent'    => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ]);

        // Save response values
        foreach ($valuesToSave as $fieldId => $v) {
            $this->db->insert('form_response_values', [
                'response_id' => $responseId,
                'field_id'    => $fieldId,
                'value_text'  => $v['text'],
                'value_json'  => $v['json'],
            ]);
        }

        // ─── Auto Word Document Generator ───
        $generatedDocToken = null;
        if (!empty($form->template_id)) {
            $template = $this->db->fetch("SELECT * FROM document_templates WHERE id = ?", [(int)$form->template_id]);
            if ($template) {
                $docNumber = 'DOC/' . date('Ym') . '/' . str_pad((string)$responseId, 5, '0', STR_PAD_LEFT);
                $verificationToken = 'ASR-' . strtoupper(bin2hex(random_bytes(4)));

                $filePathDocx = null;
                $filePathPdf = null;

                // Load Settings
                $settingsRaw = $this->db->fetchAll("SELECT `key`, `value` FROM settings");
                $settingsMap = [];
                foreach ($settingsRaw as $s) {
                    $settingsMap[$s->key] = $s->value;
                }

                // If template has a physical .docx file
                if (!empty($template->file_path) && file_exists(BASE_PATH . '/' . $template->file_path)) {
                    $variables = $this->db->fetchAll("SELECT * FROM document_template_variables WHERE template_id = ?", [$template->id]);
                    
                    $formOwner = $this->db->fetch("SELECT * FROM users WHERE id = ?", [$form->user_id]);
                    $resolvedValues = \App\Services\WordTemplateEngine::resolveVariableValues(
                        $variables,
                        $submittedAnswersMap,
                        $formOwner,
                        $settingsMap,
                        $docNumber
                    );

                    $verifyUrl = url("verify/{$verificationToken}");
                    $resolvedValues['token_verifikasi'] = $verificationToken;
                    $resolvedValues['token'] = $verificationToken;
                    $resolvedValues['link_verifikasi'] = $verifyUrl;

                    // Generate QR Code PNG file for Word template replacement if {{qr_code}} exists
                    $qrTempPath = BASE_PATH . '/storage/temp/qr_' . $verificationToken . '.png';
                    \App\Services\QrCodeService::generatePngFile($verifyUrl, $qrTempPath);
                    $resolvedValues['__qr_image_path'] = $qrTempPath;

                    $randomFileBase = 'DOC_' . date('Ym') . '_' . bin2hex(random_bytes(8));
                    $outputDocxRelative = 'storage/generated/docx/' . $randomFileBase . '.docx';
                    $outputDocxFull = BASE_PATH . '/' . $outputDocxRelative;

                    $outputPdfRelative = 'storage/generated/pdf/' . $randomFileBase . '.pdf';
                    $outputPdfFull = BASE_PATH . '/' . $outputPdfRelative;

                    try {
                        \App\Services\WordTemplateEngine::renderDocx(BASE_PATH . '/' . $template->file_path, $resolvedValues, $outputDocxFull);
                        $filePathDocx = $outputDocxRelative;

                        // Parse rendered docx to HTML for instant browser viewing and printing
                        try {
                            $renderedHtml = \App\Services\DocxParser::parseToHtml($outputDocxFull);
                        } catch (\Exception $ex) {
                            $renderedHtml = null;
                        }

                        $pdfResult = \App\Services\WordTemplateEngine::convertToPdf($outputDocxFull, $outputPdfFull);
                        if ($pdfResult && file_exists($outputPdfFull)) {
                            $filePathPdf = $outputPdfRelative;
                        }
                    } catch (\Exception $e) {
                        // Log error, continue gracefully
                    }

                    // Clean temporary QR code file
                    if (file_exists($qrTempPath)) {
                        @unlink($qrTempPath);
                    }
                }

                $docTitle = $template->name . ' - ' . ($submittedAnswersMap['nama_lengkap'] ?? ($submittedAnswersMap['nama_siswa'] ?? 'Responden'));

                $docId = $this->db->insert('documents', [
                    'template_id'         => $template->id,
                    'template_version_id' => $template->version ?? 1,
                    'form_response_id'    => $responseId,
                    'document_number'     => $docNumber,
                    'title'               => $docTitle,
                    'file_path_docx'      => $filePathDocx,
                    'file_path_pdf'       => $filePathPdf,
                    'content'             => $renderedHtml ?? null,
                    'status'              => 'generated',
                    'verification_token'  => $verificationToken,
                    'created_by'          => $form->user_id,
                    'created_at'          => date('Y-m-d H:i:s'),
                    'updated_at'          => date('Y-m-d H:i:s'),
                ]);

                $generatedDocToken = $verificationToken;
                Session::flash('generated_doc_id', $docId);
                Session::flash('generated_doc_token', $generatedDocToken);
                Session::flash('generated_doc_number', $docNumber);
                Session::flash('has_docx', !empty($filePathDocx));
                Session::flash('has_pdf', !empty($filePathPdf));
            }
        }

        // ─── Extract Respondent Contact Info for Auto WhatsApp / Email ───
        $respName = null;
        $respPhone = null;
        $respEmail = null;

        foreach ($submittedAnswersMap as $key => $val) {
            if (is_array($val)) continue;
            $strVal = trim((string)$val);
            if ($strVal === '') continue;

            $lowerKey = strtolower($key);
            if (!$respName && preg_match('/^(nama|name|nama_lengkap|nama_siswa|nama_pemohon|pemohon)$/i', $lowerKey)) {
                $respName = $strVal;
            } elseif (!$respPhone && preg_match('/(phone|wa|whatsapp|telepon|hp|no_hp|telp|kontak|nomor_hp)/i', $lowerKey)) {
                $respPhone = $strVal;
            } elseif (!$respEmail && (preg_match('/(email|surel|mail)/i', $lowerKey) || filter_var($strVal, FILTER_VALIDATE_EMAIL))) {
                $respEmail = $strVal;
            }
        }
        $respName = $respName ?: 'Responden';

        $settingModel = new \App\Models\Setting();
        $siteName = $settingModel->get('site_name', 'ASR FORM');

        // ─── Send WhatsApp Notification to Respondent ───
        $wa = \App\Services\WhatsAppService::getInstance();
        if ($wa->isEnabled() && (int)$settingModel->get('wa_notify_respondent_on_submit', '1') === 1 && !empty($respPhone)) {
            $msg = "Halo *{$respName}*,\n\n"
                 . "Terima kasih telah mengisi formulir *{$form->title}* di *{$siteName}*.\n"
                 . "Respons Anda telah berhasil kami catat pada " . date('d/m/Y H:i') . " WIB.\n\n";

            if (!empty($generatedDocToken)) {
                $docViewUrl = url("document/{$generatedDocToken}");
                $msg .= "📄 *Dokumen Resmi Anda Telah Terbit:*\n"
                      . "Nomor Dokumen: *{$docNumber}*\n"
                      . "Lihat & Unduh Dokumen:\n🔗 {$docViewUrl}\n\n";
            }
            $msg .= "Terima kasih!";
            $wa->notifyUser($respPhone, $msg);
        }

        // ─── Send WhatsApp Notification to Admin / Form Creator ───
        if ($wa->isEnabled() && (int)$settingModel->get('wa_notify_on_form_response', '1') === 1) {
            $adminWaMsg = "📝 *RESPONS FORMULIR BARU — {$siteName}*\n\n"
                        . "📋 *Formulir:* {$form->title}\n"
                        . "👤 *Responden:* {$respName}\n"
                        . "📱 *Kontak:* " . ($respPhone ?: '-') . " / " . ($respEmail ?: '-') . "\n"
                        . "📅 *Waktu:* " . date('d/m/Y H:i') . " WIB\n";

            if (!empty($generatedDocToken)) {
                $adminWaMsg .= "📄 *Dokumen:* {$docNumber} (" . url("document/{$generatedDocToken}") . ")\n";
            }
            $wa->notifyAdmin($adminWaMsg);
        }

        // ─── Send Email Notification to Respondent ───
        $mail = \App\Services\MailService::getInstance();
        if ($mail->isEnabled() && (int)$settingModel->get('smtp_notify_respondent_on_submit', '1') === 1 && !empty($respEmail)) {
            $subj = "Tanda Terima Pengisian Formulir: {$form->title}";
            $html = "<h2>Tanda Terima Formulir</h2>"
                  . "<p>Halo <strong>{$respName}</strong>,</p>"
                  . "<p>Terima kasih telah mengisi formulir <strong>{$form->title}</strong> di {$siteName}. Respons Anda telah berhasil disimpan pada " . date('d/m/Y H:i') . " WIB.</p>";

            if (!empty($generatedDocToken)) {
                $docViewUrl = url("document/{$generatedDocToken}");
                $html .= "<div style='background:#f0fdf4;border:1px solid #bbf7d0;padding:16px;border-radius:8px;margin:20px 0;'>"
                       . "<p style='margin:0 0 8px 0;font-weight:bold;color:#166534;'>📄 Surat / Dokumen Resmi Telah Terbit</p>"
                       . "<p style='margin:0 0 12px 0;color:#15803d;'>Nomor: <strong>{$docNumber}</strong></p>"
                       . "<a href='{$docViewUrl}' style='display:inline-block;padding:10px 18px;background:#16a34a;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:bold;'>Lihat & Unduh Dokumen</a>"
                       . "</div>";
            }
            $mail->notifyUser($respEmail, $subj, $html);
        }

        // ─── Send Email Notification to Admin ───
        if ($mail->isEnabled() && (int)$settingModel->get('smtp_notify_on_form_response', '1') === 1) {
            $adminSubj = "[Respons Baru] {$form->title} - {$respName}";
            $adminHtml = "<h2>Respons Formulir Baru Masuk</h2>"
                       . "<p>Ada pengisian formulir baru pada sistem {$siteName}:</p>"
                       . "<ul>"
                       . "<li><strong>Formulir:</strong> {$form->title}</li>"
                       . "<li><strong>Nama Responden:</strong> {$respName}</li>"
                       . "<li><strong>Nomor Telepon/WA:</strong> " . ($respPhone ?: '-') . "</li>"
                       . "<li><strong>Email:</strong> " . ($respEmail ?: '-') . "</li>"
                       . "<li><strong>Waktu Submit:</strong> " . date('d/m/Y H:i') . " WIB</li>"
                       . "</ul>";
            $mail->notifyAdmin($adminSubj, $adminHtml);
        }

        Response::redirect(url("{$slug}/success"));
    }

    /**
     * Thank you / success page
     */
    public function success(string $slug): void
    {
        $form = $this->db->fetch("SELECT * FROM forms WHERE slug = ?", [$slug]);
        if (!$form) {
            Response::redirect(url());
            return;
        }

        $docToken = Session::getFlash('generated_doc_token');
        $docNumber = Session::getFlash('generated_doc_number');

        View::render('public.success', [
            'title'     => 'Respons Terkirim — ' . $form->title,
            'form'      => $form,
            'docToken'  => $docToken,
            'docNumber' => $docNumber,
        ]);
    }

    /**
     * Public Document View / Print Page
     */
    public function viewDocument(string $token): void
    {
        $doc = $this->db->fetch("SELECT * FROM documents WHERE verification_token = ?", [$token]);

        if (!$doc) {
            http_response_code(404);
            View::render('public.not_found', ['title' => 'Dokumen Tidak Ditemukan']);
            return;
        }

        View::render('public.document_view', [
            'title'    => $doc->title,
            'document' => $doc,
        ]);
    }

    /**
     * Public Document Verification Certificate Page
     */
    public function verify(string $token): void
    {
        $doc = $this->db->fetch("SELECT * FROM documents WHERE verification_token = ?", [$token]);

        if (!$doc) {
            http_response_code(404);
            View::render('public.not_found', ['title' => 'Dokumen Tidak Ditemukan / Tidak Valid']);
            return;
        }

        View::render('public.verify', [
            'title'    => 'Verifikasi Keabsahan Dokumen — ' . $doc->document_number,
            'document' => $doc,
        ]);
    }

    /**
     * Public Download Generated DOCX by Token
     */
    public function downloadDocx(string $token): void
    {
        $doc = $this->db->fetch("SELECT * FROM documents WHERE verification_token = ?", [$token]);
        if (!$doc || empty($doc->file_path_docx)) {
            http_response_code(404);
            echo "Berkas Word tidak ditemukan.";
            exit;
        }

        $fullPath = BASE_PATH . '/' . $doc->file_path_docx;
        if (!file_exists($fullPath)) {
            http_response_code(404);
            echo "Berkas fisik Word tidak ditemukan di server.";
            exit;
        }

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
     * Public Download Generated PDF by Token
     */
    public function downloadPdf(string $token): void
    {
        $doc = $this->db->fetch("SELECT * FROM documents WHERE verification_token = ?", [$token]);
        if (!$doc || empty($doc->file_path_pdf)) {
            http_response_code(404);
            echo "Berkas PDF tidak ditemukan.";
            exit;
        }

        $fullPath = BASE_PATH . '/' . $doc->file_path_pdf;
        if (!file_exists($fullPath)) {
            http_response_code(404);
            echo "Berkas fisik PDF tidak ditemukan di server.";
            exit;
        }

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
     * Clean Direct Slug Handler (e.g. /pendaftaran-guru)
     */
    public function showDirect(string $slug): void
    {
        $reserved = [
            'dashboard', 'forms', 'templates', 'documents', 'responses', 'applicants', 'users', 'settings',
            'audit-log', 'login', 'register', 'logout', 'api', 'uploads', 'assets', 'form', 'document', 'f',
            'features', 'pricing', 'about', 'contact', 'privacy-policy', 'terms', 'sitemap.xml', 'robots.txt'
        ];
        if (in_array(strtolower($slug), $reserved)) {
            http_response_code(404);
            View::render('public.not_found', ['title' => 'Halaman Tidak Ditemukan']);
            return;
        }

        $form = $this->db->fetch("SELECT * FROM forms WHERE slug = ?", [$slug]);
        if ($form) {
            $this->show($slug);
            return;
        }

        http_response_code(404);
        View::render('public.not_found', ['title' => 'Halaman Tidak Ditemukan']);
    }
}
