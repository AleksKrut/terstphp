<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../config.php';

session_start();
header('Content-Type: application/json');

$response = ['success' => false, 'error' => ''];

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Требуется авторизация');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        throw new Exception('Некорректные данные');
    }

    $db = new Database();
    $user_id = $_SESSION['user_id'];

    // Обработка дат
    $start = !empty($input['start']) ? date('Y-m-d H:i:s', strtotime($input['start'])) : null;
    $end = !empty($input['end']) ? date('Y-m-d H:i:s', strtotime($input['end'])) : null;

    // Проверка обязательных полей
    if (empty($input['title']) || empty($start)) {
        throw new Exception('Не заполнены обязательные поля');
    }

    if (isset($input['id']) && !empty($input['id'])) {
        // Обновление события
        $db->update_event(
            $input['id'],
            $input['title'],
            $start,
            $end,
            $input['description'] ?? '',
            $input['assigned_to'] ?? null
        );
    } else {
        // Создание события
        $db->add_event(
            $input['title'],
            $start,
            $end,
            $input['description'] ?? '',
            $input['assigned_to'] ?? null,
            $user_id
        );
    }

    $response['success'] = true;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);