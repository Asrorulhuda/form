<?php
use App\Core\CSRF;
use App\Core\Session;
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
                        <div class="auth-widget-title">Pembayaran Aman</div>
                        <div class="auth-widget-desc">Verifikasi Transaksi Resmi 100%</div>
                    </div>
                </div>

                <!-- Vector SVG Illustration: Workspace, Smart Form & Official Document -->
                <svg class="auth-svg-illustration" viewBox="0 0 520 340" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="payDeskGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.2"/>
                            <stop offset="100%" stop-color="#1e1b4b" stop-opacity="0.8"/>
                        </linearGradient>
                        <linearGradient id="payDocGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#ffffff"/>
                            <stop offset="100%" stop-color="#e0e7ff"/>
                        </linearGradient>
                        <linearGradient id="payAccentGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#6366f1"/>
                            <stop offset="100%" stop-color="#06b6d4"/>
                        </linearGradient>
                        <filter id="payGlow" x="-20%" y="-20%" width="140%" height="140%">
                            <feGaussianBlur stdDeviation="10" result="blur"/>
                            <feComposite in="SourceGraphic" in2="blur" operator="over"/>
                        </filter>
                    </defs>

                    <!-- Ambient Glow Circles -->
                    <circle cx="260" cy="170" r="140" fill="#6366f1" fill-opacity="0.18" filter="url(#payGlow)"/>
                    <circle cx="160" cy="120" r="80" fill="#06b6d4" fill-opacity="0.15" filter="url(#payGlow)"/>

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
                    <path d="M 270 145 C 310 130, 310 110, 340 105" stroke="url(#payAccentGrad)" stroke-width="3" stroke-dasharray="6 6" stroke-linecap="round"/>

                    <!-- Layer 2: Foreground Official Generated Document -->
                    <g transform="translate(260, 50)">
                        <rect x="0" y="0" width="180" height="235" rx="12" fill="url(#payDocGrad)" stroke="#ffffff" stroke-width="2" filter="drop-shadow(0 15px 25px rgba(0,0,0,0.3))"/>
                        
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

                        <!-- Official Gold Seal & QR Badge -->
                        <circle cx="50" cy="180" r="18" fill="#f59e0b" fill-opacity="0.2" stroke="#f59e0b" stroke-width="2"/>
                        <circle cx="50" cy="180" r="13" fill="#fbbf24"/>
                        <polygon points="50,172 53,178 59,179 55,183 56,189 50,186 44,189 45,183 41,179 47,178" fill="#ffffff"/>

                        <!-- QR Code simulation inside document -->
                        <rect x="110" y="160" width="45" height="45" rx="4" fill="#ffffff" stroke="#0f172a" stroke-width="1.5"/>
                        <rect x="115" y="165" width="14" height="14" fill="#0f172a"/>
                        <rect x="117" y="167" width="10" height="10" fill="#ffffff"/>
                        <rect x="119" y="169" width="6" height="6" fill="#0f172a"/>

                        <rect x="136" y="165" width="14" height="14" fill="#0f172a"/>
                        <rect x="138" y="167" width="10" height="10" fill="#ffffff"/>
                        <rect x="140" y="169" width="6" height="6" fill="#0f172a"/>

                        <rect x="115" y="186" width="14" height="14" fill="#0f172a"/>
                        <rect x="117" y="188" width="10" height="10" fill="#ffffff"/>
                        <rect x="119" y="190" width="6" height="6" fill="#0f172a"/>

                        <rect x="136" y="186" width="6" height="6" fill="#0f172a"/>
                        <rect x="144" y="194" width="6" height="6" fill="#0f172a"/>
                    </g>
                </svg>

                <!-- Floating Widget 2 (Bottom Left) -->
                <div class="auth-floating-widget widget-bottom-left">
                    <div class="auth-widget-icon" style="background: rgba(99, 102, 241, 0.25); color: #818cf8;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    </div>
                    <div>
                        <div class="auth-widget-title">Aktivasi Instan</div>
                        <div class="auth-widget-desc">Akun langsung siap digunakan</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Trust Card in Canvas -->
        <div class="auth-side-trust">
            <div class="auth-trust-item">
                <span class="auth-trust-check">✓</span>
                <span>Enkripsi 256-bit SSL</span>
            </div>
            <div class="auth-trust-item">
                <span class="auth-trust-check">✓</span>
                <span>Verifikasi Admin Cepat</span>
            </div>
            <div class="auth-trust-item">
                <span class="auth-trust-check">✓</span>
                <span>Bantuan Teknis WhatsApp</span>
            </div>
        </div>
    </div>

    <!-- ─── Right Canvas: Focused Payment & Confirmation Form ─── -->
    <div class="auth-form-side">
        <!-- Top Navigation -->
        <div class="auth-form-top">
            <a href="<?= url('login') ?>" class="auth-back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Kembali ke Login
            </a>
            <span class="badge badge-success" style="font-size: 11px; padding: 3px 8px; font-weight: 700;">
                🟢 Sistem Online
            </span>
        </div>

        <!-- Main Form Content -->
        <div class="auth-form-content" style="max-width: 480px;">
            <div class="auth-form-header" style="margin-bottom: 20px;">
                <a href="<?= url() ?>" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; margin-bottom: 10px;">
                    <img src="<?= asset('img/logo-icon.svg') ?>" alt="ASR FORM" width="38" height="38" style="border-radius: 10px; box-shadow: 0 2px 8px rgba(79,70,229,0.25);">
                    <span style="font-size: 20px; font-weight: 900; color: #0f172a; letter-spacing: -0.4px;">ASR FORM</span>
                </a>
                <h1 class="auth-form-title" style="font-size: 22px;">Konfirmasi Pembayaran</h1>
                <p class="auth-form-subtitle" style="font-size: 13px;">Selesaikan pembayaran dan unggah bukti transfer untuk aktivasi akun Anda.</p>
            </div>

            <?php if (Session::hasFlash('error')): ?>
                <div class="alert alert-error mb-3" style="border-radius: 12px; font-size: 13px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;">
                        <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                    <span><?= e(Session::getFlash('error')) ?></span>
                </div>
            <?php endif; ?>

            <!-- Compact Order Summary Box -->
            <div style="background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%); border: 1px solid #ddd6fe; border-radius: 14px; padding: 14px 18px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-size: 11px; font-weight: 700; color: #4338ca; text-transform: uppercase;">Paket Langganan</div>
                    <div style="font-size: 16px; font-weight: 900; color: #1e1b4b;">Paket <?= e($user->plan ?? 'Pro') ?></div>
                    <div style="font-size: 11.5px; color: #64748b;">a.n. <?= e($user->name) ?></div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 11px; color: #64748b;">Total Tagihan</div>
                    <div style="font-size: 20px; font-weight: 900; color: #4f46e5;"><?= e($planInfo['price'] ?? 'Sesuai Tagihan') ?></div>
                </div>
            </div>

            <!-- Payment Method Buttons -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 16px;">
                <?php if ($qrisEnabled): ?>
                    <button type="button" id="btn-tab-qris" onclick="selectPayMethod('qris')" style="padding: 10px; border: 2px solid #4f46e5; background: #f5f3ff; border-radius: 10px; font-weight: 800; font-size: 12.5px; color: #4338ca; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
                        📱 QRIS Instant
                    </button>
                <?php endif; ?>
                <?php if ($tfEnabled): ?>
                    <button type="button" id="btn-tab-transfer" onclick="selectPayMethod('transfer')" style="padding: 10px; border: 2px solid <?= !$qrisEnabled ? '#4f46e5' : 'var(--border-subtle)' ?>; background: <?= !$qrisEnabled ? '#f5f3ff' : '#ffffff' ?>; border-radius: 10px; font-weight: 800; font-size: 12.5px; color: <?= !$qrisEnabled ? '#4338ca' : 'var(--text-secondary)' ?>; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
                        🏦 Transfer Bank
                    </button>
                <?php endif; ?>
            </div>

            <!-- QRIS Display Section -->
            <?php if ($qrisEnabled): ?>
                <div id="panel-qris" style="text-align: center; padding: 14px; background: #ffffff; border: 1px solid var(--border-subtle); border-radius: 14px; margin-bottom: 20px;">
                    <div style="font-size: 11px; font-weight: 700; color: #059669; margin-bottom: 6px;">NMID Merchant: <?= e($qrisMerchant) ?></div>
                    <div style="display: inline-block; padding: 10px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 8px;">
                        <?php if (!empty($qrisImage)): ?>
                            <img src="<?= asset($qrisImage) ?>" alt="QRIS Code" style="max-width: 180px; width: 100%; height: auto; border-radius: 6px; display: block; margin: 0 auto;" onerror="this.style.display='none'; document.getElementById('checkout-qris-fallback').style.display='flex';">
                        <?php endif; ?>
                        <div id="checkout-qris-fallback" style="width: 160px; height: 160px; background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 8px; display: <?= empty($qrisImage) ? 'flex' : 'none' ?>; flex-direction: column; align-items: center; justify-content: center; color: var(--text-muted); font-size: 11px; padding: 12px;">
                            <span style="font-size: 28px; margin-bottom: 4px;">📱</span>
                            <strong>QRIS Merchant</strong>
                            <span><?= e($qrisMerchant) ?></span>
                        </div>
                    </div>
                    <div style="font-size: 11.5px; color: #64748b;">Scan QRIS via BCA, Mandiri, BRI, GoPay, OVO, DANA, dll.</div>
                </div>
            <?php endif; ?>

            <!-- Transfer Bank Display Section -->
            <?php if ($tfEnabled): ?>
                <div id="panel-transfer" style="padding: 14px; background: #ffffff; border: 1px solid var(--border-subtle); border-radius: 14px; margin-bottom: 20px; <?= $qrisEnabled ? 'display: none;' : '' ?>">
                    <div style="font-size: 11.5px; color: #64748b; margin-bottom: 8px;">Transfer ke rekening resmi berikut:</div>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <?php foreach ($bankAccounts as $acc): ?>
                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between;">
                                <div>
                                    <div style="font-weight: 800; font-size: 12.5px; color: #4f46e5;"><?= e($acc['bank'] ?? 'Bank') ?></div>
                                    <div style="font-family: monospace; font-size: 15px; font-weight: 800; color: #0f172a;"><?= e($acc['number'] ?? '') ?></div>
                                    <div style="font-size: 11px; color: #64748b;">a.n. <?= e($acc['holder'] ?? '') ?></div>
                                </div>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="copyAccount('<?= e($acc['number'] ?? '') ?>', this)" style="font-size: 11px; font-weight: 700; padding: 4px 10px;">
                                    Salin
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Simple Upload Form -->
            <form method="POST" action="<?= url('payment/submit') ?>" enctype="multipart/form-data" id="proof-form">
                <?= CSRF::field() ?>
                <input type="hidden" name="user_id" value="<?= $user->id ?>">
                <input type="hidden" name="payment_method" id="selected-method-input" value="<?= $qrisEnabled ? 'qris' : 'transfer' ?>">

                <div class="form-group mb-3">
                    <label class="form-label font-semibold" for="sender_name" style="font-size: 13px; margin-bottom: 6px; color: #334155;">Nama Pengirim / Pemilik Rekening <span class="required">*</span></label>
                    <div class="auth-input-wrapper">
                        <span class="auth-input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </span>
                        <input type="text" id="sender_name" name="sender_name" class="auth-input" placeholder="Nama pemilik rekening" value="<?= e($user->name) ?>" required>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label font-semibold" for="sender_phone" style="font-size: 13px; margin-bottom: 6px; color: #334155;">Nomor WhatsApp Pengirim <span class="required">*</span></label>
                    <div class="auth-input-wrapper">
                        <span class="auth-input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </span>
                        <input type="tel" id="sender_phone" name="sender_phone" class="auth-input" placeholder="081234567890" required>
                    </div>
                    <small style="font-size: 11px; color: #64748b; margin-top: 4px; display: block;">Notifikasi aktivasi akun akan dikirim ke WhatsApp ini.</small>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label font-semibold" for="proof_file" style="font-size: 13px; margin-bottom: 6px; color: #334155;">Unggah Bukti Transfer / Struk <span class="required">*</span></label>
                    <div class="auth-input-wrapper" style="padding: 6px 12px; background: #ffffff;">
                        <input type="file" id="proof_file" name="proof_file" accept="image/*,application/pdf" required style="font-size: 12.5px; width: 100%; border: none; outline: none; background: transparent; cursor: pointer;">
                    </div>
                    <small style="font-size: 11px; color: #64748b; margin-top: 4px; display: block;">Format: JPG, PNG, WEBP, atau PDF (Maksimal 5MB).</small>
                </div>

                <div class="form-group mb-4">
                    <label class="form-label font-semibold" for="notes" style="font-size: 13px; margin-bottom: 6px; color: #334155;">Catatan Tambahan (Opsional)</label>
                    <div class="auth-input-wrapper">
                        <span class="auth-input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </span>
                        <input type="text" id="notes" name="notes" class="auth-input" placeholder="Contoh: Transfer via BCA Mobile pk 10:30">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary auth-submit-btn" style="margin-top: 6px;">
                    <span>Kirim Bukti Pembayaran</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
            </form>

            <div class="text-center mt-3" style="font-size: 12px; color: #94a3b8;">
                &copy; <?= date('Y') ?> ASR FORM. Dilindungi Enkripsi &amp; Hak Cipta.
            </div>
        </div>
    </div>
