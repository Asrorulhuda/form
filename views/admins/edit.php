<?php 
use App\Core\CSRF;
use App\Core\Session;
$errors = Session::getFlash('errors') ?? [];
?>

<div style="max-width: 720px;">
    <div class="card fade-in">
        <div class="card-header flex items-center justify-between">
            <div>
                <h3 class="card-title">Edit Akun Admin (Tenant Owner)</h3>
                <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">
                    Kelola data tenant, paket langganan, dan konfigurasi gateway WhatsApp milik admin ini.
                </p>
            </div>
            <span class="badge <?= $admin->status === 'active' ? 'badge-success' : ($admin->status === 'pending' ? 'badge-warning' : 'badge-danger') ?>">
                <?= ucfirst($admin->status) ?>
            </span>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= url("admins/{$admin->id}/update") ?>">
                <?= CSRF::field() ?>

                <div class="form-group mb-3">
                    <label class="form-label" for="name">Nama Admin / Organisasi <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                           placeholder="Contoh: PT Sukses Mandiri / John Doe"
                           value="<?= e(Session::old('name', $admin->name)) ?>" required>
                    <?php if (isset($errors['name'])): ?>
                        <div class="form-error"><?= e($errors['name'][0]) ?></div>
                    <?php endif; ?>
                </div>

                <div class="grid-2 mb-3">
                    <div class="form-group">
                        <label class="form-label" for="email">Email Login <span class="required">*</span></label>
                        <input type="email" id="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                               value="<?= e(Session::old('email', $admin->email)) ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="form-error"><?= e($errors['email'][0]) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">No. WhatsApp</label>
                        <input type="text" id="phone" name="phone" class="form-control"
                               placeholder="Contoh: 081234567890"
                               value="<?= e(Session::old('phone', $admin->phone ?? '')) ?>">
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label" for="password">Password Baru</label>
                    <input type="password" id="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                           placeholder="Kosongkan jika tidak ingin mengubah password">
                    <small style="color: var(--text-muted); font-size: 11px;">Biarkan kosong jika tetap menggunakan password saat ini.</small>
                    <?php if (isset($errors['password'])): ?>
                        <div class="form-error"><?= e($errors['password'][0]) ?></div>
                    <?php endif; ?>
                </div>

                <div class="grid-2 mb-4">
                    <div class="form-group">
                        <label class="form-label" for="plan">Paket Layanan <span class="required">*</span></label>
                        <select id="plan" name="plan" class="form-control" required>
                            <option value="Gratis" <?= Session::old('plan', $admin->plan) === 'Gratis' ? 'selected' : '' ?>>Gratis</option>
                            <option value="Pro" <?= Session::old('plan', $admin->plan) === 'Pro' ? 'selected' : '' ?>>Pro</option>
                            <option value="Enterprise" <?= Session::old('plan', $admin->plan) === 'Enterprise' ? 'selected' : '' ?>>Enterprise</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="status">Status Akun</label>
                        <select id="status" name="status" class="form-control">
                            <option value="active" <?= Session::old('status', $admin->status) === 'active' ? 'selected' : '' ?>>Aktif</option>
                            <option value="pending" <?= Session::old('status', $admin->status) === 'pending' ? 'selected' : '' ?>>Pending Approval</option>
                            <option value="inactive" <?= Session::old('status', $admin->status) === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
                            <option value="rejected" <?= Session::old('status', $admin->status) === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
                        </select>
                    </div>
                </div>

                <!-- WhatsApp Tenant Config -->
                <div class="card mb-4" style="background: var(--bg-subtle); border: 1px dashed var(--border-subtle);">
                    <div class="card-body">
                        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                            <span>📱</span> Konfigurasi Pengirim WhatsApp Tenant
                        </h4>
                        <p style="font-size: 12px; color: var(--text-secondary); margin-bottom: 14px;">
                            Endpoint API Gateway WA tetap terpusat diatur oleh Super Admin, namun Admin ini dapat memiliki Nomor Sender dan API Key tersendiri untuk notifikasi miliknya.
                        </p>

                        <div class="grid-2">
                            <div class="form-group">
                                <label class="form-label" for="wa_sender">Nomor WA Pengirim (Sender)</label>
                                <input type="text" id="wa_sender" name="wa_sender" class="form-control"
                                       placeholder="Contoh: 62888xxxx atau 0888xxxx"
                                       value="<?= e(Session::old('wa_sender', $admin->wa_sender ?? '')) ?>">
                                <small style="color: var(--text-muted); font-size: 11px;">Nomor perangkat WA tenant yang terhubung.</small>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="wa_api_key">API Key WhatsApp Tenant</label>
                                <input type="text" id="wa_api_key" name="wa_api_key" class="form-control"
                                       placeholder="API Key khusus tenant ini"
                                       value="<?= e(Session::old('wa_api_key', $admin->wa_api_key ?? '')) ?>">
                                <small style="color: var(--text-muted); font-size: 11px;">Jika dikosongkan, akan fallback ke gateway default Super Admin.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Perbarui Admin
                    </button>
                    <a href="<?= url('admins') ?>" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
