<?php use App\Core\CSRF; use App\Core\Session; ?>

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
                        <div class="auth-widget-title">QR Code Verified</div>
                        <div class="auth-widget-desc">Keabsahan Dokumen 100% Sah</div>
                    </div>
                </div>

                <!-- Vector SVG Illustration: Workspace, Smart Form & Official Document -->
                <svg class="auth-svg-illustration" viewBox="0 0 520 340" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="illDeskGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.2"/>
                            <stop offset="100%" stop-color="#1e1b4b" stop-opacity="0.8"/>
                        </linearGradient>
                        <linearGradient id="illDocGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#ffffff"/>
                            <stop offset="100%" stop-color="#e0e7ff"/>
                        </linearGradient>
                        <linearGradient id="illAccentGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#6366f1"/>
                            <stop offset="100%" stop-color="#06b6d4"/>
                        </linearGradient>
                        <filter id="illGlow" x="-20%" y="-20%" width="140%" height="140%">
                            <feGaussianBlur stdDeviation="10" result="blur"/>
                            <feComposite in="SourceGraphic" in2="blur" operator="over"/>
                        </filter>
                    </defs>

                    <!-- Ambient Glow Circles -->
                    <circle cx="260" cy="170" r="140" fill="#6366f1" fill-opacity="0.18" filter="url(#illGlow)"/>
                    <circle cx="160" cy="120" r="80" fill="#06b6d4" fill-opacity="0.15" filter="url(#illGlow)"/>

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
                    <path d="M 270 145 C 310 130, 310 110, 340 105" stroke="url(#illAccentGrad)" stroke-width="3" stroke-dasharray="6 6" stroke-linecap="round"/>

                    <!-- Layer 2: Foreground Official Generated Document -->
                    <g transform="translate(260, 50)">
                        <rect x="0" y="0" width="180" height="235" rx="12" fill="url(#illDocGrad)" stroke="#ffffff" stroke-width="2" filter="drop-shadow(0 15px 25px rgba(0,0,0,0.3))"/>
                        
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
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <div>
                        <div class="auth-widget-title">Otomasi Word &amp; PDF</div>
                        <div class="auth-widget-desc">Penomoran Romawi Instan</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Storyboard Text & Trust Pills -->
        <div class="auth-story-content">
            <h2 class="auth-story-title">Otomasi Formulir &amp; Penerbitan Dokumen Resmi</h2>
            <p class="auth-story-desc">Kelola ribuan formulir digital, terima respons seketika, dan terbitkan surat berpenomoran otomatis lengkap dengan validasi QR Code publik.</p>
            
            <div class="auth-trust-badges">
                <span class="auth-trust-pill">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    256-Bit SSL Enkripsi
                </span>
                <span class="auth-trust-pill">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Audit Trail Lengkap
                </span>
            </div>
        </div>
    </div>

    <!-- ─── Right Canvas: Focused Login Form ─── -->
    <div class="auth-form-side">
        <!-- Top Navigation -->
        <div class="auth-form-top">
            <a href="<?= url() ?>" class="auth-back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Kembali ke Beranda
            </a>
            <span class="badge badge-success" style="font-size: 11px; padding: 3px 8px; font-weight: 700;">
                🟢 Sistem Online
            </span>
        </div>

        <!-- Main Form Content -->
        <div class="auth-form-content">
            <div class="auth-form-header">
                <a href="<?= url() ?>" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; margin-bottom: 12px;">
                    <img src="<?= asset('img/logo-icon.svg') ?>" alt="ASR FORM" width="40" height="40" style="border-radius: 10px; box-shadow: 0 2px 8px rgba(79,70,229,0.25);">
                    <span style="font-size: 20px; font-weight: 900; color: #0f172a; letter-spacing: -0.4px;">ASR FORM</span>
                </a>
                <h1 class="auth-form-title">Masuk ke Portal</h1>
                <p class="auth-form-subtitle">Silakan masukkan email dan kata sandi akun Anda untuk mengakses dashboard.</p>
            </div>

            <?php if (Session::hasFlash('error')): ?>
                <div class="alert alert-error mb-4" style="border-radius: 12px; font-size: 13.5px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;">
                        <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                    <span><?= e(Session::getFlash('error')) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= url('login') ?>" autocomplete="off" id="form-login">
                <?= CSRF::field() ?>

                <div class="form-group mb-3">
                    <label class="form-label font-semibold" for="email" style="font-size: 13px; margin-bottom: 6px; color: #334155;">Alamat Email</label>
                    <div class="auth-input-wrapper">
                        <span class="auth-input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </span>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="auth-input" 
                               placeholder="nama@instansi.go.id" 
                               value="<?= e(Session::old('email')) ?>"
                               required 
                               autofocus>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <div class="flex items-center justify-between mb-1">
                        <label class="form-label font-semibold" for="password" style="font-size: 13px; margin: 0; color: #334155;">Kata Sandi</label>
                        <a href="<?= url('contact') ?>" style="font-size: 12px; color: var(--primary-600); font-weight: 600; text-decoration: none;">Bantuan Login?</a>
                    </div>
                    <div class="auth-input-wrapper">
                        <span class="auth-input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="auth-input" 
                               placeholder="••••••••••••" 
                               required>
                        <button type="button" class="auth-toggle-pass" id="btn-toggle-pass" aria-label="Toggle password visibility" onclick="togglePasswordVisibility()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="icon-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary auth-submit-btn">
                    <span>Masuk ke Dashboard</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
            </form>

            <div class="text-center mt-4" style="font-size: 13.5px; color: #475569;">
                Belum memiliki akun instansi? 
                <a href="<?= url('register') ?>" style="font-weight: 700; color: var(--primary-600); text-decoration: none;">Daftar Akun Baru</a>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="text-center" style="font-size: 12px; color: #94a3b8; padding-top: 24px;">
            &copy; <?= date('Y') ?> ASR FORM. Dilindungi Enkripsi &amp; Hak Cipta.
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility() {
    const passInput = document.getElementById('password');
    const eyeIcon = document.getElementById('icon-eye');
    if (!passInput) return;

    if (passInput.type === 'password') {
        passInput.type = 'text';
        if (eyeIcon) {
            eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
        }
    } else {
        passInput.type = 'password';
        if (eyeIcon) {
            eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
        }
    }
}
</script>
