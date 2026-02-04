<?php
require_once __DIR__ . '/../paths.php';
require_once CONFIG_FILE;
require_once DB_FILE;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_role'])) {
    header("Location: " . BASE_URL . "/login/login.php");
    exit();
}

// Проверка прав доступа
if (!isset($_SESSION['user_role'])) {
    header("Location: " . BASE_URL . "/login/login.php");
    exit();
}


require_once __DIR__ . '/functions.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

if (deleteSimCard($id)) {
    header('Location: index.php?deleted=1');
} else {
    die("Ошибка при удалении");
}
?>