</div>

<script>
function selectPayMethod(method) {
    const btnQris = document.getElementById('btn-tab-qris');
    const btnTf = document.getElementById('btn-tab-transfer');
    const panelQris = document.getElementById('panel-qris');
    const panelTf = document.getElementById('panel-transfer');
    const inputMethod = document.getElementById('selected-method-input');

    if (method === 'qris' && btnQris && panelQris) {
        btnQris.style.borderColor = '#4f46e5';
        btnQris.style.background = '#f5f3ff';
        btnQris.style.color = '#4338ca';
        panelQris.style.display = 'block';

        if (btnTf) {
            btnTf.style.borderColor = 'var(--border-subtle)';
            btnTf.style.background = '#ffffff';
            btnTf.style.color = 'var(--text-secondary)';
        }
        if (panelTf) panelTf.style.display = 'none';
        if (inputMethod) inputMethod.value = 'qris';
    } else if (method === 'transfer' && btnTf && panelTf) {
        btnTf.style.borderColor = '#4f46e5';
        btnTf.style.background = '#f5f3ff';
        btnTf.style.color = '#4338ca';
        panelTf.style.display = 'block';

        if (btnQris) {
            btnQris.style.borderColor = 'var(--border-subtle)';
            btnQris.style.background = '#ffffff';
            btnQris.style.color = 'var(--text-secondary)';
        }
        if (panelQris) panelQris.style.display = 'none';
        if (inputMethod) inputMethod.value = 'transfer';
    }
}

function copyAccount(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const originalText = btn.innerHTML;
        btn.innerHTML = '✅ Disalin!';
        btn.style.background = '#10b981';
        btn.style.color = '#ffffff';
        btn.style.borderColor = '#10b981';
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.style.background = '';
            btn.style.color = '';
            btn.style.borderColor = '';
        }, 2000);
    });
}
</script>
