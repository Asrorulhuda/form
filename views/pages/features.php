<section class="page-hero">
    <div class="container">
        <h1 class="page-hero-title"><?= e($pageTitle ?? 'Fitur & Solusi Terpadu') ?></h1>
        <?php if (!empty($pageSubtitle)): ?>
            <p class="page-hero-subtitle"><?= e($pageSubtitle) ?></p>
        <?php else: ?>
            <p class="page-hero-subtitle">Eksplorasi seluruh kapabilitas form builder, generator dokumen Microsoft Word, dan sistem verifikasi QR Code.</p>
        <?php endif; ?>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <?php if (!empty($featureItems)): ?>
            <div class="bento-feature-grid">
                <?php foreach ($featureItems as $item): ?>
                    <div class="bento-feature-card span-4 fade-in">
                        <?php if (!empty($item['icon'])): ?>
                            <div class="bento-feat-icon"><?= $item['icon'] ?></div>
                        <?php else: ?>
                            <div class="bento-feat-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            </div>
                        <?php endif; ?>
                        <h3 class="bento-feat-title"><?= e($item['title'] ?? '') ?></h3>
                        <p class="bento-feat-desc"><?= e($item['desc'] ?? '') ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center" style="padding: 60px 0; color: var(--text-muted);">
                <p>Informasi fitur sedang dimuat.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="page-section" style="background: #ffffff; border-top: 1px solid #e2e8f0;">
    <div class="container text-center">
        <div style="max-width: 600px; margin: 0 auto;">
            <h2 style="font-size: 28px; font-weight: 800; color: #0f172a; margin-bottom: 12px; letter-spacing: -0.5px;">Siap Mengotomasi Administrasi Anda?</h2>
            <p style="color: var(--text-secondary); margin-bottom: 28px; line-height: 1.6;">Tingkatkan efisiensi kerja tim dan permudah publik dengan formulir digital terverifikasi.</p>
            <div class="flex justify-center gap-3 flex-wrap">
                <a href="<?= url('register') ?>" class="btn btn-primary btn-lg">Daftar Akun Sekarang</a>
                <?php if (!empty($contactEnabled)): ?>
                    <a href="<?= url('contact') ?>" class="btn btn-secondary btn-lg">Hubungi Tim Kami</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<div class="container my-4">
    <?= renderAd('PUBLIC_PAGE') ?>
</div>
