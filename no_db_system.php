<?php
// no_db_system.php - Система без базы данных
session_start();

// Простая система аутентификации без БД
$users = [
    'admin' => [
        'password' => 'admin123',
        'fullname' => 'Администратор системы',
        'role' => 'director'
    ],
    'user' => [
        'password' => 'user123',
        'fullname' => 'Тестовый пользователь',
        'role' => 'manager'
    ]
];

// Обработка входа
if ($_POST['login'] ?? false) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (isset($users[$username]) && $users[$username]['password'] === $password) {
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = $username;
        $_SESSION['fullname'] = $users[$username]['fullname'];
        $_SESSION['role'] = $users[$username]['role'];
        header('Location: no_db_system.php');
        exit;
    } else {
        $error = "Неверные учетные данные";
    }
}

// Выход
if ($_GET['logout'] ?? false) {
    session_destroy();
    header('Location: no_db_system.php');
    exit;
}

// Если не авторизован - показать форму входа
if (!isset($_SESSION['user_id'])) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Вход - Smart-ITS</title>
        <style>
            body { font-family: Arial; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; }
            .login-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 300px; }
            .form-group { margin-bottom: 15px; }
            label { display: block; margin-bottom: 5px; }
            input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
            button { background: #007bff; color: white; padding: 10px; border: none; border-radius: 4px; width: 100%; cursor: pointer; }
            .error { color: red; margin-bottom: 15px; }
        </style>
    </head>
    <body>
    <div class="login-box">
        <h2>Вход в Smart-ITS</h2>
        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <input type="hidden" name="login" value="1">
            <div class="form-group">
                <label>Логин:</label>
                <input type="text" name="username" value="admin" required>
            </div>
            <div class="form-group">
                <label>Пароль:</label>
                <input type="password" name="password" value="admin123" required>
            </div>
            <button type="submit">Войти</button>
        </form>
        <p style="margin-top: 20px; font-size: 12px; color: #666;">
            Тестовые данные:<br>
            Логин: <strong>admin</strong>, Пароль: <strong>admin123</strong><br>
            Логин: <strong>user</strong>, Пароль: <strong>user123</strong>
        </p>
    </div>
    </body>
    </html>
    <?php
    exit;
}

// Основной интерфейс системы
$current_user = [
    'username' => $_SESSION['username'],
    'fullname' => $_SESSION['fullname'],
    'role' => $_SESSION['role']
];

$active_tab = $_GET['tab'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart-ITS - Без базы данных</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .navbar-brand { font-weight: 700; }
        .nav-tabs .nav-link { border: none; padding: 1rem 1.5rem; }
        .nav-tabs .nav-link.active { border-bottom: 3px solid #007bff; color: #007bff; font-weight: 600; }
        .tab-content { background: white; border-radius: 0 10px 10px 10px; padding: 20px; margin-top: -1px; }
        .module-card { background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand">Smart-ITS (режим без БД)</span>
        <div class="d-flex align-items-center text-white">
            <span class="me-3"><?= $current_user['fullname'] ?></span>
            <a href="?logout=1" class="btn btn-outline-light btn-sm">Выход</a>
        </div>
    </div>
</nav>

<div class="container-fluid mt-3">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link <?= $active_tab === 'dashboard' ? 'active' : '' ?>" href="?tab=dashboard">Дашборд</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $active_tab === 'equipment' ? 'active' : '' ?>" href="?tab=equipment">Оборудование</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $active_tab === 'simcards' ? 'active' : '' ?>" href="?tab=simcards">SIM-карты</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $active_tab === 'acts' ? 'active' : '' ?>" href="?tab=acts">Акты</a>
        </li>
    </ul>

    <div class="tab-content">
        <?php if ($active_tab === 'dashboard'): ?>
            <h4>Дашборд системы</h4>
            <div class="alert alert-warning">
                <strong>Внимание:</strong> Система работает в режиме без базы данных. Все данные хранятся в сессии и будут потеряны после выхода.
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="module-card">
                        <h5>Статистика</h5>
                        <p>Оборудование: <strong>12</strong> единиц</p>
                        <p>SIM-карты: <strong>8</strong> активных</p>
                        <p>Акты за месяц: <strong>5</strong></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="module-card">
                        <h5>Последние действия</h5>
                        <ul>
                            <li>Создан акт диагностики #001</li>
                            <li>Добавлено новое оборудование</li>
                            <li>Обновлены SIM-карты МТС</li>
                        </ul>
                    </div>
                </div>
            </div>

        <?php elseif ($active_tab === 'equipment'): ?>
            <h4>Оборудование</h4>
            <div class="module-card">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Тип</th>
                        <th>Название</th>
                        <th>Серийный номер</th>
                        <th>Статус</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Тахограф</td>
                        <td>VDO DTCO 3283</td>
                        <td>TACHO001</td>
                        <td><span class="badge bg-success">Активен</span></td>
                    </tr>
                    <tr>
                        <td>МТ-терминал</td>
                        <td>МТ-700</td>
                        <td>MT700001</td>
                        <td><span class="badge bg-success">Активен</span></td>
                    </tr>
                    </tbody>
                </table>
            </div>

        <?php elseif ($active_tab === 'simcards'): ?>
            <h4>SIM-карты</h4>
            <div class="module-card">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Номер</th>
                        <th>Оператор</th>
                        <th>Клиент</th>
                        <th>Статус</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>79001234567</td>
                        <td>МТС</td>
                        <td>ООО "ТрансАвто"</td>
                        <td><span class="badge bg-success">Активна</span></td>
                    </tr>
                    <tr>
                        <td>79009876543</td>
                        <td>Tele2</td>
                        <td>ИП Сидоров</td>
                        <td><span class="badge bg-secondary">Архив</span></td>
                    </tr>
                    </tbody>
                </table>
            </div>

        <?php elseif ($active_tab === 'acts'): ?>
            <h4>Акты работ</h4>
            <div class="module-card">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5>Акт диагностики ТАХО</h5>
                                <button class="btn btn-primary">Создать</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5>Акт диагностики МТ</h5>
                                <button class="btn btn-primary">Создать</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5>Акт выполненных работ</h5>
                                <button class="btn btn-primary">Создать</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>