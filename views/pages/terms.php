<section class="page-hero">
    <div class="container">
        <h1 class="page-hero-title"><?= e($pageTitle ?? 'Syarat dan Ketentuan') ?></h1>
        <?php if (!empty($lastUpdated)): ?>
            <p class="page-hero-subtitle">Terakhir diperbarui: <?= e($lastUpdated) ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <div class="page-content-card">
            <div class="legal-content">
                <?php
                $content = $pageContent ?? '';
                if (strip_tags($content) === $content) {
                    echo nl2br(e($content));
                } else {
                    echo $content;
                }
                ?>
            </div>
        </div>
    </div>
</section>

<?= renderAd('PUBLIC_PAGE') ?>
