<!-- ─── Hero Section ─── -->
<section class="hero-section">
    <div class="container">
        <div class="hero-badge">
            <span style="font-size: 14px;">✨</span> Platform Form Builder & Dokumen Otomatis
        </div>

        <h1 class="hero-title">
            Buat Formulir Kustom & <br>
            <span class="gradient-text">Hasilkan Dokumen Otomatis</span>
        </h1>

        <p class="hero-desc">
            <?= e($siteDesc ?? 'ASR FORM memudahkan instansi Anda membuat formulir online dinamis, mengumpulkan respons real-time, dan mencetak dokumen/surat resmi lengkap dengan nomor surat otomatis & QR Code verifikasi.') ?>
        </p>

        <div class="hero-cta">
            <?php if ($isLoggedIn): ?>
                <a href="<?= url('dashboard') ?>" class="btn btn-primary btn-lg">
                    Buka Dashboard Admin
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            <?php else: ?>
                <a href="<?= url('register') ?>" class="btn btn-primary btn-lg">
                    Daftar Gratis Sekarang
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
                <a href="<?= url('login') ?>" class="btn btn-secondary btn-lg">
                    Masuk ke Akun
                </a>
            <?php endif; ?>
            <a href="#demo" class="btn btn-secondary btn-lg">
                Lihat Preview Demo
            </a>
        </div>

        <!-- ─── Interactive Mockup Widget ─── -->
        <div class="hero-mockup-wrapper" id="demo">
            <div class="hero-mockup">
                <div class="mockup-header">
                    <div class="mockup-dots">
                        <span class="mockup-dot red"></span>
                        <span class="mockup-dot yellow"></span>
                        <span class="mockup-dot green"></span>
                    </div>
                    <div class="mockup-tabs">
                        <button class="mockup-tab-btn active" onclick="switchTab('tab-form', this)">📝 Form Engine</button>
                        <button class="mockup-tab-btn" onclick="switchTab('tab-doc', this)">📄 Document Generator</button>
                        <button class="mockup-tab-btn" onclick="switchTab('tab-qr', this)">🔍 QR Verification</button>
                    </div>
                </div>

                <div class="mockup-content">
                    <!-- Tab 1: Form Engine -->
                    <div id="tab-form" class="mockup-panel">
                        <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 24px; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                            <div style="border-bottom: 2px solid var(--primary-600); padding-bottom: 12px; margin-bottom: 20px;">
                                <h3 style="font-size: 18px; font-weight: 800; color: var(--text-primary);">Formulir Pendaftaran & Survei Kustom</h3>
                                <p style="font-size: 13px; color: var(--text-secondary); margin: 0;">Buat formulir apa saja untuk bisnis, event, survei, pendaftaran, pesanan, dan komunitas.</p>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap / Instansi <span class="required">*</span></label>
                                <input type="text" class="form-control" value="Andi Pratama" readonly style="background: #f8fafc;">
                            </div>

                            <div class="grid-2">
                                <div class="form-group">
                                    <label class="form-label">Kategori / Keperluan</label>
                                    <input type="text" class="form-control" value="Pendaftaran Event & Kemitraan" readonly style="background: #f8fafc;">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Nomor WhatsApp</label>
                                    <input type="text" class="form-control" value="081234567890" readonly style="background: #f8fafc;">
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-3" style="padding-top: 14px; border-top: 1px solid #f1f5f9;">
                                <span class="badge badge-success">✓ 18+ Tipe Field & Canvas Tanda Tangan</span>
                                <button class="btn btn-primary btn-sm" disabled style="opacity: 0.9;">Kirim Formulir</button>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Document Generator -->
                    <div id="tab-doc" class="mockup-panel" style="display: none;">
                        <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 28px; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); font-family: 'Times New Roman', serif;">
                            <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 16px;">
                                <h4 style="font-size: 16px; font-weight: bold; margin: 0; text-transform: uppercase;">Pemerintah Kota / Instansi Resmi</h4>
                                <p style="font-size: 12px; margin: 2px 0 0;">SURAT KETERANGAN RESMI</p>
                                <p style="font-size: 11px; margin: 2px 0 0; color: #475569;">Nomor: <strong style="color: #4f46e5;">087/SK/VIII/2026</strong> <span class="badge badge-primary" style="font-family: sans-serif; font-size: 10px;">Auto Sequence</span></p>
                            </div>
                            <p style="font-size: 13px; line-height: 1.6; margin-bottom: 12px;">
                                Menerangkan bahwa pemohon bernama <strong style="color: #4f46e5;">{{nama}}</strong> dengan NIK <strong style="color: #4f46e5;">{{nik}}</strong> adalah benar terdaftar untuk keperluan <strong style="color: #4f46e5;">{{keperluan}}</strong>.
                            </p>
                            <div class="flex justify-between items-center mt-4">
                                <div style="border: 1px dashed #cbd5e1; padding: 8px 12px; border-radius: 6px; font-family: sans-serif; font-size: 11px; color: #475569;">
                                    🔍 QR Token: <code style="color: #4f46e5;">DOC-87-VALID</code>
                                </div>
                                <div style="text-align: center; font-size: 12px;">
                                    <div>Pejabat Berwenang</div>
                                    <div style="height: 30px;"></div>
                                    <strong>Dr. H. Hendra, M.Si</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: QR Verification -->
                    <div id="tab-qr" class="mockup-panel" style="display: none;">
                        <div style="max-width: 520px; margin: 0 auto; text-align: center; background: #ffffff; padding: 28px; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                            <div style="width: 52px; height: 52px; background: var(--success-50); color: var(--success-600); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; font-size: 24px;">
                                ✓
                            </div>
                            <h3 style="font-size: 18px; font-weight: 800; color: #065f46; margin-bottom: 4px;">DOKUMEN VALID & RESMI</h3>
                            <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 16px;">Telah diverifikasi melalui sistem tanda tangan digital ASR FORM.</p>
                            
                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; text-align: left; font-size: 13px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                    <span style="color: #64748b;">Nomor Dokumen:</span>
                                    <strong>087/SK/VIII/2026</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                    <span style="color: #64748b;">Jenis Dokumen:</span>
                                    <strong>Surat Keterangan</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: #64748b;">Status:</span>
                                    <span class="badge badge-success">Approved & Verified</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ─── Features Grid ─── -->
