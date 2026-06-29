<?php
// app/middleware/RoleMiddleware.php

class RoleMiddleware
{
    public static function handle(string $requiredRole): void
    {
        if (Auth::role() !== $requiredRole) {
            http_response_code(403);
            require APP_PATH . '/views/errors/403.php';
            exit;
        }
    }
}
