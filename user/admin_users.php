<?php
require_once __DIR__ . '/../paths.php';
require CONFIG_FILE;
require DB_FILE;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/login/login.php");
    exit();
}

// Проверка прав (только администратор)
$current_user = $db->get_user($_SESSION['username']);
if ($current_user['role'] !== 'director') {
    die("Доступ запрещен!");
}

// Обработка добавления нового пользователя
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $fullname = trim($_POST['fullname']);
    $role = $_POST['role'];

    if (!empty($username) && !empty($password) && !empty($fullname)) {
        // Проверка существования пользователя
        $existing = $db->get_user($username);
        if ($existing) {
            $_SESSION['error'] = "Пользователь с таким логином уже существует!";
        } else {
            // Создаем пользователя
            $db->create_user($username, $password, $fullname, $role);
            $_SESSION['success'] = "Пользователь успешно добавлен!";
        }
    } else {
        $_SESSION['error'] = "Все поля обязательны для заполнения!";
    }

    header("Location: admin_users.php");
    exit();
}

// Обработка действий с пользователями
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Нельзя удалить текущего пользователя
    if ($id !== $current_user['id']) {
        $db->query("DELETE FROM users WHERE id = ?", [$id]);
        $_SESSION['success'] = "Пользователь успешно удален!";
    } else {
        $_SESSION['error'] = "Вы не можете удалить свою учетную запись!";
    }
    header("Location: admin_users.php");
    exit();
}

// Получение всех пользователей
$users = $db->get_all_users();

