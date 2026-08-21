<?php
use App\Core\Auth;
use App\Models\User;

$currentUrl = $currentUrl ?? '';
$user = $user ?? Auth::user();
$role = $user ? $user->role : '';
$isSuperAdmin = ($role === 'Super Admin');

// Get pending applicant and payment counts for badge (only for Super Admin)
$pendingCount = 0;
$pendingPaymentCount = 0;
if ($isSuperAdmin) {
    $pendingCount = (new User())->countPending();
    $pendingPaymentCount = (new \App\Models\Payment())->countPending();
}

// Define nav items for the 2 roles
$navItems = [
    'main' => [
        'label' => 'Menu Utama',
        'items' => [
            ['url' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard', 'roles' => '*'],
            ['url' => 'forms', 'icon' => 'forms', 'label' => 'Formulir Saya', 'roles' => '*'],
            ['url' => 'documents', 'icon' => 'documents', 'label' => 'Generator Surat', 'roles' => '*'],
            ['url' => 'templates', 'icon' => 'templates', 'label' => 'Template Surat Word', 'roles' => '*'],
            ['url' => 'responses', 'icon' => 'responses', 'label' => 'Data Respons', 'roles' => '*'],
        ],
    ],
];

if ($isSuperAdmin) {
    $navItems['admin'] = [
        'label' => 'Developer & Admin',
        'items' => [
            ['url' => 'applicants', 'icon' => 'applicants', 'label' => 'Pendaftar Baru', 'roles' => ['Super Admin'], 'badge' => $pendingCount],
            ['url' => 'payments', 'icon' => 'payment', 'label' => 'Kelola Pembayaran', 'roles' => ['Super Admin'], 'badge' => $pendingPaymentCount],
            ['url' => 'users', 'icon' => 'users', 'label' => 'Kelola Pengguna', 'roles' => ['Super Admin']],
            ['url' => 'settings', 'icon' => 'settings', 'label' => 'Pengaturan Aplikasi', 'roles' => ['Super Admin']],
            ['url' => 'settings/payment', 'icon' => 'bank', 'label' => 'Metode Pembayaran', 'roles' => ['Super Admin']],
            ['url' => 'settings/gateway', 'icon' => 'gateway', 'label' => 'Gateway & Notifikasi', 'roles' => ['Super Admin']],
            ['url' => 'settings/site', 'icon' => 'globe', 'label' => 'Pengaturan Situs', 'roles' => ['Super Admin']],
            ['url' => 'settings/pages', 'icon' => 'pages', 'label' => 'Kelola Halaman', 'roles' => ['Super Admin']],
            ['url' => 'settings/ads', 'icon' => 'ads', 'label' => 'Iklan & AdSense', 'roles' => ['Super Admin']],
            ['url' => 'settings/github', 'icon' => 'github', 'label' => 'GitHub Webhook', 'roles' => ['Super Admin']],
            ['url' => 'audit-log', 'icon' => 'audit', 'label' => 'Audit Log Sistem', 'roles' => ['Super Admin']],
        ],
    ];
}

// SVG icons
$icons = [
    'dashboard' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>',
    'forms' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
    'documents' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>',
    'templates' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>',
    'responses' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
    'applicants' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>',
    'payment' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>',
    'bank' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="21" x2="21" y2="21"/><line x1="3" y1="10" x2="21" y2="10"/><polyline points="5 6 12 3 19 6"/><line x1="4" y1="10" x2="4" y2="21"/><line x1="20" y1="10" x2="20" y2="21"/><line x1="8" y1="14" x2="8" y2="17"/><line x1="12" y1="14" x2="12" y2="17"/><line x1="16" y1="14" x2="16" y2="17"/></svg>',
    'gateway' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
    'users' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
    'settings' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
    'audit' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>',
    'globe' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
    'pages' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"/><path d="M13 2v5h5"/><line x1="10" y1="9" x2="10" y2="13"/><line x1="14" y1="9" x2="14" y2="13"/></svg>',
    'ads' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
    'github' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>',
];

function isActive($currentUrl, $url) {
    if ($url === 'dashboard' && $currentUrl === 'dashboard') return true;
    if ($url !== 'dashboard' && str_starts_with($currentUrl, $url)) return true;
    return false;
}
?>

<aside class="sidebar">
    <!-- Header -->
    <div class="sidebar-header">
        <div class="sidebar-logo">A</div>
        <div>
            <span class="sidebar-brand">ASR FORM</span>
            <div style="font-size: 10px; color: var(--text-tertiary); font-weight: 700; text-transform: uppercase; margin-top: -2px;">
                <?= $isSuperAdmin ? 'Developer / Admin' : 'Creator Panel' ?>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <?php foreach ($navItems as $section): ?>
            <div class="nav-section">
                <div class="nav-section-title"><?= $section['label'] ?></div>
                <?php foreach ($section['items'] as $item): ?>
                    <a href="<?= url($item['url']) ?>" 
                       class="nav-link <?= isActive($currentUrl, $item['url']) ? 'active' : '' ?>">
                        <span class="nav-icon"><?= $icons[$item['icon']] ?? '' ?></span>
                        <span class="nav-link-text"><?= $item['label'] ?></span>
                        <?php if (!empty($item['badge']) && $item['badge'] > 0): ?>
                            <span class="nav-badge" style="background: var(--warning-500); margin-left: auto; color: white; font-size: 11px; padding: 2px 7px; border-radius: 99px; font-weight: 700;">
                                <?= $item['badge'] ?>
                            </span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </nav>

    <!-- Footer -->
    <div class="sidebar-footer">
        <a href="<?= url('logout') ?>" class="nav-link" style="color: var(--danger-500);">
            <span class="nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
            </span>
            <span class="nav-link-text">Logout</span>
        </a>
    </div>
</aside>
