<?php
use App\Core\CSRF;
use App\Core\Session;

$formErrors = Session::getFlash('form_errors') ?? [];

// Group fields into sections based on 'heading' (Judul Bagian)
$sections = [];
$currentSection = [
    'title' => 'Informasi Utama',
    'fields' => []
];

foreach ($fields as $field) {
    if ($field->field_type === 'heading') {
        if (!empty($currentSection['fields'])) {
            $sections[] = $currentSection;
        }
        $currentSection = [
            'title' => $field->label,
            'fields' => []
        ];
    } else {
        $currentSection['fields'][] = $field;
    }
}
if (!empty($currentSection['fields']) || empty($sections)) {
    $sections[] = $currentSection;
}

$totalSections = count($sections);
$hasMultipleSections = $totalSections > 1;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Formulir Online') ?></title>
    <meta name="description" content="<?= e(substr(strip_tags($form->description ?? ''), 0, 150)) ?>">
    <?= CSRF::meta() ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%234f46e5'/><text x='50' y='68' font-size='55' font-weight='bold' fill='white' text-anchor='middle' font-family='Arial'>A</text></svg>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <?= adsenseHead() ?>
    <style>
        :root {
            --font-family-base: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --success-gradient: linear-gradient(135deg, #059669 0%, #10b981 100%);
        }
        body {
            font-family: var(--font-family-base);
            background: #f8fafc radial-gradient(circle at top, rgba(99, 102, 241, 0.04) 0%, transparent 60%);
            min-height: 100vh;
            padding: 36px 16px 80px;
            color: #1e293b;
            -webkit-font-smoothing: antialiased;
        }
        .public-container {
            max-width: 700px;
            margin: 0 auto;
        }
        
        /* ─── Form Header Card ─── */
        .form-header-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            padding: 36px 36px 28px;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 2px 6px -1px rgba(15, 23, 42, 0.02);
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }
        .form-header-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--primary-gradient);
        }
        .form-title {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.025em;
            line-height: 1.3;
            margin-bottom: 12px;
        }
        .form-desc-box {
            font-size: 14.5px;
            color: #475569;
            line-height: 1.7;
            margin-bottom: 20px;
            white-space: pre-line;
        }
        .form-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding-top: 16px;
            border-top: 1px solid #f1f5f9;
        }
        .req-hint-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 600;
            color: #dc2626;
            background: #fef2f2;
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid #fee2e2;
        }

        /* ─── Step Progress & Section Indicator ─── */
        .step-progress-wrapper {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 18px 24px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
            margin-bottom: 24px;
        }
        .step-progress-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .step-progress-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .step-progress-pill {
            background: #eef2ff;
            color: #4338ca;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 800;
        }
        .step-progress-percent {
            font-size: 12px;
            font-weight: 800;
            color: #4f46e5;
            background: #e0e7ff;
            padding: 3px 8px;
            border-radius: 6px;
        }
        .step-progress-track {
            height: 7px;
            background: #f1f5f9;
            border-radius: 999px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .step-progress-fill {
            height: 100%;
            background: var(--primary-gradient);
            border-radius: 999px;
            transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ─── Step Breadcrumbs ─── */
        .step-crumbs-list {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px dashed #f1f5f9;
            overflow-x: auto;
            scrollbar-width: none;
        }
        .step-crumb-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
            color: #94a3b8;
            white-space: nowrap;
        }
        .step-crumb-item.active {
            color: #4f46e5;
        }
        .step-crumb-item.completed {
            color: #059669;
        }
        .step-crumb-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            background: #f1f5f9;
            color: #64748b;
            font-weight: 800;
        }
        .step-crumb-item.active .step-crumb-dot {
            background: #4f46e5;
            color: #ffffff;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
        .step-crumb-item.completed .step-crumb-dot {
            background: #10b981;
            color: #ffffff;
        }

        /* ─── Section Banner Card ─── */
        .section-banner-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            border-left: 5px solid #4f46e5;
            padding: 20px 24px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
            margin-bottom: 20px;
        }
        .section-banner-title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ─── Question Box ─── */
        .field-box {
            background: #ffffff;
            border-radius: 16px;
            border: 1.5px solid #e2e8f0;
            padding: 24px 28px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
            margin-bottom: 20px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .field-box:hover {
            border-color: #cbd5e1;
        }
        .field-box:focus-within {
            border-color: #6366f1;
            box-shadow: 0 8px 24px -4px rgba(79, 70, 229, 0.1), 0 2px 6px -1px rgba(79, 70, 229, 0.04);
        }
        .field-box.is-invalid-box {
            border-color: #f87171 !important;
            border-left: 5px solid #ef4444 !important;
            background: #fffafa;
            animation: shake 0.35s ease;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-4px); }
            40%, 80% { transform: translateX(4px); }
        }

        .field-label {
            font-size: 15.5px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 6px;
            display: block;
            line-height: 1.4;
        }
        .field-label .req-star {
            color: #ef4444;
            font-weight: 800;
            margin-left: 2px;
            font-size: 15px;
        }
        .field-desc {
            font-size: 13px;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 14px;
        }

        /* ─── Form Inputs ─── */
        .form-control {
            width: 100%;
            font-family: inherit;
            font-size: 14.5px;
            font-weight: 500;
            color: #0f172a;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            transition: all 0.15s ease;
            box-sizing: border-box;
        }
        .form-control:hover {
            border-color: #cbd5e1;
        }
        .form-control:focus {
            background: #ffffff;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
            outline: none;
        }
        .form-control::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
            line-height: 1.6;
        }
        select.form-control {
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 40px;
            appearance: none;
        }

        /* ─── Interactive Option Cards (Radio & Checkbox) ─── */
        .option-cards-group {
            display: flex;
            flex-direction: column;
            gap: 9px;
            margin-top: 6px;
        }
        .option-card-label {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.15s ease;
            user-select: none;
        }
        .option-card-label:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }
        .option-card-label input[type="radio"],
        .option-card-label input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #4f46e5;
            cursor: pointer;
            margin: 0;
            flex-shrink: 0;
        }
        .option-card-text {
            font-size: 14px;
            font-weight: 500;
            color: #1e293b;
            flex: 1;
            line-height: 1.4;
        }
        .option-card-label:has(input:checked) {
            border-color: #6366f1;
            background: #f5f3ff;
            box-shadow: 0 2px 6px rgba(79, 70, 229, 0.08);
        }
        .option-card-label:has(input:checked) .option-card-text {
            font-weight: 700;
            color: #3730a3;
        }

        /* ─── Signature Pad ─── */
        .signature-wrapper {
            border: 1.5px dashed #cbd5e1;
            border-radius: 12px;
            background: #fafafa;
            position: relative;
            overflow: hidden;
            touch-action: none;
            transition: border-color 0.2s ease;
        }
        .signature-wrapper:hover {
            border-color: #94a3b8;
        }
        .signature-wrapper canvas {
            display: block;
            width: 100%;
            height: 160px;
            cursor: crosshair;
        }
        .signature-guide-line {
            position: absolute;
            bottom: 45px;
            left: 20px;
            right: 20px;
            border-bottom: 1px dashed #e2e8f0;
            pointer-events: none;
            display: flex;
            justify-content: flex-start;
        }
        .signature-guide-text {
            font-size: 11px;
            color: #cbd5e1;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .signature-actions {
            position: absolute;
            bottom: 10px;
            right: 12px;
            z-index: 10;
        }

        /* ─── File Upload Box ─── */
        .file-upload-box {
            border: 1.5px dashed #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            background: #f8fafc;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .file-upload-box:hover {
            border-color: #6366f1;
            background: #f5f3ff;
        }

        /* ─── Error Message ─── */
        .field-error-msg {
            color: #dc2626;
            font-size: 12.5px;
            font-weight: 700;
            margin-top: 8px;
            display: none;
            align-items: center;
            gap: 4px;
        }
        .field-box.is-invalid-box .field-error-msg {
            display: flex;
        }

        /* ─── Step Navigation Bar ─── */
        .step-nav-bar {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            padding: 20px 28px;
            box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.05);
            margin-top: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .btn-step-prev {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            color: #475569;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-step-prev:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
        .btn-step-next {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 26px;
            border-radius: 10px;
            font-size: 14.5px;
            font-weight: 700;
            color: #ffffff;
            background: var(--primary-gradient);
            border: none;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-step-next:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.45);
        }
        .btn-submit-public {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px 30px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 800;
            color: #ffffff;
            background: var(--success-gradient);
            border: none;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-submit-public:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.45);
        }

        .form-step-pane {
            animation: fadeInPane 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        @keyframes fadeInPane {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 640px) {
            body {
                padding: 16px 12px 60px;
            }
            .form-header-card {
                padding: 24px 20px;
            }
            .field-box {
                padding: 18px 18px;
            }
            .step-nav-bar {
                padding: 16px 18px;
                flex-direction: column-reverse;
            }
            .step-nav-bar button, .step-nav-bar .btn-step-prev, .step-nav-bar .btn-step-next, .step-nav-bar .btn-submit-public {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

    <div class="public-container">
        <?= renderAd('FORM_TOP') ?>

        <!-- ─── Form Header Card ─── -->
        <div class="form-header-card" id="form-header-card">
            <h1 class="form-title"><?= e($form->title) ?></h1>
            
            <?php if (!empty($form->description)): ?>
                <div class="form-desc-box"><?= nl2br(e($form->description)) ?></div>
            <?php endif; ?>

            <div class="form-meta-row">
                <span class="req-hint-pill">
                    <span style="color: #ef4444; font-weight: 800;">*</span> Wajib diisi
                </span>
                <span style="font-size: 12px; color: #64748b; font-weight: 600;">
                    🔒 Formulir Resmi Terverifikasi
                </span>
            </div>
        </div>

        <?php if ($hasMultipleSections): ?>
            <!-- ─── Multi-Section Step Tracker ─── -->
            <div class="step-progress-wrapper" id="step-progress-wrapper">
                <div class="step-progress-header">
                    <div class="step-progress-title">
                        <span class="step-progress-pill">Bagian <span id="current-step-num">1</span> / <?= $totalSections ?></span>
                        <span id="current-step-title-display" style="color: #0f172a;"><?= e($sections[0]['title']) ?></span>
                    </div>
                    <span class="step-progress-percent" id="current-step-percent">
                        <?= round((1 / $totalSections) * 100) ?>%
                    </span>
                </div>
                
                <div class="step-progress-track">
                    <div class="step-progress-fill" id="step-progress-fill" style="width: <?= round((1 / $totalSections) * 100) ?>%;"></div>
                </div>

                <!-- Breadcrumbs Pills -->
                <div class="step-crumbs-list">
                    <?php foreach ($sections as $sIdx => $s): ?>
                        <div class="step-crumb-item <?= $sIdx === 0 ? 'active' : '' ?>" id="step-crumb-<?= $sIdx ?>">
                            <span class="step-crumb-dot"><?= $sIdx + 1 ?></span>
                            <span><?= e($s['title']) ?></span>
                            <?php if ($sIdx < $totalSections - 1): ?>
                                <span style="color: #cbd5e1; font-weight: 400; margin-left: 2px;">›</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($formErrors)): ?>
            <div class="card mb-4" style="background: #fef2f2; border: 1px solid #fca5a5; padding: 16px 20px; border-radius: 14px;">
                <div class="flex items-center gap-2" style="color: #b91c1c; font-weight: 700; font-size: 14px;">
                    <span>⚠️</span>
                    <span>Mohon periksa dan lengkapi pertanyaan wajib di bawah ini.</span>
                </div>
            </div>
        <?php endif; ?>

        <!-- ─── Dynamic Multi-Section Form ─── -->
        <form method="POST" action="<?= url("form/{$form->slug}/submit") ?>" enctype="multipart/form-data" id="public-form">
            <?= CSRF::field() ?>

            <?php foreach ($sections as $secIndex => $sec): ?>
                <div class="form-step-pane" id="step-pane-<?= $secIndex ?>" data-step-index="<?= $secIndex ?>" data-step-title="<?= e($sec['title']) ?>" style="<?= $secIndex === 0 ? '' : 'display: none;' ?>">
                    
                    <?php if ($hasMultipleSections && !empty($sec['title']) && $sec['title'] !== 'Informasi Utama'): ?>
                        <div class="section-banner-card">
                            <h2 class="section-banner-title">
                                📑 <?= e($sec['title']) ?>
                            </h2>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($sec['fields'] as $fIdx => $field): ?>
                        <?php 
                        $fieldName = $field->field_name;
                        $hasError = isset($formErrors[$fieldName]);
                        $options = json_decode($field->options_json ?? '[]', true) ?: [];
                        $oldValue = Session::old($fieldName);
                        ?>

                        <?php if ($field->field_type === 'description'): ?>
                            <div class="card mb-4" style="background: #ffffff; padding: 18px 22px; border-radius: 14px; border-left: 4px solid var(--info-500); border-top: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                                <p style="font-size: 14px; color: #475569; margin: 0; line-height: 1.65;">
                                    <?= nl2br(e($field->description || $field->label)) ?>
                                </p>
                            </div>
                        <?php else: ?>

                            <!-- Standard Question Box -->
                            <?php
                                $settingsData = json_decode($field->settings_json ?? '{}', true) ?: [];
                                $condLogic = $settingsData['conditional_logic'] ?? null;
                                $hasCond = !empty($condLogic['enabled']) && !empty($condLogic['target_field']);
                                $condLogicJson = $hasCond ? json_encode($condLogic) : '';
                                $initiallyHidden = ($hasCond && ($condLogic['action'] ?? 'show') === 'show');
                            ?>
                            <div class="field-box <?= $hasError ? 'is-invalid-box' : '' ?>"
                                 id="field-box-<?= e($fieldName) ?>"
                                 data-field-name="<?= e($fieldName) ?>"
                                 data-field-type="<?= e($field->field_type) ?>"
                                 data-is-required="<?= $field->is_required ? '1' : '0' ?>"
                                 data-conditional='<?= e($condLogicJson) ?>'
                                 <?= $initiallyHidden ? 'style="display: none;"' : '' ?>>
                                
                                <label class="field-label" for="input-field-<?= e($fieldName) ?>">
                                    <?= e($field->label) ?>
                                    <?php if ($field->is_required): ?>
                                        <span class="req-star" title="Wajib Diisi">*</span>
                                    <?php endif; ?>
                                </label>

                                <?php if (!empty($field->description)): ?>
                                    <div class="field-desc">
                                        <?= e($field->description) ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Field Type Renderers -->
                                <?php switch ($field->field_type): 
                                    case 'text': ?>
                                        <input type="text" 
                                               id="input-field-<?= e($fieldName) ?>"
                                               name="<?= e($fieldName) ?>" 
                                               class="form-control"
                                               placeholder="<?= e($field->placeholder ?: 'Tulis jawaban Anda di sini...') ?>"
                                               value="<?= e($oldValue) ?>"
                                               <?= $field->is_required ? 'required' : '' ?>>
                                        <?php break; ?>

                                    <?php case 'textarea': ?>
                                        <textarea id="input-field-<?= e($fieldName) ?>"
                                                  name="<?= e($fieldName) ?>" 
                                                  class="form-control"
                                                  placeholder="<?= e($field->placeholder ?: 'Tulis jawaban panjang Anda di sini...') ?>"
                                                  <?= $field->is_required ? 'required' : '' ?>><?= e($oldValue) ?></textarea>
                                        <?php break; ?>

                                    <?php case 'number': ?>
                                        <input type="number" 
                                               id="input-field-<?= e($fieldName) ?>"
                                               name="<?= e($fieldName) ?>" 
                                               class="form-control"
                                               placeholder="<?= e($field->placeholder ?: '0') ?>"
                                               value="<?= e($oldValue) ?>"
                                               <?= $field->is_required ? 'required' : '' ?>>
                                        <?php break; ?>

                                    <?php case 'email': ?>
                                        <input type="email" 
                                               id="input-field-<?= e($fieldName) ?>"
                                               name="<?= e($fieldName) ?>" 
                                               class="form-control"
                                               placeholder="<?= e($field->placeholder ?: 'nama@email.com') ?>"
                                               value="<?= e($oldValue) ?>"
                                               <?= $field->is_required ? 'required' : '' ?>>
                                        <?php break; ?>

                                    <?php case 'phone': ?>
                                        <input type="tel" 
                                               id="input-field-<?= e($fieldName) ?>"
                                               name="<?= e($fieldName) ?>" 
                                               class="form-control"
                                               placeholder="<?= e($field->placeholder ?: '0812-3456-7890') ?>"
                                               value="<?= e($oldValue) ?>"
                                               <?= $field->is_required ? 'required' : '' ?>>
                                        <?php break; ?>

                                    <?php case 'date': ?>
                                        <input type="date" 
                                               id="input-field-<?= e($fieldName) ?>"
                                               name="<?= e($fieldName) ?>" 
                                               class="form-control"
                                               value="<?= e($oldValue) ?>"
                                               <?= $field->is_required ? 'required' : '' ?>>
                                        <?php break; ?>

                                    <?php case 'time': ?>
                                        <input type="time" 
                                               id="input-field-<?= e($fieldName) ?>"
                                               name="<?= e($fieldName) ?>" 
                                               class="form-control"
                                               value="<?= e($oldValue) ?>"
                                               <?= $field->is_required ? 'required' : '' ?>>
                                        <?php break; ?>

                                    <?php case 'dropdown': ?>
                                        <select id="input-field-<?= e($fieldName) ?>"
                                                name="<?= e($fieldName) ?>" 
                                                class="form-control" 
                                                onchange="evaluateConditionalLogic()" 
                                                <?= $field->is_required ? 'required' : '' ?>>
                                            <option value=""><?= e($field->placeholder ?: '-- Pilih salah satu opsi --') ?></option>
                                            <?php foreach ($options as $opt): ?>
                                                <option value="<?= e($opt) ?>" <?= $oldValue === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php break; ?>

                                    <?php case 'radio': ?>
                                        <div class="option-cards-group">
                                            <?php foreach ($options as $opt): ?>
                                                <label class="option-card-label">
                                                    <input type="radio" 
                                                           name="<?= e($fieldName) ?>" 
                                                           value="<?= e($opt) ?>" 
                                                           onchange="evaluateConditionalLogic()" 
                                                           <?= $oldValue === $opt ? 'checked' : '' ?> 
                                                           <?= $field->is_required ? 'required' : '' ?>>
                                                    <span class="option-card-text"><?= e($opt) ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php break; ?>

                                    <?php case 'checkbox': ?>
                                        <div class="option-cards-group">
                                            <?php foreach ($options as $opt): ?>
                                                <label class="option-card-label">
                                                    <input type="checkbox" 
                                                           name="<?= e($fieldName) ?>[]" 
                                                           value="<?= e($opt) ?>" 
                                                           onchange="evaluateConditionalLogic()" 
                                                           <?= (is_array($oldValue) && in_array($opt, $oldValue)) ? 'checked' : '' ?>>
                                                    <span class="option-card-text"><?= e($opt) ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php break; ?>

                                    <?php case 'file':
                                          case 'image': ?>
                                        <div class="file-upload-box" onclick="document.getElementById('file-input-<?= e($fieldName) ?>').click()">
                                            <div style="font-size: 28px; margin-bottom: 6px;"><?= $field->field_type === 'image' ? '🖼️' : '📎' ?></div>
                                            <div style="font-size: 14px; font-weight: 700; color: #1e293b;" id="file-label-<?= e($fieldName) ?>">
                                                Klik untuk memilih berkas <?= $field->field_type === 'image' ? 'foto' : 'dokumen' ?>
                                            </div>
                                            <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">
                                                Maksimal ukuran 10 MB
                                            </div>
                                            <input type="file" 
                                                   id="file-input-<?= e($fieldName) ?>"
                                                   name="<?= e($fieldName) ?>" 
                                                   style="display: none;" 
                                                   onchange="handleFileInputChange(this, '<?= e($fieldName) ?>')"
                                                   <?= $field->is_required ? 'required' : '' ?>>
                                        </div>
                                        <?php break; ?>

                                    <?php case 'signature': ?>
                                        <div class="signature-wrapper">
                                            <canvas id="canvas-<?= e($fieldName) ?>" width="650" height="160"></canvas>
                                            <div class="signature-guide-line">
                                                <span class="signature-guide-text">Tanda Tangan Digital Di Sini</span>
                                            </div>
                                            <input type="hidden" name="<?= e($fieldName) ?>" id="input-<?= e($fieldName) ?>" value="<?= e($oldValue) ?>">
                                            <div class="signature-actions">
                                                <button type="button" class="btn btn-secondary btn-sm" onclick="clearSignature('<?= e($fieldName) ?>')" style="font-size: 11px; padding: 4px 10px; border-radius: 6px;">
                                                    🔄 Hapus Goresan
                                                </button>
                                            </div>
                                        </div>
                                        <?php break; ?>

                                <?php endswitch; ?>

                                <div class="field-error-msg" id="error-feedback-<?= e($fieldName) ?>">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    <span><?= $hasError ? e($formErrors[$fieldName]) : 'Pertanyaan ini wajib diisi.' ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <!-- Step Navigation Bar -->
                    <div class="step-nav-bar">
                        <div>
                            <?php if ($secIndex > 0): ?>
                                <button type="button" class="btn-step-prev" onclick="goToStep(<?= $secIndex - 1 ?>)">
                                    &larr; Bagian Sebelumnya
                                </button>
                            <?php endif; ?>
                        </div>

                        <div>
                            <?php if ($secIndex < $totalSections - 1): ?>
                                <button type="button" class="btn-step-next" onclick="validateAndGoToStep(<?= $secIndex ?>, <?= $secIndex + 1 ?>)">
                                    <span>Lanjut ke Bagian Berikutnya</span>
                                    <span>&rarr;</span>
                                </button>
                            <?php else: ?>
                                <button type="submit" class="btn-submit-public" onclick="return validateCurrentSectionBeforeSubmit(<?= $secIndex ?>)">
                                    <span>Kirim Formulir</span>
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>

        </form>

        <!-- ─── Form Bottom Ad Slot ─── -->
        <?= renderAd('FORM_BOTTOM') ?>

        <div class="text-center mt-5" style="font-size: 12.5px; color: #94a3b8; font-weight: 500;">
            Diberdayakan oleh <strong style="color: #4f46e5;">ASR FORM</strong> &bull; Platform Form Builder & Dokumen Otomatis
        </div>
    </div>

    <!-- ─── Interactive Script for Multi-Section Flow, Signatures & Conditional Logic ─── -->
    <script>
    const totalSections = <?= (int) $totalSections ?>;
    let currentStep = 0;

    // ─── File Upload Label Handler ───
    function handleFileInputChange(input, fieldName) {
        const labelEl = document.getElementById('file-label-' + fieldName);
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2);
            if (labelEl) labelEl.textContent = `✓ ${fileName} (${fileSize} MB)`;
            const box = document.getElementById('field-box-' + fieldName);
            if (box) box.classList.remove('is-invalid-box');
        }
    }

    // ─── Multi-Section Step Navigation & Client Validation ───
    function validateSection(stepIndex) {
        const stepPane = document.getElementById('step-pane-' + stepIndex);
        if (!stepPane) return true;

        const visibleBoxes = Array.from(stepPane.querySelectorAll('.field-box')).filter(box => {
            return box.style.display !== 'none' && box.offsetParent !== null;
        });

        let isStepValid = true;
        let firstInvalidElement = null;

        visibleBoxes.forEach(box => {
            const isRequired = box.dataset.isRequired === '1';
            const fieldName = box.dataset.fieldName;
            const fieldType = box.dataset.fieldType;
            const errorEl = document.getElementById('error-feedback-' + fieldName);

            let isFieldValid = true;

            if (isRequired) {
                if (fieldType === 'radio') {
                    const radios = box.querySelectorAll('input[type="radio"]');
                    const isChecked = Array.from(radios).some(r => r.checked);
                    if (!isChecked) isFieldValid = false;
                } else if (fieldType === 'checkbox') {
                    const checkboxes = box.querySelectorAll('input[type="checkbox"]');
                    const isChecked = Array.from(checkboxes).some(c => c.checked);
                    if (!isChecked) isFieldValid = false;
                } else if (fieldType === 'dropdown') {
                    const select = box.querySelector('select');
                    if (!select || !select.value) isFieldValid = false;
                } else if (fieldType === 'signature') {
                    const sigInput = document.getElementById('input-' + fieldName);
                    if (!sigInput || !sigInput.value.trim()) isFieldValid = false;
                } else if (fieldType === 'file' || fieldType === 'image') {
                    const fileInput = box.querySelector('input[type="file"]');
                    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                        isFieldValid = false;
                    }
                } else {
                    const input = box.querySelector('input, textarea');
                    if (!input || !input.value.trim()) {
                        isFieldValid = false;
                        if (errorEl) errorEl.querySelector('span').textContent = 'Pertanyaan ini wajib diisi.';
                    } else if (fieldType === 'email') {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(input.value.trim())) {
                            isFieldValid = false;
                            if (errorEl) errorEl.querySelector('span').textContent = 'Format alamat email tidak valid.';
                        }
                    }
                }
            }

            if (!isFieldValid) {
                box.classList.add('is-invalid-box');
                isStepValid = false;
                if (!firstInvalidElement) {
                    firstInvalidElement = box;
                }
            } else {
                box.classList.remove('is-invalid-box');
            }
        });

        if (!isStepValid && firstInvalidElement) {
            firstInvalidElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            const focusTarget = firstInvalidElement.querySelector('input, select, textarea');
            if (focusTarget) focusTarget.focus();
        }

        return isStepValid;
    }

    function validateAndGoToStep(fromStep, toStep) {
        if (!validateSection(fromStep)) {
            return false;
        }
        goToStep(toStep);
    }

    function validateCurrentSectionBeforeSubmit(secIndex) {
        return validateSection(secIndex);
    }

    function goToStep(stepIndex) {
        if (stepIndex < 0 || stepIndex >= totalSections) return;

        // Hide all step panes
        document.querySelectorAll('.form-step-pane').forEach((pane, idx) => {
            pane.style.display = (idx === stepIndex) ? 'block' : 'none';
        });

        currentStep = stepIndex;

        // Update progress bar & text
        const stepNumDisplay = document.getElementById('current-step-num');
        const stepTitleDisplay = document.getElementById('current-step-title-display');
        const stepPercentDisplay = document.getElementById('current-step-percent');
        const stepProgressFill = document.getElementById('step-progress-fill');

        const activePane = document.getElementById('step-pane-' + stepIndex);
        const activeTitle = activePane ? activePane.dataset.stepTitle : '';

        const progressPercent = Math.round(((stepIndex + 1) / totalSections) * 100);

        if (stepNumDisplay) stepNumDisplay.textContent = stepIndex + 1;
        if (stepTitleDisplay) stepTitleDisplay.textContent = activeTitle;
        if (stepPercentDisplay) stepPercentDisplay.textContent = progressPercent + '%';
        if (stepProgressFill) stepProgressFill.style.width = progressPercent + '%';

        // Update breadcrumb pills
        document.querySelectorAll('.step-crumb-item').forEach((item, idx) => {
            item.classList.toggle('active', idx === stepIndex);
            item.classList.toggle('completed', idx < stepIndex);
        });

        // Re-evaluate conditional logic for the newly opened step
        evaluateConditionalLogic();

        // Smooth scroll to top of form
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // ─── Canvas Signatures ───
    document.querySelectorAll('canvas[id^="canvas-"]').forEach(canvas => {
        const fieldName = canvas.id.replace('canvas-', '');
        const ctx = canvas.getContext('2d');
        let drawing = false;

        function resizeCanvas() {
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
            ctx.strokeStyle = '#0f172a';
            ctx.lineWidth = 2.5;
            ctx.lineCap = 'round';
        }
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return { x: clientX - rect.left, y: clientY - rect.top };
        }

        function start(e) {
            drawing = true;
            const pos = getPos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
            e.preventDefault();
        }

        function draw(e) {
            if (!drawing) return;
            const pos = getPos(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            document.getElementById('input-' + fieldName).value = canvas.toDataURL();
            const box = document.getElementById('field-box-' + fieldName);
            if (box) box.classList.remove('is-invalid-box');
            e.preventDefault();
        }

        function stop() {
            drawing = false;
        }

        canvas.addEventListener('mousedown', start);
        canvas.addEventListener('mousemove', draw);
        window.addEventListener('mouseup', stop);

        canvas.addEventListener('touchstart', start, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        window.addEventListener('touchend', stop);
        window.addEventListener('touchcancel', stop);
    });

    function clearSignature(fieldName) {
        const canvas = document.getElementById('canvas-' + fieldName);
        if (canvas) {
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            document.getElementById('input-' + fieldName).value = '';
        }
    }

    // ─── Dynamic Conditional Logic Engine ───
    function evaluateConditionalLogic() {
        const fieldBoxes = document.querySelectorAll('.field-box[data-conditional]');
        
        function getFieldValue(targetName) {
            const cleanTarget = (targetName || '').trim();
            if (!cleanTarget) return '';

            // 1. Radio check
            const radioGroup = document.querySelectorAll(`input[type="radio"][name="${cleanTarget}"], #field-box-${cleanTarget} input[type="radio"]`);
            if (radioGroup.length > 0) {
                const checked = Array.from(radioGroup).find(r => r.checked);
                return checked ? checked.value : '';
            }

            // 2. Checkbox check
            const checkboxGroup = document.querySelectorAll(`input[type="checkbox"][name="${cleanTarget}[]"], input[type="checkbox"][name="${cleanTarget}"], #field-box-${cleanTarget} input[type="checkbox"]`);
            if (checkboxGroup.length > 0) {
                const checked = Array.from(checkboxGroup).filter(c => c.checked);
                return checked.map(c => c.value);
            }

            // 3. Select dropdown
            const selectEl = document.querySelector(`select[name="${cleanTarget}"], #field-box-${cleanTarget} select`);
            if (selectEl) return selectEl.value;

            // 4. Text / Number / Textarea inputs
            const textEl = document.querySelector(`input:not([type="radio"]):not([type="checkbox"])[name="${cleanTarget}"], textarea[name="${cleanTarget}"], #field-box-${cleanTarget} input:not([type="radio"]):not([type="checkbox"])`);
            if (textEl) return textEl.value;

            return '';
        }

        function testCondition(val, op, cmp) {
            if (Array.isArray(val)) {
                if (op === 'not_empty') return val.length > 0;
                if (op === 'empty') return val.length === 0;
                return val.some(v => testCondition(v, op, cmp));
            }

            const str = (val || '').toString().trim().toLowerCase();
            const target = (cmp || '').toString().trim().toLowerCase();

            if (op === 'not_empty') return str.length > 0;
            if (op === 'empty') return str.length === 0;
            if (!str) return false;

            if (op === 'equals') {
                return str === target || str.startsWith(target + ' ') || str.startsWith(target + ',');
            }
            if (op === 'not_equals') {
                return str !== target && !str.startsWith(target + ' ') && !str.startsWith(target + ',');
            }
            if (op === 'contains') {
                const words = str.split(/[\s,._-]+/);
                return words.includes(target) || str === target || str.startsWith(target + ' ');
            }
            return false;
        }

        fieldBoxes.forEach(box => {
            const rawCond = box.dataset.conditional;
            if (!rawCond || rawCond === '""' || rawCond === '{}') return;

            try {
                const cond = JSON.parse(rawCond);
                if (!cond || !cond.enabled || !cond.target_field) return;

                const targetVal = getFieldValue(cond.target_field);
                const isMatch = testCondition(targetVal, cond.operator, cond.value || '');
                const shouldShow = (cond.action === 'show') ? isMatch : !isMatch;

                if (shouldShow) {
                    box.style.display = 'block';
                } else {
                    box.style.display = 'none';
                    box.classList.remove('is-invalid-box');
                }
            } catch (e) {
                // Ignore parse errors
            }
        });
    }

    // Attach listeners on all form inputs and run immediately
    evaluateConditionalLogic();

    document.addEventListener('DOMContentLoaded', () => {
        evaluateConditionalLogic();

        const form = document.getElementById('public-form');
        if (form) {
            form.addEventListener('input', (e) => {
                evaluateConditionalLogic();
                const fieldBox = e.target.closest('.field-box');
                if (fieldBox) fieldBox.classList.remove('is-invalid-box');
            });
            form.addEventListener('change', (e) => {
                evaluateConditionalLogic();
                const fieldBox = e.target.closest('.field-box');
                if (fieldBox) fieldBox.classList.remove('is-invalid-box');
            });
        }
    });
    </script>
</body>
</html>
