<!-- ─── Hero Section ─── -->
<section class="hero-section">
    <div class="container">
        <div class="hero-badge">
            <span class="hero-badge-dot"></span>
            Sistem Otomasi Formulir &amp; Penerbitan Dokumen Resmi
        </div>

        <h1 class="hero-title">
            Transformasi Formulir Digital &amp; <br>
            <span class="gradient-text">Penerbitan Dokumen Sah Otomatis</span>
        </h1>

        <p class="hero-desc">
            Platform terpadu untuk instansi pemerintah, institusi pendidikan, dan korporasi dalam mengumpulkan data formulir secara terstruktur serta menerbitkan dokumen resmi berpenomoran otomatis lengkap dengan validasi QR Code.
        </p>

        <div class="hero-cta-group">
            <?php if ($isLoggedIn): ?>
                <a href="<?= url('dashboard') ?>" class="btn btn-primary btn-lg">
                    <span>Akses Dashboard Admin</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            <?php else: ?>
                <a href="<?= url('register') ?>" class="btn btn-primary btn-lg">
                    <span>Mulai Buat Formulir</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
                <a href="<?= url('login') ?>" class="btn btn-secondary btn-lg">
                    <span>Masuk ke Portal</span>
                </a>
            <?php endif; ?>
            <a href="#arsitektur" class="btn btn-secondary btn-lg">
                <span>Lihat Alur Kerja</span>
            </a>
        </div>

        <!-- Trust Signals Bar -->
        <div class="hero-trust-bar">
            <div class="trust-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Validasi QR Code Publik</span>
            </div>
            <div class="trust-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Format Template Microsoft Word &amp; PDF</span>
            </div>
            <div class="trust-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Penomoran Romawi Anti-Duplikasi</span>
            </div>
            <div class="trust-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Keamanan Standar Instansi</span>
            </div>
        </div>

        <!-- ─── Interactive Form-to-Document Architecture Studio ─── -->
        <div class="bento-showcase-grid" id="arsitektur">
            <!-- 1. Input Panel: Form Dinamis & Tanda Tangan -->
            <div class="bento-showcase-card bento-col-7 fade-in">
                <div class="bento-showcase-header">
                    <div class="flex items-center gap-2">
                        <div class="bento-panel-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </div>
                        <span class="bento-showcase-title">
                            Studio Formulir &amp; Pengisian Real-Time
                        </span>
                    </div>
                    <span class="status-indicator-live">
                        <span class="pulse-dot"></span> Sinkronisasi Aktif
                    </span>
                </div>

                <div class="studio-form-container">
                    <div class="form-group mb-3">
                        <label class="form-label font-semibold text-slate-700">Nama Lengkap Pemohon</label>
                        <input type="text" id="demo-input-name" class="form-control" value="Ir. H. Pratama Wibowo, M.T." placeholder="Ketik nama pemohon..." oninput="updateLivePreview()">
                    </div>

                    <div class="grid-2 gap-3 mb-3">
                        <div class="form-group">
                            <label class="form-label font-semibold text-slate-700">Nomor Induk Kependudukan (NIK)</label>
                            <input type="text" id="demo-input-nik" class="form-control" value="3271028809950001" placeholder="16 digit NIK..." oninput="updateLivePreview()">
                        </div>
                        <div class="form-group">
                            <label class="form-label font-semibold text-slate-700">Keperluan Pelayanan</label>
                            <select id="demo-input-purpose" class="form-control" onchange="updateLivePreview()">
                                <option value="Izin Operasional &amp; Legalitas Usaha">Izin Operasional &amp; Legalitas Usaha</option>
                                <option value="Surat Keterangan Domisili Lembaga">Surat Keterangan Domisili Lembaga</option>
                                <option value="Rekomendasi Akreditasi Program">Rekomendasi Akreditasi Program</option>
                            </select>
                        </div>
                    </div>

                    <!-- Digital Signature Canvas Simulator -->
                    <div class="form-group mb-3">
                        <div class="flex items-center justify-between mb-1">
                            <label class="form-label font-semibold text-slate-700" style="margin:0;">Tanda Tangan Elektronik Pemohon</label>
                            <span style="font-size: 11.5px; color: var(--primary-600); font-weight: 600;">Touch &amp; Pen Canvas</span>
                        </div>
                        <div class="signature-simulator-box">
                            <svg viewBox="0 0 220 40" class="signature-path-svg">
                                <path d="M 12,24 Q 30,6 52,22 T 95,14 T 135,26 T 175,8 T 205,22"/>
                            </svg>
                            <span class="signature-verified-pill">✓ Tersimpan Aman</span>
                        </div>
                    </div>

                    <div class="studio-form-footer">
                        <span class="studio-hint">Setiap entri data otomatis disubstitusikan ke template dokumen.</span>
                        <button type="button" class="btn btn-primary btn-sm" id="btn-demo-sim" onclick="triggerSubmitSim()">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            Terbitkan Dokumen
                        </button>
                    </div>
                </div>
            </div>

            <!-- 2. Output Panel: Dokumen Resmi Terbit -->
            <div class="bento-showcase-card bento-col-5 fade-in">
                <div class="bento-showcase-header">
                    <div class="flex items-center gap-2">
                        <div class="bento-panel-icon" style="color: var(--success-600); background: var(--success-50);">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <span class="bento-showcase-title">
                            Pratinjau Dokumen Sah
                        </span>
                    </div>
                    <span class="badge badge-success" style="font-size: 11px; font-weight: 700;">Format Resmi</span>
                </div>

                <div class="bento-doc-paper">
                    <div class="bento-doc-watermark">DOKUMEN RESMI</div>
                    
                    <!-- Kop Surat Resmi -->
                    <div class="doc-official-header">
                        <div class="doc-instansi-title">PEMERINTAH KOTA / INSTANSI RESMI</div>
                        <div class="doc-surat-title">SURAT KETERANGAN RESMI</div>
                        <div class="doc-surat-nomor">
                            Nomor: <strong id="doc-live-number">087/SK/VIII/2026</strong>
                        </div>
                    </div>

                    <div class="doc-official-body">
                        Yang bertanda tangan di bawah ini menerangkan bahwa pemohon:
                        <div style="margin: 8px 0; padding-left: 12px; border-left: 2px solid var(--primary-300);">
                            <div>Nama: <strong id="doc-live-name" class="doc-highlight">Ir. H. Pratama Wibowo, M.T.</strong></div>
                            <div style="margin-top: 2px;">NIK: <strong id="doc-live-nik" class="doc-highlight">3271028809950001</strong></div>
                            <div style="margin-top: 2px;">Keperluan: <strong id="doc-live-purpose" class="doc-highlight-success">Izin Operasional &amp; Legalitas Usaha</strong></div>
                        </div>
                        Telah memenuhi persyaratan administratif sesuai ketentuan yang berlaku.
                    </div>

                    <!-- Dokumen Footer with QR Seal and Signature -->
                    <div class="doc-official-footer">
                        <div class="doc-qr-seal">
                            <div style="font-weight: 800; color: #065f46; font-size: 9px; letter-spacing: 0.3px;">QR VERIFIED</div>
                            <code id="doc-live-token" style="font-size: 8.5px; color: var(--primary-700); font-weight: 700;">DOC-087-VALID</code>
                        </div>
                        <div class="doc-signature-block">
                            <div class="doc-signer-title">Pejabat Pengesah</div>
                            <div style="height: 24px;"></div>
                            <div class="doc-signer-name">Drs. H. Hendra, M.Si</div>
                            <div class="doc-signer-nip">NIP. 19780512 200212 1 003</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Key Capability 1: Validasi QR Code -->
            <div class="bento-showcase-card bento-col-4 fade-in">
                <div class="bento-showcase-header">
                    <span class="bento-showcase-title">Validasi Keaslian QR</span>
                    <span class="badge badge-success" style="font-size: 10.5px;">Tanpa Login</span>
                </div>
                <div style="padding: 4px 0;">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="feature-micro-icon bg-emerald-50 text-emerald-600">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                        <div>
                            <div style="font-size: 14px; font-weight: 800; color: var(--text-primary);">Verifikasi Publik Instan</div>
                            <div style="font-size: 12px; color: var(--text-secondary);">Masyarakat &amp; auditor cukup memindai QR Code untuk memeriksa keaslian berkas.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Key Capability 2: Penomoran Romawi Otomatis -->
            <div class="bento-showcase-card bento-col-4 fade-in">
                <div class="bento-showcase-header">
                    <span class="bento-showcase-title">Penomoran Dokumen</span>
                    <span class="badge badge-primary" style="font-size: 10.5px;">Auto Sequence</span>
                </div>
                <div style="padding: 4px 0;">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="feature-micro-icon bg-indigo-50 text-indigo-600">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div>
                            <div style="font-size: 14px; font-weight: 800; color: var(--text-primary);">Format Romawi &amp; Kode Seksi</div>
                            <div style="font-size: 12px; color: var(--text-secondary);">Konfigurasi format nomor surat fleksibel dengan nomor urut yang bertambah otomatis.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Key Capability 3: Integrasi & Format Ekspor -->
            <div class="bento-showcase-card bento-col-4 fade-in">
                <div class="bento-showcase-header">
                    <span class="bento-showcase-title">Ekspor &amp; Distribusi</span>
                    <span class="badge badge-warning" style="font-size: 10.5px;">Multi Format</span>
                </div>
                <div style="padding: 4px 0;">
                    <div class="flex flex-wrap gap-2">
                        <span class="format-tag">Microsoft Word (.DOCX)</span>
                        <span class="format-tag">Adobe PDF Siap Cetak</span>
                        <span class="format-tag">Rekap Data Excel (CSV)</span>
                        <span class="format-tag">Notifikasi WhatsApp</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ─── 3-Step Practical Workflow Section ─── -->
