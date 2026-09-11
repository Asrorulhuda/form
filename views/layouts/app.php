<?php
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Session;
use App\Core\View;

$user = Auth::user();
$currentUrl = trim($_GET['url'] ?? '', '/');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Dashboard') ?> — ASR FORM</title>
    <meta name="description" content="ASR FORM - Platform Form Builder & Document Generator">
    <?= CSRF::meta() ?>
    <link rel="icon" type="image/svg+xml" href="<?= asset('img/logo-icon.svg') ?>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
    <?php if (Auth::isImpersonating()): ?>
        <div class="impersonation-bar" style="background: linear-gradient(135deg, #b45309, #d97706); color: #ffffff; padding: 10px 24px; display: flex; align-items: center; justify-content: space-between; font-size: 13.5px; position: sticky; top: 0; z-index: 10000; box-shadow: 0 4px 12px rgba(180, 83, 9, 0.3);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 18px;">🛡️</span>
                <span>
                    Anda sedang login sebagai Admin: <strong><?= e(Auth::name()) ?></strong> 
                    <span style="opacity: 0.85; font-size: 12px; margin-left: 4px;">(Akun Asli: <?= e(Auth::impersonatorName()) ?>)</span>
                </span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <a href="<?= url('admins/leave-impersonate') ?>" class="btn btn-sm" style="background: #ffffff; color: #b45309; font-weight: 800; border: none; border-radius: 6px; padding: 6px 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 14 4 9 9 4"/><path d="M20 20v-7a4 4 0 0 0-4-4H4"/></svg>
                    Kembali ke Super Admin
                </a>
            </div>
        </div>
    <?php endif; ?>
    <div class="app-layout">
        <!-- Sidebar -->
        <?php View::component('sidebar', ['currentUrl' => $currentUrl, 'user' => $user]); ?>
        
        <!-- Sidebar Overlay (Mobile) -->
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- Main Wrapper -->
        <div class="main-wrapper">
            <!-- Topbar -->
            <?php View::component('topbar', ['pageTitle' => $pageTitle ?? '', 'user' => $user]); ?>

            <!-- Main Content -->
            <main class="main-content fade-in">
                <?= $content ?? '' ?>
            </main>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div class="modal-backdrop" id="confirm-modal-backdrop"></div>
    <div class="modal" id="confirm-modal">
        <div class="modal-header">
            <h3 class="modal-title">Konfirmasi</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <p class="confirm-message" style="color: var(--text-secondary); font-size: 14px;">Apakah Anda yakin?</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('confirm-modal')">Batal</button>
            <button class="btn btn-danger confirm-action">Hapus</button>
        </div>
    </div>

    <!-- Flash Toast -->
    <?php if (Session::hasFlash('toast_type')): ?>
        <div id="flash-toast" 
             data-type="<?= e(Session::getFlash('toast_type')) ?>" 
             data-message="<?= e(Session::getFlash('toast_message')) ?>">
        </div>
    <?php endif; ?>
    <?php if (Session::hasFlash('error')): ?>
        <div id="flash-toast" data-type="error" data-message="<?= e(Session::getFlash('error')) ?>"></div>
    <?php endif; ?>

    <script src="<?= asset('js/app.js') ?>"></script>
    <?php if (isset($scripts)): ?>
        <?= $scripts ?>
    <?php endif; ?>
</body>
</html>
