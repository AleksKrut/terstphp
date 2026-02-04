<?php
require_once __DIR__ . '/../paths.php';
if (!defined('ROOT_DIR')) die('ROOT_DIR not defined!');
require_once ROOT_DIR . '/config.php';
require_once ROOT_DIR . '/db.php';

// Проверка авторизации и iframe
session_start();
$isIframe = isset($_GET['iframe']) && $_GET['iframe'] == 1;

// Проверка прав администратора
if ($_SESSION['role'] !== 'director') {
    if ($isIframe) {
        echo "<script>parent.location.href = '" . BASE_URL . "/dashboard.php';</script>";
    } else {
        header('Location: ' . BASE_URL . '/dashboard.php');
    }
    exit;
}

$db = new Database();
$logs = $db->get_logs(100);

// Стили для iframe
$customStyles = '
    <style>
        :root {
            --primary-dark: #14213d;
            --primary: #1d3557;
            --accent: #e63946;
            --light: #f1faee;
        }
        
        .log-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            margin: 20px;
        }
        
        .log-header {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: white;
            padding: 20px;
        }
        
        .log-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .log-subtitle {
            font-size: 1.1rem;
            opacity: 0.85;
        }
        
        .log-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }
        
        .log-table th {
            background-color: #f8f9fa;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #e0e0e0;
            color: var(--primary);
        }
        
        .log-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        
        .log-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .log-table .nowrap {
            white-space: nowrap;
        }
        
        .user-badge {
            display: inline-block;
            background: #e3f2fd;
            border-radius: 4px;
            padding: 3px 8px;
            font-weight: 500;
            color: #1976d2;
        }
        
        .action-badge {
            display: inline-block;
            background: #e8f5e9;
            border-radius: 4px;
            padding: 3px 8px;
            font-weight: 500;
            color: #388e3c;
        }
        
        .ip-address {
            font-family: monospace;
            font-size: 0.9rem;
        }
        
        .empty-logs {
            text-align: center;
            padding: 40px;
            color: #777;
            font-style: italic;
        }
        
        /* Адаптивные стили */
        @media (max-width: 768px) {
            .log-table {
                display: block;
                overflow-x: auto;
            }
            
            .log-table th, 
            .log-table td {
                min-width: 120px;
            }
        }
    </style>
';

// Для режима iframe выводим полную HTML-структуру
if ($isIframe) {
    echo '<!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Журнал событий - ' . APP_NAME . '</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        ' . $customStyles . '
    </head>
    <body>';
}
?>

<?php if (!$isIframe): ?>
    <?php require_once HEADER_FILE; ?>
<?php endif; ?>

    <div class="log-container">
        <div class="log-header">
            <h1 class="log-title">Журнал событий системы</h1>
            <p class="log-subtitle">История всех значимых действий пользователей</p>
        </div>

        <div class="table-responsive">
            <table class="log-table">
                <thead>
                <tr>
                    <th class="nowrap">Дата</th>
                    <th>Пользователь</th>
                    <th>Действие</th>
                    <th>Детали</th>
                    <th>IP</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($logs)): ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td class="nowrap"><?= date('d.m.Y H:i', strtotime($log['created_at'])) ?></td>
                            <td>
                                <span class="user-badge">
                                    <?= htmlspecialchars($log['username'] ?? 'Система') ?>
                                </span>
                            </td>
                            <td>
                                <span class="action-badge">
                                    <?= htmlspecialchars($log['action']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($log['details']) ?></td>
                            <td class="ip-address"><?= htmlspecialchars($log['ip_address']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="empty-logs">
                            <i class="bi bi-journal-x"></i> Журнал событий пуст
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php if ($isIframe): ?>
    </body>
    </html>
<?php else: ?>
    <?php require_once ROOT_DIR . '/footer.php'; ?>
<?php endif; ?>