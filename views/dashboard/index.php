<?php
use App\Core\Auth;

$user = Auth::user();
$role = $user ? $user->role : '';
$isSuperAdmin = ($role === 'Super Admin');

$greeting = match(true) {
    (int)date('H') < 12  => 'Selamat Pagi',
    (int)date('H') < 15  => 'Selamat Siang',
    (int)date('H') < 18  => 'Selamat Sore',
    default               => 'Selamat Malam',
};
?>

<!-- ═══════════════════════════════════════════
     ILLUSTRATIVE BENTO DASHBOARD GRID
     ═══════════════════════════════════════════ -->
<div class="bento-grid">
    <!-- 1. Hero Bento Card (Illustrative Banner) -->
    <div class="bento-col-12 bento-hero fade-in" style="background: radial-gradient(circle at 85% 30%, rgba(99, 102, 241, 0.12) 0%, rgba(255, 255, 255, 0) 60%), #ffffff; border: 1px solid #e2e8f0; position: relative; overflow: hidden;">
        <div class="bento-hero-left">
            <div class="bento-hero-avatar" style="background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%); color: white; box-shadow: 0 4px 12px rgba(79,70,229,0.3);">
                <?= strtoupper(substr($user->name ?? 'A', 0, 1)) ?>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <h2 class="bento-hero-title" style="margin: 0; font-size: 22px; font-weight: 900; color: #0f172a; letter-spacing: -0.4px;">
                        <?= $greeting ?>, <?= e($user->name ?? 'Pengguna') ?>!
                    </h2>
                    <span class="badge <?= $isSuperAdmin ? 'badge-primary' : 'badge-success' ?>" style="font-size: 11px; padding: 2px 8px; font-weight: 700;">
                        <?= $isSuperAdmin ? '👑 Super Admin' : ('🏢 ' . e($user->plan ?? 'Creator Pro')) ?>
                    </span>
                </div>
                <div class="bento-hero-desc" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; font-size: 13px; color: #64748b;">
                    <span style="display: inline-flex; align-items: center; gap: 5px; color: var(--success-600); font-weight: 600; background: var(--success-50); padding: 2px 8px; border-radius: 99px; border: 1px solid rgba(16,185,129,0.2);">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background: var(--success-500); display: inline-block;"></span>
                        Sistem Online &amp; Terverifikasi
                    </span>
                    <span>•</span>
                    <span>Kelola formulir kustom, tangkap data respons, dan terbitkan dokumen berpenomoran otomatis.</span>
                </div>
            </div>
        </div>

        <div class="bento-hero-actions">
            <a href="<?= url('forms/create') ?>" class="btn btn-primary btn-sm" style="box-shadow: 0 4px 14px rgba(79,70,229,0.28); font-weight: 600;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Buat Formulir
            </a>
            <a href="<?= url('templates') ?>" class="btn btn-secondary btn-sm" style="font-weight: 600;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Template Word
            </a>
            <?php if ($isSuperAdmin): ?>
                <a href="<?= url('settings') ?>" class="btn btn-secondary btn-sm" style="font-weight: 600;" title="Pengaturan Sistem">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- 2. Stat Bento Tiles (6 Distinct Cards) -->
    <!-- Tile 1: Total Forms -->
    <a href="<?= url('forms') ?>" class="bento-col-2 bento-stat-tile primary fade-in stagger-1">
        <div class="bento-stat-top">
            <div class="bento-stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
            </div>
            <span class="bento-stat-pill">Formulir</span>
        </div>
        <div>
            <div class="bento-stat-val"><?= number_format($totalForms) ?></div>
            <div class="bento-stat-lbl">Total Formulir Aktif</div>
        </div>
    </a>

    <!-- Tile 2: Total Responses -->
    <a href="<?= url('responses') ?>" class="bento-col-2 bento-stat-tile success fade-in stagger-2">
        <div class="bento-stat-top">
            <div class="bento-stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <span class="bento-stat-pill" style="background: var(--success-50); color: var(--success-700);">Respons</span>
        </div>
        <div>
            <div class="bento-stat-val"><?= number_format($totalResponses) ?></div>
            <div class="bento-stat-lbl">Data Masuk</div>
        </div>
    </a>

    <!-- Tile 3: Total Documents -->
    <a href="<?= url('documents') ?>" class="bento-col-2 bento-stat-tile info fade-in stagger-3">
        <div class="bento-stat-top">
            <div class="bento-stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
            <span class="bento-stat-pill" style="background: var(--info-50); color: var(--info-700);">Dokumen</span>
        </div>
        <div>
            <div class="bento-stat-val"><?= number_format($totalDocuments) ?></div>
            <div class="bento-stat-lbl">Dokumen Tergenerate</div>
        </div>
    </a>

    <!-- Tile 4: Pending Approval -->
    <a href="<?= url($isSuperAdmin ? 'applicants' : 'documents?status=pending') ?>" class="bento-col-2 bento-stat-tile warning fade-in stagger-4">
        <div class="bento-stat-top">
            <div class="bento-stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <span class="bento-stat-pill" style="background: var(--warning-50); color: var(--warning-700);">Review</span>
        </div>
        <div>
            <div class="bento-stat-val"><?= number_format($pendingDocs) ?></div>
            <div class="bento-stat-lbl"><?= $isSuperAdmin ? 'Pending Approval' : 'Menunggu Validasi' ?></div>
        </div>
    </a>

    <!-- Tile 5: Approved Docs -->
    <a href="<?= url('documents?status=approved') ?>" class="bento-col-2 bento-stat-tile success fade-in stagger-5">
        <div class="bento-stat-top">
            <div class="bento-stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <span class="bento-stat-pill" style="background: #d1fae5; color: #047857;">Selesai</span>
        </div>
        <div>
            <div class="bento-stat-val"><?= number_format($approvedDocs) ?></div>
            <div class="bento-stat-lbl">Dokumen Sah Terbit</div>
        </div>
    </a>

    <!-- Tile 6: Total Users or Server Status -->
    <?php if ($isSuperAdmin): ?>
        <a href="<?= url('users') ?>" class="bento-col-2 bento-stat-tile purple fade-in stagger-6">
            <div class="bento-stat-top">
                <div class="bento-stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <span class="bento-stat-pill" style="background: #f3e8ff; color: #7e22ce;">User</span>
            </div>
            <div>
                <div class="bento-stat-val"><?= number_format($totalUsers) ?></div>
                <div class="bento-stat-lbl">Total Pengguna</div>
            </div>
        </a>
    <?php else: ?>
        <a href="<?= url('settings') ?>" class="bento-col-2 bento-stat-tile purple fade-in stagger-6">
            <div class="bento-stat-top">
                <div class="bento-stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <span class="bento-stat-pill" style="background: #f3e8ff; color: #7e22ce;">Status</span>
            </div>
            <div>
                <div class="bento-stat-val" style="font-size: 16px; margin-top: 4px; font-weight: 800; color: #7e22ce;">Aktif</div>
                <div class="bento-stat-lbl">Status Lisensi Akun</div>
            </div>
        </a>
    <?php endif; ?>

    <!-- 3. Bottom Bento Row: Chart (Span 7) & Quick Workflow / Activity (Span 5) -->
    <!-- Monthly Analytics Bento Card -->
    <div class="bento-col-7 bento-card fade-in">
        <div class="flex items-center justify-between mb-4 pb-2" style="border-bottom: 1px solid var(--border-subtle);">
            <div>
                <h3 class="card-title" style="margin: 0; font-size: 16px; font-weight: 800; color: #0f172a;">Statistik &amp; Tren Bulanan</h3>
                <p class="text-sm text-muted" style="margin: 2px 0 0; font-size: 12.5px;">Grafik respons formulir masuk &amp; penerbitan dokumen resmi</p>
            </div>
            <span class="badge badge-primary" style="font-size: 11px; font-weight: 700;">6 Bulan Terakhir</span>
        </div>
        <div style="height: 270px; position: relative; width: 100%;">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <!-- Right Side Bento Card: Illustrative Workflow or Activity -->
    <div class="bento-col-5 bento-card fade-in" style="display: flex; flex-direction: column;">
        <div class="flex items-center justify-between mb-3 pb-2" style="border-bottom: 1px solid var(--border-subtle);">
            <div>
                <h3 class="card-title" style="margin: 0; font-size: 16px; font-weight: 800; color: #0f172a;">
                    <?= $isSuperAdmin ? 'Log Aktivitas Sistem' : 'Panduan Alur Kerja' ?>
                </h3>
                <p class="text-sm text-muted" style="margin: 2px 0 0; font-size: 12.5px;">
                    <?= $isSuperAdmin ? 'Riwayat aksi pengguna terbaru' : 'Langkah cepat integrasi formulir Anda' ?>
                </p>
            </div>
            <?php if ($isSuperAdmin): ?>
                <a href="<?= url('audit-log') ?>" class="text-sm" style="color: var(--primary-600); font-weight: 700; text-decoration: none;">Semua &rarr;</a>
            <?php endif; ?>
        </div>

        <?php if ($isSuperAdmin): ?>
            <!-- Recent Activity List for Super Admin -->
            <div style="flex: 1; overflow-y: auto; max-height: 270px; padding-right: 4px;">
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
                            $timeAgo = (new DateTime($activity->created_at))->format('d/m H:i');
                            ?>
                            <div class="activity-item">
                                <div class="activity-dot <?= $dotClass ?>"></div>
                                <div class="activity-content">
                                    <div class="activity-text" style="font-size: 12.5px;">
                                        <strong><?= e($activity->user_name ?? 'System') ?></strong>
                                        &mdash; <?= e($activity->description) ?>
                                    </div>
                                    <div class="activity-time" style="font-size: 11px;"><?= $timeAgo ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state" style="padding: 40px 20px; text-align: center;">
                        <p class="empty-state-desc" style="font-size: 13px; color: var(--text-muted);">Belum ada aktivitas tercatat.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Illustrative Quick Start Guide for Creators / Users -->
            <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-around; gap: 10px;">
                <a href="<?= url('forms/create') ?>" style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--primary-400)'" onmouseout="this.style.borderColor='#e2e8f0'">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: var(--primary-50); color: var(--primary-600); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px;">1</div>
                    <div style="flex: 1;">
                        <div style="font-size: 13.5px; font-weight: 800; color: #0f172a;">Buat Formulir Online Baru</div>
                        <div style="font-size: 11.5px; color: #64748b;">Gunakan 18+ tipe field dinamis &amp; tanda tangan digital</div>
                    </div>
                    <span style="color: var(--primary-600); font-weight: 800;">&rarr;</span>
                </a>

                <a href="<?= url('templates') ?>" style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--primary-400)'" onmouseout="this.style.borderColor='#e2e8f0'">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: var(--success-50); color: var(--success-600); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px;">2</div>
                    <div style="flex: 1;">
                        <div style="font-size: 13.5px; font-weight: 800; color: #0f172a;">Pasang Template Dokumen Word</div>
                        <div style="font-size: 11.5px; color: #64748b;">Gunakan tag <code>{{nama}}</code> untuk substitusi data otomatis</div>
                    </div>
                    <span style="color: var(--success-600); font-weight: 800;">&rarr;</span>
                </a>

                <a href="<?= url('forms') ?>" style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--primary-400)'" onmouseout="this.style.borderColor='#e2e8f0'">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: var(--info-50); color: var(--info-600); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px;">3</div>
                    <div style="flex: 1;">
                        <div style="font-size: 13.5px; font-weight: 800; color: #0f172a;">Bagikan Link &amp; Terbitkan Surat</div>
                        <div style="font-size: 11.5px; color: #64748b;">Unduh PDF / Word dengan validasi QR Code resmi</div>
                    </div>
                    <span style="color: var(--info-600); font-weight: 800;">&rarr;</span>
                </a>
            </div>
        <?php endif; ?>
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
                    label: 'Respons Masuk',
                    data: responsesData,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.08)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#4f46e5',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    borderWidth: 2.5,
                },
                {
                    label: 'Dokumen Terbit',
                    data: documentsData,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.06)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    borderWidth: 2.5,
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
                        padding: 16,
                    }
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#ffffff',
                    bodyColor: '#e2e8f0',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    padding: 10,
                    titleFont: { family: 'Plus Jakarta Sans, Inter', size: 12, weight: '700' },
                    bodyFont: { family: 'Plus Jakarta Sans, Inter', size: 11.5 },
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
