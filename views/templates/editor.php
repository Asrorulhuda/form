<?php
use App\Core\CSRF;
use App\Core\Session;

$isEdit = $isEdit ?? false;
$template = $template ?? null;
$content = $template->content ?? '';
$preset = $preset ?? '';
?>

<!-- ═══════════════════════════════════════════════════════════
     BENTO STUDIO — PROFESSIONAL WORD & PRINT LETTER EDITOR
     Standardized for Indonesian Official Letter Format (Tata Naskah Dinas)
     ═══════════════════════════════════════════════════════════ -->
<div class="bento-grid">

    <!-- ─── 1. BENTO HERO BANNER (SPAN 12) ─── -->
    <div class="bento-col-12 bento-hero fade-in" style="background: radial-gradient(circle at 90% 20%, rgba(99, 102, 241, 0.15) 0%, rgba(255, 255, 255, 0) 60%), #ffffff; border: 1px solid rgba(99, 102, 241, 0.25);">
        <div class="bento-hero-left">
            <a href="<?= url('templates') ?>" class="btn btn-secondary btn-sm" style="font-weight: 700; border-radius: 10px;">
                &larr; Daftar Template
            </a>
            <div style="height: 28px; width: 1px; background: var(--border-subtle);"></div>
            <div>
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <span style="font-size: 22px;">📜</span>
                    <h2 class="bento-hero-title" style="margin: 0; font-size: 20px; font-weight: 900; color: #0f172a;">
                        <?= $isEdit ? 'Edit Template: ' . e($template->name) : 'Studio Editor Surat Resmi (.DOCX & Cetak)' ?>
                    </h2>
                    <span class="badge badge-success" style="font-size: 11px; font-weight: 800; padding: 3px 8px;">
                        ✓ Standar Tata Naskah Dinas
                    </span>
                </div>
                <div class="bento-hero-desc" style="font-size: 13px; color: var(--text-muted);">
                    Rancang dan cetak surat resmi berstandar A4/F4 (Kop Surat Gambar/Teks, Nomor, Biodata Rata Titik Dua, Tanda Tangan &amp; QR Code). Otomatis tersimpan ke Microsoft Word (.docx).
                </div>
            </div>
        </div>

        <div class="bento-hero-actions flex items-center gap-2 flex-wrap">
            <span id="detected-vars-badge" class="badge badge-primary" style="font-size: 12px; font-weight: 700; padding: 6px 12px; border-radius: 99px; display: inline-flex; align-items: center; gap: 6px; background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe;">
                🧩 <strong id="vars-count">0</strong> Variabel Terdeteksi
            </span>
            <button type="button" class="btn btn-secondary btn-sm" onclick="openPreviewModal()" id="btn-preview-top" style="font-weight: 800; background: #ffffff; border-color: #6366f1; color: #4338ca; box-shadow: 0 2px 8px rgba(99,102,241,0.15);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                Pratinjau / Siap Cetak
            </button>
            <button type="button" class="btn btn-primary btn-sm" onclick="submitLetterForm()" id="btn-save-top" style="box-shadow: 0 4px 14px rgba(79,70,229,0.35); font-weight: 800;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                <?= $isEdit ? 'Perbarui & Simpan Word' : 'Simpan & Konversi ke Word' ?>
            </button>
        </div>
    </div>

    <!-- ─── 2. BENTO SETTINGS & PRESETS (SPAN 12) ─── -->
    <div class="bento-col-12 bento-card fade-in" style="padding: 18px 24px; background: #ffffff;">
        <form method="POST" action="<?= $isEdit ? url("templates/{$template->id}/update-editor") : url('templates/store-editor') ?>" id="form-letter-editor">
            <?= CSRF::field() ?>
            <textarea name="content" id="hidden-content-input" style="display: none;"><?= e($content) ?></textarea>

            <div class="grid-3" style="gap: 16px; align-items: flex-end;">
                <div>
                    <label class="form-label" style="font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 4px;">
                        Nama Template Surat <span class="required">*</span>
                    </label>
                    <input type="text" name="name" id="template-name" class="form-control form-control-sm" 
                           placeholder="Contoh: Surat Keterangan Siswa / Surat Tugas Dinas" 
                           value="<?= e($template->name ?? ($preset ? ucwords(str_replace('-', ' ', $preset)) : 'Surat Keterangan Resmi')) ?>" required>
                </div>
                <div>
                    <label class="form-label" style="font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 4px;">
                        Kategori Surat <span class="required">*</span>
                    </label>
                    <select name="category" id="template-category" class="form-control form-control-sm" required>
                        <?php 
                        $cat = $template->category ?? 'Surat Keterangan';
                        $categories = ['Surat Keterangan', 'Surat Tugas', 'Surat Pernyataan', 'Surat Rekomendasi', 'Sertifikat & Piagam', 'Kwitansi & Invoice', 'Umum'];
                        foreach ($categories as $c): ?>
                            <option value="<?= $c ?>" <?= $cat === $c ? 'selected' : '' ?>><?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label" style="font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 4px;">
                        Status Publikasi
                    </label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="active" <?= ($template->status ?? 'active') === 'active' ? 'selected' : '' ?>>Aktif (Siap Digunakan Form & Generator)</option>
                        <option value="inactive" <?= ($template->status ?? '') === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
            </div>

            <!-- Presets Row -->
            <div class="mt-3 pt-3" style="border-top: 1px dashed var(--border-subtle); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                <div class="flex items-center gap-2 flex-wrap">
                    <span style="font-size: 12px; font-weight: 800; color: #4338ca; display: flex; align-items: center; gap: 4px;">
                        ⚡ Format Surat Resmi Siap Pakai:
                    </span>
                    <button type="button" class="btn btn-xs btn-white shadow-sm" onclick="loadPresetTemplate('surat-keterangan')">📜 Surat Keterangan</button>
                    <button type="button" class="btn btn-xs btn-white shadow-sm" onclick="loadPresetTemplate('surat-tugas')">🚗 Surat Tugas</button>
                    <button type="button" class="btn btn-xs btn-white shadow-sm" onclick="loadPresetTemplate('surat-rekomendasi')">⭐ Surat Rekomendasi</button>
                    <button type="button" class="btn btn-xs btn-white shadow-sm" onclick="loadPresetTemplate('surat-pernyataan')">✍️ Surat Pernyataan</button>
                    <button type="button" class="btn btn-xs btn-white shadow-sm" onclick="loadPresetTemplate('surat-izin')">📅 Permohonan Izin</button>
                    <button type="button" class="btn btn-xs btn-white shadow-sm" onclick="loadPresetTemplate('kwitansi')">🧾 Kwitansi</button>
                    <button type="button" class="btn btn-xs btn-white shadow-sm" onclick="loadPresetTemplate('undangan')">✉️ Undangan Dinas</button>
                    <button type="button" class="btn btn-xs btn-white shadow-sm" onclick="loadPresetTemplate('blank')" style="color: #64748b;">📄 Lembar Kosong</button>
                </div>

                <div class="text-sm text-muted" style="font-size: 11.5px; display: flex; align-items: center; gap: 6px;">
                    <span>📐 Ukuran Kertas:</span>
                    <select id="paper-size-select" class="t-select" onchange="changePaperSize(this.value)" style="height: 24px; font-size: 11px; padding: 2px 6px;">
                        <option value="A4" selected>A4 (210 × 297 mm)</option>
                        <option value="F4">F4 / Folio (215 × 330 mm)</option>
                        <option value="Letter">Letter (215.9 × 279.4 mm)</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- ─── 3. BENTO MAIN STAGE: EDITOR TOOLBAR & PRINT-ACCURATE CANVAS (SPAN 8) ─── -->
    <div class="bento-col-8">
        
        <!-- Document Sticky Toolbar -->
        <div class="bento-card mb-3" style="padding: 10px 14px; position: sticky; top: 70px; z-index: 30; background: #ffffff; border-color: rgba(99, 102, 241, 0.2); box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
            <div class="document-toolbar-inner">
                
                <!-- History -->
                <div class="t-group">
                    <button type="button" class="t-btn" onclick="execCmd('undo')" title="Undo (Ctrl+Z)"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"/></svg></button>
                    <button type="button" class="t-btn" onclick="execCmd('redo')" title="Redo (Ctrl+Y)"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 7v6h-6"/><path d="M3 17a9 9 0 0 1 9-9 9 9 0 0 1 6 2.3L21 13"/></svg></button>
                </div>

                <div class="t-sep"></div>

                <!-- Font & Size -->
                <div class="t-group">
                    <select class="t-select" onchange="execCmd('fontName', this.value)" title="Pilih Jenis Huruf Baku" style="width: 135px;">
                        <option value="Times New Roman" selected>Times New Roman (Resmi)</option>
                        <option value="Arial">Arial (Dinas)</option>
                        <option value="Calibri">Calibri</option>
                        <option value="Georgia">Georgia</option>
                        <option value="Bookman Old Style">Bookman Old Style</option>
                        <option value="Courier New">Courier New</option>
                    </select>

                    <select class="t-select" onchange="setFontSizePt(this.value)" title="Ukuran Huruf" style="width: 72px;">
                        <option value="9pt">9 pt</option>
                        <option value="10pt">10 pt</option>
                        <option value="11pt">11 pt</option>
                        <option value="12pt" selected>12 pt (Isi)</option>
                        <option value="14pt">14 pt (Judul)</option>
                        <option value="16pt">16 pt (Kop)</option>
                        <option value="18pt">18 pt</option>
                        <option value="20pt">20 pt</option>
                    </select>
                </div>

                <div class="t-sep"></div>

                <!-- Text Style -->
                <div class="t-group">
                    <button type="button" class="t-btn" onclick="execCmd('bold')" title="Tebal (Ctrl+B)"><strong>B</strong></button>
                    <button type="button" class="t-btn" onclick="execCmd('italic')" title="Miring (Ctrl+I)"><em>I</em></button>
                    <button type="button" class="t-btn" onclick="execCmd('underline')" title="Garis Bawah (Ctrl+U)"><u>U</u></button>
                    <button type="button" class="t-btn" onclick="execCmd('strikeThrough')" title="Coret"><s>S</s></button>
                    
                    <div class="color-picker-box" title="Warna Teks">
                        <span class="color-label">A</span>
                        <input type="color" onchange="execCmd('foreColor', this.value)" value="#000000">
                    </div>
                    <div class="color-picker-box" title="Warna Latar (Highlight)">
                        <span class="color-label" style="background: #fef08a; padding: 0 3px; border-radius: 2px;">H</span>
                        <input type="color" onchange="execCmd('hiliteColor', this.value)" value="#fef08a">
                    </div>
                </div>

                <div class="t-sep"></div>

                <!-- Alignments -->
                <div class="t-group">
                    <button type="button" class="t-btn" onclick="execCmd('justifyLeft')" title="Rata Kiri"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="17" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="17" y1="18" x2="3" y2="18"/></svg></button>
                    <button type="button" class="t-btn" onclick="execCmd('justifyCenter')" title="Rata Tengah"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="10" x2="6" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="18" y1="18" x2="6" y2="18"/></svg></button>
                    <button type="button" class="t-btn" onclick="execCmd('justifyRight')" title="Rata Kanan"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" y1="10" x2="7" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="21" y1="18" x2="7" y2="18"/></svg></button>
                    <button type="button" class="t-btn" onclick="execCmd('justifyFull')" title="Rata Kanan Kiri (Standar Paragraf)"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="21" y1="18" x2="3" y2="18"/></svg></button>
                </div>

                <div class="t-sep"></div>

                <!-- Spacing & Lists -->
                <div class="t-group">
                    <select class="t-select" onchange="setLineSpacing(this.value)" title="Spasi Baris" style="width: 70px;">
                        <option value="1.0">1.0</option>
                        <option value="1.15" selected>1.15</option>
                        <option value="1.5">1.5</option>
                        <option value="2.0">2.0</option>
                    </select>

                    <button type="button" class="t-btn" onclick="execCmd('insertUnorderedList')" title="Bullet List"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg></button>
                    <button type="button" class="t-btn" onclick="execCmd('insertOrderedList')" title="Numbered List"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/><path d="M4 6h1v4"/><path d="M4 10h2"/></svg></button>
                </div>

                <div class="t-sep"></div>

                <!-- Special Inserts (With Direct 1-Click Kop Image Upload) -->
                <div class="t-group">
                    <button type="button" class="t-btn-tag" onclick="triggerDirectKopUpload()" title="Klik langsung untuk pilih file gambar Kop Surat" style="background: #eef2ff; border-color: #6366f1; color: #4338ca; font-weight: 800;">
                        🖼️ Upload Kop Surat
                    </button>
                    <button type="button" class="t-btn-tag" onclick="openKopSuratModal()" title="Rancang Kop Surat Teks & Logo Manual" style="color: #64748b;">
                        ✍️ Rancang Kop Teks
                    </button>
                    <button type="button" class="t-btn-tag" onclick="openTableModal()" title="Sisipkan Tabel Biodata / Data">📊 Tabel</button>
                    <button type="button" class="t-btn-tag" onclick="insertSignatureBlock()" title="Sisipkan Kolom Tanda Tangan">✍️ TTD</button>
                    <button type="button" class="t-btn-tag" onclick="insertKopDivider()" title="Garis Ganda Tebal-Tipis Kop">➖ Garis Kop</button>
                    <button type="button" class="t-btn-tag" onclick="insertQrTag()" title="Sisipkan QR Code Verifikasi">🔲 QR Code</button>
                    <button type="button" class="t-btn-tag" onclick="triggerImageUpload()" title="Unggah Logo / Cap">📎 Gambar</button>
                </div>

            </div>
        </div>

        <!-- Hidden Native File Inputs for 1-Click Instant Operation -->
        <input type="file" id="direct-kop-file-input" accept="image/png, image/jpeg, image/jpg, image/webp" style="position: fixed; top: -9999px; left: -9999px; opacity: 0; pointer-events: none;" onchange="handleDirectKopUpload(this)">
        <input type="file" id="editor-image-file-input" accept="image/*" style="position: fixed; top: -9999px; left: -9999px; opacity: 0; pointer-events: none;" onchange="handleImageUpload(this)">

        <!-- Physical A4 / F4 Sheet Canvas Container -->
        <div class="bento-card" style="background: #475569; padding: 32px 20px; border-radius: 20px; align-items: center; box-shadow: inset 0 2px 10px rgba(0,0,0,0.3);">
            
            <!-- Margin Ruler Indicator -->
            <div class="a4-ruler-bar" id="ruler-bar">
                <div class="ruler-margin-l" title="Margin Kiri: 3.0 cm (Lubang Binder/Arsip)"></div>
                <div class="ruler-page-body" title="Area Cetak Efektif"></div>
                <div class="ruler-margin-r" title="Margin Kanan: 2.0 cm"></div>
            </div>

            <!-- Print Accurate Editable Sheet -->
            <div class="a4-paper-document" id="letter-canvas" contenteditable="true" spellcheck="false">
                <!-- Content loaded here -->
            </div>

            <div style="color: #cbd5e1; font-size: 11.5px; margin-top: 14px; text-align: center; display: flex; align-items: center; gap: 8px;">
                <span id="paper-info-label">📄 Format Fisik A4 (210 × 297 mm)</span>
                <span>•</span>
                <span>Margin Kiri: 3.0 cm, Kanan/Atas/Bawah: 2.0 cm</span>
                <span>•</span>
                <span style="color: #86efac; font-weight: bold;">✓ 100% Sesuai Skala Cetak &amp; Word 1 Halaman</span>
            </div>
        </div>

    </div>

    <!-- ─── 4. BENTO RIGHT SIDEBAR ASSISTANT (SPAN 4) ─── -->
    <div class="bento-col-4" style="display: flex; flex-direction: column; gap: 16px;">
        
        <!-- Bento Card: Interactive Kop Surat Controller & Adjuster -->
        <div class="bento-card" style="padding: 18px; background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); border: 1.5px solid #818cf8; box-shadow: 0 4px 16px rgba(99,102,241,0.12);">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <span style="font-size: 20px;">🖼️</span>
                    <strong style="font-size: 13.5px; color: #1e1b4b;">Pengatur Kop Surat</strong>
                </div>
                <span class="badge badge-primary" style="font-size: 10px;">Interaktif</span>
            </div>
            
            <!-- Quick Upload Action -->
            <button type="button" class="btn btn-primary btn-sm mb-3" style="width: 100%; justify-content: center; font-weight: 800; box-shadow: 0 2px 8px rgba(79,70,229,0.3);" onclick="triggerDirectKopUpload()">
                📤 Ganti / Upload Gambar Kop
            </button>

            <!-- Interactive Position & Scale Sliders (Live Control) -->
            <div id="kop-controls-panel" style="background: #ffffff; border: 1px solid #c7d2fe; border-radius: 12px; padding: 14px; margin-bottom: 8px;">
                
                <!-- 1. Vertical Movement (Geser Naik / Turun) -->
                <div class="mb-3">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                        <label style="font-size: 11px; font-weight: 800; color: #334155; margin: 0;">
                            ↕️ Posisi Vertikal (Atas / Bawah):
                        </label>
                        <span id="kop-top-val-label" style="font-size: 11px; font-weight: 800; color: #4338ca; background: #e0e7ff; padding: 1px 6px; border-radius: 4px;">0px</span>
                    </div>
                    
                    <!-- Quick Step Buttons -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 4px; margin-bottom: 6px;">
                        <button type="button" class="btn btn-xs btn-white shadow-sm" onclick="adjustKopTop(-5)" title="Geser Naik 5px" style="font-weight: 700;">⬆️ Naik (-5)</button>
                        <button type="button" class="btn btn-xs btn-white shadow-sm" onclick="setKopTop(0)" title="Reset ke Posisi Normal" style="font-weight: 700;">🔄 Reset (0)</button>
                        <button type="button" class="btn btn-xs btn-white shadow-sm" onclick="adjustKopTop(5)" title="Geser Turun 5px" style="font-weight: 700;">⬇️ Turun (+5)</button>
                    </div>

                    <input type="range" id="kop-top-slider" min="-30" max="50" value="0" step="2" 
                           style="width: 100%; accent-color: #4f46e5; cursor: pointer;" 
                           oninput="setKopTop(this.value)">
                    <div style="display: flex; justify-content: space-between; font-size: 9.5px; color: #94a3b8; margin-top: 2px;">
                        <span>-30px (Mepet Atas)</span>
                        <span>0px (Normal)</span>
                        <span>+50px (Turun)</span>
                    </div>
                </div>

                <!-- 2. Horizontal Alignment (Rata Kiri, Tengah, Kanan) -->
                <div class="mb-3">
                    <label style="display: block; font-size: 11px; font-weight: 800; color: #334155; margin-bottom: 6px;">
                        🎯 Posisi Rata Horizontal:
                    </label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 4px;">
                        <button type="button" class="btn btn-xs btn-white shadow-sm" onclick="setKopAlign('left')" id="btn-kop-align-left" title="Rata Kiri" style="font-weight: 700;">⬅️ Kiri</button>
                        <button type="button" class="btn btn-xs btn-primary shadow-sm" onclick="setKopAlign('center')" id="btn-kop-align-center" title="Rata Tengah" style="font-weight: 800;">⏺️ Tengah</button>
                        <button type="button" class="btn btn-xs btn-white shadow-sm" onclick="setKopAlign('right')" id="btn-kop-align-right" title="Rata Kanan" style="font-weight: 700;">➡️ Kanan</button>
                    </div>
                </div>

                <!-- 3. Width / Scale Slider -->
                <div class="mb-3">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                        <label style="font-size: 11px; font-weight: 800; color: #334155; margin: 0;">
                            ↔️ Skala Lebar Kop:
                        </label>
                        <span id="kop-scale-val-label" style="font-size: 11px; font-weight: 800; color: #4338ca; background: #e0e7ff; padding: 1px 6px; border-radius: 4px;">100%</span>
                    </div>
                    <input type="range" id="kop-scale-slider" min="50" max="100" value="100" step="2" 
                           style="width: 100%; accent-color: #4f46e5; cursor: pointer;" 
                           oninput="setKopScale(this.value)">
                    <div style="display: flex; justify-content: space-between; font-size: 9.5px; color: #94a3b8; margin-top: 2px;">
                        <span>50% (Kecil)</span>
                        <span>75%</span>
                        <span>100% (Penuh A4)</span>
                    </div>
                </div>

                <!-- 4. Spacing / Margin Bottom Slider -->
                <div class="mb-3">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                        <label style="font-size: 11px; font-weight: 800; color: #334155; margin: 0;">
                            ⏬ Jarak Spasi ke Isi Surat:
                        </label>
                        <span id="kop-margin-val-label" style="font-size: 11px; font-weight: 800; color: #4338ca; background: #e0e7ff; padding: 1px 6px; border-radius: 4px;">10px</span>
                    </div>
                    <input type="range" id="kop-margin-slider" min="0" max="50" value="10" step="2" 
                           style="width: 100%; accent-color: #4f46e5; cursor: pointer;" 
                           oninput="setKopMargin(this.value)">
                    <div style="display: flex; justify-content: space-between; font-size: 9.5px; color: #94a3b8; margin-top: 2px;">
                        <span>0px (Rapat)</span>
                        <span>25px (Sedang)</span>
                        <span>50px (Renggang)</span>
                    </div>
                </div>

                <!-- 5. Quick Action Buttons -->
                <div style="display: flex; gap: 6px; border-top: 1px dashed #e2e8f0; padding-top: 10px;">
                    <button type="button" class="btn btn-xs btn-white" onclick="toggleKopDivider()" style="flex: 1; font-weight: 700;" title="Tambah / Hapus Garis Ganda di Bawah Kop">
                        ➖ Garis Ganda
                    </button>
                    <button type="button" class="btn btn-xs btn-danger" onclick="removeKopSurat()" style="font-weight: 700;" title="Hapus Kop Surat dari Dokumen">
                        🗑️ Hapus
                    </button>
                </div>

            </div>
        </div>

        <!-- Bento Card: Searchable Dynamic Variables -->
        <div class="bento-card" style="padding: 20px; background: #ffffff;">
            <div class="flex items-center justify-between mb-2">
                <h3 style="font-size: 14px; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 6px;">
                    🏷️ Tag Variabel Dinamis
                </h3>
                <span class="badge badge-primary" style="font-size: 10px;">Klik untuk Sisip</span>
            </div>
            <p class="text-sm text-muted" style="margin-bottom: 12px; font-size: 12px; line-height: 1.4;">
                Gunakan tag <code>{{...}}</code> untuk mengotomasi data nama, nomor surat, tanggal, dan NIK dari form.
            </p>

            <!-- Search Filter -->
            <div class="mb-3">
                <input type="text" id="tag-search-input" class="form-control form-control-sm" placeholder="🔍 Cari tag variabel..." oninput="filterTags(this.value)">
            </div>

            <!-- Scrollable Tag Categories -->
            <div style="max-height: 280px; overflow-y: auto; padding-right: 4px;">
                
                <div class="tag-group-box">
                    <div class="tag-group-title">🏢 Kepala Surat & Instansi</div>
                    <div class="tag-chips-wrap">
                        <button type="button" class="bento-chip" onclick="insertVariable('{{nomor_surat}}')"><code>{{nomor_surat}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{tanggal_surat}}')"><code>{{tanggal_surat}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{nama_instansi}}')"><code>{{nama_instansi}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{alamat_instansi}}')"><code>{{alamat_instansi}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{kota}}')"><code>{{kota}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{tahun}}')"><code>{{tahun}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{bulan}}')"><code>{{bulan}}</code></button>
                    </div>
                </div>

                <div class="tag-group-box mt-3">
                    <div class="tag-group-title">👤 Data Penerima / Siswa / Pegawai</div>
                    <div class="tag-chips-wrap">
                        <button type="button" class="bento-chip" onclick="insertVariable('{{nama_lengkap}}')"><code>{{nama_lengkap}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{nik}}')"><code>{{nik}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{nisn}}')"><code>{{nisn}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{nip}}')"><code>{{nip}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{tempat_lahir}}')"><code>{{tempat_lahir}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{tanggal_lahir}}')"><code>{{tanggal_lahir}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{jenis_kelamin}}')"><code>{{jenis_kelamin}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{alamat}}')"><code>{{alamat}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{no_hp}}')"><code>{{no_hp}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{email}}')"><code>{{email}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{jabatan}}')"><code>{{jabatan}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{instansi_asal}}')"><code>{{instansi_asal}}</code></button>
                    </div>
                </div>

                <div class="tag-group-box mt-3">
                    <div class="tag-group-title">📋 Isi & Keterangan Tambahan</div>
                    <div class="tag-chips-wrap">
                        <button type="button" class="bento-chip" onclick="insertVariable('{{keperluan}}')"><code>{{keperluan}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{keterangan}}')"><code>{{keterangan}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{judul_kegiatan}}')"><code>{{judul_kegiatan}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{tanggal_mulai}}')"><code>{{tanggal_mulai}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{tanggal_selesai}}')"><code>{{tanggal_selesai}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{lokasi}}')"><code>{{lokasi}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{nominal}}')"><code>{{nominal}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{terbilang}}')"><code>{{terbilang}}</code></button>
                    </div>
                </div>

                <div class="tag-group-box mt-3">
                    <div class="tag-group-title">🔐 Pejabat Penandatangan & QR</div>
                    <div class="tag-chips-wrap">
                        <button type="button" class="bento-chip" onclick="insertVariable('{{nama_pejabat}}')"><code>{{nama_pejabat}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{jabatan_pejabat}}')"><code>{{jabatan_pejabat}}</code></button>
                        <button type="button" class="bento-chip" onclick="insertVariable('{{nip_pejabat}}')"><code>{{nip_pejabat}}</code></button>
                        <button type="button" class="bento-chip bento-chip-highlight" onclick="insertVariable('{{qr_code}}')"><code>{{qr_code}}</code></button>
                    </div>
                </div>

            </div>

            <!-- Custom Tag Creator -->
            <div class="mt-3 pt-3" style="border-top: 1px dashed var(--border-subtle);">
                <label class="form-label" style="font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 4px;">Buat Tag Kustom:</label>
                <div class="flex gap-1">
                    <input type="text" id="custom-var-input" class="form-control form-control-sm" placeholder="nama_tag_baru">
                    <button type="button" class="btn btn-soft-primary btn-sm" onclick="insertCustomVariable()" style="white-space: nowrap;">+ Sisip</button>
                </div>
            </div>

            <!-- Active Detected Variables -->
            <div class="mt-3 pt-3" style="border-top: 1px solid var(--border-subtle);">
                <div style="font-size: 12px; font-weight: 800; color: #0f172a; margin-bottom: 6px;">
                    📋 Tag Terdeteksi di Dokumen:
                </div>
                <div id="active-tags-list" style="display: flex; flex-wrap: wrap; gap: 4px; font-size: 11px;">
                    <span class="text-muted" style="font-size: 11px;">Belum ada tag variabel.</span>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- ═══════════════════════════════════════════════════════════
     MODALS: STANDALONE ZERO-CONFLICT OVERLAYS
     ═══════════════════════════════════════════════════════════ -->

