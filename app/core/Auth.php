<?php
// app/core/Auth.php

/**
 * Auth – thin session-based authentication helper.
 *
 * Stores the authenticated user array in $_SESSION['auth_user'].
 */
class Auth
{
    private const SESSION_KEY = 'auth_user';

    // ── Session bootstrap ────────────────────────────────────────────────────

    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $name     = $_ENV['SESSION_NAME']     ?? 'qr_att_session';
            $lifetime = (int)($_ENV['SESSION_LIFETIME'] ?? 7200);

            session_name($name);
            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path'     => '/',
                'secure'   => ($_ENV['APP_ENV'] ?? 'development') === 'production',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    // ── Login / Logout ───────────────────────────────────────────────────────

    /**
     * Store user data in session after successful login.
     */
    public static function login(array $user): void
    {
        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);
        $_SESSION[self::SESSION_KEY] = [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(
                session_name(), '',
                time() - 42000,
                $p['path'], $p['domain'],
                $p['secure'], $p['httponly']
            );
        }
        session_destroy();
    }

    // ── Checks ───────────────────────────────────────────────────────────────

    public static function check(): bool
    {
        return isset($_SESSION[self::SESSION_KEY]);
    }

    public static function user(): ?array
    {
        return $_SESSION[self::SESSION_KEY] ?? null;
    }

    public static function id(): ?int
    {
        return $_SESSION[self::SESSION_KEY]['id'] ?? null;
    }

    public static function role(): ?string
    {
        return $_SESSION[self::SESSION_KEY]['role'] ?? null;
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }

    public static function isLecturer(): bool
    {
        return self::role() === 'lecturer';
    }

    public static function isStudent(): bool
    {
        return self::role() === 'student';
    }

    // ── CSRF ────────────────────────────────────────────────────────────────

    public static function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrfToken(string $token): bool
    {
        return isset($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $token);
    }
}
