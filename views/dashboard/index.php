<?php
use App\Core\Auth;

$user = Auth::user();
$greeting = match(true) {
    (int)date('H') < 12  => 'Selamat Pagi',
    (int)date('H') < 15  => 'Selamat Siang',
    (int)date('H') < 18  => 'Selamat Sore',
    default               => 'Selamat Malam',
};
?>

<!-- Welcome Banner -->
<div class="card mb-5" style="background: linear-gradient(135deg, rgba(99,102,241,0.12) 0%, rgba(99,102,241,0.04) 100%); border-color: rgba(99,102,241,0.2);">
    <div class="card-body" style="padding: 28px 30px;">
        <h2 style="font-size: 22px; font-weight: 700; margin-bottom: 6px;">
            <?= $greeting ?>, <?= e($user->name ?? 'Admin') ?>! 👋
        </h2>
        <p style="color: var(--text-secondary); font-size: 14px; margin: 0;">
            Kelola form dan dokumen Anda dari dashboard ini.
        </p>
    </div>
</div>

<!-- Stat Cards -->
<div class="stat-grid">
    <div class="stat-card primary fade-in stagger-1">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Forms</div>
            <div class="stat-value"><?= number_format($totalForms) ?></div>
        </div>
    </div>

    <div class="stat-card success fade-in stagger-2">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Respons</div>
            <div class="stat-value"><?= number_format($totalResponses) ?></div>
        </div>
    </div>

    <div class="stat-card info fade-in stagger-3">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Dokumen</div>
            <div class="stat-value"><?= number_format($totalDocuments) ?></div>
        </div>
    </div>

    <div class="stat-card warning fade-in stagger-4">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Pending Approval</div>
            <div class="stat-value"><?= number_format($pendingDocs) ?></div>
        </div>
    </div>

    <div class="stat-card danger fade-in stagger-5">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Pengguna</div>
            <div class="stat-value"><?= number_format($totalUsers) ?></div>
        </div>
    </div>

    <div class="stat-card success fade-in stagger-6">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Dokumen Selesai</div>
            <div class="stat-value"><?= number_format($approvedDocs) ?></div>
        </div>
    </div>
</div>

<!-- Charts & Activity -->
<div class="grid-2">
    <!-- Chart -->
    <div class="card fade-in">
        <div class="card-header">
            <h3 class="card-title">Statistik Bulanan</h3>
        </div>
        <div class="card-body">
            <canvas id="monthlyChart" height="280"></canvas>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card fade-in">
        <div class="card-header">
            <h3 class="card-title">Aktivitas Terbaru</h3>
        </div>
        <div class="card-body" style="padding: 12px 24px;">
            <?php if (!empty($recentActivity)): ?>
                <div class="activity-list">
                    <?php foreach ($recentActivity as $activity): ?>
                        <?php
                        $dotClass = match($activity->action) {
                            'create' => 'success',
                            'update' => 'primary',
                            'delete' => 'danger',
                            'login'  => 'warning',
                            default  => 'primary',
                        };
                        $timeAgo = (new DateTime($activity->created_at))->format('d/m/Y H:i');
                        ?>
                        <div class="activity-item">
                            <div class="activity-dot <?= $dotClass ?>"></div>
                            <div class="activity-content">
                                <div class="activity-text">
                                    <strong><?= e($activity->user_name ?? 'System') ?></strong>
                                    — <?= e($activity->description) ?>
                                </div>
                                <div class="activity-time"><?= $timeAgo ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state" style="padding: 40px 20px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:48px;height:48px;color:var(--text-muted);margin-bottom:12px;">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                    </svg>
                    <p class="empty-state-desc">Belum ada aktivitas.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('monthlyChart');
    if (!ctx) return;

    // Process monthly data
    const monthLabels = [];
    const responsesData = [];
    const documentsData = [];

    // Generate last 6 months labels
    for (let i = 5; i >= 0; i--) {
        const d = new Date();
        d.setMonth(d.getMonth() - i);
        const key = d.toISOString().slice(0, 7);
        monthLabels.push(d.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' }));
        
        // Find data
        const rData = <?= json_encode($monthlyResponses ?? []) ?>;
        const dData = <?= json_encode($monthlyDocuments ?? []) ?>;
        
        const rMatch = rData.find(r => r.month === key);
        const dMatch = dData.find(d => d.month === key);
        
        responsesData.push(rMatch ? parseInt(rMatch.total) : 0);
        documentsData.push(dMatch ? parseInt(dMatch.total) : 0);
    }

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [
                {
                    label: 'Respons',
                    data: responsesData,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#6366f1',
                    borderWidth: 2,
                },
                {
                    label: 'Dokumen',
                    data: documentsData,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#10b981',
                    borderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: '#475569',
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { family: 'Plus Jakarta Sans, Inter', size: 12, weight: '600' },
                        padding: 20,
                    }
                },
                tooltip: {
                    backgroundColor: '#ffffff',
                    titleColor: '#0f172a',
                    bodyColor: '#475569',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    cornerRadius: 8,
                    padding: 12,
                    boxShadow: '0 4px 12px rgba(0,0,0,0.08)',
                    titleFont: { family: 'Plus Jakarta Sans, Inter', size: 13, weight: '700' },
                    bodyFont: { family: 'Plus Jakarta Sans, Inter', size: 12 },
                }
            },
            scales: {
                x: {
                    grid: { color: '#f1f5f9', drawBorder: false },
                    ticks: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans, Inter', size: 11, weight: '500' } },
                    border: { display: false },
                },
                y: {
                    grid: { color: '#f1f5f9', drawBorder: false },
                    ticks: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans, Inter', size: 11, weight: '500' }, stepSize: 1 },
                    border: { display: false },
                    beginAtZero: true,
                }
            }
        }
    });
});
</script>
