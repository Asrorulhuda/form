<?php 
use App\Core\CSRF;
use App\Core\Session;
$errors = Session::getFlash('errors') ?? [];
?>

<div style="max-width: 640px;">
    <div class="card fade-in">
        <div class="card-header flex items-center justify-between">
            <div>
                <h3 class="card-title">Edit Pengguna: <?= e($user->name) ?></h3>
                <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">
                    Perbarui profil atau kredensial login pengguna.
                </p>
            </div>
            <span class="badge <?= $user->status === 'active' ? 'badge-success' : 'badge-danger' ?>">
                <?= ucfirst($user->status) ?>
            </span>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= url("users/{$user->id}/update") ?>">
                <?= CSRF::field() ?>

                <div class="form-group mb-3">
                    <label class="form-label" for="name">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                           value="<?= e(Session::old('name', $user->name)) ?>" required>
                    <?php if (isset($errors['name'])): ?>
                        <div class="form-error"><?= e($errors['name'][0]) ?></div>
                    <?php endif; ?>
                </div>

                <div class="grid-2 mb-3">
                    <div class="form-group">
                        <label class="form-label" for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                               value="<?= e(Session::old('email', $user->email)) ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="form-error"><?= e($errors['email'][0]) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">No. WhatsApp</label>
                        <input type="text" id="phone" name="phone" class="form-control"
                               placeholder="Contoh: 081234567890"
                               value="<?= e(Session::old('phone', $user->phone ?? '')) ?>">
                    </div>
                </div>

                <div class="grid-2 mb-3">
                    <div class="form-group">
                        <label class="form-label" for="password">Password Baru</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                        <small style="color: var(--text-muted); font-size: 11px;">Biarkan kosong jika tetap menggunakan password lama.</small>
                        <?php if (isset($errors['password'])): ?>
                            <div class="form-error"><?= e($errors['password'][0]) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="status">Status Akun</label>
                        <select id="status" name="status" class="form-control">
                            <option value="active" <?= Session::old('status', $user->status) === 'active' ? 'selected' : '' ?>>Aktif</option>
                            <option value="inactive" <?= Session::old('status', $user->status) === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Perbarui Pengguna
                    </button>
                    <a href="<?= url('users') ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
