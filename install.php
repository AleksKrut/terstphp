<?php
// install.php - Установщик системы Smart-ITS
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Проверяем, была ли уже установка
if (file_exists('installed.lock')) {
    die('Система уже установлена! Удалите файл installed.lock для повторной установки.');
}

$steps = [];
$current_step = 1;
$error = '';
$success = '';

// Обработка формы установки
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = $_POST['db_host'] ?? 'localhost';
    $db_name = $_POST['db_name'] ?? 'smartact_db';
    $db_user = $_POST['db_user'] ?? 'root';
    $db_pass = $_POST['db_pass'] ?? '';

    try {
        // Пытаемся подключиться к MySQL
        $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $steps[] = "✅ Подключение к MySQL успешно";

        // Создаем базу данных если не существует
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8 COLLATE utf8_general_ci");
        $steps[] = "✅ База данных '$db_name' создана/проверена";

        // Выбираем базу данных
        $pdo->exec("USE `$db_name`");
        $steps[] = "✅ Используем базу данных '$db_name'";

        // Создаем таблицы
        $tables_sql = [
            // Таблица пользователей
                "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                fullname VARCHAR(100) NOT NULL,
                role VARCHAR(20) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            // Таблица оборудования
                "CREATE TABLE IF NOT EXISTS equipment (
                id INT AUTO_INCREMENT PRIMARY KEY,
                type VARCHAR(100) NOT NULL,
                name VARCHAR(100) NOT NULL,
                serial_number VARCHAR(50) UNIQUE,
                location VARCHAR(100),
                status ENUM('active', 'inactive', 'maintenance') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            // Таблица ролей
                "CREATE TABLE IF NOT EXISTS roles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL UNIQUE,
                permissions TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            // Таблица событий
                "CREATE TABLE IF NOT EXISTS events (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                start DATETIME NOT NULL,
                end DATETIME,
                description TEXT,
                assigned_to INT,
                user_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            // Таблица SIM-карт
                "CREATE TABLE IF NOT EXISTS sim_cards (
                id INT AUTO_INCREMENT PRIMARY KEY,
                number VARCHAR(50) UNIQUE,
                client VARCHAR(100),
                car_number VARCHAR(20),
                terminal VARCHAR(50),
                system VARCHAR(50),
                status VARCHAR(50),
                operator VARCHAR(50),
                tab VARCHAR(50) DEFAULT 'Основная'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

            // Таблица логов
                "CREATE TABLE IF NOT EXISTS logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                action VARCHAR(255) NOT NULL,
                details TEXT,
                ip_address VARCHAR(45) NOT NULL,
                user_agent TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
        ];

        foreach ($tables_sql as $sql) {
            $pdo->exec($sql);
        }
        $steps[] = "✅ Все таблицы созданы успешно";

        // Добавляем базовые роли
        $default_roles = ['director', 'manager', 'specialist'];
        foreach ($default_roles as $role) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO roles (name) VALUES (?)");
            $stmt->execute([$role]);
        }
        $steps[] = "✅ Базовые роли созданы";

        // Создаем администратора
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, password, fullname, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['admin', $admin_password, 'Администратор системы', 'director']);
        $steps[] = "✅ Пользователь admin создан (пароль: admin123)";

        // Создаем тестового пользователя
        $user_password = password_hash('user123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, password, fullname, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['user', $user_password, 'Тестовый пользователь', 'manager']);
        $steps[] = "✅ Пользователь user создан (пароль: user123)";

        // Создаем тестовое оборудование
        $equipment_data = [
                ['Тахограф', 'Тахограф VDO DTCO 3283', 'TACHO001', 'Склад', 'active'],
                ['МТ-терминал', 'Терминал МТ-700', 'MT700001', 'Автопарк', 'active'],
                ['SIM-модем', 'Модем Sierra Wireless', 'MODEM001', 'Офис', 'maintenance']
        ];

        foreach ($equipment_data as $eq) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO equipment (type, name, serial_number, location, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute($eq);
        }
        $steps[] = "✅ Тестовое оборудование добавлено";

        // Создаем тестовые SIM-карты
        $sim_data = [
                ['79001234567', 'ООО \"ТрансАвто\"', 'А123БВ77', 'MT-700', 'Мониторинг', 'Активна', 'МТС', 'МТС Конорыгин'],
                ['79009876543', 'ИП Сидоров', 'В456ГД78', 'Тахограф', 'Тахография', 'Архив', 'Tele2', 'Tele2 Архив']
        ];

        foreach ($sim_data as $sim) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO sim_cards (number, client, car_number, terminal, system, status, operator, tab) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute($sim);
        }
        $steps[] = "✅ Тестовые SIM-карты добавлены";

        // Обновляем config.php с новыми настройками БД
        $config_content = "<?php
