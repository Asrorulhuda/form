<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;
use App\Models\AuditLog;

/**
 * Dashboard Controller — Multi-SaaS
 * Shows role-specific dashboards.
 */
class DashboardController
{
    /**
     * Show dashboard based on user role
     */
    public function index(): void
    {
        if (Auth::isSuperAdmin()) {
            $this->superAdminDashboard();
        } elseif (Auth::isAdmin()) {
            $this->adminDashboard();
        } else {
            $this->userDashboard();
        }
    }

    /**
     * Super Admin dashboard — global stats
     */
    private function superAdminDashboard(): void
    {
        $db = Database::getInstance();

        $totalAdmins    = (int) $db->fetchColumn("SELECT COUNT(*) FROM admins WHERE role_id = 2") ?: 0;
        $pendingAdmins  = (int) $db->fetchColumn("SELECT COUNT(*) FROM admins WHERE status = 'pending' AND role_id = 2") ?: 0;
        $totalUsers     = (int) $db->fetchColumn("SELECT COUNT(*) FROM users") ?: 0;
        $totalForms     = (int) $db->fetchColumn("SELECT COUNT(*) FROM forms") ?: 0;
        $totalResponses = (int) $db->fetchColumn("SELECT COUNT(*) FROM form_responses") ?: 0;
        $totalDocuments = (int) $db->fetchColumn("SELECT COUNT(*) FROM documents") ?: 0;

        $auditModel = new AuditLog();
        $recentActivity = $auditModel->getRecent(8);

        $monthlyResponses = $db->fetchAll(
            "SELECT DATE_FORMAT(submitted_at, '%Y-%m') as month, COUNT(*) as total 
             FROM form_responses 
             WHERE submitted_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
             GROUP BY month ORDER BY month ASC"
        );

        $monthlyDocuments = $db->fetchAll(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total 
             FROM documents 
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
             GROUP BY month ORDER BY month ASC"
        );

        View::page('dashboard.index', [
            'title'            => 'Dashboard Super Admin',
            'pageTitle'        => 'Dashboard',
            'dashboardType'    => 'superadmin',
            'totalAdmins'      => $totalAdmins,
            'pendingAdmins'    => $pendingAdmins,
            'totalUsers'       => $totalUsers,
            'totalForms'       => $totalForms,
            'totalResponses'   => $totalResponses,
            'totalDocuments'   => $totalDocuments,
            'pendingDocs'      => 0,
            'approvedDocs'     => 0,
            'recentActivity'   => $recentActivity,
            'monthlyResponses' => $monthlyResponses,
            'monthlyDocuments' => $monthlyDocuments,
        ]);
    }

    /**
     * Admin dashboard — tenant-scoped stats
     */
    private function adminDashboard(): void
    {
        $db = Database::getInstance();
        $adminId = Auth::adminId();

        $totalForms     = (int) $db->fetchColumn("SELECT COUNT(*) FROM forms WHERE admin_id = ?", [$adminId]) ?: 0;
        $totalResponses = (int) $db->fetchColumn(
            "SELECT COUNT(*) FROM form_responses fr 
             JOIN forms f ON fr.form_id = f.id 
             WHERE f.admin_id = ?", [$adminId]
        ) ?: 0;
        $totalDocuments = (int) $db->fetchColumn("SELECT COUNT(*) FROM documents WHERE admin_id = ?", [$adminId]) ?: 0;
        $pendingDocs    = (int) $db->fetchColumn("SELECT COUNT(*) FROM documents WHERE admin_id = ? AND status = 'pending'", [$adminId]) ?: 0;
        $totalUsers     = (int) $db->fetchColumn("SELECT COUNT(*) FROM users WHERE admin_id = ?", [$adminId]) ?: 0;
        $approvedDocs   = (int) $db->fetchColumn("SELECT COUNT(*) FROM documents WHERE admin_id = ? AND status = 'approved'", [$adminId]) ?: 0;

        $auditModel = new AuditLog();
        $recentActivity = $auditModel->getRecent(8);

        $monthlyResponses = $db->fetchAll(
            "SELECT DATE_FORMAT(fr.submitted_at, '%Y-%m') as month, COUNT(*) as total 
             FROM form_responses fr
             JOIN forms f ON fr.form_id = f.id
             WHERE f.admin_id = ? AND fr.submitted_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
             GROUP BY month ORDER BY month ASC",
            [$adminId]
        );

        $monthlyDocuments = $db->fetchAll(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total 
             FROM documents 
             WHERE admin_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
             GROUP BY month ORDER BY month ASC",
            [$adminId]
        );

        View::page('dashboard.index', [
            'title'            => 'Dashboard',
            'pageTitle'        => 'Dashboard',
            'dashboardType'    => 'admin',
            'totalForms'       => $totalForms,
            'totalResponses'   => $totalResponses,
            'totalDocuments'   => $totalDocuments,
            'pendingDocs'      => $pendingDocs,
            'totalUsers'       => $totalUsers,
            'approvedDocs'     => $approvedDocs,
            'recentActivity'   => $recentActivity,
            'monthlyResponses' => $monthlyResponses,
            'monthlyDocuments' => $monthlyDocuments,
        ]);
    }

    /**
     * User dashboard — personal view
     */
    private function userDashboard(): void
    {
        $db = Database::getInstance();
        $adminId = Auth::adminId();

        // Show published forms from their admin
        $availableForms = $db->fetchAll(
            "SELECT id, title, slug, description FROM forms 
             WHERE admin_id = ? AND status = 'published' 
             ORDER BY created_at DESC LIMIT 10",
            [$adminId]
        );

        View::page('dashboard.index', [
            'title'          => 'Dashboard',
            'pageTitle'      => 'Dashboard',
            'dashboardType'  => 'user',
            'availableForms' => $availableForms,
        ]);
    }
}
