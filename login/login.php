<?php
require_once __DIR__ . '/../paths.php';
if (!defined('ROOT_DIR')) die('ROOT_DIR not defined!');
require_once ROOT_DIR . '/config.php';
require_once ROOT_DIR . '/db.php';
require_once ROOT_DIR . '/login/logger.php';

session_start();

// Если уже авторизован - редирект
if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/../desktop.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        $db = new Database();
        $user = $db->get_user($username);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];

            Logger::log('login_success', "Успешный вход в систему");
            header('Location: ' . BASE_URL . '/../desktop.php');
            exit;
        } else {
            Logger::log('login_failed', "Неудачная попытка входа для пользователя: $username");
            $error = "Неверные учетные данные";
        }
    } catch (Exception $e) {
        error_log('Login error: ' . $e->getMessage());
        $error = "Ошибка системы. Пожалуйста, попробуйте позже.";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход | <?= htmlspecialchars(APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
            --glass: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        body {
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #1a2a6c);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, sans-serif;
            margin: 0;
            padding: 20px;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            perspective: 1000px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25),
            0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            transform-style: preserve-3d;
            transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .login-card:hover {
            transform: translateY(-5px) rotateX(5deg);
        }

        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
            transform: translateZ(20px);
        }

        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
            margin-bottom: 1.5rem;
            transition: transform 0.5s ease;
        }

        .logo:hover {
            transform: scale(1.1) rotate(15deg);
        }

        .logo i {
            font-size: 2.5rem;
            color: white;
        }

        h4 {
            color: white;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            margin-bottom: 0;
            transform: translateZ(20px);
        }

        .form-label {
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
            margin-bottom: 0.5rem;
            transform: translateZ(20px);
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: white;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            transform: translateZ(20px);
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
            color: white;
            outline: none;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            border: none;
            border-radius: 12px;
            padding: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform: translateZ(20px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            transform: translateY(-3px) translateZ(20px);
            box-shadow: 0 12px 25px rgba(67, 97, 238, 0.4);
        }

        .btn-primary:active {
            transform: translateY(1px) translateZ(20px);
        }

        .btn-primary::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                    rgba(255, 255, 255, 0.2),
                    rgba(255, 255, 255, 0)
            );
            transform: rotate(30deg);
            transition: all 0.6s ease;
        }

        .btn-primary:hover::after {
            transform: rotate(30deg) translate(20%, 20%);
        }

        .alert {
            border-radius: 12px;
            transform: translateZ(20px);
        }

        .footer-note {
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.85rem;
            margin-top: 2rem;
            transform: translateZ(20px);
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-card">
        <div class="logo-container">
            <div class="logo">
                <i class="bi bi-shield-lock"></i>
            </div>
            <h4>Добро пожаловать в <?= htmlspecialchars(APP_NAME) ?></h4>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Имя пользователя</label>
                <input type="text" class="form-control" name="username" id="username" required autofocus
                       placeholder="Введите ваш логин">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input type="password" class="form-control" name="password" id="password" required
                       placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary w-100">Войти в систему</button>
        </form>
        <div class="footer-note">
            &copy; <?= date('Y') ?> Smart-ITS | Версия 1.0
        </div>
    </div>
</div>
</body>
</html>