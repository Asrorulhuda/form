<section class="page-hero">
    <div class="container">
        <h1 class="page-hero-title"><?= e($pageTitle ?? 'Paket & Harga') ?></h1>
        <?php if (!empty($pageSubtitle)): ?>
            <p class="page-hero-subtitle"><?= e($pageSubtitle) ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <?php if (!empty($pricingItems)): ?>
            <div class="pricing-grid">
                <?php foreach ($pricingItems as $plan): ?>
                    <div class="pricing-card <?= !empty($plan['highlighted']) ? 'pricing-highlighted' : '' ?>">
                        <?php if (!empty($plan['highlighted'])): ?>
                            <div class="pricing-badge">Populer</div>
                        <?php endif; ?>

                        <h3 class="pricing-name"><?= e($plan['name'] ?? '') ?></h3>
                        
                        <div class="pricing-price">
                            <span class="pricing-amount"><?= e($plan['price'] ?? '') ?></span>
                            <?php if (!empty($plan['period'])): ?>
                                <span class="pricing-period">/ <?= e($plan['period']) ?></span>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($plan['desc'])): ?>
                            <p class="pricing-desc"><?= e($plan['desc']) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($plan['features'])): ?>
                            <ul class="pricing-features">
                                <?php foreach ($plan['features'] as $feature): ?>
                                    <li>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                        <?= e($feature) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if (!empty($plan['cta'])): ?>
                            <a href="<?= url('register?plan=' . urlencode($plan['name'] ?? '')) ?>" class="btn <?= !empty($plan['highlighted']) ? 'btn-primary' : 'btn-secondary' ?> btn-lg" style="width: 100%;">
                                <?= e($plan['cta']) ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center" style="padding: 60px 0; color: var(--text-muted);">
                <p>Informasi harga belum tersedia.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= renderAd('PUBLIC_PAGE') ?>
