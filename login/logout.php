<?php
require_once __DIR__ . '/../paths.php';
if (!defined('ROOT_DIR')) die('ROOT_DIR not defined!');
require_once ROOT_DIR . '/login/logger.php';

session_start();

// Логируем выход перед уничтожением сессии
if (isset($_SESSION['username'])) {
    Logger::log('logout', "Пользователь вышел из системы");
}

session_unset();
session_destroy();
header('Location: ' . BASE_URL . '/login/login.php');
exit;