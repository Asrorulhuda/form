<?php
use App\Core\CSRF;
use App\Core\Session;

$formErrors = Session::getFlash('form_errors') ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Formulir Online') ?></title>
    <meta name="description" content="<?= e(substr(strip_tags($form->description ?? ''), 0, 150)) ?>">
    <?= CSRF::meta() ?>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%234f46e5'/><text x='50' y='68' font-size='55' font-weight='bold' fill='white' text-anchor='middle' font-family='Arial'>A</text></svg>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <?= adsenseHead() ?>
    <style>
        body {
            background-color: #f1f5f9;
            min-height: 100vh;
            padding: 30px 16px 60px;
        }
        .public-container {
            max-width: 680px;
            margin: 0 auto;
        }
        .form-header-card {
            background: #ffffff;
            border-radius: var(--radius-lg);
            border-top: 8px solid var(--primary-600);
            padding: 32px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 20px;
            border-left: 1px solid var(--border-subtle);
            border-right: 1px solid var(--border-subtle);
            border-bottom: 1px solid var(--border-subtle);
        }
        .field-box {
            background: #ffffff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-subtle);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 18px;
            transition: all 0.2s ease;
        }
        .field-box:focus-within {
            border-color: var(--primary-400);
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.08);
        }
        .field-box.is-invalid-box {
            border-left: 4px solid var(--danger-500);
        }
        .signature-pad-wrapper {
            border: 1px solid var(--border-input);
            border-radius: var(--radius-md);
            background: #fafafa;
            position: relative;
            touch-action: none;
        }
        .rating-stars {
            display: flex;
            gap: 10px;
            font-size: 28px;
            cursor: pointer;
        }
        .rating-star {
            color: #cbd5e1;
            transition: color 0.15s ease, transform 0.15s ease;
        }
        .rating-star.active,
        .rating-star.hovered {
            color: #f59e0b;
            transform: scale(1.15);
        }
        .scale-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            padding: 10px 0;
        }
        .scale-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="public-container">
        <?= renderAd('FORM_TOP') ?>

        <!-- ─── Form Header Card ─── -->
        <div class="form-header-card fade-in">
            <h1 style="font-size: 26px; font-weight: 800; color: var(--text-primary); margin-bottom: 8px; line-height: 1.25;">
                <?= e($form->title) ?>
            </h1>
            <?php if (!empty($form->description)): ?>
                <div style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 16px;">
                    <?= nl2br(e($form->description)) ?>
                </div>
            <?php endif; ?>
            <div style="border-top: 1px solid #f1f5f9; padding-top: 12px; font-size: 12px; color: var(--danger-500); font-weight: 600;">
                * Menunjukkan pertanyaan yang wajib diisi
            </div>
        </div>

        <?php if (!empty($formErrors)): ?>
            <div class="login-error mb-4" style="border-radius: var(--radius-lg);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>Mohon lengkapi seluruh isian wajib sebelum mengirimkan formulir.</div>
            </div>
        <?php endif; ?>

        <!-- ─── Dynamic Form ─── -->
        <form method="POST" action="<?= url("form/{$form->slug}/submit") ?>" enctype="multipart/form-data" id="public-form">
            <?= CSRF::field() ?>

            <?php foreach ($fields as $field): ?>
                <?php 
                $fieldName = $field->field_name;
                $hasError = isset($formErrors[$fieldName]);
                $options = json_decode($field->options_json ?? '[]', true) ?: [];
                $oldValue = Session::old($fieldName);
                ?>

                <!-- Section / Heading -->
                <?php if ($field->field_type === 'heading'): ?>
                    <div style="margin: 28px 0 14px;">
                        <h3 style="font-size: 18px; font-weight: 800; color: var(--primary-700); margin: 0;">
                            <?= e($field->label) ?>
                        </h3>
                    </div>
                <?php elseif ($field->field_type === 'description'): ?>
                    <div class="card mb-4" style="background: #ffffff; padding: 18px; border-left: 4px solid var(--info-500);">
                        <p style="font-size: 14px; color: var(--text-secondary); margin: 0; line-height: 1.6;">
                            <?= nl2br(e($field->description)) ?>
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
                    <div class="field-box <?= $hasError ? 'is-invalid-box' : '' ?> fade-in"
                         id="field-box-<?= e($fieldName) ?>"
                         data-field-name="<?= e($fieldName) ?>"
                         data-is-required="<?= $field->is_required ? '1' : '0' ?>"
                         data-conditional='<?= e($condLogicJson) ?>'
                         <?= $initiallyHidden ? 'style="display: none;"' : '' ?>>
                        <label class="form-label" style="font-size: 15px; margin-bottom: 8px;">
                            <?= e($field->label) ?>
                            <?php if ($field->is_required): ?>
                                <span style="color: var(--danger-500); font-weight: bold;">*</span>
                            <?php endif; ?>
                        </label>

                        <?php if (!empty($field->description)): ?>
                            <p class="form-help" style="margin-bottom: 12px; font-size: 13px;">
                                <?= e($field->description) ?>
                            </p>
                        <?php endif; ?>

                        <!-- Field Type Renderers -->
                        <?php switch ($field->field_type): 
                            case 'text': ?>
                                <input type="text" name="<?= e($fieldName) ?>" class="form-control <?= $hasError ? 'is-invalid' : '' ?>"
                                       placeholder="<?= e($field->placeholder ?? 'Jawaban Anda') ?>"
                                       value="<?= e($oldValue) ?>"
                                       <?= $field->is_required ? 'required' : '' ?>>
                                <?php break; ?>

                            <?php case 'textarea': ?>
                                <textarea name="<?= e($fieldName) ?>" class="form-control <?= $hasError ? 'is-invalid' : '' ?>"
                                          placeholder="<?= e($field->placeholder ?? 'Jawaban Anda') ?>"
                                          style="min-height: 90px;"
                                          <?= $field->is_required ? 'required' : '' ?>><?= e($oldValue) ?></textarea>
                                <?php break; ?>

                            <?php case 'number': ?>
                                <input type="number" name="<?= e($fieldName) ?>" class="form-control <?= $hasError ? 'is-invalid' : '' ?>"
                                       placeholder="<?= e($field->placeholder ?? 'Angka') ?>"
                                       value="<?= e($oldValue) ?>"
                                       <?= $field->is_required ? 'required' : '' ?>>
                                <?php break; ?>

                            <?php case 'email': ?>
                                <input type="email" name="<?= e($fieldName) ?>" class="form-control <?= $hasError ? 'is-invalid' : '' ?>"
                                       placeholder="<?= e($field->placeholder ?? 'email@domain.com') ?>"
                                       value="<?= e($oldValue) ?>"
                                       <?= $field->is_required ? 'required' : '' ?>>
                                <?php break; ?>

                            <?php case 'phone': ?>
                                <input type="tel" name="<?= e($fieldName) ?>" class="form-control <?= $hasError ? 'is-invalid' : '' ?>"
                                       placeholder="<?= e($field->placeholder ?? '081234567890') ?>"
                                       value="<?= e($oldValue) ?>"
                                       <?= $field->is_required ? 'required' : '' ?>>
                                <?php break; ?>

                            <?php case 'date': ?>
                                <input type="date" name="<?= e($fieldName) ?>" class="form-control <?= $hasError ? 'is-invalid' : '' ?>"
                                       value="<?= e($oldValue) ?>"
                                       <?= $field->is_required ? 'required' : '' ?>>
                                <?php break; ?>

                            <?php case 'time': ?>
                                <input type="time" name="<?= e($fieldName) ?>" class="form-control <?= $hasError ? 'is-invalid' : '' ?>"
                                       value="<?= e($oldValue) ?>"
                                       <?= $field->is_required ? 'required' : '' ?>>
                                <?php break; ?>

                            <?php case 'dropdown': ?>
                                <select name="<?= e($fieldName) ?>" class="form-control <?= $hasError ? 'is-invalid' : '' ?>" onchange="evaluateConditionalLogic()" <?= $field->is_required ? 'required' : '' ?>>
                                    <option value=""><?= e($field->placeholder ?? 'Pilih Opsi...') ?></option>
                                    <?php foreach ($options as $opt): ?>
                                        <option value="<?= e($opt) ?>" <?= $oldValue === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php break; ?>

                            <?php case 'radio': ?>
                                <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 4px;">
                                    <?php foreach ($options as $opt): ?>
                                        <label style="display: flex; align-items: center; gap: 10px; font-size: 14px; color: var(--text-primary); cursor: pointer;">
                                            <input type="radio" name="<?= e($fieldName) ?>" value="<?= e($opt) ?>" onchange="evaluateConditionalLogic()" <?= $oldValue === $opt ? 'checked' : '' ?> <?= $field->is_required ? 'required' : '' ?>>
                                            <span><?= e($opt) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <?php break; ?>

                            <?php case 'checkbox': ?>
                                <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 4px;">
                                    <?php foreach ($options as $opt): ?>
                                        <label style="display: flex; align-items: center; gap: 10px; font-size: 14px; color: var(--text-primary); cursor: pointer;">
                                            <input type="checkbox" name="<?= e($fieldName) ?>[]" value="<?= e($opt) ?>" onchange="evaluateConditionalLogic()" <?= (is_array($oldValue) && in_array($opt, $oldValue)) ? 'checked' : '' ?>>
                                            <span><?= e($opt) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <?php break; ?>

                            <?php case 'file':
                                  case 'image': ?>
                                <input type="file" name="<?= e($fieldName) ?>" class="form-control <?= $hasError ? 'is-invalid' : '' ?>" <?= $field->is_required ? 'required' : '' ?>>
                                <?php break; ?>

                            <?php case 'signature': ?>
                                <div class="signature-pad-wrapper">
                                    <canvas id="canvas-<?= e($fieldName) ?>" width="600" height="150" style="width: 100%; height: 150px; cursor: crosshair;"></canvas>
                                    <input type="hidden" name="<?= e($fieldName) ?>" id="input-<?= e($fieldName) ?>">
                                    <div style="position: absolute; bottom: 8px; right: 8px;">
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="clearSignature('<?= e($fieldName) ?>')">Hapus Goresan</button>
                                    </div>
                                </div>
                                <?php break; ?>

                            <?php case 'rating': ?>
                                <div class="rating-stars" id="rating-<?= e($fieldName) ?>">
                                    <?php for ($s = 1; $s <= 5; $s++): ?>
                                        <span class="rating-star" data-val="<?= $s ?>" onclick="setRating('<?= e($fieldName) ?>', <?= $s ?>)">★</span>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="<?= e($fieldName) ?>" id="input-<?= e($fieldName) ?>" value="<?= e($oldValue) ?>" <?= $field->is_required ? 'required' : '' ?>>
                                <?php break; ?>

                            <?php case 'scale': ?>
                                <div class="scale-options">
                                    <?php for ($sc = 1; $sc <= 10; $sc++): ?>
                                        <label class="scale-item">
                                            <span><?= $sc ?></span>
                                            <input type="radio" name="<?= e($fieldName) ?>" value="<?= $sc ?>" <?= $oldValue == $sc ? 'checked' : '' ?> <?= $field->is_required ? 'required' : '' ?>>
                                        </label>
                                    <?php endfor; ?>
                                </div>
                                <?php break; ?>

                        <?php endswitch; ?>

                        <?php if ($hasError): ?>
                            <div class="form-error"><?= e($formErrors[$fieldName]) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <div class="flex items-center justify-between mt-4">
                <button type="submit" class="btn btn-primary btn-lg" style="min-width: 160px;">
                    Kirim Formulir
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
                <button type="reset" class="btn btn-secondary btn-sm">Kosongkan Formulir</button>
            </div>
        </form>

        <!-- ─── Form Bottom Ad Slot ─── -->
        <?= renderAd('FORM_BOTTOM') ?>

        <div class="text-center mt-5" style="font-size: 12px; color: var(--text-muted);">
            Diberdayakan oleh <strong style="color: var(--primary-600);">ASR FORM</strong> &bull; Platform Form Builder & Dokumen Mandiri
        </div>
    </div>

    <!-- ─── Interactive Script for Canvas Signatures & Rating ─── -->
    <script>
    // Signature Pads
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
            e.preventDefault();
        }

        function stop() {
            drawing = false;
        }

        canvas.addEventListener('mousedown', start);
        canvas.addEventListener('mousemove', draw);
        window.addEventListener('mouseup', stop);

        canvas.addEventListener('touchstart', start);
        canvas.addEventListener('touchmove', draw);
        window.addEventListener('touchend', stop);
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

            // 1. Radio check (ONLY return value if checked!)
            const radioGroup = document.querySelectorAll(`input[type="radio"][name="${cleanTarget}"], #field-box-${cleanTarget} input[type="radio"]`);
            if (radioGroup.length > 0) {
                const checked = Array.from(radioGroup).find(r => r.checked);
                return checked ? checked.value : '';
            }

            // 2. Checkbox check (ONLY return values if checked!)
            const checkboxGroup = document.querySelectorAll(`input[type="checkbox"][name="${cleanTarget}[]"], input[type="checkbox"][name="${cleanTarget}"], #field-box-${cleanTarget} input[type="checkbox"]`);
            if (checkboxGroup.length > 0) {
                const checked = Array.from(checkboxGroup).filter(c => c.checked);
                return checked.map(c => c.value);
            }

            // 3. Select dropdown
            const selectEl = document.querySelector(`select[name="${cleanTarget}"], #field-box-${cleanTarget} select`);
            if (selectEl) return selectEl.value;

            // 4. Text / Number / Textarea inputs (NEVER match unchecked radio/checkbox)
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
                    const inputs = box.querySelectorAll('input, select, textarea');
                    if (box.dataset.isRequired === '1') {
                        inputs.forEach(inp => {
                            if (inp.type !== 'hidden') inp.required = true;
                        });
                    }
                } else {
                    box.style.display = 'none';
                    const inputs = box.querySelectorAll('input, select, textarea');
                    inputs.forEach(inp => {
                        inp.required = false;
                    });
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
            form.addEventListener('input', evaluateConditionalLogic);
            form.addEventListener('change', evaluateConditionalLogic);
        }
    });
    </script>
</body>
</html>
