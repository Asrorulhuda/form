<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;
use App\Models\AuditLog;

/**
 * Dashboard Controller
 */
class DashboardController
{
    /**
     * Show dashboard
     */
    public function index(): void
    {
        $db = Database::getInstance();

        // Get stats
        $totalForms     = (int) $db->fetchColumn("SELECT COUNT(*) FROM forms") ?: 0;
        $totalResponses = (int) $db->fetchColumn("SELECT COUNT(*) FROM form_responses") ?: 0;
        $totalDocuments = (int) $db->fetchColumn("SELECT COUNT(*) FROM documents") ?: 0;
        $pendingDocs    = (int) $db->fetchColumn("SELECT COUNT(*) FROM documents WHERE status = 'pending'") ?: 0;
        $totalUsers     = (int) $db->fetchColumn("SELECT COUNT(*) FROM users") ?: 0;
        $approvedDocs   = (int) $db->fetchColumn("SELECT COUNT(*) FROM documents WHERE status = 'approved'") ?: 0;

        // Get recent activity
        $auditModel = new AuditLog();
        $recentActivity = $auditModel->getRecent(8);

        // Monthly stats for chart (last 6 months)
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
            'title'           => 'Dashboard',
            'pageTitle'       => 'Dashboard',
            'totalForms'      => $totalForms,
            'totalResponses'  => $totalResponses,
            'totalDocuments'  => $totalDocuments,
            'pendingDocs'     => $pendingDocs,
            'totalUsers'      => $totalUsers,
            'approvedDocs'    => $approvedDocs,
            'recentActivity'  => $recentActivity,
            'monthlyResponses'=> $monthlyResponses,
            'monthlyDocuments'=> $monthlyDocuments,
        ]);
    }
}
