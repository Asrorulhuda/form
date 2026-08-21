<?php 
use App\Core\CSRF; 
use App\Core\Session; 
$errors = Session::getFlash('errors') ?? [];
$plans = $plans ?? [];
$selectedPlan = $selectedPlan ?? 'Gratis';
?>

<div class="login-page" style="padding: 40px 16px;">
    <div class="login-card" style="max-width: 540px;">
        <div class="login-header">
            <div class="login-logo">A</div>
            <h1 class="login-title">Daftar Akun Baru</h1>
            <p class="login-subtitle">Pilih paket dan mulai buat formulir digital Anda</p>
        </div>

        <?php if (Session::hasFlash('error')): ?>
            <div class="login-error">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                <?= e(Session::getFlash('error')) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('register') ?>" autocomplete="off" id="register-form">
            <?= CSRF::field() ?>

            <!-- ─── Plan Selection ─── -->
            <div class="form-group mb-4">
                <label class="form-label" style="font-weight: 700; margin-bottom: 8px;">Pilih Paket Layanan <span class="required">*</span></label>
                
                <div class="plan-selector-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 10px;">
                    <?php foreach ($plans as $p): 
                        $isPlanSelected = (strcasecmp($selectedPlan, $p['name'] ?? '') === 0);
                    ?>
                        <label class="plan-select-card <?= $isPlanSelected ? 'selected' : '' ?>" style="border: 2px solid <?= $isPlanSelected ? 'var(--primary-600)' : 'var(--border-subtle)' ?>; background: <?= $isPlanSelected ? '#f5f3ff' : '#ffffff' ?>; border-radius: var(--radius-lg); padding: 14px 10px; cursor: pointer; text-align: center; display: block; position: relative; transition: all var(--transition-fast);">
                            <input type="radio" name="plan" value="<?= e($p['name'] ?? '') ?>" <?= $isPlanSelected ? 'checked' : '' ?> style="position: absolute; opacity: 0;" onchange="selectPlan(this)">
                            
                            <?php if (!empty($p['highlighted'])): ?>
                                <span class="badge badge-primary" style="font-size: 9px; padding: 2px 6px; position: absolute; top: -8px; left: 50%; transform: translateX(-50%); font-weight: 800; text-transform: uppercase;">Populer</span>
                            <?php endif; ?>

                            <div style="font-weight: 800; font-size: 14px; color: var(--text-primary); margin-bottom: 2px; margin-top: 2px;">
                                <?= e($p['name'] ?? '') ?>
                            </div>
                            <div style="font-weight: 800; font-size: 13px; color: var(--primary-600); margin-bottom: 4px;">
                                <?= e($p['price'] ?? '') ?>
                            </div>
                            <?php if (!empty($p['period'])): ?>
                                <div style="font-size: 10px; color: var(--text-muted); margin-bottom: 4px;"><?= e($p['period']) ?></div>
                            <?php endif; ?>
                            <div style="font-size: 11px; color: var(--text-secondary); line-height: 1.3;">
                                <?= e($p['desc'] ?? '') ?>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ─── Account Credentials ─── -->
            <div class="form-group">
                <label class="form-label" for="name">Nama Lengkap <span class="required">*</span></label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                       placeholder="Masukkan nama lengkap Anda" 
                       value="<?= e(Session::old('name')) ?>"
                       required 
                       autofocus>
                <?php if (isset($errors['name'])): ?>
                    <div class="form-error"><?= e($errors['name'][0]) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Alamat Email <span class="required">*</span></label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                       placeholder="nama@email.com" 
                       value="<?= e(Session::old('email')) ?>"
                       required>
                <?php if (isset($errors['email'])): ?>
                    <div class="form-error"><?= e($errors['email'][0]) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="phone">Nomor WhatsApp Aktif <span class="required">*</span></label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-size: 14px; color: var(--text-muted); font-weight: 600;">📱</span>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                           placeholder="Contoh: 081234567890" 
                           value="<?= e(Session::old('phone')) ?>"
                           style="padding-left: 38px;"
                           required>
                </div>
                <small style="color: var(--text-muted); font-size: 11.5px; margin-top: 4px; display: block;">Digunakan untuk konfirmasi aktivasi akun dan notifikasi sistem.</small>
                <?php if (isset($errors['phone'])): ?>
                    <div class="form-error"><?= e($errors['phone'][0]) ?></div>
                <?php endif; ?>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label" for="password">Password <span class="required">*</span></label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                           placeholder="Minimal 6 karakter" 
                           required>
                    <?php if (isset($errors['password'])): ?>
                        <div class="form-error"><?= e($errors['password'][0]) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Ulangi Password <span class="required">*</span></label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           class="form-control" 
                           placeholder="Konfirmasi password" 
                           required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary login-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>
                </svg>
                Daftar Akun Sekarang
            </button>

            <div class="text-center mt-4" style="font-size: 13px; color: var(--text-secondary);">
                Sudah memiliki akun? 
                <a href="<?= url('login') ?>" style="font-weight: 700; color: var(--primary-600);">Masuk di sini</a>
            </div>
        </form>
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
