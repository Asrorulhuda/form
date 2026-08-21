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
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%234f46e5'/><text x='50' y='68' font-size='55' font-weight='bold' fill='white' text-anchor='middle' font-family='Arial'>A</text></svg>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
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
