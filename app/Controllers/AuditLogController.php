<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\AuditLog as AuditLogModel;

/**
 * Audit Log Controller
 */
class AuditLogController
{
    private AuditLogModel $auditModel;

    public function __construct()
    {
        $this->auditModel = new AuditLogModel();
    }

    /**
     * Show audit logs
     */
    public function index(): void
    {
        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $filters = [
            'search' => $_GET['search'] ?? '',
            'module' => $_GET['module'] ?? '',
            'action' => $_GET['action'] ?? '',
        ];

        $result = $this->auditModel->getAll($filters, $page);

        View::page('audit.index', [
            'title'     => 'Audit Log',
            'pageTitle' => 'Audit Log',
            'logs'      => $result['data'],
            'total'     => $result['total'],
            'page'      => $result['page'],
            'lastPage'  => $result['lastPage'],
            'filters'   => $filters,
        ]);
    }
}
