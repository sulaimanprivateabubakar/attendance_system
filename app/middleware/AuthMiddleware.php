<?php
class AuthMiddleware
{
    public static function handle(): void
    {
        if (!Auth::check()) {
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }
}