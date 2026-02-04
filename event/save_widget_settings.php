<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $widget = $_POST['widget'] ?? null;
    $enabled = isset($_POST['enabled']) ? (bool)$_POST['enabled'] : false;

    // Инициализируем настройки виджетов
    if (!isset($_SESSION['widget_settings'])) {
        $_SESSION['widget_settings'] = [
            'calendar' => false,
            'weather' => false,
            'tasks' => false
        ];
    }

    // Сохраняем настройку
    if (array_key_exists($widget, $_SESSION['widget_settings'])) {
        $_SESSION['widget_settings'][$widget] = $enabled;
        echo json_encode(['success' => true]);
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Invalid widget']);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid request']);