<section class="workflow-section">
    <div class="container">
        <div class="section-header text-center">
            <div class="section-tag">Alur Kerja Praktis</div>
            <h2 class="section-title">Bagaimana Sistem Bekerja untuk Instansi Anda</h2>
            <p class="section-desc">Tiga tahapan terstruktur yang menyederhanakan birokrasi dan administrasi dari manual menjadi otomatis.</p>
        </div>

        <div class="workflow-grid">
            <!-- Step 1 -->
            <div class="workflow-card fade-in">
                <div class="workflow-step-num">01</div>
                <div class="workflow-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                </div>
                <h3 class="workflow-title">Rancang Formulir Kustom</h3>
                <p class="workflow-desc">Gunakan 18+ jenis kolom dinamis (teks, NIK, tanggal, upload berkas, rating, hingga tanda tangan digital) sesuai kebutuhan instansi Anda.</p>
            </div>

            <!-- Step 2 -->
            <div class="workflow-card fade-in">
                <div class="workflow-step-num">02</div>
                <div class="workflow-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <h3 class="workflow-title">Pasang Template Dokumen</h3>
                <p class="workflow-desc">Unggah template surat resmi dalam format Microsoft Word (.docx) dengan tag variabel cerdas seperti <code>{{nama}}</code> dan <code>{{nomor_surat}}</code>.</p>
            </div>

            <!-- Step 3 -->
            <div class="workflow-card fade-in">
                <div class="workflow-step-num">03</div>
                <div class="workflow-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <h3 class="workflow-title">Terbitkan Surat Sah Otomatis</h3>
                <p class="workflow-desc">Saat publik atau pemohon mengisi form, dokumen resmi langsung diterbitkan lengkap dengan nomor urut, stempel, dan QR Code verifikasi.</p>
            </div>
        </div>
    </div>