<!-- ─── 1. MODAL: FULLSCREEN PRINT PREVIEW (SIAP CETAK) ─── -->
<div id="preview-modal" class="custom-modal-wrapper" style="display: none;" onclick="if(event.target === this) closeModal('preview-modal')">
    <div class="custom-modal-dialog" style="max-width: 960px; width: 95vw; max-height: 94vh; display: flex; flex-direction: column;" onclick="event.stopPropagation()">
        <div class="custom-modal-header" style="background: #1e293b; color: #ffffff; padding: 14px 22px; border-radius: 16px 16px 0 0; display: flex; align-items: center; justify-content: space-between;">
            <div class="flex items-center gap-3">
                <span style="font-size: 22px;">🖨️</span>
                <div>
                    <h3 style="font-size: 16px; font-weight: 800; color: #ffffff; margin: 0;">Pratinjau Dokumen Siap Cetak</h3>
                    <div style="font-size: 11.5px; color: #94a3b8;">Format fisik A4/F4 resmi &bull; Siap cetak ke printer fisik atau Ekspor ke PDF</div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" class="btn btn-sm btn-primary" onclick="printLetterDirectly()" style="font-weight: 800; background: #4f46e5; border-color: #6366f1; box-shadow: 0 4px 14px rgba(79,70,229,0.4);">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    Cetak / Simpan PDF (Ctrl+P)
                </button>
                <button type="button" class="btn btn-sm btn-secondary" onclick="openPrintInNewTab()" style="font-weight: 700; color: #ffffff; background: #334155; border-color: #475569;">
                    Buka di Tab Baru ↗
                </button>
                <button type="button" class="custom-modal-close-btn" onclick="closeModal('preview-modal')">&times;</button>
            </div>
        </div>
        
        <div class="custom-modal-body" style="padding: 24px; background: #525659; overflow-y: auto; text-align: center; flex: 1;">
            <div id="print-preview-content" class="print-preview-sheet" style="background: #ffffff; width: 210mm; min-height: 297mm; padding: 20mm 20mm 20mm 30mm; margin: 0 auto; text-align: left; box-shadow: 0 12px 35px rgba(0,0,0,0.45); font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.35; color: #000000; box-sizing: border-box;">
            </div>
        </div>
    </div>
