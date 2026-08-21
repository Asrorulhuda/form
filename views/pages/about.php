<section class="page-hero">
    <div class="container">
        <h1 class="page-hero-title"><?= e($pageTitle ?? 'Tentang') ?></h1>
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

            <?php if (!empty($pageVision)): ?>
                <div class="vision-box">
                    <h3>🎯 Visi</h3>
                    <p><?= e($pageVision) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?= renderAd('PUBLIC_PAGE') ?>