// Сообщения об успехе/ошибке
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление пользователями - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-dark: #14213d;
            --primary: #1d3557;
            --accent: #e63946;
            --light: #f1faee;
            --secondary: #457b9d;
            --highlight: #a8dadc;
            --dark-bg: #0d1b2a;
            --card-bg: #1b263b;
            --text-light: #e0e1dd;
        }

        body {
            background-color: var(--dark-bg);
            color: var(--text-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(25, 40, 65, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .section-title {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 25px;
            color: var(--text-light);
            font-weight: 600;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--accent), var(--primary));
            border-radius: 3px;
        }

        .role-header {
            background: linear-gradient(90deg, var(--primary-dark), var(--primary));
            padding: 20px;
            border-radius: 12px 12px 0 0;
        }

        .role-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: white;
        }

        .role-subtitle {
            font-size: 1.1rem;
            opacity: 0.85;
            margin-bottom: 1.5rem;
        }

        .user-card {
            height: 100%;
            border-left: 4px solid var(--accent);
            transition: all 0.3s ease;
        }

        .user-card:hover {
            border-left: 4px solid var(--highlight);
        }

        .user-card-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--highlight);
        }

        .user-meta {
            font-size: 0.9rem;
            color: #a3b1c6;
            margin-bottom: 1rem;
        }

        .btn-modern {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(29, 53, 87, 0.4);
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }

        .btn-modern:hover {
            background: linear-gradient(135deg, var(--primary-dark), #0d1b2a);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(29, 53, 87, 0.6);
        }

        .btn-modern-outline {
            background: transparent;
            color: var(--highlight);
            border: 2px solid var(--highlight);
            padding: 0.5rem 1.2rem;
        }

        .btn-modern-outline:hover {
            background: rgba(168, 218, 220, 0.1);
        }

        .stats-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.9rem;
        }

        .action-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .action-table th {
            background: rgba(25, 40, 65, 0.8);
            color: var(--highlight);
            font-weight: 500;
            padding: 1rem;
            text-align: left;
        }

        .action-table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: var(--text-light);
        }

        .action-table tr:last-child td {
            border-bottom: none;
        }

        .action-table tr:hover td {
            background: rgba(255, 255, 255, 0.03);
        }

        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            margin: 2rem 0;
        }

        .modal-content {
            background: var(--card-bg);
            color: var(--text-light);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            padding: 0.75rem 1rem;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--secondary);
            color: white;
            box-shadow: 0 0 0 0.25rem rgba(69, 123, 157, 0.25);
        }

        .form-label {
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }

        .form-check-input {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .form-check-input:checked {
            background-color: var(--secondary);
            border-color: var(--secondary);
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.4rem;
            margin-right: 15px;
        }

        .alert-success {
            background: rgba(46, 204, 113, 0.15);
            border: 1px solid rgba(46, 204, 113, 0.2);
            color: #2ecc71;
        }

        .alert-danger {
            background: rgba(231, 76, 60, 0.15);
            border: 1px solid rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }

        .permissions-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .permissions-list li {
            padding: 0.6rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            transition: background 0.2s;
        }

        .permissions-list li:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .permissions-list li:last-child {
            border-bottom: none;
        }

        .permissions-list .bi {
            margin-right: 10px;
            color: var(--highlight);
            font-size: 1.2rem;
        }

        .search-container {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .search-container .form-control {
            border-radius: 50px;
            padding-left: 2.5rem;
        }

        .search-container .bi {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #a3b1c6;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
<?php include HEADER_FILE; ?>

<div class="container py-5">
    <div class="glass-card p-4 p-lg-5 mb-5">
        <div class="role-header rounded">
            <h1 class="role-title">Управление пользователями</h1>
            <p class="role-subtitle">Администрирование учетных записей системы</p>

            <div class="d-flex flex-wrap gap-3">
                    <span class="stats-badge">
                        <i class="bi bi-people-fill"></i>
                        <?= count($users) ?> пользователей
                    </span>
                <span class="stats-badge">
                        <i class="bi bi-shield-lock"></i>
                        <?= count(array_filter($users, fn($u) => $u['role'] === 'director')) ?> администраторов
                    </span>
                <span class="stats-badge">
                        <i class="bi bi-person-gear"></i>
                        <?= count(array_filter($users, fn($u) => $u['role'] === 'manager')) ?> менеджеров
                    </span>
                <span class="stats-badge">
                        <i class="bi bi-tools"></i>
                        <?= count(array_filter($users, fn($u) => $u['role'] === 'specialist')) ?> специалистов
                    </span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Сообщения об ошибках/успехе -->
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i> <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i> <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between mb-4">
            <div class="search-container w-50">
                <i class="bi bi-search"></i>
                <input type="text" id="userSearch" class="form-control" placeholder="Поиск пользователей...">
            </div>

            <button class="btn-modern" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-plus-lg"></i> Добавить пользователя
            </button>
        </div>

        <h3 class="section-title">Список пользователей</h3>

        <div class="row g-4">
            <?php foreach ($users as $user):
                $roleName = ROLES[$user['role']] ?? $user['role'];
                $isCurrent = ($user['id'] === $current_user['id']);
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="glass-card user-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start mb-4">
                                <div class="user-avatar">
                                    <?= mb_substr($user['fullname'], 0, 1) ?>
                                </div>
                                <div>
                                    <h5 class="user-card-title mb-1"><?= htmlspecialchars($user['fullname']) ?></h5>
                                    <div class="user-meta">
                                        <div>@<?= htmlspecialchars($user['username']) ?></div>
                                        <div class="mt-1">Зарегистрирован: <?= date('d.m.Y', strtotime($user['created_at'])) ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mb-4">
                                    <span class="stats-badge">
                                        <i class="bi bi-person-badge"></i>
                                        <?= $roleName ?>
                                    </span>

                                <span class="stats-badge">
                                        <i class="bi bi-shield-check"></i>
                                        <?= $isCurrent ? 'Текущий пользователь' : 'Активен' ?>
                                    </span>
                            </div>

                            <h6 class="mt-4 mb-3">Права доступа:</h6>
                            <ul class="permissions-list">
                                <li>
                                    <i class="bi bi-journal-check"></i>
                                    Просмотр системы
                                </li>
                                <?php if ($user['role'] === 'director'): ?>
                                    <li>
                                        <i class="bi bi-people"></i>
                                        Управление пользователями
                                    </li>
                                    <li>
                                        <i class="bi bi-shield-lock"></i>
                                        Управление ролями
                                    </li>
                                <?php endif; ?>
                                <?php if ($user['role'] === 'manager'): ?>
                                    <li>
                                        <i class="bi bi-clipboard-data"></i>
                                        Управление активациями
                                    </li>
                                    <li>
                                        <i class="bi bi-file-earmark-text"></i>
                                        Формирование отчетов
                                    </li>
                                <?php endif; ?>
                                <?php if ($user['role'] === 'specialist'): ?>
                                    <li>
                                        <i class="bi bi-tools"></i>
                                        Работа с оборудованием
                                    </li>
                                    <li>
                                        <i class="bi bi-calendar-check"></i>
                                        Выполнение заданий
                                    </li>
                                <?php endif; ?>
                            </ul>

                            <div class="d-flex gap-2 mt-4">
                                <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn-modern btn-modern-outline">
                                    <i class="bi bi-pencil"></i> Изменить
                                </a>
                                <?php if (!$isCurrent): ?>
                                    <a href="#" class="btn-modern btn-modern-outline text-danger"
                                       data-bs-toggle="modal" data-bs-target="#deleteModal"
                                       data-userid="<?= $user['id'] ?>">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="divider"></div>

        <h3 class="section-title mt-5">Действия</h3>

        <div class="glass-card p-4">
            <table class="action-table">
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Дата</th>
                    <th>Фактор</th>
                    <th>Размер</th>
                    <th>Бренды</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Приказ Дилетана Администрации</td>
                    <td>Молодые</td>
                    <td>Натуральный</td>
                    <td>Неглупая</td>
                    <td>Неглупая</td>
                </tr>
                <tr>
                    <td>Ротация управления</td>
                    <td>15.07.2023</td>
                    <td>Автоматический</td>
                    <td>Средний</td>
                    <td>Системный</td>
                </tr>
                <tr>
                    <td>Обновление прав</td>
                    <td>20.07.2023</td>
                    <td>Ручной</td>
                    <td>Полный</td>
                    <td>Административный</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Модальное окно добавления пользователя -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Добавить нового пользователя</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Полное имя</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" required>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Логин</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label">Роль</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="director">Администратор</option>
                            <option value="manager">Менеджер</option>
                            <option value="specialist">Специалист</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modern btn-modern-outline" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" name="add_user" class="btn-modern">Создать пользователя</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно удаления -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Подтверждение удаления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите удалить этого пользователя?</p>
                <p class="text-danger"><strong>Это действие нельзя отменить!</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modern btn-modern-outline" data-bs-dismiss="modal">Отмена</button>
                <a href="#" id="confirmDelete" class="btn-modern bg-danger border-danger">Удалить</a>
            </div>
        </div>
    </div>
</div>

<?php include FOOTER_FILE; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Обработка модального окна удаления
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const userId = button.getAttribute('data-userid');
        const confirmDelete = document.getElementById('confirmDelete');
        confirmDelete.href = `admin_users.php?delete=${userId}`;
    });

    // Поиск пользователей
    document.getElementById('userSearch').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.glass-card.user-card').forEach(card => {
            const userName = card.querySelector('.user-card-title').textContent.toLowerCase();
            const userLogin = card.querySelector('.user-meta div:first-child').textContent.toLowerCase();

            if (userName.includes(searchTerm) || userLogin.includes(searchTerm)) {
                card.parentElement.style.display = 'block';
            } else {
                card.parentElement.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>