<section class="features-section" id="fitur">
    <div class="container">
        <div class="section-header">
            <div class="section-tag">Fitur Lengkap</div>
            <h2 class="section-title">Semua Kebutuhan Form & Dokumen dalam Satu Tempat</h2>
            <p class="section-desc">Fondasi Dynamic Field & Variable Engine memungkinkan pembuatan dokumen apa saja tanpa mengubah struktur database.</p>
        </div>

        <div class="feature-grid">
            <!-- 1 -->
            <div class="feature-card">
                <div class="feature-icon-box">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <h3 class="feature-title">18+ Dynamic Field Types</h3>
                <p class="feature-text">Mendukung input teks, email, number, tanggal/waktu, dropdown, file upload, rating, hingga canvas digital signature.</p>
            </div>

            <!-- 2 -->
            <div class="feature-card">
                <div class="feature-icon-box">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <h3 class="feature-title">Smart Template Generator</h3>
                <p class="feature-text">Buat template surat dengan tag variabel dinamis seperti <code>{{nama}}</code>, <code>{{tanggal}}</code>, dan <code>{{nomor_surat}}</code> secara otomatis.</p>
            </div>

            <!-- 3 -->
            <div class="feature-card">
                <div class="feature-icon-box">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M7 7h3v3H7z"/><path d="M14 7h3v3h-3z"/><path d="M7 14h3v3H7z"/><path d="M14 14h3v3h-3z"/></svg>
                </div>
                <h3 class="feature-title">QR Code Verification</h3>
                <p class="feature-text">Setiap dokumen resmi yang terbit dilengkapi token unik dan QR Code untuk verifikasi keabsahan secara publik.</p>
            </div>

            <!-- 4 -->
            <div class="feature-card">
                <div class="feature-icon-box">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h3 class="feature-title">Nomor Surat Otomatis</h3>
                <p class="feature-text">Penomoran surat fleksibel dengan format romawi, kode seksi, dan reset sequence tahunan/bulanan otomatis.</p>
            </div>

            <!-- 5 -->
            <div class="feature-card">
                <div class="feature-icon-box">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
                </div>
                <h3 class="feature-title">Approval Workflow</h3>
                <p class="feature-text">Mekanisme persetujuan dokumen berjenjang dari Operator, Editor, Approver hingga diterbitkan dan diarsipkan.</p>
            </div>

            <!-- 6 -->
            <div class="feature-card">
                <div class="feature-icon-box">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3 class="feature-title">Aman & Terverifikasi</h3>
                <p class="feature-text">Dilengkapi proteksi CSRF, enkripsi password bcrypt, PDO prepared statements, dan pencatatan audit log lengkap.</p>
            </div>
        </div>
    </div>
</section>

<!-- ─── Stats Counter Banner ─── -->
<section class="stats-section" id="keunggulan">
    <div class="container">
        <div class="stats-grid-landing">
            <div class="stat-item-landing">
                <div class="stat-num"><?= number_format($totalForms ?? 18) ?>+</div>
                <div class="stat-lbl">Formulir Terbit</div>
            </div>
            <div class="stat-item-landing">
                <div class="stat-num"><?= number_format($totalResponses ?? 100) ?>+</div>
                <div class="stat-lbl">Respons Masuk</div>
            </div>
            <div class="stat-item-landing">
                <div class="stat-num"><?= number_format($totalDocuments ?? 50) ?>+</div>
                <div class="stat-lbl">Dokumen & QR Valid</div>
            </div>
            <div class="stat-item-landing">
                <div class="stat-num">100%</div>
                <div class="stat-lbl">PHP Native & Ringan</div>
            </div>
        </div>
    </div>
</section>

<!-- ─── Ad Placement ─── -->
<?= renderAd('PUBLIC_PAGE') ?>

<!-- Interactive script for tabs -->
<script>
function switchTab(tabId, btn) {
    document.querySelectorAll('.mockup-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.mockup-tab-btn').forEach(b => b.classList.remove('active'));
    
    const target = document.getElementById(tabId);
    if (target) target.style.display = 'block';
    btn.classList.add('active');
}
</script>
