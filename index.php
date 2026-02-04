<?php
require_once __DIR__ . '/paths.php';
require CONFIG_FILE;
require DB_FILE;

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/login/login.php");
    exit();
}

$current_user = $db->get_user($_SESSION['username']);
$user_fullname = $current_user['fullname'] ?? 'Пользователь';
$user_role = $current_user['role'] ?? 'user';
$is_admin = ($user_role === 'director');

// Активная вкладка
$active_tab = $_GET['tab'] ?? 'dashboard';
$allowed_tabs = ['dashboard', 'forum', 'acts', 'equipment', 'simcards', 'calendar', 'chat'];
if ($is_admin) $allowed_tabs[] = 'admin';

if (!in_array($active_tab, $allowed_tabs)) {
    $active_tab = 'dashboard';
}

// Получаем статистику для дашборда
$stats = $db->get_dashboard_stats();
$recent_activities = $db->get_recent_activities(5);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> v<?= APP_VERSION ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #1d3557;
            --secondary: #457b9d;
            --accent: #e63946;
            --light: #f1faee;
            --dark: #14213d;
            --success: #2a9d8f;
            --warning: #e9c46a;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .nav-tabs {
            border-bottom: 1px solid #dee2e6;
            background: white;
            padding: 0 1rem;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #495057;
            font-weight: 500;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
            border-radius: 0;
        }

        .nav-tabs .nav-link:hover {
            background-color: #f8f9fa;
            border: none;
            color: var(--primary);
        }

        .nav-tabs .nav-link.active {
            background-color: white;
            border-bottom: 3px solid var(--primary);
            color: var(--primary);
            font-weight: 600;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .module-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
            height: 100%;
        }

        .module-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
        }

        .module-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .quick-stats {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            line-height: 1;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tab-content {
            background: white;
            border-radius: 0 12px 12px 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            min-height: 600px;
            border: 1px solid #e9ecef;
            border-top: none;
        }

        .activity-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f3f4;
        }

        .activity-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
<!-- Верхняя навигация -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="?tab=dashboard">
            <i class="bi bi-building me-2"></i><?= APP_NAME ?>
        </a>

        <div class="d-flex align-items-center">
            <div class="text-white me-3 text-end">
                <div class="small" id="current-time"><?= date('d.m.Y H:i') ?></div>
                <div class="small opacity-75"><?= htmlspecialchars($user_fullname) ?></div>
            </div>
            <div class="user-avatar me-2" title="<?= htmlspecialchars(ROLES[$user_role] ?? $user_role) ?>">
                <?= mb_substr($user_fullname, 0, 1) ?>
            </div>
            <a href="<?= BASE_URL ?>/login/logout.php" class="btn btn-outline-light btn-sm" title="Выход">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </div>
</nav>

<!-- Основные вкладки -->
<div class="container-fluid mt-3">
    <ul class="nav nav-tabs" id="mainTabs">
        <li class="nav-item">
            <a class="nav-link <?= $active_tab === 'dashboard' ? 'active' : '' ?>"
               href="?tab=dashboard">
                <i class="bi bi-speedometer2 me-2"></i>Дашборд
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $active_tab === 'forum' ? 'active' : '' ?>"
               href="?tab=forum">
                <i class="bi bi-chat-dots me-2"></i>Форум
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $active_tab === 'acts' ? 'active' : '' ?>"
               href="?tab=acts">
                <i class="bi bi-file-earmark-text me-2"></i>Акты работ
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $active_tab === 'equipment' ? 'active' : '' ?>"
               href="?tab=equipment">
                <i class="bi bi-tools me-2"></i>Оборудование
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $active_tab === 'simcards' ? 'active' : '' ?>"
               href="?tab=simcards">
                <i class="bi bi-sim me-2"></i>SIM-карты
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $active_tab === 'calendar' ? 'active' : '' ?>"
               href="?tab=calendar">
                <i class="bi bi-calendar-event me-2"></i>Календарь
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $active_tab === 'chat' ? 'active' : '' ?>"
               href="?tab=chat">
                <i class="bi bi-chat-left-text me-2"></i>Чат
            </a>
        </li>
        <?php if ($is_admin): ?>
            <li class="nav-item">
                <a class="nav-link <?= $active_tab === 'admin' ? 'active' : '' ?>"
                   href="?tab=admin">
                    <i class="bi bi-gear me-2"></i>Администрирование
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <!-- Контент вкладок -->
    <div class="tab-content p-4" id="mainTabContent">
        <?php
        // Динамическая загрузка контента вкладки
        $tab_file = TABS_DIR . $active_tab . '.php';
        if (file_exists($tab_file)) {
            include $tab_file;
        } else {
            echo '<div class="alert alert-warning">Вкладка не найдена</div>';
        }
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Автоматическое обновление времени
    function updateTime() {
        const now = new Date();
        const timeElement = document.getElementById('current-time');
        if (timeElement) {
            timeElement.textContent = now.toLocaleDateString('ru-RU') + ' ' + now.toLocaleTimeString('ru-RU');
        }
    }
    setInterval(updateTime, 1000);

    // Подсветка активной вкладки при загрузке
    document.addEventListener('DOMContentLoaded', function() {
        const currentTab = '<?= $active_tab ?>';
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('href').includes(currentTab)) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    });
</script>
</body>
</html>