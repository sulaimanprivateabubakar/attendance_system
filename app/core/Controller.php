<?php
// app/core/Controller.php

/**
 * Controller – base class for all controllers.
 */
class Controller
{
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── View rendering ───────────────────────────────────────────────────────

    /**
     * Render a view file with data.
     *
     * @param string $view   e.g. 'lecturer/dashboard'
     * @param array  $data   variables to extract into view scope
     */
    protected function view(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $file = APP_PATH . "/views/{$view}.php";

        if (!file_exists($file)) {
            throw new RuntimeException("View [{$view}] not found.");
        }

        require APP_PATH . '/views/layouts/header.php';
        require $file;
        require APP_PATH . '/views/layouts/footer.php';
    }

    // ── Redirects ────────────────────────────────────────────────────────────

protected function redirect(string $path): void
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

    // ── JSON responses (for AJAX / API) ─────────────────────────────────────

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // ── Flash messages ───────────────────────────────────────────────────────

    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = compact('type', 'message');
    }

    protected function getFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }

    // ── Input helpers ────────────────────────────────────────────────────────

    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    protected function get(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    /** Sanitise a string input. */
    protected function clean(string $value): string
    {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }

    // ── CSRF ─────────────────────────────────────────────────────────────────

 protected function validateCsrf(): void
{
    $token = $this->post('_csrf') ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!Auth::verifyCsrfToken($token)) {
        http_response_code(419);
        die('CSRF token mismatch.');
    }
}
}