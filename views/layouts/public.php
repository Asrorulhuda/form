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
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%234f46e5'/><text x='50' y='68' font-size='55' font-weight='bold' fill='white' text-anchor='middle' font-family='Arial'>A</text></svg>">
    
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <?= adsenseHead() ?>
</head>
<body class="public-body">

    <!-- ─── Unified Public Navbar ─── -->
    <header class="public-nav">
        <div class="container public-nav-inner">
            <a href="<?= url() ?>" class="brand-link">
                <div class="sidebar-logo">A</div>
                <div>
                    <span class="brand-title"><?= e($siteName) ?></span>
                    <span class="brand-badge">v1.1</span>
                </div>
            </a>

            <button class="public-nav-toggle" id="public-nav-toggle" aria-label="Toggle navigation">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>

            <nav class="public-nav-links" id="public-nav-links">
                <a href="<?= url() ?>" class="nav-link-item <?= ($currentPath === '' || $currentPath === 'home') ? 'active' : '' ?>">Home</a>
                <?php if (!empty($featuresEnabled)): ?>
                    <a href="<?= url('features') ?>" class="nav-link-item <?= $currentPath === 'features' ? 'active' : '' ?>">Fitur</a>
                <?php endif; ?>
                <?php if (!empty($pricingEnabled)): ?>
                    <a href="<?= url('pricing') ?>" class="nav-link-item <?= $currentPath === 'pricing' ? 'active' : '' ?>">Pricing</a>
                <?php endif; ?>
                <?php if (!empty($aboutEnabled)): ?>
                    <a href="<?= url('about') ?>" class="nav-link-item <?= $currentPath === 'about' ? 'active' : '' ?>">About</a>
                <?php endif; ?>
                <?php if (!empty($contactEnabled)): ?>
                    <a href="<?= url('contact') ?>" class="nav-link-item <?= $currentPath === 'contact' ? 'active' : '' ?>">Kontak</a>
                <?php endif; ?>

                <div class="public-nav-auth">
                    <?php if ($isLoggedIn): ?>
                        <a href="<?= url('dashboard') ?>" class="btn btn-primary btn-sm">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                            Ke Dashboard
                        </a>
                    <?php else: ?>
                        <a href="<?= url('login') ?>" class="btn btn-secondary btn-sm">Masuk</a>
                        <a href="<?= url('register') ?>" class="btn btn-primary btn-sm">Daftar Akun</a>
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
                        <div class="sidebar-logo" style="width:30px;height:30px;font-size:14px;">A</div>
                        <strong style="font-size: 17px;"><?= e($siteName) ?></strong>
                    </div>
                    <?php if (!empty($siteDesc)): ?>
                        <p style="color: var(--text-secondary); font-size: 13.5px; line-height: 1.6; margin-bottom: 12px;"><?= e($siteDesc) ?></p>
                    <?php endif; ?>
                </div>

                <div class="public-footer-links">
                    <h4>Produk & Navigasi</h4>
                    <a href="<?= url() ?>">Home</a>
                    <?php if (!empty($featuresEnabled)): ?>
                        <a href="<?= url('features') ?>">Fitur Unggulan</a>
                    <?php endif; ?>
                    <?php if (!empty($pricingEnabled)): ?>
                        <a href="<?= url('pricing') ?>">Paket & Harga</a>
                    <?php endif; ?>
                    <a href="<?= url('login') ?>">Masuk ke Akun</a>
                    <a href="<?= url('register') ?>">Daftar Akun Baru</a>
                </div>

                <div class="public-footer-links">
                    <h4>Informasi</h4>
                    <?php if (!empty($aboutEnabled)): ?>
                        <a href="<?= url('about') ?>">Tentang Kami</a>
                    <?php endif; ?>
                    <?php if (!empty($contactEnabled)): ?>
                        <a href="<?= url('contact') ?>">Hubungi Kami</a>
                    <?php endif; ?>
                </div>

                <div class="public-footer-links">
                    <h4>Legal & Privasi</h4>
                    <?php if (!empty($privacyEnabled)): ?>
                        <a href="<?= url('privacy-policy') ?>">Kebijakan Privasi</a>
                    <?php endif; ?>
                    <?php if (!empty($termsEnabled)): ?>
                        <a href="<?= url('terms') ?>">Syarat & Ketentuan</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="public-footer-bottom">
                <span><?= e($footerText) ?></span>
                <?php if (!empty($contactEmail)): ?>
                    <span style="color: var(--text-muted);">📧 <?= e($contactEmail) ?></span>
                <?php endif; ?>
                <div>
                    <a href="<?= url('login') ?>" style="color: var(--primary-600); font-weight: 700;">Masuk ke Aplikasi &rarr;</a>
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