</section>

<!-- ─── Core Enterprise Capabilities (Bento Feature Matrix) ─── -->
<section class="features-section" id="fitur">
    <div class="container">
        <div class="section-header">
            <div class="section-tag">Kapabilitas Unggulan</div>
            <h2 class="section-title">Solusi Menyeluruh untuk Kebutuhan Dokumen Modern</h2>
            <p class="section-desc">Dibangun di atas arsitektur performa tinggi yang aman, fleksibel, dan mudah dioperasikan oleh seluruh staf administrasi.</p>
        </div>

        <div class="bento-feature-grid">
            <!-- Feature 1 (Span 8) -->
            <div class="bento-feature-card span-8 fade-in">
                <div class="bento-feat-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                </div>
                <h3 class="bento-feat-title">18+ Tipe Kolom Dinamis &amp; Tanda Tangan Digital</h3>
                <p class="bento-feat-desc">Sistem form builder visual yang mendukung berbagai jenis isian data spesifik: NIK, nomor telepon, tanggal lahir, upload lampiran multi-file, dropdown bertingkat, hingga tanda tangan digital kanvas responsif di layar sentuh.</p>
                <div class="bento-chips-wrapper">
                    <span class="bento-chip">Tanda Tangan Digital</span>
                    <span class="bento-chip">Multi-File Attachment</span>
                    <span class="bento-chip">Validasi Regex NIK/Email</span>
                    <span class="bento-chip">Cascading Select</span>
                    <span class="bento-chip">Linear Scale &amp; Star Rating</span>
                </div>
            </div>

            <!-- Feature 2 (Span 4) -->
            <div class="bento-feature-card span-4 fade-in">
                <div class="bento-feat-icon" style="background: var(--success-50); color: var(--success-600); border-color: rgba(16,185,129,0.2);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <h3 class="bento-feat-title">Dynamic Variable Engine</h3>
                <p class="bento-feat-desc">Substitusi otomatis seluruh field formulir ke dalam template dokumen Microsoft Word (.docx) secara presisi tanpa merusak format tata letak.</p>
            </div>

            <!-- Feature 3 (Span 4) -->
            <div class="bento-feature-card span-4 fade-in">
                <div class="bento-feat-icon" style="background: var(--info-50); color: var(--info-600); border-color: rgba(14,165,233,0.2);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M7 7h3v3H7z"/><path d="M14 7h3v3h-3z"/><path d="M7 14h3v3H7z"/><path d="M14 14h3v3h-3z"/></svg>
                </div>
                <h3 class="bento-feat-title">Halaman Validasi QR Code</h3>
                <p class="bento-feat-desc">Setiap dokumen yang terbit otomatis dilengkapi halaman verifikasi daring dengan stempel keabsahan dan ringkasan isi dokumen asli.</p>
            </div>

            <!-- Feature 4 (Span 4) -->
            <div class="bento-feature-card span-4 fade-in">
                <div class="bento-feat-icon" style="background: var(--warning-50); color: var(--warning-600); border-color: rgba(245,158,11,0.2);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h3 class="bento-feat-title">Penomoran Dokumen Cerdas</h3>
                <p class="bento-feat-desc">Atur struktur nomor surat: nomor urut otomatis, kode klasifikasi instansi, bulan romawi, dan tahun berjalan tanpa bentrok nomor ganda.</p>
            </div>

            <!-- Feature 5 (Span 4) -->
            <div class="bento-feature-card span-4 fade-in">
                <div class="bento-feat-icon" style="background: #f3e8ff; color: #9333ea; border-color: rgba(147,51,234,0.2);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3 class="bento-feat-title">Keamanan &amp; Audit Log</h3>
                <p class="bento-feat-desc">Dilengkapi perlindungan CSRF, enkripsi password bcrypt, PDO prepared statements, manajemen hak akses, dan pencatatan audit log sistem.</p>
            </div>
        </div>
    </div>
