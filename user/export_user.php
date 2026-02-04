<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../header.php'; // Добавлен header для проверки авторизации

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'director') {
    header('Location: ../login.php'); // Исправлен путь
    exit;
}

$db = new Database();
$users = $db->get_all_users();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=users_' . date('Ymd') . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Логин', 'Имя', 'Роль', 'Дата создания']);

foreach ($users as $user) {
    fputcsv($output, [
        $user['id'],
        $user['username'],
        $user['fullname'],
        ROLES[$user['role']] ?? $user['role'], // Добавлена проверка на существование ROLES
        $user['created_at']
    ]);
}

fclose($output);
exit;