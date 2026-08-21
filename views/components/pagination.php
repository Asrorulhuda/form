<?php
/**
 * Pagination Component
 * 
 * Usage: View::component('pagination', ['page' => $page, 'lastPage' => $lastPage, 'total' => $total, 'baseUrl' => 'users'])
 */
$page     = $page ?? 1;
$lastPage = $lastPage ?? 1;
$total    = $total ?? 0;
$baseUrl  = $baseUrl ?? '';
$perPage  = $perPage ?? 15;

// Build query string (preserve filters)
$queryParams = $_GET;
unset($queryParams['url']); // Remove router param

function paginationUrl($baseUrl, $pageNum, $queryParams) {
    $queryParams['page'] = $pageNum;
    return url($baseUrl . '?' . http_build_query($queryParams));
}

$start = ($page - 1) * $perPage + 1;
$end   = min($page * $perPage, $total);
?>

<?php if ($lastPage > 1): ?>
<div class="pagination-wrapper">
    <div class="pagination-info">
        Menampilkan <?= $start ?>-<?= $end ?> dari <?= number_format($total) ?> data
    </div>
    <div class="pagination">
        <!-- Previous -->
        <a href="<?= paginationUrl($baseUrl, max(1, $page - 1), $queryParams) ?>" 
           class="pagination-link <?= $page <= 1 ? 'disabled' : '' ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
        </a>

        <?php
        // Calculate visible pages
        $startPage = max(1, $page - 2);
        $endPage = min($lastPage, $page + 2);
        
        if ($startPage > 1): ?>
            <a href="<?= paginationUrl($baseUrl, 1, $queryParams) ?>" class="pagination-link">1</a>
            <?php if ($startPage > 2): ?>
                <span class="pagination-link disabled">…</span>
            <?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <a href="<?= paginationUrl($baseUrl, $i, $queryParams) ?>" 
               class="pagination-link <?= $i === $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($endPage < $lastPage): ?>
            <?php if ($endPage < $lastPage - 1): ?>
                <span class="pagination-link disabled">…</span>
            <?php endif; ?>
            <a href="<?= paginationUrl($baseUrl, $lastPage, $queryParams) ?>" class="pagination-link"><?= $lastPage ?></a>
        <?php endif; ?>

        <!-- Next -->
        <a href="<?= paginationUrl($baseUrl, min($lastPage, $page + 1), $queryParams) ?>" 
           class="pagination-link <?= $page >= $lastPage ? 'disabled' : '' ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
        </a>
    </div>
</div>
<?php endif; ?>