</section>

<!-- ─── Real-World Use Cases (Sektor Solusi) ─── -->
<section class="use-cases-section">
    <div class="container">
        <div class="section-header text-center">
            <div class="section-tag">Solusi Terapan</div>
            <h2 class="section-title">Dipercaya untuk Berbagai Sektor &amp; Kebutuhan</h2>
            <p class="section-desc">Penerapan nyata yang telah membantu efisiensi operasional organisasi.</p>
        </div>

        <div class="use-cases-grid">
            <div class="use-case-card fade-in">
                <div class="use-case-header">
                    <div class="use-case-icon">🏛️</div>
                    <div>
                        <h3 class="use-case-title">Pemerintahan &amp; Kelurahan</h3>
                        <span class="use-case-sub">Pelayanan Publik &amp; Administrasi Warga</span>
                    </div>
                </div>
                <p class="use-case-desc">Penerbitan Surat Keterangan Domisili, Surat Pengantar Nikah, Izin Usaha Mikro, dan permohonan bantuan sosial secara daring.</p>
            </div>

            <div class="use-case-card fade-in">
                <div class="use-case-header">
                    <div class="use-case-icon">🎓</div>
                    <div>
                        <h3 class="use-case-title">Pendidikan &amp; Kampus</h3>
                        <span class="use-case-sub">Pendaftaran &amp; Administrasi Akademik</span>
                    </div>
                </div>
                <p class="use-case-desc">Formulir PPDB, Surat Keterangan Aktif Kuliah, Surat Bebas Pustaka, Rekomendasi Beasiswa, dan Legalisir Ijazah ber-QR.</p>
            </div>

            <div class="use-case-card fade-in">
                <div class="use-case-header">
                    <div class="use-case-icon">🏢</div>
                    <div>
                        <h3 class="use-case-title">Korporasi &amp; HRD</h3>
                        <span class="use-case-sub">Rekrutmen &amp; Surat Keputusan</span>
                    </div>
                </div>
                <p class="use-case-desc">Pengumpulan berkas pelamar kerja, penerbitan Surat Perjanjian Kerja (PKWT), Surat Perintah Tugas (SPT), dan Berita Acara resmi.</p>
            </div>

            <div class="use-case-card fade-in">
                <div class="use-case-header">
                    <div class="use-case-icon">🤝</div>
                    <div>
                        <h3 class="use-case-title">Organisasi &amp; Komunitas</h3>
                        <span class="use-case-sub">Sertifikasi &amp; Keanggotaan</span>
                    </div>
                </div>
                <p class="use-case-desc">Registrasi anggota, penerbitan Kartu Tanda Anggota (KTA), Sertifikat Pelatihan Otomatis, dan surat mandat kegiatan.</p>
            </div>
        </div>
    </div>
