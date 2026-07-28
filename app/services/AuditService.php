<?php
// app/services/AuditService.php

class AuditService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Log an audit event.
     *
     * @param string $action     e.g. 'user.created', 'session.activated'
     * @param string $module     e.g. 'users', 'sessions', 'attendance'
     * @param string $description Human-readable description
     * @param mixed  $oldValue   Previous value (optional)
     * @param mixed  $newValue   New value (optional)
     */
    public function log(
        string $action,
        string $module,
        string $description,
        mixed  $oldValue = null,
        mixed  $newValue = null
    ): void {
        try {
            $user     = Auth::user();
            $userId   = Auth::id();
            $userName = $user['name']  ?? 'System';
            $userRole = $user['role']  ?? 'system';
            $ip       = $_SERVER['HTTP_X_FORWARDED_FOR']
                        ?? $_SERVER['REMOTE_ADDR']
                        ?? '';
            $ua       = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);

            $this->db->insert(
                "INSERT INTO audit_logs
                    (user_id, user_name, user_role, action, module,
                     description, old_value, new_value, ip_address, user_agent)
                 VALUES (?,?,?,?,?,?,?,?,?,?)",
                [
                    $userId,
                    $userName,
                    $userRole,
                    $action,
                    $module,
                    $description,
                    $oldValue !== null ? (is_string($oldValue) ? $oldValue : json_encode($oldValue)) : null,
                    $newValue !== null ? (is_string($newValue) ? $newValue : json_encode($newValue)) : null,
                    $ip,
                    $ua,
                ]
            );
        } catch (Throwable $e) {
            // Never let audit logging break the main flow
            error_log('Audit log failed: ' . $e->getMessage());
        }
    }

    /**
     * Shorthand static-style call.
     */
    public static function record(
        string $action,
        string $module,
        string $description,
        mixed  $old = null,
        mixed  $new = null
    ): void {
        (new self())->log($action, $module, $description, $old, $new);
    }
}