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

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    try {
        if ($_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Ошибка загрузки файла');
        }

        require_once VENDOR_AUTOLOAD;

        $file = $_FILES['excel_file']['tmp_name'];
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

        $rows = [];
        $sheetNames = $spreadsheet->getSheetNames();

        foreach ($sheetNames as $sheetName) {
            $worksheet = $spreadsheet->getSheetByName($sheetName);
            $sheetRows = $worksheet->toArray();

            array_shift($sheetRows);

            $operator = 'МТС';
            $tab = $sheetName;

            if (stripos($sheetName, 'ТЕЛЕ') !== false || stripos($sheetName, 'Tele2') !== false) {
                $operator = 'Tele2';
            }

            foreach ($sheetRows as $row) {
                if (empty($row[0])) continue;

                $status = '';
                if (isset($row[5]) && !empty(trim($row[5]))) {
                    $status = normalizeStatus($row[5]);
                } elseif (isset($row[8]) && !empty(trim($row[8]))) {
                    $status = normalizeStatus($row[8]);
                }

                if (empty($status)) $status = 'Установлены';

                $rows[] = [
                    $row[0],
                    $row[1] ?? '',
                    $row[2] ?? '',
                    $row[3] ?? '',
                    $row[4] ?? '',
                    $status,
                    $operator,
                    $tab
                ];
            }
        }

        $result = importSimCards($rows);

        $success = sprintf(
            "Импорт завершен!<br>Добавлено: %d записей<br>Обновлено: %d записей<br>Создано вкладок: %d",
            $result['imported'],
            $result['updated'],
            $result['tabs_created']
        );
    } catch (Exception $e) {
        $error = "Ошибка импорта: " . $e->getMessage();
    }
}

require_once HEADER_FILE;
?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-warning">
                        <h4 class="card-title"><i class="bi bi-upload me-2"></i>Импорт из Excel</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>

                        <form method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="excel_file" class="form-label">Файл Excel</label>
                                <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xlsx,.xls" required>
                                <div class="form-text">
                                    Поддерживаемые форматы: .xlsx, .xls. Названия вкладок будут сохранены как есть.
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-upload me-1"></i> Импортировать
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left-circle me-1"></i> Назад
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once FOOTER_FILE; ?>