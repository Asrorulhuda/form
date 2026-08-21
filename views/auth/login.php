<?php use App\Core\CSRF; use App\Core\Session; ?>

<div class="login-page">
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">A</div>
            <h1 class="login-title">ASR FORM</h1>
            <p class="login-subtitle">Masuk ke akun Anda</p>
        </div>

        <?php if (Session::hasFlash('error')): ?>
            <div class="login-error">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                <?= e(Session::getFlash('error')) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('login') ?>" autocomplete="off">
            <?= CSRF::field() ?>

            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-control" 
                       placeholder="admin@asrform.app" 
                       value="<?= e(Session::old('email')) ?>"
                       required 
                       autofocus>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       class="form-control" 
                       placeholder="••••••••" 
                       required>
            </div>

            <button type="submit" class="btn btn-primary login-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                    <polyline points="10 17 15 12 10 7"/>
                    <line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
                Masuk
            </button>

            <div class="text-center mt-4" style="font-size: 13px; color: var(--text-secondary);">
                Belum memiliki akun? 
                <a href="<?= url('register') ?>" style="font-weight: 700; color: var(--primary-600);">Daftar sekarang</a>
            </div>

            <div class="text-center mt-3" style="font-size: 12px;">
                <a href="<?= url() ?>" style="color: var(--text-muted);">&larr; Kembali ke Halaman Utama</a>
            </div>
        </form>
    </div>
</div>
