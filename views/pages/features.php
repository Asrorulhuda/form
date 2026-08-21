<section class="page-hero">
    <div class="container">
        <h1 class="page-hero-title"><?= e($pageTitle ?? 'Fitur') ?></h1>
        <?php if (!empty($pageSubtitle)): ?>
            <p class="page-hero-subtitle"><?= e($pageSubtitle) ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <?php if (!empty($featureItems)): ?>
            <div class="feature-grid-page">
                <?php foreach ($featureItems as $item): ?>
                    <div class="feature-card-page">
                        <?php if (!empty($item['icon'])): ?>
                            <div class="feature-card-icon"><?= $item['icon'] ?></div>
                        <?php endif; ?>
                        <h3><?= e($item['title'] ?? '') ?></h3>
                        <p><?= e($item['desc'] ?? '') ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center" style="padding: 60px 0; color: var(--text-muted);">
                <p>Belum ada fitur yang ditambahkan.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="page-section" style="background: var(--bg-subtle);">
    <div class="container text-center">
        <h2 style="font-size: 28px; font-weight: 800; margin-bottom: 12px;">Siap untuk memulai?</h2>
        <p style="color: var(--text-secondary); margin-bottom: 24px;">Buat formulir pertama Anda dan mulai kumpulkan data sekarang.</p>
        <div class="flex justify-center gap-3">
            <a href="<?= url('register') ?>" class="btn btn-primary btn-lg">Mulai Gratis</a>
            <?php if (!empty($contactEnabled)): ?>
                <a href="<?= url('contact') ?>" class="btn btn-secondary btn-lg">Hubungi Kami</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<div class="container my-4">
    <?= renderAd('PUBLIC_PAGE') ?>
</div>