</div>

<!-- ─── 2. MODAL: UPLOAD & KOP SURAT GENERATOR (PREMIUM) ─── -->
<div id="kop-modal" class="custom-modal-wrapper" style="display: none;" onclick="if(event.target === this) closeModal('kop-modal')">
    <div class="custom-modal-dialog" style="max-width: 680px; width: 94vw;" onclick="event.stopPropagation()">
        
        <!-- Modal Header -->
        <div style="padding: 18px 24px; background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); color: #ffffff; border-radius: 16px 16px 0 0; display: flex; align-items: center; justify-content: space-between;">
            <div class="flex items-center gap-3">
                <div style="width: 42px; height: 42px; background: rgba(255,255,255,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; backdrop-filter: blur(6px);">🏛️</div>
                <div>
                    <h3 style="font-size: 17px; font-weight: 800; margin: 0; color: #ffffff; letter-spacing: -0.3px;">Pengaturan Kop Surat Resmi</h3>
                    <div style="font-size: 11.5px; color: #c7d2fe; margin-top: 2px;">Upload gambar atau rancang Kop Surat instansi Anda</div>
                </div>
            </div>
            <button type="button" class="custom-modal-close-btn" onclick="closeModal('kop-modal')" style="color: #a5b4fc; font-size: 26px;">&times;</button>
        </div>

        <!-- Kop Modal Tabs -->
        <div style="display: flex; background: #ffffff; border-bottom: 1px solid #e2e8f0;">
            <button type="button" id="tab-btn-banner" onclick="switchKopTab('banner')" class="kop-tab-btn kop-tab-active">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                <span>Upload Gambar</span>
                <span class="kop-tab-badge">Disarankan</span>
            </button>
            <button type="button" id="tab-btn-text" onclick="switchKopTab('text')" class="kop-tab-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7V4h16v3"/><path d="M9 20h6"/><path d="M12 4v16"/></svg>
                <span>Rancang Teks &amp; Logo</span>
            </button>
        </div>

        <div class="custom-modal-body" style="padding: 24px; max-height: 68vh; overflow-y: auto; background: #f8fafc;">
            
            <!-- TAB 1: UPLOAD FULL BANNER KOP SURAT -->
            <div id="kop-tab-banner-content">
                
                <!-- Step Guide -->
                <div style="display: flex; gap: 8px; margin-bottom: 18px;">
                    <div class="kop-step-pill"><span class="kop-step-num">1</span> Pilih Gambar</div>
                    <div class="kop-step-arrow">→</div>
                    <div class="kop-step-pill"><span class="kop-step-num">2</span> Lihat Pratinjau</div>
                    <div class="kop-step-arrow">→</div>
                    <div class="kop-step-pill"><span class="kop-step-num">3</span> Pasang ke Surat</div>
                </div>

                <!-- Prominent Direct File Selector Box -->
                <div style="background: #ffffff; border: 2px dashed #6366f1; border-radius: 14px; padding: 24px 20px; text-align: center; margin-bottom: 16px;" 
                     id="kop-dropzone"
                     ondragenter="kopDragEnter(event)" ondragover="kopDragOver(event)" ondragleave="kopDragLeave(event)" ondrop="kopDrop(event)">
                    
                    <div style="font-size: 36px; margin-bottom: 8px;">🖼️</div>
                    
                    <h4 style="font-size: 15px; font-weight: 800; color: #1e1b4b; margin: 0 0 6px;">
                        Pilih Gambar Banner Kop Surat
                    </h4>
                    
                    <p style="font-size: 12px; color: #64748b; margin: 0 0 16px; line-height: 1.4;">
                        Format yang didukung: <strong>PNG, JPG, JPEG, WebP</strong><br>
                        (Gambar otomatis disesuaikan 100% membentang di lembar kerja &amp; Microsoft Word)
                    </p>

                    <!-- DIRECT VISIBLE NATIVE FILE INPUT -->
                    <div style="max-width: 440px; margin: 0 auto;">
                        <label for="kop-banner-file-input" style="display: block; font-size: 12px; font-weight: 800; color: #4338ca; margin-bottom: 6px; text-align: left;">
                            Klik tombol "Choose File / Browse" di bawah ini:
                        </label>
                        <input type="file" id="kop-banner-file-input" 
                               accept="image/png, image/jpeg, image/jpg, image/webp" 
                               class="form-control" 
                               style="font-size: 13px; font-weight: 700; padding: 10px 12px; border: 2px solid #6366f1; border-radius: 10px; background: #eef2ff; color: #1e1b4b; cursor: pointer; width: 100%; box-sizing: border-box; display: block;" 
                               onchange="handleKopBannerUpload(this)">
                    </div>

                    <div style="font-size: 11.5px; color: #94a3b8; margin-top: 10px;">
                        💡 Anda juga bisa menarik (drag &amp; drop) berkas gambar langsung ke dalam kotak ini
                    </div>
                </div>

                <!-- Preview Selected Banner -->
                <div id="kop-banner-preview-box" class="kop-preview-box" style="display: none;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                        <div class="flex items-center gap-2">
                            <div style="width: 8px; height: 8px; border-radius: 50%; background: #22c55e; animation: kopPulse 2s infinite;"></div>
                            <span style="font-size: 13px; font-weight: 800; color: #065f46;">Pratinjau Kop Surat</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span id="kop-banner-info" style="font-size: 11px; color: #64748b; font-weight: 600;"></span>
                            <button type="button" class="kop-remove-btn" onclick="clearKopBannerPreview()" title="Hapus gambar">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                Hapus
                            </button>
                        </div>
                    </div>
                    <div class="kop-preview-frame">
                        <img id="kop-banner-img-preview" src="" alt="Pratinjau Kop Surat">
                    </div>
                    <div style="margin-top: 12px; display: flex; flex-direction: column; gap: 8px;">
                        <button type="button" class="btn btn-primary" onclick="applyKopSurat()" style="font-weight: 800; font-size: 14px; width: 100%; padding: 12px; border-radius: 10px; background: #4f46e5; border-color: #6366f1; box-shadow: 0 4px 14px rgba(79,70,229,0.4); display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            ✓ Pasang Kop Surat Ini ke Dokumen Sekarang
                        </button>
                    </div>
                </div>
            </div>

            <!-- TAB 2: TEXT & LOGO BUILDER -->
            <div id="kop-tab-text-content" style="display: none;">
                <div style="padding: 14px 16px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; margin-bottom: 18px; font-size: 12px; color: #92400e; display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 18px;">💡</span>
                    <span>Mode ini untuk merancang Kop Surat secara manual dengan teks dan logo. Jika sudah punya gambar Kop, gunakan tab <strong>"Upload Gambar"</strong>.</span>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label" style="font-size: 12px; font-weight: 700;">Nama Lembaga / Pemerintah Atas</label>
                    <input type="text" id="kop-instansi-top" class="form-control form-control-sm" value="PEMERINTAH DAERAH PROVINSI / KABUPATEN" placeholder="Contoh: PEMERINTAH KABUPATEN BOGOR">
                </div>
                <div class="form-group mb-3">
                    <label class="form-label" style="font-size: 12px; font-weight: 700;">Nama Dinas / Instansi / Badan <span class="required">*</span></label>
                    <input type="text" id="kop-instansi-main" class="form-control form-control-sm" value="DINAS PENDIDIKAN DAN KEBUDAYAAN" placeholder="Contoh: DINAS PENDIDIKAN DAN KEBUDAYAAN">
                </div>
                <div class="form-group mb-3">
                    <label class="form-label" style="font-size: 12px; font-weight: 700;">Nama Satuan Pendidikan / Unit Kerja</label>
                    <input type="text" id="kop-instansi-sub" class="form-control form-control-sm" value="SMA NEGERI 1 CONTOH" placeholder="Contoh: SMA NEGERI 1 CIBINONG">
                </div>
                <div class="form-group mb-3">
                    <label class="form-label" style="font-size: 12px; font-weight: 700;">Alamat Lengkap, Kontak, &amp; Kode Pos</label>
                    <input type="text" id="kop-alamat" class="form-control form-control-sm" value="Jl. Pendidikan No. 45 Telp. (021) 1234567, Pos 12345, Email: info@sekolah.sch.id">
                </div>
                <div class="form-group mb-0">
                    <label class="form-label" style="font-size: 12px; font-weight: 700;">Upload Logo Instansi (Opsional)</label>
                    <input type="file" id="kop-logo-file-input" accept="image/*" class="form-control form-control-sm" style="cursor: pointer; background: #ffffff;" onchange="handleLogoUpload(this)">
                    <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">Logo akan ditampilkan di sisi kiri Kop Surat (ukuran 80×80 px)</div>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div style="padding: 16px 24px; background: #ffffff; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; border-radius: 0 0 16px 16px;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('kop-modal')" style="font-weight: 600;">Batal</button>
            <button type="button" class="btn btn-primary btn-sm" id="btn-apply-kop" onclick="applyKopSurat()" style="font-weight: 800; padding: 8px 20px; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 14px rgba(79,70,229,0.35);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Pasang Kop Surat ke Dokumen
            </button>
        </div>
    </div>
