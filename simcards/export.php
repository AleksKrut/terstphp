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
if ($_SESSION['user_role'] !== 'director' && $_SESSION['user_role'] !== 'manager') {
    die("Доступ запрещен!");
}


require_once __DIR__ . '/functions.php';
require_once VENDOR_AUTOLOAD;

$tab = $_GET['tab'] ?? '';
$search = $_GET['search'] ?? '';

$sim_cards = getSimCards($tab, $search);

$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Заголовки
$headers = ['Номер SIM', 'Клиент', 'Гос. номер', 'Терминал', 'Система', 'Статус', 'Оператор', 'Вкладка'];
$sheet->fromArray($headers, null, 'A1');

// Данные
$rowIndex = 2;
foreach ($sim_cards as $sim) {
    $sheet->fromArray([
        $sim['number'],
        $sim['client'],
        $sim['car_number'],
        $sim['terminal'],
        $sim['system'],
        $sim['status'],
        $sim['operator'],
        $sim['tab']
    ], null, 'A' . $rowIndex);
    $rowIndex++;
}

// Авто-ширина колонок
foreach (range('A', 'H') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Заголовки для скачивания
$filename = "sim_cards_export_" . date('Y-m-d') . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>