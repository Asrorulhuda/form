<?php 
use App\Core\CSRF; 
use App\Core\Session; 
$errors = Session::getFlash('errors') ?? [];
$plans = $plans ?? [];
$selectedPlan = $selectedPlan ?? 'Gratis';
?>

<div class="auth-split-wrapper">
    <!-- ─── Left Canvas: Illustrative Brand Storyboard ─── -->
    <div class="auth-illustration-side">
        <div class="auth-mesh-grid"></div>

        <!-- Top Header Brand in Canvas -->
        <div class="auth-side-brand">
            <img src="<?= asset('img/logo-icon.svg') ?>" alt="ASR FORM" width="42" height="42" style="border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
            <div>
                <div class="auth-side-brand-name">ASR FORM</div>
                <div class="auth-side-brand-sub">Platform Otomasi Formulir &amp; Dokumen Sah</div>
            </div>
        </div>

        <!-- Central Illustrative Scene -->
        <div class="auth-illustration-scene">
            <div class="auth-scene-wrapper">
                <!-- Floating Widget 1 (Top Right) -->
                <div class="auth-floating-widget widget-top-right">
                    <div class="auth-widget-icon" style="background: rgba(16, 185, 129, 0.25); color: #34d399;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <div>
                        <div class="auth-widget-title">Aktivasi Cepat</div>
                        <div class="auth-widget-desc">Mulai dalam hitungan menit</div>
                    </div>
                </div>

                <!-- Vector SVG Illustration: Workspace, Smart Form & Official Document -->
                <svg class="auth-svg-illustration" viewBox="0 0 520 340" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="regDeskGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.2"/>
                            <stop offset="100%" stop-color="#1e1b4b" stop-opacity="0.8"/>
                        </linearGradient>
                        <linearGradient id="regDocGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#ffffff"/>
                            <stop offset="100%" stop-color="#e0e7ff"/>
                        </linearGradient>
                        <linearGradient id="regAccentGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#6366f1"/>
                            <stop offset="100%" stop-color="#06b6d4"/>
                        </linearGradient>
                        <filter id="regGlow" x="-20%" y="-20%" width="140%" height="140%">
                            <feGaussianBlur stdDeviation="10" result="blur"/>
                            <feComposite in="SourceGraphic" in2="blur" operator="over"/>
                        </filter>
                    </defs>

                    <!-- Ambient Glow Circles -->
                    <circle cx="260" cy="170" r="140" fill="#6366f1" fill-opacity="0.18" filter="url(#regGlow)"/>
                    <circle cx="160" cy="120" r="80" fill="#06b6d4" fill-opacity="0.15" filter="url(#regGlow)"/>

                    <!-- Platform Base Shadow -->
                    <ellipse cx="260" cy="285" rx="200" ry="35" fill="#0b0f19" fill-opacity="0.45"/>

                    <!-- Layer 1: Background Tablet / Form Studio Screen -->
                    <rect x="90" y="65" width="220" height="175" rx="16" fill="#1e293b" stroke="rgba(255,255,255,0.2)" stroke-width="2"/>
                    <!-- Tablet Screen Content -->
                    <rect x="105" y="80" width="190" height="24" rx="6" fill="#334155"/>
                    <circle cx="118" cy="92" r="4" fill="#ef4444"/>
                    <circle cx="130" cy="92" r="4" fill="#f59e0b"/>
                    <circle cx="142" cy="92" r="4" fill="#10b981"/>
                    <rect x="160" y="88" width="80" height="8" rx="4" fill="#64748b"/>

                    <!-- Form Inputs Simulation on Screen -->
                    <rect x="105" y="115" width="90" height="18" rx="4" fill="rgba(99,102,241,0.25)" stroke="#6366f1" stroke-width="1"/>
                    <rect x="112" y="121" width="50" height="6" rx="3" fill="#a5b4fc"/>

                    <rect x="205" y="115" width="90" height="18" rx="4" fill="#334155"/>
                    <rect x="212" y="121" width="40" height="6" rx="3" fill="#64748b"/>

                    <rect x="105" y="142" width="190" height="18" rx="4" fill="#334155"/>
                    <rect x="112" y="148" width="70" height="6" rx="3" fill="#64748b"/>

                    <!-- Digital Signature Wave on Screen -->
                    <rect x="105" y="168" width="190" height="32" rx="6" fill="#0f172a" stroke="rgba(99,102,241,0.4)" stroke-width="1"/>
                    <path d="M 115 186 Q 130 172 145 184 T 175 178 T 205 188 T 235 174 T 260 184" stroke="#38bdf8" stroke-width="2" stroke-linecap="round" fill="none"/>

                    <!-- Connecting Data Stream Flow -->
                    <path d="M 270 145 C 310 130, 310 110, 340 105" stroke="url(#regAccentGrad)" stroke-width="3" stroke-dasharray="6 6" stroke-linecap="round"/>

                    <!-- Layer 2: Foreground Official Generated Document -->
                    <g transform="translate(260, 50)">
                        <rect x="0" y="0" width="180" height="235" rx="12" fill="url(#regDocGrad)" stroke="#ffffff" stroke-width="2" filter="drop-shadow(0 15px 25px rgba(0,0,0,0.3))"/>
                        
                        <!-- Top Official Ribbon / Header -->
                        <rect x="20" y="20" width="140" height="14" rx="4" fill="#4338ca"/>
                        <rect x="40" y="42" width="100" height="6" rx="3" fill="#6366f1"/>
                        <rect x="55" y="52" width="70" height="5" rx="2.5" fill="#94a3b8"/>

                        <line x1="20" y1="68" x2="160" y2="68" stroke="#cbd5e1" stroke-width="1.5"/>

                        <!-- Body Text Lines -->
                        <rect x="20" y="80" width="140" height="5" rx="2.5" fill="#64748b"/>
                        <rect x="20" y="92" width="125" height="5" rx="2.5" fill="#64748b"/>
                        <rect x="20" y="104" width="135" height="5" rx="2.5" fill="#64748b"/>
                        <rect x="20" y="116" width="90" height="5" rx="2.5" fill="#64748b"/>

                        <!-- Document Highlight Box -->
                        <rect x="20" y="132" width="140" height="30" rx="6" fill="#eef2ff" stroke="#c7d2fe" stroke-width="1"/>
                        <rect x="28" y="140" width="60" height="5" rx="2.5" fill="#4338ca"/>
                        <rect x="28" y="149" width="100" height="5" rx="2.5" fill="#6366f1"/>

                        <!-- QR Code Seal in Document -->
                        <rect x="22" y="176" width="38" height="38" rx="6" fill="#ffffff" stroke="#94a3b8" stroke-width="1"/>
                        <rect x="26" y="180" width="12" height="12" fill="#0f172a"/>
                        <rect x="44" y="180" width="12" height="12" fill="#0f172a"/>
                        <rect x="26" y="198" width="12" height="12" fill="#0f172a"/>
                        <circle cx="48" cy="202" r="3" fill="#10b981"/>

                        <!-- Stamp Badge -->
                        <circle cx="130" cy="195" r="22" fill="#10b981" fill-opacity="0.15" stroke="#10b981" stroke-width="2"/>
                        <path d="M 122 195 L 128 201 L 138 190" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <text x="130" y="224" font-size="7" font-weight="bold" fill="#065f46" text-anchor="middle" font-family="sans-serif">TERVERIFIKASI</text>
                    </g>
                </svg>

                <!-- Floating Widget 2 (Bottom Left) -->
                <div class="auth-floating-widget widget-bottom-left">
                    <div class="auth-widget-icon" style="background: rgba(99, 102, 241, 0.3); color: #a5b4fc;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div>
                        <div class="auth-widget-title">Privasi &amp; Enkripsi</div>
                        <div class="auth-widget-desc">Perlindungan Data Berlapis</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Storyboard Text & Trust Pills -->
        <div class="auth-story-content">
            <h2 class="auth-story-title">Mulai Transformasi Administrasi Anda</h2>
            <p class="auth-story-desc">Daftarkan akun dan akses seluruh fasilitas perancangan form cerdas, generator dokumen Word (.docx), dan QR validation.</p>
            
            <div class="auth-trust-badges">
                <span class="auth-trust-pill">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    256-Bit SSL Enkripsi
                </span>
                <span class="auth-trust-pill">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Dukungan WhatsApp &amp; Email
                </span>
            </div>
        </div>
    </div>

    <!-- ─── Right Canvas: Focused Registration Form ─── -->
    <div class="auth-form-side" style="max-width: 640px;">
        <!-- Top Navigation -->
        <div class="auth-form-top">
            <a href="<?= url() ?>" class="auth-back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Kembali ke Beranda
            </a>
            <span class="badge badge-primary" style="font-size: 11px; padding: 3px 8px; font-weight: 700;">
                Pendaftaran Instansi
            </span>
        </div>

        <!-- Main Form Content -->
        <div class="auth-form-content" style="max-width: 520px;">
            <div class="auth-form-header">
                <a href="<?= url() ?>" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; margin-bottom: 12px;">
                    <img src="<?= asset('img/logo-icon.svg') ?>" alt="ASR FORM" width="40" height="40" style="border-radius: 10px; box-shadow: 0 2px 8px rgba(79,70,229,0.25);">
                    <span style="font-size: 20px; font-weight: 900; color: #0f172a; letter-spacing: -0.4px;">ASR FORM</span>
                </a>
                <h1 class="auth-form-title">Daftar Akun Baru</h1>
                <p class="auth-form-subtitle">Pilih paket layanan dan lengkapi profil instansi Anda.</p>
            </div>

            <?php if (Session::hasFlash('error')): ?>
                <div class="alert alert-error mb-4" style="border-radius: 12px; font-size: 13.5px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;">
                        <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                    <span><?= e(Session::getFlash('error')) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= url('register') ?>" autocomplete="off" id="register-form">
                <?= CSRF::field() ?>

                <!-- ─── Plan Selection ─── -->
                <div class="form-group mb-4">
                    <label class="form-label font-semibold" style="font-size: 13px; margin-bottom: 8px; color: #334155;">Pilih Paket Layanan <span class="required">*</span></label>
                    
                    <div class="plan-selector-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 10px;">
                        <?php foreach ($plans as $p): 
                            $isPlanSelected = (strcasecmp($selectedPlan, $p['name'] ?? '') === 0);
                        ?>
                            <label class="plan-select-card <?= $isPlanSelected ? 'selected' : '' ?>" style="border: 2px solid <?= $isPlanSelected ? 'var(--primary-600)' : 'var(--border-subtle)' ?>; background: <?= $isPlanSelected ? '#f5f3ff' : '#ffffff' ?>; border-radius: 12px; padding: 12px 10px; cursor: pointer; text-align: center; display: block; position: relative; transition: all var(--transition-fast);">
                                <input type="radio" name="plan" value="<?= e($p['name'] ?? '') ?>" <?= $isPlanSelected ? 'checked' : '' ?> style="position: absolute; opacity: 0;" onchange="selectPlan(this)">
                                
                                <?php if (!empty($p['highlighted'])): ?>
                                    <span class="badge badge-primary" style="font-size: 8.5px; padding: 1px 5px; position: absolute; top: -7px; left: 50%; transform: translateX(-50%); font-weight: 800; text-transform: uppercase;">Populer</span>
                                <?php endif; ?>

                                <div style="font-weight: 800; font-size: 13.5px; color: var(--text-primary); margin-bottom: 2px; margin-top: 2px;">
                                    <?= e($p['name'] ?? '') ?>
                                </div>
                                <div style="font-weight: 800; font-size: 12.5px; color: var(--primary-600); margin-bottom: 2px;">
                                    <?= e($p['price'] ?? '') ?>
                                </div>
                                <?php if (!empty($p['period'])): ?>
                                    <div style="font-size: 9.5px; color: var(--text-muted); margin-bottom: 4px;"><?= e($p['period']) ?></div>
                                <?php endif; ?>
                                <div style="font-size: 10.5px; color: var(--text-secondary); line-height: 1.3;">
                                    <?= e($p['desc'] ?? '') ?>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- ─── Account Credentials ─── -->
                <div class="form-group mb-3">
                    <label class="form-label font-semibold" for="name" style="font-size: 13px; margin-bottom: 6px; color: #334155;">Nama Lengkap / Penanggung Jawab <span class="required">*</span></label>
                    <div class="auth-input-wrapper">
                        <span class="auth-input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </span>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="auth-input <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                               placeholder="Nama lengkap Anda" 
                               value="<?= e(Session::old('name')) ?>"
                               required 
                               autofocus>
                    </div>
                    <?php if (isset($errors['name'])): ?>
                        <div class="form-error mt-1" style="font-size: 12px; color: var(--danger-600);"><?= e($errors['name'][0]) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label font-semibold" for="email" style="font-size: 13px; margin-bottom: 6px; color: #334155;">Alamat Email <span class="required">*</span></label>
                    <div class="auth-input-wrapper">
                        <span class="auth-input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </span>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="auth-input <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                               placeholder="nama@instansi.go.id" 
                               value="<?= e(Session::old('email')) ?>"
                               required>
                    </div>
                    <?php if (isset($errors['email'])): ?>
                        <div class="form-error mt-1" style="font-size: 12px; color: var(--danger-600);"><?= e($errors['email'][0]) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label font-semibold" for="phone" style="font-size: 13px; margin-bottom: 6px; color: #334155;">Nomor WhatsApp Aktif <span class="required">*</span></label>
                    <div class="auth-input-wrapper">
                        <span class="auth-input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </span>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               class="auth-input <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                               placeholder="Contoh: 081234567890" 
                               value="<?= e(Session::old('phone')) ?>"
                               required>
                    </div>
                    <?php if (isset($errors['phone'])): ?>
                        <div class="form-error mt-1" style="font-size: 12px; color: var(--danger-600);"><?= e($errors['phone'][0]) ?></div>
                    <?php endif; ?>
                </div>

                <div class="grid-2 gap-3 mb-4">
                    <div class="form-group">
                        <label class="form-label font-semibold" for="password" style="font-size: 13px; margin-bottom: 6px; color: #334155;">Kata Sandi <span class="required">*</span></label>
                        <div class="auth-input-wrapper">
                            <span class="auth-input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </span>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="auth-input <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                                   placeholder="Min 6 karakter" 
                                   required>
                        </div>
                        <?php if (isset($errors['password'])): ?>
                            <div class="form-error mt-1" style="font-size: 12px; color: var(--danger-600);"><?= e($errors['password'][0]) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label font-semibold" for="password_confirmation" style="font-size: 13px; margin-bottom: 6px; color: #334155;">Ulangi Sandi <span class="required">*</span></label>
                        <div class="auth-input-wrapper">
                            <span class="auth-input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </span>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   class="auth-input" 
                                   placeholder="Konfirmasi sandi" 
                                   required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary auth-submit-btn">
                    <span>Daftar Akun Sekarang</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
            </form>

            <div class="text-center mt-4" style="font-size: 13.5px; color: #475569;">
                Sudah memiliki akun? 
                <a href="<?= url('login') ?>" style="font-weight: 700; color: var(--primary-600); text-decoration: none;">Masuk di sini</a>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="text-center" style="font-size: 12px; color: #94a3b8; padding-top: 24px;">
            &copy; <?= date('Y') ?> ASR FORM. Dilindungi Enkripsi &amp; Hak Cipta.
        </div>
    </div>
</div>

<script>
function selectPlan(radio) {
    document.querySelectorAll('.plan-select-card').forEach(card => {
        card.classList.remove('selected');
        card.style.borderColor = 'var(--border-subtle)';
        card.style.background = '#ffffff';
    });
    const label = radio.closest('.plan-select-card');
    if (label) {
        label.classList.add('selected');
        label.style.borderColor = 'var(--primary-600)';
        label.style.background = '#f5f3ff';
    }
}
</script>
