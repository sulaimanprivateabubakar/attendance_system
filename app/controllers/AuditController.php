<?php
// app/controllers/AuditController.php

class AuditController extends Controller
{
    // GET /admin/audit
    public function index(): void
    {
        $filter  = $this->get('filter', 'today');
        $module  = $this->get('module', '');
        $action  = $this->get('action', '');
        $search  = $this->get('search', '');
        $page    = max(1, (int)$this->get('page', 1));
        $perPage = 50;
        $offset  = ($page - 1) * $perPage;

        // Date filter
        $dateWhere = match($filter) {
            'today'   => "DATE(al.created_at) = CURDATE()",
            'week'    => "al.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)",
            'month'   => "al.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
            'year'    => "al.created_at >= DATE_SUB(NOW(), INTERVAL 365 DAY)",
            default   => "1=1",
        };

        $conditions = [$dateWhere];
        $params     = [];

        if ($module) {
            $conditions[] = "al.module = ?";
            $params[]     = $module;
        }

        if ($action) {
            $conditions[] = "al.action LIKE ?";
            $params[]     = "%$action%";
        }

        if ($search) {
            $conditions[] = "(al.user_name LIKE ? OR al.description LIKE ? OR al.action LIKE ?)";
            $params[]     = "%$search%";
            $params[]     = "%$search%";
            $params[]     = "%$search%";
        }

        $where = implode(' AND ', $conditions);

        // Total count
        $total = (int)$this->db->scalar(
            "SELECT COUNT(*) FROM audit_logs al WHERE $where", $params
        );

        // Fetch logs
        $logs = $this->db->all(
            "SELECT al.* FROM audit_logs al
              WHERE $where
              ORDER BY al.created_at DESC
              LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        // Stats for period
        $stats = $this->db->single(
            "SELECT
                COUNT(*) AS total_events,
                COUNT(DISTINCT user_id) AS unique_users,
                SUM(CASE WHEN action LIKE 'auth.%' THEN 1 ELSE 0 END) AS auth_events,
                SUM(CASE WHEN action LIKE 'attendance.%' THEN 1 ELSE 0 END) AS att_events,
                SUM(CASE WHEN action LIKE 'user.%' THEN 1 ELSE 0 END) AS user_events,
                SUM(CASE WHEN action LIKE 'session.%' THEN 1 ELSE 0 END) AS session_events
               FROM audit_logs al WHERE $where",
            $params
        );

        // Modules for filter dropdown
        $modules = $this->db->all(
            "SELECT DISTINCT module FROM audit_logs ORDER BY module"
        );

        // Activity by hour for chart (today only)
        $hourlyActivity = $this->db->all(
            "SELECT HOUR(created_at) AS hour, COUNT(*) AS count
               FROM audit_logs
              WHERE DATE(created_at) = CURDATE()
              GROUP BY HOUR(created_at)
              ORDER BY hour ASC"
        );

        // Top users
        $topUsers = $this->db->all(
            "SELECT user_name, user_role, COUNT(*) AS event_count
               FROM audit_logs al
              WHERE $where AND user_id IS NOT NULL
              GROUP BY user_id
              ORDER BY event_count DESC
              LIMIT 5",
            $params
        );

        $totalPages = ceil($total / $perPage);

        $this->view('admin/audit', [
            'user'            => Auth::user(),
            'logs'            => $logs,
            'stats'           => $stats,
            'modules'         => $modules,
            'topUsers'        => $topUsers,
            'hourlyActivity'  => $hourlyActivity,
            'filter'          => $filter,
            'module'          => $module,
            'action'          => $action,
            'search'          => $search,
            'total'           => $total,
            'page'            => $page,
            'perPage'         => $perPage,
            'totalPages'      => $totalPages,
            'flash'           => $this->getFlash(),
        ]);
    }

    // GET /admin/audit/:id
    public function show(array $params): void
    {
        $log = $this->db->single(
            "SELECT * FROM audit_logs WHERE id = ?", [(int)$params['id']]
        );

        if (!$log) {
            $this->flash('error', 'Audit log not found.');
            $this->redirect('/admin/audit');
        }

        $this->view('admin/audit_detail', [
            'user'  => Auth::user(),
            'log'   => $log,
        ]);
    }

    // GET /admin/audit/export
    public function export(): void
    {
        $filter = $this->get('filter', 'today');

        $dateWhere = match($filter) {
            'today'  => "DATE(created_at) = CURDATE()",
            'week'   => "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)",
            'month'  => "created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
            default  => "1=1",
        };

        $logs = $this->db->all(
            "SELECT * FROM audit_logs WHERE $dateWhere ORDER BY created_at DESC"
        );

        $filename = 'audit_log_' . $filter . '_' . date('Ymd') . '.csv';
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID','User','Role','Action','Module','Description','Old Value','New Value','IP','Date/Time']);

        foreach ($logs as $log) {
            fputcsv($out, [
                $log['id'],
                $log['user_name']   ?? '',
                $log['user_role']   ?? '',
                $log['action'],
                $log['module'],
                $log['description'] ?? '',
                $log['old_value']   ?? '',
                $log['new_value']   ?? '',
                $log['ip_address']  ?? '',
                $log['created_at'],
            ]);
        }

        fclose($out);
        exit;
    }
}