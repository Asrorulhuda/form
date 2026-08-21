<?php 
use App\Core\CSRF;
use App\Core\Session;
$errors = Session::getFlash('errors') ?? [];
?>

<div style="max-width: 640px;">
    <div class="card fade-in">
        <div class="card-header">
            <h3 class="card-title">Edit Pengguna: <?= e($user->name) ?></h3>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= url("users/{$user->id}/update") ?>">
                <?= CSRF::field() ?>

                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                           value="<?= e(Session::old('name', $user->name)) ?>" required>
                    <?php if (isset($errors['name'])): ?>
                        <div class="form-error"><?= e($errors['name'][0]) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                           value="<?= e(Session::old('email', $user->email)) ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <div class="form-error"><?= e($errors['email'][0]) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                    <div class="form-help">Biarkan kosong jika tidak ingin mengubah password.</div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="form-error"><?= e($errors['password'][0]) ?></div>
                    <?php endif; ?>
                </div>

                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label" for="role_id">Role <span class="required">*</span></label>
                        <select id="role_id" name="role_id" class="form-control" required>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role->id ?>" <?= $user->role_id == $role->id ? 'selected' : '' ?>>
                                    <?= e($role->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="plan">Paket Layanan <span class="required">*</span></label>
                        <select id="plan" name="plan" class="form-control" required>
                            <?php if (!empty($plans)): ?>
                                <?php foreach ($plans as $p): ?>
                                    <option value="<?= e($p['name'] ?? '') ?>" <?= ($user->plan ?? 'Gratis') === ($p['name'] ?? '') ? 'selected' : '' ?>>
                                        <?= e($p['name'] ?? '') ?> (<?= e($p['price'] ?? '') ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="Gratis" <?= ($user->plan ?? '') === 'Gratis' ? 'selected' : '' ?>>Gratis</option>
                                <option value="Pro" <?= ($user->plan ?? '') === 'Pro' ? 'selected' : '' ?>>Pro</option>
                                <option value="Enterprise" <?= ($user->plan ?? '') === 'Enterprise' ? 'selected' : '' ?>>Enterprise</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="active" <?= $user->status === 'active' ? 'selected' : '' ?>>Aktif</option>
                            <option value="inactive" <?= $user->status === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Perbarui
                    </button>
                    <a href="<?= url('users') ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
