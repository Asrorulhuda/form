<?php
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Session;

$isLoggedIn = $isLoggedIn ?? Auth::check();
$user = $user ?? Auth::user();
$siteName = $siteName ?? 'ASR FORM';
$siteTagline = $siteTagline ?? 'Platform Form Builder & Document Generator';
$siteDesc = $siteDesc ?? 'ASR FORM adalah platform untuk membuat formulir digital dinamis dan menghasilkan dokumen resmi otomatis.';
$footerText = $footerText ?? ('© ' . date('Y') . ' ' . $siteName . '. All rights reserved.');
$contactEmail = $contactEmail ?? '';

// Determine active navigation link
$requestUri = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '', '/');
$basePath = trim(parse_url(url(), PHP_URL_PATH) ?? '', '/');
if ($basePath && str_starts_with($requestUri, $basePath)) {
    $currentPath = trim(substr($requestUri, strlen($basePath)), '/');
} else {
    $currentPath = $requestUri;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? ($siteName . ' — ' . $siteTagline)) ?></title>
    <meta name="description" content="<?= e($siteDesc) ?>">
    <link rel="canonical" href="<?= e(url($currentPath)) ?>">
    <?= CSRF::meta() ?>

    <!-- Open Graph -->
    <meta property="og:title" content="<?= e($title ?? $siteName) ?>">
    <meta property="og:description" content="<?= e($siteDesc) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e(url($currentPath)) ?>">
    <?php if (!empty($ogImage ?? '')): ?>
        <meta property="og:image" content="<?= e($ogImage) ?>">
    <?php endif; ?>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= asset('img/logo-icon.svg') ?>">
    
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <?= adsenseHead() ?>
</head>
<body class="public-body">

    <!-- ─── Unified Public Navbar ─── -->
    <header class="public-nav">
        <div class="container public-nav-inner">
            <a href="<?= url() ?>" class="brand-link" aria-label="<?= e($siteName) ?> Home">
                <img src="<?= asset('img/logo-icon.svg') ?>" alt="ASR FORM Logo" class="brand-logo-img" width="38" height="38" style="display: block; border-radius: 10px;">
                <div class="brand-text-wrap">
                    <div class="flex items-center gap-1">
                        <span class="brand-title" style="font-weight: 800; letter-spacing: -0.3px;"><?= e($siteName) ?></span>
                        <span class="brand-badge" style="background: rgba(79, 70, 229, 0.1); color: var(--primary-700); font-weight: 700;">PRO</span>
                    </div>
                    <span style="font-size: 10.5px; color: var(--text-tertiary); font-weight: 600; line-height: 1; display: block; margin-top: 1px;">Smart Doc & Form Automation</span>
                </div>
            </a>

            <button class="public-nav-toggle" id="public-nav-toggle" aria-label="Toggle navigation">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>

            <nav class="public-nav-links" id="public-nav-links">
                <a href="<?= url() ?>" class="nav-link-item <?= ($currentPath === '' || $currentPath === 'home') ? 'active' : '' ?>">Beranda</a>
                <?php if (!empty($featuresEnabled)): ?>
                    <a href="<?= url('features') ?>" class="nav-link-item <?= $currentPath === 'features' ? 'active' : '' ?>">Fitur & Solusi</a>
                <?php endif; ?>
                <?php if (!empty($pricingEnabled)): ?>
                    <a href="<?= url('pricing') ?>" class="nav-link-item <?= $currentPath === 'pricing' ? 'active' : '' ?>">Paket Layanan</a>
                <?php endif; ?>
                <?php if (!empty($aboutEnabled)): ?>
                    <a href="<?= url('about') ?>" class="nav-link-item <?= $currentPath === 'about' ? 'active' : '' ?>">Tentang</a>
                <?php endif; ?>
                <?php if (!empty($contactEnabled)): ?>
                    <a href="<?= url('contact') ?>" class="nav-link-item <?= $currentPath === 'contact' ? 'active' : '' ?>">Kontak & Bantuan</a>
                <?php endif; ?>

                <div class="public-nav-auth">
                    <?php if ($isLoggedIn): ?>
                        <a href="<?= url('dashboard') ?>" class="btn btn-primary btn-sm" style="box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25);">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                            Panel Dashboard
                        </a>
                    <?php else: ?>
                        <a href="<?= url('login') ?>" class="btn btn-secondary btn-sm" style="font-weight: 600;">Masuk</a>
                        <a href="<?= url('register') ?>" class="btn btn-primary btn-sm" style="box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25); font-weight: 600;">Mulai Sekarang</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <!-- ─── Main Content ─── -->
    <main class="public-main">
        <?= $content ?? '' ?>
    </main>

    <!-- ─── Unified Public Footer ─── -->
    <footer class="public-footer">
        <div class="container">
            <div class="public-footer-grid">
                <div class="public-footer-brand">
                    <div class="flex items-center gap-2 mb-3">
                        <img src="<?= asset('img/logo-icon.svg') ?>" alt="ASR FORM" width="32" height="32" style="border-radius: 8px;">
                        <div>
                            <strong style="font-size: 17px; font-weight: 800; color: #0f172a; letter-spacing: -0.3px;"><?= e($siteName) ?></strong>
                            <div style="font-size: 11px; color: var(--text-tertiary); font-weight: 600;">Sistem Otomasi Formulir & Dokumen</div>
                        </div>
                    </div>
                    <?php if (!empty($siteDesc)): ?>
                        <p style="color: var(--text-secondary); font-size: 13.5px; line-height: 1.65; margin-bottom: 16px;"><?= e($siteDesc) ?></p>
                    <?php endif; ?>
                    <div class="flex items-center gap-2" style="font-size: 12px; color: var(--text-tertiary);">
                        <span style="display:inline-flex; align-items:center; gap:4px; background:#f1f5f9; padding:3px 8px; border-radius:6px; font-weight:600; color:#475569;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg> 256-Bit SSL Enkripsi
                        </span>
                        <span style="display:inline-flex; align-items:center; gap:4px; background:#f1f5f9; padding:3px 8px; border-radius:6px; font-weight:600; color:#475569;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Standar Dokumen Sah
                        </span>
                    </div>
                </div>

                <div class="public-footer-links">
                    <h4>Produk & Layanan</h4>
                    <a href="<?= url() ?>">Beranda Utama</a>
                    <?php if (!empty($featuresEnabled)): ?>
                        <a href="<?= url('features') ?>">Fitur Unggulan</a>
                    <?php endif; ?>
                    <?php if (!empty($pricingEnabled)): ?>
                        <a href="<?= url('pricing') ?>">Paket & Penawaran</a>
                    <?php endif; ?>
                    <a href="<?= url('login') ?>">Masuk ke Akun</a>
                    <a href="<?= url('register') ?>">Pendaftaran Akun Baru</a>
                </div>

                <div class="public-footer-links">
                    <h4>Institusi & Solusi</h4>
                    <a href="<?= url('features#administrasi') ?>">Pemerintahan & Desa</a>
                    <a href="<?= url('features#akademik') ?>">Institusi Pendidikan</a>
                    <a href="<?= url('features#korporat') ?>">Perusahaan & HR</a>
                    <?php if (!empty($aboutEnabled)): ?>
                        <a href="<?= url('about') ?>">Tentang Platform</a>
                    <?php endif; ?>
                    <?php if (!empty($contactEnabled)): ?>
                        <a href="<?= url('contact') ?>">Hubungi Bantuan</a>
                    <?php endif; ?>
                </div>

                <div class="public-footer-links">
                    <h4>Legalitas & Keamanan</h4>
                    <?php if (!empty($privacyEnabled)): ?>
                        <a href="<?= url('privacy-policy') ?>">Kebijakan Privasi</a>
                    <?php endif; ?>
                    <?php if (!empty($termsEnabled)): ?>
                        <a href="<?= url('terms') ?>">Syarat & Ketentuan Layanan</a>
                    <?php endif; ?>
                    <a href="<?= url('contact') ?>">Layanan Dukungan</a>
                </div>
            </div>

            <div class="public-footer-bottom">
                <span><?= e($footerText) ?></span>
                <?php if (!empty($contactEmail)): ?>
                    <span style="color: var(--text-muted); font-size: 13px;">Dukungan Teknis: <a href="mailto:<?= e($contactEmail) ?>" style="color: var(--primary-600); font-weight: 600;"><?= e($contactEmail) ?></a></span>
                <?php endif; ?>
                <div>
                    <a href="<?= url('login') ?>" style="color: var(--primary-600); font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                        Akses Portal Pengguna &rarr;
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script>
    // Mobile nav toggle
    document.getElementById('public-nav-toggle')?.addEventListener('click', function() {
        document.getElementById('public-nav-links')?.classList.toggle('open');
    });
    </script>
</body>
</html>
