<?php
use App\Core\CSRF;
use App\Core\Session;

$errors = Session::getFlash('contact_errors') ?? [];
$old = Session::getFlash('contact_old') ?? [];
$success = Session::getFlash('contact_success');
?>

<section class="page-hero">
    <div class="container">
        <h1 class="page-hero-title"><?= e($pageTitle ?? 'Hubungi Kami') ?></h1>
        <?php if (!empty($pageSubtitle)): ?>
            <p class="page-hero-subtitle"><?= e($pageSubtitle) ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Form -->
            <div class="page-content-card">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span><?= e($successMsg ?? 'Pesan berhasil dikirim.') ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <div>
                            <?php foreach ($errors as $err): ?>
                                <div><?= e($err) ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= url('contact') ?>" id="contact-form">
                    <?= CSRF::field() ?>

                    <div class="form-group">
                        <label class="form-label" for="contact-name">Nama <span class="required">*</span></label>
                        <input type="text" id="contact-name" name="name" class="form-control" required minlength="2" maxlength="100" value="<?= e($old['name'] ?? '') ?>" placeholder="Nama lengkap Anda">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact-email">Email <span class="required">*</span></label>
                        <input type="email" id="contact-email" name="email" class="form-control" required maxlength="150" value="<?= e($old['email'] ?? '') ?>" placeholder="email@contoh.com">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact-subject">Subjek <span class="required">*</span></label>
                        <input type="text" id="contact-subject" name="subject" class="form-control" required minlength="3" maxlength="255" value="<?= e($old['subject'] ?? '') ?>" placeholder="Perihal pesan Anda">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact-message">Pesan <span class="required">*</span></label>
                        <textarea id="contact-message" name="message" class="form-control" rows="6" required minlength="10" placeholder="Tulis pesan Anda di sini..."><?= e($old['message'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        Kirim Pesan
                    </button>
                </form>
            </div>

            <!-- Contact Info -->
            <div>
                <?php if (!empty($contactEmail) || !empty($contactPhone ?? '') || !empty($contactAddr ?? '')): ?>
                    <div class="page-content-card contact-info-card">
                        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px;">Informasi Kontak</h3>

                        <?php if (!empty($contactEmail)): ?>
                            <div class="contact-info-item">
                                <div class="contact-info-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                </div>
                                <div>
                                    <div class="contact-info-label">Email</div>
                                    <a href="mailto:<?= e($contactEmail) ?>" style="color: var(--primary-600); font-weight: 600;"><?= e($contactEmail) ?></a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($contactPhone)): ?>
                            <div class="contact-info-item">
                                <div class="contact-info-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                </div>
                                <div>
                                    <div class="contact-info-label">Telepon</div>
                                    <span style="font-weight: 600;"><?= e($contactPhone) ?></span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($contactAddr)): ?>
                            <div class="contact-info-item">
                                <div class="contact-info-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                </div>
                                <div>
                                    <div class="contact-info-label">Alamat</div>
                                    <span style="font-weight: 600;"><?= e($contactAddr) ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<div class="container my-4">
    <?= renderAd('PUBLIC_PAGE') ?>
</div>