</div>

<!-- ─── 3. MODAL: TABLE BUILDER ─── -->
<div id="table-modal" class="custom-modal-wrapper" style="display: none;" onclick="if(event.target === this) closeModal('table-modal')">
    <div class="custom-modal-dialog" style="max-width: 440px; width: 92vw;" onclick="event.stopPropagation()">
        <div class="custom-modal-header" style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
            <h3 style="font-size: 16px; font-weight: 800; margin: 0; color: #0f172a;">📊 Sisipkan Tabel Dokumen</h3>
            <button type="button" class="custom-modal-close-btn" onclick="closeModal('table-modal')" style="color: #64748b;">&times;</button>
        </div>
        <div class="custom-modal-body" style="padding: 20px;">
            <div class="grid-2 mb-3">
                <div class="form-group mb-0">
                    <label class="form-label" style="font-size: 12px;">Jumlah Baris</label>
                    <input type="number" id="tbl-rows" class="form-control form-control-sm" value="3" min="1" max="50">
                </div>
                <div class="form-group mb-0">
                    <label class="form-label" style="font-size: 12px;">Jumlah Kolom</label>
                    <input type="number" id="tbl-cols" class="form-control form-control-sm" value="3" min="1" max="10">
                </div>
            </div>
            <div class="form-group mb-0">
                <label class="form-label" style="font-size: 12px;">Gaya Tabel</label>
                <select id="tbl-style" class="form-control form-control-sm">
                    <option value="bordered">Tabel Bergaris Lengkap (Laporan / Rincian)</option>
                    <option value="compact">Tabel Biodata (Tanpa Border / Titik Dua Lurus)</option>
                    <option value="striped">Tabel Bergaris Belang (Zebra Striped)</option>
                </select>
            </div>
        </div>
        <div class="custom-modal-footer" style="padding: 12px 20px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 8px; border-radius: 0 0 16px 16px;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('table-modal')">Batal</button>
            <button type="button" class="btn btn-primary btn-sm" onclick="insertTableFromModal()">Sisipkan Tabel</button>
        </div>
    </div>
</div>

<!-- ─── STYLES FOR BENTO EDITOR STUDIO & MODALS ─── -->
<style>
/* ─── Seamless Modal Wrapper CSS ─── */
.custom-modal-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.75);
    backdrop-filter: blur(4px);
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    box-sizing: border-box;
}

.custom-modal-dialog {
    position: relative;
    z-index: 1000000;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.45);
    overflow: hidden;
    animation: modalScaleIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes modalScaleIn {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

.custom-modal-close-btn {
    background: transparent;
    border: none;
    font-size: 24px;
    cursor: pointer;
    line-height: 1;
    color: #94a3b8;
    padding: 0 4px;
    transition: color 0.15s ease;
}

.custom-modal-close-btn:hover {
    color: #0f172a;
}

/* ─── Toolbar Styles ─── */
.document-toolbar-inner {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 5px;
}

.t-group {
    display: flex;
    align-items: center;
    gap: 3px;
}

.t-sep {
    width: 1px;
    height: 22px;
    background: #e2e8f0;
    margin: 0 4px;
}

.t-btn {
    background: transparent;
    border: 1px solid transparent;
    border-radius: 8px;
    width: 28px;
    height: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 13px;
    color: #475569;
    transition: all 0.15s ease;
}

.t-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
    border-color: #cbd5e1;
}

.t-btn-tag {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 3px 8px;
    font-size: 11.5px;
    font-weight: 700;
    color: #334155;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.15s ease;
}

.t-btn-tag:hover {
    background: #eef2ff;
    border-color: #818cf8;
    color: #3730a3;
    transform: translateY(-1px);
}

.t-select {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 3px 6px;
    font-size: 12px;
    background: #ffffff;
    color: #1e293b;
    height: 28px;
    cursor: pointer;
}

.color-picker-box {
    position: relative;
    width: 28px;
    height: 28px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    overflow: hidden;
}