</section>

<!-- ─── Bento Stats Counter Banner ─── -->
<section class="stats-section" id="keunggulan">
    <div class="container">
        <div class="stats-grid-landing">
            <div class="stat-item-landing">
                <div class="stat-num"><?= number_format($totalForms ?? 18) ?>+</div>
                <div class="stat-lbl">Formulir Aktif Terbit</div>
            </div>
            <div class="stat-item-landing">
                <div class="stat-num"><?= number_format($totalResponses ?? 100) ?>+</div>
                <div class="stat-lbl">Respons Data Masuk</div>
            </div>
            <div class="stat-item-landing">
                <div class="stat-num"><?= number_format($totalDocuments ?? 50) ?>+</div>
                <div class="stat-lbl">Dokumen Sah Terverifikasi</div>
            </div>
            <div class="stat-item-landing">
                <div class="stat-num">100%</div>
                <div class="stat-lbl">Reliabilitas &amp; Keamanan Server</div>
            </div>
        </div>
    </div>
</section>

<!-- ─── Interactive FAQ Accordion Section ─── -->
<section class="faq-section">
    <div class="container">
        <div class="section-header text-center">
            <div class="section-tag">Pusat Informasi</div>
            <h2 class="section-title">Pertanyaan yang Sering Diajukan</h2>
            <p class="section-desc">Jawaban transparan mengenai fungsionalitas, keamanan, dan penerapan sistem.</p>
        </div>

        <div class="faq-container">
            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    <span>Apakah dokumen yang diterbitkan memiliki kekuatan hukum yang sah?</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="faq-chevron"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="faq-answer">
                    <p>Ya. Dokumen dilengkapi tanda tangan elektronik yang terekam aman, nomor surat resmi berurutan, stempel digital, serta QR Code unik yang terhubung langsung ke basis data verifikasi publik resmi instansi penerbit.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    <span>Bisakah saya menggunakan format template Word (.docx) yang sudah dimiliki kantor?</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="faq-chevron"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="faq-answer">
                    <p>Tentu saja. Anda cukup membuka file Microsoft Word kantor Anda, lalu menyisipkan placeholder tag variabel seperti <code>{{nama_pemohon}}</code>, <code>{{nik}}</code>, <code>{{alamat}}</code>. Sistem akan mengisi titik-titik tersebut secara otomatis dari isian form.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    <span>Bagaimana cara publik memverifikasi keaslian dokumen?</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="faq-chevron"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="faq-answer">
                    <p>Setiap dokumen mencetak QR Code khusus. Siapa pun dapat memindai QR Code tersebut menggunakan kamera smartphone tanpa perlu login. Sistem akan membuka halaman validasi resmi yang menampilkan status keabsahan dan ringkasan data dokumen.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    <span>Apakah formulir dapat diakses dengan nyaman di perangkat ponsel (smartphone)?</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="faq-chevron"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="faq-answer">
                    <p>Ya. Seluruh tampilan formulir dan portal publik dirancang 100% responsif mobile, mendukung pengisian cepat via Android/iOS, upload foto dokumen langsung dari kamera HP, serta tanda tangan sentuh yang presisi.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ─── Final CTA Banner ─── -->