// Защита от прямого доступа
if (!defined('ROOT_DIR')) die('Доступ запрещен!');

// Динамический BASE_URL
if (!defined('APP_NAME')) define('APP_NAME', 'Smart-ITS');
if (!defined('BASE_URL')) {
    define('BASE_URL', (isset(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . \$_SERVER['HTTP_HOST'] . '/Smart-ITS');
}

// Фиксированная соль для безопасности
if (!defined('SALT')) {
    define('SALT', 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6');
}

// Настройки базы данных
if (!defined('DB_HOST')) define('DB_HOST', '$db_host');
if (!defined('DB_NAME')) define('DB_NAME', '$db_name');
if (!defined('DB_USER')) define('DB_USER', '$db_user');
if (!defined('DB_PASS')) define('DB_PASS', '$db_pass');

// Роли
\$roles = [];
if (!defined('ROLES')) {
    define('ROLES', \$roles ?: [
        'manager' => 'Менеджер',
        'specialist' => 'Специалист монтажных работ',
        'director' => 'Администратор'
    ]);
}

// Логирование ошибок
if (!defined('LOG_FILE')) define('LOG_FILE', __DIR__ . '/logs/errors.log');
ini_set('log_errors', 1);
ini_set('error_log', LOG_FILE);

if (!defined('FULLCALENDAR_CSS')) define('FULLCALENDAR_CSS', BASE_URL . '/assets/fullcalendar/main.min.css');
if (!defined('FULLCALENDAR_JS')) define('FULLCALENDAR_JS', BASE_URL . '/assets/fullcalendar/main.min.js');
?>";

        file_put_contents('config.php', $config_content);
        $steps[] = "✅ Настройки базы данных обновлены в config.php";

        // Создаем файл-маркер установки
        file_put_contents('installed.lock', date('Y-m-d H:i:s'));
        $steps[] = "✅ Установка завершена!";

        $success = "Установка успешно завершена!";
        $current_step = 3;

    } catch (PDOException $e) {
        $error = "Ошибка базы данных: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Установка Smart-ITS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px;
        }
        .install-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 800px;
            margin: 0 auto;
        }
        .install-header {
            background: linear-gradient(135deg, #1d3557, #457b9d);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .install-body {
            padding: 30px;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
        }
        .step.active {
            background: #457b9d;
            color: white;
        }
        .step.completed {
            background: #28a745;
            color: white;
        }
        .step-line {
            flex: 1;
            height: 3px;
            background: #e9ecef;
            margin: 0 5px;
            align-self: center;
        }
        .step-line.completed {
            background: #28a745;
        }
        .log-entry {
            padding: 10px;
            border-left: 4px solid #28a745;
            background: #f8f9fa;
            margin-bottom: 5px;
            border-radius: 0 5px 5px 0;
        }
        .log-entry.error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
    </style>
</head>
<body>
<div class="install-container">
    <div class="install-header">
        <h1><i class="bi bi-gear-fill"></i> Установка Smart-ITS</h1>
        <p class="mb-0">Система управления реестрами и активациями</p>
    </div>

    <div class="install-body">
        <!-- Индикатор шагов -->
        <div class="step-indicator">
            <div class="step <?php echo $current_step >= 1 ? 'completed' : 'active'; ?>">1</div>
            <div class="step-line <?php echo $current_step >= 2 ? 'completed' : ''; ?>"></div>
            <div class="step <?php echo $current_step >= 2 ? 'completed' : ($current_step == 2 ? 'active' : ''); ?>">2</div>
            <div class="step-line <?php echo $current_step >= 3 ? 'completed' : ''; ?>"></div>
            <div class="step <?php echo $current_step >= 3 ? 'completed' : ($current_step == 3 ? 'active' : ''); ?>">3</div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <h5>❌ Ошибка установки</h5>
                <?php echo htmlspecialchars($error); ?>
                <hr>
                <small>Проверьте:<br>
                    - Запущен ли MySQL сервер<br>
                    - Правильность логина/пароля MySQL<br>
                    - Существует ли база данных<br>
                    - Доступность хоста базы данных</small>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <h5>✅ Установка завершена!</h5>
                <?php echo $success; ?>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">Данные для входа:</h6>
                </div>
                <div class="card-body">
                    <p><strong>Администратор:</strong><br>
                        Логин: <code>admin</code><br>
                        Пароль: <code>admin123</code></p>

                    <p><strong>Тестовый пользователь:</strong><br>
                        Логин: <code>user</code><br>
                        Пароль: <code>user123</code></p>
                </div>
            </div>

            <div class="text-center">
                <a href="index.php" class="btn btn-success btn-lg">
                    <i class="bi bi-box-arrow-in-right"></i> Перейти к системе
                </a>
                <br>
                <small class="text-muted mt-2 d-block">
                    ❗ Удалите файл install.php после установки
                </small>
            </div>
        <?php elseif (!empty($steps)): ?>
            <!-- Шаг 2: Установка -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Ход установки:</h6>
                </div>
                <div class="card-body">
                    <?php foreach ($steps as $step): ?>
                        <div class="log-entry"><?php echo $step; ?></div>
                    <?php endforeach; ?>
                </div>
            </div>

            <form method="POST">
                <input type="hidden" name="db_host" value="<?php echo htmlspecialchars($_POST['db_host'] ?? 'localhost'); ?>">
                <input type="hidden" name="db_name" value="<?php echo htmlspecialchars($_POST['db_name'] ?? 'smartact_db'); ?>">
                <input type="hidden" name="db_user" value="<?php echo htmlspecialchars($_POST['db_user'] ?? 'root'); ?>">
                <input type="hidden" name="db_pass" value="<?php echo htmlspecialchars($_POST['db_pass'] ?? ''); ?>">
                <div class="text-center">
                    <button type="submit" class="btn btn-primary" name="continue_install">
                        Продолжить установку
                    </button>
                </div>
            </form>
        <?php else: ?>
            <!-- Шаг 1: Настройка БД -->
            <form method="POST">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Настройка базы данных</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Хост БД</label>
                                <input type="text" class="form-control" name="db_host" value="localhost" required>
                                <small class="form-text text-muted">Обычно localhost</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Имя базы данных</label>
                                <input type="text" class="form-control" name="db_name" value="smartact_db" required>
                                <small class="form-text text-muted">Будет создана автоматически</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Пользователь БД</label>
                                <input type="text" class="form-control" name="db_user" value="root" required>
                                <small class="form-text text-muted">Обычно root</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Пароль БД</label>
                                <input type="password" class="form-control" name="db_pass" value="">
                                <small class="form-text text-muted">По умолчанию пустой в XAMPP</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Информация об установке</h6>
                    </div>
                    <div class="card-body">
                        <p>Установщик выполнит следующие действия:</p>
                        <ul>
                            <li>✅ Проверит подключение к MySQL</li>
                            <li>✅ Создаст базу данных (если не существует)</li>
                            <li>✅ Создаст все необходимые таблицы</li>
                            <li>✅ Добавит базовые роли и пользователей</li>
                            <li>✅ Создаст тестовые данные</li>
                            <li>✅ Обновит конфигурацию системы</li>
                        </ul>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">
                        Начать установку
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>