.color-picker-box input[type="color"] {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.color-label {
    font-size: 12px;
    font-weight: 800;
}

/* ─── Ruler & Letter Document Canvas ─── */
.a4-ruler-bar {
    width: 100%;
    max-width: 800px;
    height: 10px;
    background: #334155;
    border-radius: 4px;
    margin-bottom: 12px;
    display: flex;
}
.ruler-margin-l { width: 14%; background: #64748b; border-radius: 4px 0 0 4px; }
.ruler-page-body { width: 76%; background: #e2e8f0; }
.ruler-margin-r { width: 10%; background: #64748b; border-radius: 0 4px 4px 0; }

.a4-paper-document {
    background: #ffffff;
    width: 100%;
    max-width: 800px;
    min-height: 1060px;
    padding: 36px 36px 36px 54px; /* Standar Margin Tata Naskah Dinas (Kiri 3cm, Kanan/Atas/Bawah 2.0cm) */
    box-shadow: 0 10px 30px rgba(0,0,0,0.4);
    border-radius: 2px;
    font-family: 'Times New Roman', Times, serif;
    font-size: 12pt;
    line-height: 1.35;
    color: #000000;
    outline: none;
    box-sizing: border-box;
}

.a4-paper-document p {
    margin: 0 0 6px 0;
}

.a4-paper-document table {
    width: 100%;
    border-collapse: collapse;
    margin: 6px 0;
}

.a4-paper-document td, .a4-paper-document th {
    padding: 2px 4px;
    vertical-align: top;
}

.kop-surat-banner {
    width: 100%;
    text-align: center;
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    border-radius: 4px;
    position: relative;
}

.kop-surat-banner:hover {
    outline: 2px dashed #6366f1;
    outline-offset: 4px;
}

.kop-surat-banner img {
    max-width: 100%;
    height: auto;
    display: block;
    margin: 0 auto;
    transition: width 0.15s ease, margin 0.15s ease;
}

/* ─── Bento Sidebar Helper ─── */
.tag-group-title {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #64748b;
    margin-bottom: 6px;
}

.tag-chips-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}

.bento-chip {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 2px 6px;
    cursor: pointer;
    font-size: 11px;
    transition: all 0.15s ease;
    text-align: left;
}

.bento-chip code {
    color: #4f46e5;
    font-weight: 700;
}

.bento-chip:hover {
    background: #e0e7ff;
    border-color: #6366f1;
    transform: translateY(-1px);
}

.bento-chip-highlight {
    background: #eef2ff;
    border-color: #818cf8;
}

.active-var-pill {
    background: #ecfdf5;
    border: 1px solid #6ee7b7;
    color: #065f46;
    padding: 2px 6px;
    border-radius: 4px;
    font-family: monospace;
    font-weight: bold;
}

/* ─── Kop Surat Modal Premium Styles ─── */
.kop-tab-btn {
    flex: 1;
    padding: 13px 16px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    background: transparent;
    color: #64748b;
    border-bottom: 2.5px solid transparent;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.kop-tab-btn:hover {
    color: #4338ca;
    background: #f5f3ff;
}

.kop-tab-btn.kop-tab-active {
    color: #4f46e5;
    font-weight: 800;
    border-bottom-color: #4f46e5;
    background: #faf5ff;
}

.kop-tab-badge {
    font-size: 9px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: #e0e7ff;
    color: #4338ca;
    padding: 2px 6px;
    border-radius: 4px;
    line-height: 1;
}

.kop-step-pill {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    color: #475569;
    flex: 1;
    justify-content: center;
}

.kop-step-num {
    width: 20px;
    height: 20px;
    background: #6366f1;
    color: #ffffff;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 800;
    flex-shrink: 0;
}

.kop-step-arrow {
    display: flex;
    align-items: center;
    color: #cbd5e1;
    font-size: 14px;
    flex-shrink: 0;
}

.kop-dropzone {
    position: relative;
    background: #ffffff;
    border: 2px dashed #c7d2fe;
    border-radius: 16px;
    padding: 32px 24px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.kop-dropzone:hover {
    border-color: #6366f1;
    background: #faf5ff;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.08);
}

.kop-dropzone.kop-drag-active {
    border-color: #4f46e5;
    background: #eef2ff;
    box-shadow: 0 0 0 6px rgba(99, 102, 241, 0.15);
    transform: scale(1.01);
}

.kop-dropzone-inner {
    pointer-events: auto;
}

.kop-upload-icon {
    width: 72px;
    height: 72px;
    margin: 0 auto 14px;
    background: #eef2ff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s ease;
}

.kop-dropzone:hover .kop-upload-icon {
    transform: translateY(-4px);
}

.kop-file-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 24px;
    background: #4f46e5;
    color: #ffffff;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
}

.kop-file-label:hover {
    background: #4338ca;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(79, 70, 229, 0.45);
}

.kop-preview-box {
    margin-top: 18px;
    border: 1.5px solid #bbf7d0;
    border-radius: 14px;
    padding: 16px;
    background: #ffffff;
    animation: kopSlideUp 0.3s ease;
}

.kop-preview-frame {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px;
    text-align: center;
}

.kop-preview-frame img {
    width: 100%;
    max-height: 150px;
    object-fit: contain;
    border-radius: 6px;
}

.kop-remove-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 6px;
    color: #dc2626;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
}

.kop-remove-btn:hover {
    background: #fee2e2;
    border-color: #f87171;
}

@keyframes kopPulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}

@keyframes kopSlideUp {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<!-- ─── SCRIPTS ─── -->
<script>
// Standalone safe notification function
function notify(type, message) {
    if (typeof window.showToast === 'function') {
        window.showToast(type, message);
        return;
    }
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `<span class="toast-message">${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
}

const rawInitialContent = <?= json_encode($content) ?>;
const activePreset = <?= json_encode($preset) ?>;
const canvas = document.getElementById('letter-canvas');
const hiddenInput = document.getElementById('hidden-content-input');
const form = document.getElementById('form-letter-editor');

let currentKopTab = 'banner';
let uploadedKopBannerData = null;
let uploadedLogoData = null;

// ─── Standard Indonesian Official Letter Presets (Tata Naskah Dinas - 1 Page Fit) ───
const letterPresets = {
    'surat-keterangan': `
        <div class="kop-surat-banner" onclick="openKopSuratModal()" title="Klik untuk ganti Kop Surat dengan gambar/logo Anda">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="width: 85px; text-align: center; vertical-align: middle;">
                        <div style="font-size: 38px;">🏛️</div>
                    </td>
                    <td style="text-align: center; line-height: 1.2;">
                        <div style="font-size: 13pt; font-weight: bold; text-transform: uppercase;">PEMERINTAH PROVINSI / KABUPATEN</div>
                        <div style="font-size: 15pt; font-weight: bold; text-transform: uppercase;">DINAS PENDIDIKAN DAN KEBUDAYAAN</div>
                        <div style="font-size: 16pt; font-weight: bold; text-transform: uppercase;">SMA NEGERI 1 CONTOH</div>
                        <div style="font-size: 9.5pt; margin-top: 2px;">Jl. Pendidikan No. 45 Telp. (021) 1234567, Pos 12345, Website: www.sekolah.sch.id</div>
                    </td>
                </tr>
            </table>
            <div style="border-bottom: 3px double #000000; margin: 4px 0 10px 0; height: 0;"></div>
        </div>

        <div style="text-align: center; margin-bottom: 12px;">
            <div style="font-size: 14pt; font-weight: bold; text-decoration: underline; text-transform: uppercase;">SURAT KETERANGAN</div>
            <div style="font-size: 11pt; margin-top: 2px;">Nomor: {{nomor_surat}}</div>
        </div>

        <p style="text-indent: 1.27cm; text-align: justify; margin-bottom: 8px;">
            Yang bertanda tangan di bawah ini Kepala SMA Negeri 1 Contoh, dengan ini menerangkan bahwa:
        </p>

        <table style="width: 100%; border: none; font-size: 12pt;">
            <tr>
                <td style="width: 220px;">Nama Lengkap</td>
                <td style="width: 20px; text-align: center;">:</td>
                <td><strong>{{nama_lengkap}}</strong></td>
            </tr>
            <tr>
                <td>NIS / NISN</td>
                <td style="text-align: center;">:</td>
                <td>{{nisn}}</td>
            </tr>
            <tr>
                <td>Tempat, Tanggal Lahir</td>
                <td style="text-align: center;">:</td>
                <td>{{tempat_lahir}}, {{tanggal_lahir}}</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td style="text-align: center;">:</td>
                <td>{{jenis_kelamin}}</td>
            </tr>
            <tr>
                <td>Alamat Tinggal</td>
                <td style="text-align: center;">:</td>
                <td>{{alamat}}</td>
            </tr>
        </table>

        <p style="text-indent: 1.27cm; text-align: justify; margin-bottom: 8px; margin-top: 8px;">
            Adalah benar yang bersangkutan merupakan siswa aktif tahun ajaran {{tahun}} pada sekolah kami dan memiliki rekam jejak perilaku yang baik. Surat keterangan ini diterbitkan guna keperluan <strong>{{keperluan}}</strong>.
        </p>

        <p style="text-indent: 1.27cm; text-align: justify; margin-bottom: 16px;">
            Demikian surat keterangan ini kami buat dengan sebenarnya agar dapat dipergunakan sebagaimana mestinya.
        </p>

        <table style="width: 100%; border: none; margin-top: 16px;">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: middle;">
                    <div style="font-size: 9.5pt; color: #475569; margin-bottom: 4px;">Keaslian Dokumen Terverifikasi:</div>
                    <div style="border: 1px dashed #94a3b8; display: inline-block; padding: 6px 10px; border-radius: 6px;">
                        <span style="font-weight: bold; color: #2563eb;">{{qr_code}}</span>
                    </div>
                </td>
                <td style="width: 50%; text-align: center;">
                    <div>{{kota}}, {{tanggal_surat}}</div>
                    <div style="font-weight: bold;">Kepala Sekolah,</div>
                    <div style="height: 45px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;">{{nama_pejabat}}</div>
                    <div>NIP. {{nip_pejabat}}</div>
                </td>
            </tr>
        </table>
    `,

    'surat-tugas': `
        <div class="kop-surat-banner" onclick="openKopSuratModal()" title="Klik untuk ganti Kop Surat dengan gambar/logo Anda">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="width: 85px; text-align: center; vertical-align: middle;">
                        <div style="font-size: 38px;">🏢</div>
                    </td>
                    <td style="text-align: center; line-height: 1.2;">
                        <div style="font-size: 15pt; font-weight: bold; text-transform: uppercase;">{{nama_instansi}}</div>
                        <div style="font-size: 10pt; font-style: italic; margin-top: 2px;">{{alamat_instansi}}</div>
                    </td>
                </tr>
            </table>
            <div style="border-bottom: 3px double #000000; margin: 4px 0 10px 0; height: 0;"></div>
        </div>

        <div style="text-align: center; margin-bottom: 12px;">
            <div style="font-size: 14pt; font-weight: bold; text-decoration: underline; text-transform: uppercase;">SURAT PERINTAH TUGAS</div>
            <div style="font-size: 11pt; margin-top: 2px;">Nomor: {{nomor_surat}}</div>
        </div>

        <p style="text-align: justify; margin-bottom: 8px;">
            Dasar: Sehubungan dengan agenda pelaksanaan <strong>{{judul_kegiatan}}</strong>, Pimpinan memberikan tugas kedinasan kepada:
        </p>

        <table style="width: 100%; border: none; font-size: 12pt;">
            <tr>
                <td style="width: 220px;">Nama Pegawai</td>
                <td style="width: 20px; text-align: center;">:</td>
                <td><strong>{{nama_lengkap}}</strong></td>
            </tr>
            <tr>
                <td>NIP / NIK</td>
                <td style="text-align: center;">:</td>
                <td>{{nip}}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td style="text-align: center;">:</td>
                <td>{{jabatan}}</td>
            </tr>
        </table>

        <p style="text-align: justify; margin-bottom: 8px; margin-top: 8px;">
            Untuk melaksanakan tugas dinas pada:
        </p>

        <table style="width: 100%; border: none; font-size: 12pt;">
            <tr>
                <td style="width: 220px;">Waktu Pelaksanaan</td>
                <td style="width: 20px; text-align: center;">:</td>
                <td>{{tanggal_mulai}} s.d. {{tanggal_selesai}}</td>
            </tr>
            <tr>
                <td>Lokasi / Tujuan</td>
                <td style="text-align: center;">:</td>
                <td>{{lokasi}}</td>
            </tr>
            <tr>
                <td>Rincian Tugas</td>
                <td style="text-align: center;">:</td>
                <td>{{keterangan}}</td>
            </tr>
        </table>

        <p style="text-indent: 1.27cm; text-align: justify; margin-bottom: 16px; margin-top: 8px;">
            Demikian Surat Perintah Tugas ini dibuat untuk dapat dilaksanakan dengan penuh tanggung jawab dan menyampaikan laporan setelah selesai bertugas.
        </p>

        <table style="width: 100%; border: none; margin-top: 16px;">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%; text-align: center;">
                    <div>{{kota}}, {{tanggal_surat}}</div>
                    <div style="font-weight: bold;">Pejabat Pemberi Tugas,</div>
                    <div style="height: 45px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;">{{nama_pejabat}}</div>
                    <div>NIP. {{nip_pejabat}}</div>
                </td>
            </tr>
        </table>
    `,

    'surat-rekomendasi': `
        <div class="kop-surat-banner" onclick="openKopSuratModal()" title="Klik untuk ganti Kop Surat dengan gambar/logo Anda">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="text-align: center; line-height: 1.2;">
                        <div style="font-size: 15pt; font-weight: bold; text-transform: uppercase;">{{nama_instansi}}</div>
                        <div style="font-size: 10pt; font-style: italic; margin-top: 2px;">{{alamat_instansi}}</div>
                    </td>
                </tr>
            </table>
            <div style="border-bottom: 3px double #000000; margin: 4px 0 10px 0; height: 0;"></div>
        </div>

        <div style="text-align: center; margin-bottom: 12px;">
            <div style="font-size: 14pt; font-weight: bold; text-decoration: underline; text-transform: uppercase;">SURAT REKOMENDASI</div>
            <div style="font-size: 11pt; margin-top: 2px;">Nomor: {{nomor_surat}}</div>
        </div>

        <p style="text-indent: 1.27cm; text-align: justify; margin-bottom: 8px;">
            Saya yang bertanda tangan di bawah ini memberikan rekomendasi penuh kepada:
        </p>

        <table style="width: 100%; border: none; font-size: 12pt;">
            <tr>
                <td style="width: 220px;">Nama Lengkap</td>
                <td style="width: 20px; text-align: center;">:</td>
                <td><strong>{{nama_lengkap}}</strong></td>
            </tr>
            <tr>
                <td>NIK / No. Identitas</td>
                <td style="text-align: center;">:</td>
                <td>{{nik}}</td>
            </tr>
            <tr>
                <td>Posisi / Bidang</td>
                <td style="text-align: center;">:</td>
                <td>{{jabatan}}</td>
            </tr>
        </table>

        <p style="text-indent: 1.27cm; text-align: justify; margin-bottom: 8px; margin-top: 8px;">
            Selama berinteraksi dan bekerja sama, yang bersangkutan memiliki dedikasi, integritas moral, serta kecakapan profesional yang sangat baik. Surat rekomendasi ini dibuat sebagai syarat kelengkapan administrasi <strong>{{keperluan}}</strong>.
        </p>

        <p style="text-indent: 1.27cm; text-align: justify; margin-bottom: 16px;">
            Demikian surat rekomendasi ini kami berikan dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.
        </p>

        <table style="width: 100%; border: none; margin-top: 16px;">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: middle;">
                    <div style="font-size: 10pt; color: #64748b;">{{qr_code}}</div>
                </td>
                <td style="width: 50%; text-align: center;">
                    <div>{{kota}}, {{tanggal_surat}}</div>
                    <div style="font-weight: bold;">Pemberi Rekomendasi,</div>
                    <div style="height: 45px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;">{{nama_pejabat}}</div>
                    <div>{{jabatan_pejabat}}</div>
                </td>
            </tr>
        </table>
    `,

    'surat-pernyataan': `
        <div style="text-align: center; margin-bottom: 16px; margin-top: 8px;">
            <div style="font-size: 15pt; font-weight: bold; text-decoration: underline; text-transform: uppercase;">SURAT PERNYATAAN</div>
        </div>

        <p style="text-align: justify; margin-bottom: 8px;">
            Saya yang bertanda tangan di bawah ini:
        </p>

        <table style="width: 100%; border: none; font-size: 12pt;">
            <tr>
                <td style="width: 220px;">Nama Lengkap</td>
                <td style="width: 20px; text-align: center;">:</td>
                <td><strong>{{nama_lengkap}}</strong></td>
            </tr>
            <tr>
                <td>NIK</td>
                <td style="text-align: center;">:</td>
                <td>{{nik}}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td style="text-align: center;">:</td>
                <td>{{alamat}}</td>
            </tr>
            <tr>
                <td>No. Telepon/HP</td>
                <td style="text-align: center;">:</td>
                <td>{{no_hp}}</td>
            </tr>
        </table>

        <p style="text-align: justify; margin-bottom: 8px; margin-top: 10px;">
            Dengan ini menyatakan dengan penuh kesadaran dan tanpa paksaan bahwa:
        </p>

        <p style="text-indent: 1.27cm; text-align: justify; margin-bottom: 8px; line-height: 1.5;">
            1. Seluruh keterangan dan berkas data yang saya sampaikan adalah sah dan sesuai keadaan sebenarnya.<br>
            2. Saya bersedia mentaati seluruh peraturan perundang-undangan dan tata tertib pada <strong>{{nama_instansi}}</strong>.<br>
            3. Rincian keterangan: <em>{{keterangan}}</em>.
        </p>

        <p style="text-indent: 1.27cm; text-align: justify; margin-bottom: 16px;">
            Demikian surat pernyataan ini saya buat dengan sesungguhnya untuk dapat digunakan sebagaimana mestinya.
        </p>

        <table style="width: 100%; border: none; margin-top: 16px;">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: middle;">
                    <div style="border: 1px solid #94a3b8; padding: 8px 14px; display: inline-block; font-size: 9pt; color: #475569;">Materai Rp 10.000</div>
                </td>
                <td style="width: 50%; text-align: center;">
                    <div>{{kota}}, {{tanggal_surat}}</div>
                    <div style="font-weight: bold;">Yang Membuat Pernyataan,</div>
                    <div style="height: 45px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;">{{nama_lengkap}}</div>
                </td>
            </tr>
        </table>
    `,

    'surat-izin': `
        <table style="width: 100%; border: none; margin-bottom: 12px;">
            <tr>
                <td style="width: 90px;">Perihal</td>
                <td style="width: 15px;">:</td>
                <td><strong>Permohonan Izin / Cuti</strong></td>
                <td style="text-align: right;">{{kota}}, {{tanggal_surat}}</td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>:</td>
                <td>-</td>
                <td></td>
            </tr>
        </table>

        <p style="margin-bottom: 10px;">
            Yth. <strong>{{nama_pejabat}}</strong><br>
            {{jabatan_pejabat}} {{nama_instansi}}<br>
            di Tempat
        </p>

        <p style="text-indent: 1.27cm; text-align: justify; margin-bottom: 8px;">
            Dengan hormat, saya yang bertanda tangan di bawah ini:
        </p>

        <table style="width: 100%; border: none; font-size: 12pt;">
            <tr>
                <td style="width: 220px;">Nama Lengkap</td>
                <td style="width: 20px; text-align: center;">:</td>
                <td><strong>{{nama_lengkap}}</strong></td>
            </tr>
            <tr>
                <td>NIP / NIK</td>
                <td style="text-align: center;">:</td>
                <td>{{nik}}</td>
            </tr>
            <tr>
                <td>Jabatan / Unit</td>
                <td style="text-align: center;">:</td>
                <td>{{jabatan}}</td>
            </tr>
        </table>

        <p style="text-indent: 1.27cm; text-align: justify; margin-bottom: 8px; margin-top: 8px;">
            Bermaksud mengajukan permohonan izin tidak masuk kerja terhitung mulai tanggal <strong>{{tanggal_mulai}}</strong> sampai dengan <strong>{{tanggal_selesai}}</strong> sehubungan dengan <strong>{{keperluan}}</strong>.
        </p>

        <p style="text-indent: 1.27cm; text-align: justify; margin-bottom: 16px;">
            Demikian surat permohonan ini saya sampaikan, atas perhatian dan izin yang diberikan saya ucapkan terima kasih.
        </p>

        <table style="width: 100%; border: none; margin-top: 16px;">
            <tr>
                <td style="width: 50%; text-align: center;">
                    <div style="font-weight: bold;">Menyetujui / Mengetahui,</div>
                    <div style="height: 45px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;">{{nama_pejabat}}</div>
                    <div>NIP. {{nip_pejabat}}</div>
                </td>
                <td style="width: 50%; text-align: center;">
                    <div style="font-weight: bold;">Hormat saya,</div>
                    <div style="height: 45px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;">{{nama_lengkap}}</div>
                </td>
            </tr>
        </table>
    `,

    'kwitansi': `
        <table style="width: 100%; border: 2px solid #000000; padding: 8px; margin-bottom: 16px;">
            <tr>
                <td style="width: 50%;">
                    <div style="font-size: 15pt; font-weight: bold;">KWITANSI PEMBAYARAN</div>
                    <div style="font-size: 9.5pt; color: #475569;">No. Kwitansi: {{nomor_surat}}</div>
                </td>
                <td style="width: 50%; text-align: right;">
                    <div style="font-size: 13pt; font-weight: bold; color: #2563eb;">{{nama_instansi}}</div>
                </td>
            </tr>
        </table>

        <table style="width: 100%; border: none; font-size: 12pt; line-height: 1.6;">
            <tr>
                <td style="width: 220px;">Telah Diterima Dari</td>
                <td style="width: 15px;">:</td>
                <td><strong>{{nama_lengkap}}</strong></td>
            </tr>
            <tr>
                <td>Uang Sejumlah</td>
                <td>:</td>
                <td style="background: #f1f5f9; padding: 3px 6px; font-style: italic; font-weight: bold;">" {{terbilang}} "</td>
            </tr>
            <tr>
                <td>Untuk Pembayaran</td>
                <td>:</td>
                <td>{{keperluan}}</td>
            </tr>
            <tr>
                <td>Keterangan / Rincian</td>
                <td>:</td>
                <td>{{keterangan}}</td>
            </tr>
        </table>

        <table style="width: 100%; border: none; margin-top: 20px;">
            <tr>
                <td style="width: 45%; vertical-align: middle;">
                    <div style="background: #e2e8f0; border: 2px solid #0f172a; padding: 10px 16px; display: inline-block; font-size: 15pt; font-weight: 800;">
                        Rp {{nominal}}
                    </div>
                </td>
                <td style="width: 55%; text-align: center;">
                    <div>{{kota}}, {{tanggal_surat}}</div>
                    <div style="font-weight: bold;">Penerima Pembayaran,</div>
                    <div style="height: 45px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;">{{nama_pejabat}}</div>
                </td>
            </tr>
        </table>
    `,

    'undangan': `
        <div class="kop-surat-banner" onclick="openKopSuratModal()" title="Klik untuk ganti Kop Surat dengan gambar/logo Anda">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="width: 85px; text-align: center; vertical-align: middle;">
                        <div style="font-size: 38px;">🏛️</div>
                    </td>
                    <td style="text-align: center; line-height: 1.2;">
                        <div style="font-size: 15pt; font-weight: bold; text-transform: uppercase;">{{nama_instansi}}</div>
                        <div style="font-size: 10pt; font-style: italic; margin-top: 2px;">{{alamat_instansi}}</div>
                    </td>
                </tr>
            </table>
            <div style="border-bottom: 3px double #000000; margin: 4px 0 10px 0; height: 0;"></div>
        </div>

        <table style="width: 100%; border: none; margin-bottom: 12px;">
            <tr>
                <td style="width: 90px;">Nomor</td>
                <td style="width: 15px;">:</td>
                <td>{{nomor_surat}}</td>
                <td style="text-align: right;">{{kota}}, {{tanggal_surat}}</td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>:</td>
                <td>-</td>
                <td></td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>:</td>
                <td><strong>Undangan {{judul_kegiatan}}</strong></td>
                <td></td>
            </tr>
        </table>

        <p style="margin-bottom: 10px;">
            Yth. Bapak/Ibu/Saudara:<br>
            <strong>{{nama_lengkap}}</strong><br>
            di Tempat
        </p>

        <p style="text-indent: 1.27cm; text-align: justify; margin-bottom: 8px;">
            Dengan hormat, sehubungan dengan agenda pelaksanaan <strong>{{judul_kegiatan}}</strong>, kami bermaksud mengundang Bapak/Ibu untuk berkenan hadir pada:
        </p>

        <table style="width: 100%; border: none; font-size: 12pt;">
            <tr>
                <td style="width: 220px;">Hari, Tanggal</td>
                <td style="width: 20px; text-align: center;">:</td>
                <td>{{tanggal_mulai}}</td>
            </tr>
            <tr>
                <td>Tempat Pelaksanaan</td>
                <td style="text-align: center;">:</td>
                <td>{{lokasi}}</td>
            </tr>
            <tr>
                <td>Agenda Kegiatan</td>
                <td style="text-align: center;">:</td>
                <td>{{keterangan}}</td>
            </tr>
        </table>

        <p style="text-indent: 1.27cm; text-align: justify; margin-bottom: 16px; margin-top: 8px;">
            Mengingat pentingnya agenda ini, kami sangat mengharapkan kehadiran Bapak/Ibu tepat pada waktunya. Atas perhatian dan kerjasamanya kami ucapkan terima kasih.
        </p>

        <table style="width: 100%; border: none; margin-top: 16px;">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%; text-align: center;">
                    <div>{{kota}}, {{tanggal_surat}}</div>
                    <div style="font-weight: bold;">Pimpinan / Kepala Instansi,</div>
                    <div style="height: 45px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;">{{nama_pejabat}}</div>
                    <div>NIP. {{nip_pejabat}}</div>
                </td>
            </tr>
        </table>
    `,

    'blank': `
        <div style="text-align: center; margin-bottom: 16px;">
            <div style="font-size: 14pt; font-weight: bold; text-decoration: underline; text-transform: uppercase;">SURAT KETERANGAN RESMI</div>
            <div style="font-size: 11pt; margin-top: 2px;">Nomor: {{nomor_surat}}</div>
        </div>

        <p style="text-indent: 1.27cm; text-align: justify; margin-bottom: 8px;">
            Yang bertanda tangan di bawah ini menerangkan bahwa:
        </p>

        <table style="width: 100%; border: none; font-size: 12pt;">
            <tr>
                <td style="width: 220px;">Nama Lengkap</td>
                <td style="width: 20px; text-align: center;">:</td>
                <td><strong>{{nama_lengkap}}</strong></td>
            </tr>
            <tr>
                <td>NIK / No. Identitas</td>
                <td style="text-align: center;">:</td>
                <td>{{nik}}</td>
            </tr>
        </table>

        <p style="text-indent: 1.27cm; text-align: justify; margin-bottom: 16px; margin-top: 8px;">
            Demikian surat ini dibuat dengan sebenarnya agar dapat dipergunakan sebagaimana mestinya.
        </p>

        <table style="width: 100%; border: none; margin-top: 20px;">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%; text-align: center;">
                    <div>{{kota}}, {{tanggal_surat}}</div>
                    <div style="font-weight: bold;">Pejabat Berwenang,</div>
                    <div style="height: 45px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;">{{nama_pejabat}}</div>
                    <div>NIP. {{nip_pejabat}}</div>
                </td>
            </tr>
        </table>
    `
};

document.addEventListener('DOMContentLoaded', () => {
    if (rawInitialContent && rawInitialContent.trim() !== '') {
        canvas.innerHTML = rawInitialContent;
    } else if (activePreset && letterPresets[activePreset]) {
        canvas.innerHTML = letterPresets[activePreset];
    } else {
        canvas.innerHTML = letterPresets['surat-keterangan'];
    }

    scanDetectedVariables();
});

function execCmd(command, value = null) {
    document.execCommand(command, false, value);
    canvas.focus();
    scanDetectedVariables();
}

function setFontSizePt(size) {
    const sel = window.getSelection();
    if (sel.rangeCount > 0) {
        const range = sel.getRangeAt(0);
        const span = document.createElement('span');
        span.style.fontSize = size;
        try {
            range.surroundContents(span);
        } catch(e) {
            execCmd('fontSize', '3');
        }
    }
}

function setLineSpacing(val) {
    const sel = window.getSelection();
    let node = sel.anchorNode;
    while (node && node !== canvas) {
        if (node.nodeType === 1 && (node.tagName === 'P' || node.tagName === 'DIV')) {
            node.style.lineHeight = val;
            break;
        }
        node = node.parentNode;
    }
    if (!node || node === canvas) {
        canvas.style.lineHeight = val;
    }
}

function changePaperSize(size) {
    const paper = document.getElementById('letter-canvas');
    const info = document.getElementById('paper-info-label');
    if (size === 'F4') {
        paper.style.minHeight = '1180px';
        info.textContent = '📄 Format Fisik F4 / Folio (215 × 330 mm)';
    } else if (size === 'Letter') {
        paper.style.minHeight = '1000px';
        info.textContent = '📄 Format Fisik Letter (215.9 × 279.4 mm)';
    } else {
        paper.style.minHeight = '1060px';
        info.textContent = '📄 Format Fisik A4 (210 × 297 mm)';
    }
}

function insertVariable(tag) {
    canvas.focus();
    execCmd('insertHTML', `<strong>${tag}</strong> `);
    scanDetectedVariables();
}

function insertCustomVariable() {
    const input = document.getElementById('custom-var-input');
    const rawVal = input.value.trim();
    if (!rawVal) return;
    const cleanTag = '{{' + rawVal.replace(/[^a-zA-Z0-9_]/g, '_').toLowerCase() + '}}';
    insertVariable(cleanTag);
    input.value = '';
}

function insertKopDivider() {
    canvas.focus();
    execCmd('insertHTML', '<div style="border-bottom: 3px double #000000; margin: 4px 0 10px 0; height: 0;"></div><p></p>');
}

function insertQrTag() {
    insertVariable('{{qr_code}}');
}

function insertSignatureBlock() {
    const html = `
        <table style="width: 100%; border: none; margin-top: 16px;">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: middle;">
                    <div style="font-size: 10pt; color: #64748b;">{{qr_code}}</div>
                </td>
                <td style="width: 50%; text-align: center;">
                    <div>{{kota}}, {{tanggal_surat}}</div>
                    <div style="font-weight: bold;">Kepala / Pimpinan,</div>
                    <div style="height: 45px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;">{{nama_pejabat}}</div>
                    <div>NIP. {{nip_pejabat}}</div>
                </td>
            </tr>
        </table>
        <p></p>
    `;
    execCmd('insertHTML', html);
    scanDetectedVariables();
}

function loadPresetTemplate(presetKey) {
    if (!letterPresets[presetKey]) return;
    if (confirm('Muat format surat resmi ini? Isi editor saat ini akan digantikan.')) {
        canvas.innerHTML = letterPresets[presetKey];
        scanDetectedVariables();
        notify('success', 'Format surat resmi berhasil dimuat!');
    }
}

// ─── INTERACTIVE KOP SURAT CONTROLLER (GESER, SKALA & POSISI) ───
let currentKopAlign = 'center';

function getKopBannerElement() {
    return canvas.querySelector('.kop-surat-banner');
}

function getKopImageElement() {
    const kop = getKopBannerElement();
    return kop ? kop.querySelector('img.kop-banner-img, img') : null;
}

function setKopTop(val) {
    val = parseInt(val) || 0;
    const kop = getKopBannerElement();
    const label = document.getElementById('kop-top-val-label');
    const slider = document.getElementById('kop-top-slider');
    
    if (label) label.textContent = (val > 0 ? '+' : '') + val + 'px';
    if (slider) slider.value = val;
    
    if (kop) {
        kop.style.marginTop = val + 'px';
        kop.setAttribute('data-margin-top', val);
    }
}

function adjustKopTop(delta) {
    const slider = document.getElementById('kop-top-slider');
    const currentVal = parseInt(slider ? slider.value : 0) || 0;
    const newVal = Math.max(-30, Math.min(50, currentVal + delta));
    setKopTop(newVal);
}

function setKopScale(val) {
    const kop = getKopBannerElement();
    const img = getKopImageElement();
    const label = document.getElementById('kop-scale-val-label');
    const slider = document.getElementById('kop-scale-slider');
    
    if (label) label.textContent = val + '%';
    if (slider) slider.value = val;
    
    if (img) {
        img.style.width = val + '%';
        img.style.maxWidth = val + '%';
    }
    if (kop) {
        kop.setAttribute('data-scale', val);
        kop.style.width = '100%';
    }
}

function setKopMargin(val) {
    const kop = getKopBannerElement();
    const label = document.getElementById('kop-margin-val-label');
    const slider = document.getElementById('kop-margin-slider');
    
    if (label) label.textContent = val + 'px';
    if (slider) slider.value = val;
    
    if (kop) {
        kop.style.marginBottom = val + 'px';
        kop.setAttribute('data-margin-bottom', val);
    }
}

function setKopAlign(align) {
    currentKopAlign = align;
    const kop = getKopBannerElement();
    const img = getKopImageElement();
    
    ['left', 'center', 'right'].forEach(a => {
        const btn = document.getElementById('btn-kop-align-' + a);
        if (btn) {
            if (a === align) {
                btn.className = 'btn btn-xs btn-primary shadow-sm';
                btn.style.fontWeight = '800';
            } else {
                btn.className = 'btn btn-xs btn-white shadow-sm';
                btn.style.fontWeight = '700';
            }
        }
    });

    if (kop) {
        kop.style.textAlign = align;
        kop.setAttribute('data-align', align);
    }
    if (img) {
        if (align === 'left') {
            img.style.margin = '0 auto 0 0';
        } else if (align === 'right') {
            img.style.margin = '0 0 0 auto';
        } else {
            img.style.margin = '0 auto';
        }
    }
}

function toggleKopDivider() {
    const kop = getKopBannerElement();
    if (!kop) {
        notify('error', 'Belum ada Kop Surat pada dokumen.');
        return;
    }
    let divider = kop.querySelector('.kop-divider-line');
    if (divider) {
        divider.remove();
        notify('info', 'Garis pembatas Kop dihapus.');
    } else {
        const line = document.createElement('div');
        line.className = 'kop-divider-line';
        line.style.cssText = 'border-bottom: 3px double #000000; margin: 6px 0 0 0; height: 0;';
        kop.appendChild(line);
        notify('success', 'Garis ganda pembatas Kop ditambahkan!');
    }
}

function removeKopSurat() {
    const kop = getKopBannerElement();
    if (!kop) {
        notify('info', 'Tidak ada Kop Surat yang terpasang.');
        return;
    }
    if (confirm('Hapus Kop Surat dari dokumen?')) {
        kop.remove();
        notify('success', 'Kop Surat berhasil dihapus dari dokumen.');
        scanDetectedVariables();
    }
}

// ─── 1-CLICK INSTANT KOP SURAT UPLOAD ───
function triggerDirectKopUpload() {
    const input = document.getElementById('direct-kop-file-input');
    if (input) {
        input.click();
    }
}

function handleDirectKopUpload(input) {
    if (!input.files || input.files.length === 0) return;
    const file = input.files[0];

    if (!file.type.match('image.*')) {
        notify('error', 'Mohon pilih berkas gambar yang valid (PNG, JPG, JPEG, WebP).');
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const base64Data = e.target.result;
        const bannerHtml = `
            <div class="kop-surat-banner" onclick="openKopSuratModal()" title="Klik untuk ganti Kop Surat">
                <img src="${base64Data}" class="kop-banner-img" style="width: 100%; max-width: 100%; height: auto; display: block; margin: 0 auto 10px auto;" />
            </div>
        `;

        const existingKop = canvas.querySelector('.kop-surat-banner');
        if (existingKop) {
            existingKop.outerHTML = bannerHtml;
        } else {
            canvas.innerHTML = bannerHtml + canvas.innerHTML;
        }

        notify('success', '✓ Gambar Kop Surat berhasil dipasang di bagian atas dokumen!');
        scanDetectedVariables();
        input.value = '';
    };
    reader.onerror = function() {
        notify('error', 'Gagal membaca berkas gambar.');
    };
    reader.readAsDataURL(file);
}

// ─── KOP SURAT MODAL & UPLOAD LOGIC ───
function openKopSuratModal() {
    openModal('kop-modal');
}

function switchKopTab(tab) {
    currentKopTab = tab;
    const btnBanner = document.getElementById('tab-btn-banner');
    const btnText = document.getElementById('tab-btn-text');
    const contentBanner = document.getElementById('kop-tab-banner-content');
    const contentText = document.getElementById('kop-tab-text-content');

    if (tab === 'banner') {
        btnBanner.classList.add('kop-tab-active');
        btnText.classList.remove('kop-tab-active');
        contentBanner.style.display = 'block';
        contentText.style.display = 'none';
    } else {
        btnText.classList.add('kop-tab-active');
        btnBanner.classList.remove('kop-tab-active');
        contentText.style.display = 'block';
        contentBanner.style.display = 'none';
    }
}

function handleKopBannerUpload(input) {
    if (!input.files || input.files.length === 0) return;
    const file = input.files[0];

    if (!file.type.match('image.*')) {
        alert('Mohon pilih berkas gambar yang valid (PNG, JPG, JPEG, WebP).');
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        uploadedKopBannerData = e.target.result;
        const previewImg = document.getElementById('kop-banner-img-preview');
        const previewBox = document.getElementById('kop-banner-preview-box');
        const infoSpan = document.getElementById('kop-banner-info');

        if (previewImg && previewBox) {
            previewImg.src = uploadedKopBannerData;
            previewBox.style.display = 'block';

            // Show image dimensions and file size
            const img = new Image();
            img.onload = function() {
                const sizeKB = Math.round(file.size / 1024);
                const sizeStr = sizeKB > 1024 ? (sizeKB / 1024).toFixed(1) + ' MB' : sizeKB + ' KB';
                if (infoSpan) {
                    infoSpan.textContent = img.width + ' × ' + img.height + ' px  •  ' + sizeStr;
                }
            };
            img.src = uploadedKopBannerData;
        }
        notify('success', 'Gambar Kop Surat siap dipasang!');
    };
    reader.onerror = function() {
        alert('Gagal membaca berkas gambar.');
    };
    reader.readAsDataURL(file);
}

function processKopBannerFile(file) {
    if (!file || !file.type.match('image.*')) {
        alert('Mohon pilih berkas gambar yang valid (PNG, JPG, JPEG, WebP).');
        return;
    }
    // Reuse the main handler via a mock input
    const dt = new DataTransfer();
    dt.items.add(file);
    const input = document.getElementById('kop-banner-file-input');
    if (input) {
        input.files = dt.files;
        handleKopBannerUpload(input);
    }
}

function triggerKopFileInput() {
    const input = document.getElementById('kop-banner-file-input');
    if (input) {
        input.click();
    }
}

function clearKopBannerPreview() {
    uploadedKopBannerData = null;
    const input = document.getElementById('kop-banner-file-input');
    if (input) input.value = '';
    const previewBox = document.getElementById('kop-banner-preview-box');
    if (previewBox) previewBox.style.display = 'none';
    const infoSpan = document.getElementById('kop-banner-info');
    if (infoSpan) infoSpan.textContent = '';
}

// ─── DRAG & DROP HANDLERS FOR KOP SURAT ───
function kopDragEnter(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('kop-dropzone').classList.add('kop-drag-active');
}

function kopDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
}

function kopDragLeave(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('kop-dropzone').classList.remove('kop-drag-active');
}

function kopDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('kop-dropzone').classList.remove('kop-drag-active');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        processKopBannerFile(files[0]);
    }
}

function handleLogoUpload(input) {
    if (!input.files || input.files.length === 0) return;
    const file = input.files[0];

    const reader = new FileReader();
    reader.onload = function(e) {
        uploadedLogoData = e.target.result;
        notify('success', 'Logo instansi siap dipasang!');
    };
    reader.readAsDataURL(file);
}

function applyKopSurat() {
    if (currentKopTab === 'banner') {
        if (!uploadedKopBannerData) {
            alert('Silakan pilih file gambar Kop Surat Anda terlebih dahulu.');
            return;
        }

        const bannerHtml = `
            <div class="kop-surat-banner" onclick="openKopSuratModal()" title="Klik untuk ganti Kop Surat">
                <img src="${uploadedKopBannerData}" class="kop-banner-img" style="width: 100%; max-width: 100%; height: auto; display: block; margin: 0 auto 10px auto;" />
            </div>
        `;

        // Replace existing kop banner if present, else prepend to top
        const existingKop = canvas.querySelector('.kop-surat-banner');
        if (existingKop) {
            existingKop.outerHTML = bannerHtml;
        } else {
            canvas.innerHTML = bannerHtml + canvas.innerHTML;
        }

        closeModal('kop-modal');
        notify('success', 'Kop Surat Gambar berhasil dipasang di bagian atas dokumen!');
        scanDetectedVariables();

    } else {
        // Tab Text & Logo
        const top = document.getElementById('kop-instansi-top').value.trim();
        const main = document.getElementById('kop-instansi-main').value.trim();
        const sub = document.getElementById('kop-instansi-sub').value.trim();
        const alamat = document.getElementById('kop-alamat').value.trim();

        let logoHtml = '<div style="font-size: 38px;">🏛️</div>';
        if (uploadedLogoData) {
            logoHtml = `<img src="${uploadedLogoData}" width="80" height="80" style="max-height: 80px; object-fit: contain;">`;
        }

        const kopHtml = `
            <div class="kop-surat-banner" onclick="openKopSuratModal()" title="Klik untuk ganti Kop Surat">
                <table style="width: 100%; border: none;">
                    <tr>
                        <td style="width: 85px; text-align: center; vertical-align: middle;">
                            ${logoHtml}
                        </td>
                        <td style="text-align: center; line-height: 1.2;">
                            ${top ? `<div style="font-size: 13pt; font-weight: bold; text-transform: uppercase;">${top}</div>` : ''}
                            <div style="font-size: 15pt; font-weight: bold; text-transform: uppercase;">${main}</div>
                            ${sub ? `<div style="font-size: 16pt; font-weight: bold; text-transform: uppercase;">${sub}</div>` : ''}
                            <div style="font-size: 9.5pt; margin-top: 2px;">${alamat}</div>
                        </td>
                    </tr>
                </table>
                <div style="border-bottom: 3px double #000000; margin: 4px 0 10px 0; height: 0;"></div>
            </div>
        `;

        const existingKop = canvas.querySelector('.kop-surat-banner');
        if (existingKop) {
            existingKop.outerHTML = kopHtml;
        } else {
            canvas.innerHTML = kopHtml + canvas.innerHTML;
        }

        closeModal('kop-modal');
        notify('success', 'Kop Surat Teks & Logo berhasil dipasang!');
        scanDetectedVariables();
    }
}

function openTableModal() {
    openModal('table-modal');
}

function insertTableFromModal() {
    const rows = parseInt(document.getElementById('tbl-rows').value) || 3;
    const cols = parseInt(document.getElementById('tbl-cols').value) || 3;
    const styleType = document.getElementById('tbl-style').value;

    let borderStyle = 'border: 1px solid #000000; width: 100%;';
    let cellBorder = 'border: 1px solid #000000; padding: 4px 6px;';
    
    if (styleType === 'compact') {
        borderStyle = 'border: none; width: 100%; margin: 6px 0;';
        cellBorder = 'border: none; padding: 2px 4px;';
    }

    let html = `<table style="${borderStyle}">`;
    
    for (let r = 0; r < rows; r++) {
        html += '<tr>';
        for (let c = 0; c < cols; c++) {
            if (styleType === 'compact') {
                if (c === 0) html += `<td style="${cellBorder} width: 220px;">Data ${r+1}</td>`;
                else if (c === 1) html += `<td style="${cellBorder} width: 20px; text-align: center;">:</td>`;
                else html += `<td style="${cellBorder}"><strong>{{data_${r+1}}}</strong></td>`;
            } else {
                if (r === 0) {
                    html += `<th style="${cellBorder} background: #f1f5f9; text-align: left; font-weight: bold;">Kolom ${c+1}</th>`;
                } else {
                    html += `<td style="${cellBorder}">Data Baris ${r+1}, Kolom ${c+1}</td>`;
                }
            }
        }
        html += '</tr>';
    }
    html += '</table><p></p>';

    closeModal('table-modal');
    canvas.focus();
    execCmd('insertHTML', html);
    scanDetectedVariables();
}

function triggerImageUpload() {
    document.getElementById('editor-image-file-input').click();
}

async function handleImageUpload(input) {
    if (!input.files || input.files.length === 0) return;
    const file = input.files[0];

    const reader = new FileReader();
    reader.onload = function(e) {
        const base64Img = e.target.result;
        canvas.focus();
        execCmd('insertHTML', `<img src="${base64Img}" width="120" style="max-width: 100%; height: auto; margin: 4px;" /><p></p>`);
        notify('success', 'Gambar berhasil disisipkan ke surat!');
    };
    reader.readAsDataURL(file);
    input.value = '';
}

canvas.addEventListener('input', () => {
    scanDetectedVariables();
});

canvas.addEventListener('click', (e) => {
    const kop = e.target.closest('.kop-surat-banner');
    if (kop) {
        openKopSuratModal();
    }
});

function scanDetectedVariables() {
    const text = canvas.innerHTML;
    const regex = /\{\{([a-zA-Z0-9_]+)\}\}/g;
    const matches = [];
    let match;
    while ((match = regex.exec(text)) !== null) {
        if (!matches.includes(match[1])) {
            matches.push(match[1]);
        }
    }

    document.getElementById('vars-count').textContent = matches.length;

    const activeList = document.getElementById('active-tags-list');
    if (matches.length === 0) {
        activeList.innerHTML = '<span class="text-muted" style="font-size: 11px;">Belum ada tag variabel.</span>';
    } else {
        activeList.innerHTML = matches.map(m => `<span class="active-var-pill">{{${m}}}</span>`).join(' ');
    }
}

function filterTags(query) {
    const q = query.toLowerCase();
    const chips = document.querySelectorAll('.bento-chip');
    chips.forEach(chip => {
        const text = chip.textContent.toLowerCase();
        chip.style.display = text.includes(q) ? 'inline-block' : 'none';
    });
}

function submitLetterForm() {
    hiddenInput.value = canvas.innerHTML;
    if (!hiddenInput.value || hiddenInput.value.trim() === '') {
        notify('error', 'Isi surat dalam editor tidak boleh kosong.');
        return;
    }
    const saveBtn = document.getElementById('btn-save-top');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<span class="spinner-inline"></span> Mengonversi ke Word...';
    form.submit();
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

function openPreviewModal() {
    const previewBox = document.getElementById('print-preview-content');
    const paperSize = document.getElementById('paper-size-select').value;
    
    if (paperSize === 'F4') {
        previewBox.style.width = '215mm';
        previewBox.style.minHeight = '330mm';
    } else if (paperSize === 'Letter') {
        previewBox.style.width = '215.9mm';
        previewBox.style.minHeight = '279.4mm';
    } else {
        previewBox.style.width = '210mm';
        previewBox.style.minHeight = '297mm';
    }

    previewBox.innerHTML = canvas.innerHTML;
    openModal('preview-modal');
}

function printLetterDirectly() {
    const printContents = canvas.innerHTML;
    const paperSize = document.getElementById('paper-size-select').value;
    const pageCssSize = paperSize === 'F4' ? '215mm 330mm' : (paperSize === 'Letter' ? 'letter portrait' : 'A4 portrait');

    const win = window.open('', '_blank');
    if (!win) {
        alert('Pop-up terblokir oleh browser. Izinkan pop-up untuk mencetak dokumen.');
        return;
    }
    win.document.write(`
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <title>Cetak Dokumen Resmi</title>
            <style>
                @page { 
                    size: ${pageCssSize}; 
                    margin: 20mm 20mm 20mm 30mm; /* Standar Margin Cetak Tata Naskah Dinas (Kiri 3cm, Kanan 2cm) */
                }
                * { box-sizing: border-box; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                body { 
                    font-family: 'Times New Roman', Times, serif; 
                    font-size: 12pt; 
                    line-height: 1.35; 
                    color: #000000; 
                    background: #ffffff; 
                    margin: 0; 
                    padding: 0; 
                }
                table { width: 100%; border-collapse: collapse; page-break-inside: avoid; }
                p { margin: 0 0 6px 0; }
                .kop-surat-banner img { width: 100%; max-width: 100%; height: auto; display: block; margin: 0 auto 10px auto; }
                @media print {
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body onload="window.print();">
            ${printContents}
        </body>
        </html>
    `);
    win.document.close();
}

function openPrintInNewTab() {
    const printContents = canvas.innerHTML;
    const paperSize = document.getElementById('paper-size-select').value;
    const pageCssSize = paperSize === 'F4' ? '215mm 330mm' : (paperSize === 'Letter' ? 'letter portrait' : 'A4 portrait');

    const win = window.open('', '_blank');
    if (!win) {
        alert('Pop-up terblokir oleh browser. Izinkan pop-up untuk membuka tab baru.');
        return;
    }
    win.document.write(`
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <title>Dokumen Siap Cetak</title>
            <style>
                @page { 
                    size: ${pageCssSize}; 
                    margin: 20mm 20mm 20mm 30mm;
                }
                * { box-sizing: border-box; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                body { 
                    background: #525659; 
                    margin: 0; 
                    padding: 30px 20px; 
                    display: flex; 
                    justify-content: center; 
                    font-family: system-ui, -apple-system, sans-serif;
                }
                .sheet {
                    background: #ffffff;
                    width: 210mm;
                    min-height: 297mm;
                    padding: 20mm 20mm 20mm 30mm;
                    box-shadow: 0 10px 40px rgba(0,0,0,0.5);
                    font-family: 'Times New Roman', Times, serif;
                    font-size: 12pt;
                    line-height: 1.35;
                    color: #000000;
                }
                .kop-surat-banner img { width: 100%; max-width: 100%; height: auto; display: block; margin: 0 auto 10px auto; }
                .top-bar {
                    position: fixed;
                    top: 10px;
                    right: 20px;
                    display: flex;
                    gap: 10px;
                    z-index: 999;
                }
                .btn {
                    background: #4f46e5;
                    color: #ffffff;
                    border: none;
                    padding: 10px 18px;
                    border-radius: 8px;
                    font-weight: bold;
                    cursor: pointer;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                }
                @media print {
                    body { background: transparent; padding: 0; }
                    .top-bar { display: none; }
                    .sheet { box-shadow: none; padding: 0; width: 100%; }
                }
            </style>
        </head>
        <body>
            <div class="top-bar">
                <button class="btn" onclick="window.print()">🖨️ Cetak / Simpan PDF (Ctrl+P)</button>
            </div>
            <div class="sheet">
                ${printContents}
            </div>
        </body>
        </html>
    `);
    win.document.close();
}

// Close modals on Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeModal('preview-modal');
        closeModal('kop-modal');
        closeModal('table-modal');
    }
});
</script>
