<section class="page-hero">
    <div class="container">
        <h1 class="page-hero-title"><?= e($pageTitle ?? 'Tentang Platform') ?></h1>
        <p class="page-hero-subtitle">Membangun ekosistem administrasi digital yang efisien, transparan, dan terverifikasi untuk seluruh lapisan instansi.</p>
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
                <div class="vision-box" style="background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%); border: 1px solid #c7d2fe; border-radius: 16px; padding: 24px;">
                    <h3 style="font-size: 16px; font-weight: 800; color: var(--primary-700); margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Visi &amp; Komitmen Layanan
                    </h3>
                    <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.65; margin: 0;"><?= e($pageVision) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<div class="container my-4">
    <?= renderAd('PUBLIC_PAGE') ?>
</div>
