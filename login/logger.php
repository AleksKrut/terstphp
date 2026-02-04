<?php
require_once __DIR__ . '/../paths.php';
if (!defined('ROOT_DIR')) die('ROOT_DIR not defined!');
require_once ROOT_DIR . '/db.php';

class Logger {
    public static function log($action, $details = '') {
        // Безопасная проверка сессии
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $user_id = $_SESSION['user_id'] ?? 0;
        $username = $_SESSION['username'] ?? 'system';
        $message = "[$username] $details";
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        try {
            $db = new Database();
            $db->add_log(
                $user_id,
                $action,
                $message,
                $ip,
                $user_agent
            );
        } catch (\Throwable $e) {
            // Записываем ошибку логгера в системный лог
            error_log('Logger error: ' . $e->getMessage());

            // Fallback: логируем в системный лог
            $fallbackMessage = sprintf(
                "FALLBACK LOG: [%s] Action: %s, Details: %s, User ID: %d, IP: %s, User Agent: %s",
                date('Y-m-d H:i:s'),
                $action,
                $message,
                $user_id,
                $ip,
                $user_agent
            );
            error_log($fallbackMessage);
        }
    }
}