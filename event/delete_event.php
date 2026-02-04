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
    if (empty($input['id'])) {
        throw new Exception('ID события не указан');
    }

    $db = new Database();
    $db->delete_event($input['id']);

    $response['success'] = true;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);