<section class="cta-banner-section">
    <div class="container">
        <div class="cta-banner-card">
            <div class="cta-banner-content">
                <h2 class="cta-banner-title">Mulai Otomasi Formulir &amp; Dokumen Anda Hari Ini</h2>
                <p class="cta-banner-desc">Tingkatkan efisiensi kerja instansi, minimalkan human-error, dan hadirkan layanan dokumen yang cepat, modern, serta terpercaya.</p>
                <div class="flex gap-3 flex-wrap justify-center">
                    <a href="<?= url('register') ?>" class="btn btn-primary btn-lg" style="background:#ffffff; color:#4338ca; border-color:#ffffff; font-weight:700;">
                        Daftar Akun Gratis Sekarang
                    </a>
                    <?php if (!empty($contactEnabled)): ?>
                        <a href="<?= url('contact') ?>" class="btn btn-secondary btn-lg" style="color:#ffffff; border-color:rgba(255,255,255,0.4); background:rgba(255,255,255,0.1);">
                            Konsultasi Kebutuhan Instansi
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ─── Ad Placement ─── -->
<div class="container my-4">
    <?= renderAd('PUBLIC_PAGE') ?>
</div>

<!-- Interactive Live Preview Synchronization & FAQ Script -->
<script>
function updateLivePreview() {
    const nameInput = document.getElementById('demo-input-name');
    const nikInput = document.getElementById('demo-input-nik');
    const purposeInput = document.getElementById('demo-input-purpose');

    const nameDoc = document.getElementById('doc-live-name');
    const nikDoc = document.getElementById('doc-live-nik');
    const purposeDoc = document.getElementById('doc-live-purpose');

    if (nameDoc && nameInput) {
        nameDoc.textContent = nameInput.value.trim() || 'Nama Lengkap Pemohon';
    }
    if (nikDoc && nikInput) {
        nikDoc.textContent = nikInput.value.trim() || '3271028809950001';
    }
    if (purposeDoc && purposeInput) {
        purposeDoc.textContent = purposeInput.value;
    }
}

function triggerSubmitSim() {
    const btn = document.getElementById('btn-demo-sim');
    if (!btn) return;
    
    const originalText = btn.innerHTML;
    btn.innerHTML = 'Memproses Dokumen...';
    btn.disabled = true;

    setTimeout(() => {
        btn.innerHTML = '✓ Dokumen Sukses Diterbitkan';
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-success');

        const token = document.getElementById('doc-live-token');
        const num = document.getElementById('doc-live-number');
        if (token) {
            const randomCode = Math.floor(100 + Math.random() * 900);
            token.textContent = 'DOC-' + randomCode + '-VALID';
            if (num) {
                num.textContent = '0' + (randomCode % 100) + '/SK/VIII/2026';
            }
        }

        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-primary');
            btn.disabled = false;
        }, 2500);
    }, 400);
}

function toggleFaq(btn) {
    const item = btn.closest('.faq-item');
    if (!item) return;
    item.classList.toggle('open');
}
</script>
