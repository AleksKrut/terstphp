<?php
require_once __DIR__ . '/../paths.php';
require CONFIG_FILE;
require DB_FILE;

// Проверка сессии
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

// Обработка действий
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_role'])) {
        $roleName = trim($_POST['role_name']);
        $permissions = isset($_POST['permissions']) ? implode(',', $_POST['permissions']) : '';

        if (!empty($roleName)) {
            $db->createRole($roleName, $permissions);
            header("Location: admin_roles.php");
            exit();
        }
    }
    elseif (isset($_POST['update_role'])) {
        $roleId = (int)$_POST['role_id'];
        $roleName = trim($_POST['role_name']);
        $permissions = isset($_POST['permissions']) ? implode(',', $_POST['permissions']) : '';

        if (!empty($roleName) && $roleId > 0) {
            $db->updateRole($roleId, $roleName, $permissions);
            header("Location: admin_roles.php");
            exit();
        }
    }
}

if (isset($_GET['delete'])) {
    $roleId = (int)$_GET['delete'];
    if ($roleId > 0) {
        $db->deleteRole($roleId);
        header("Location: admin_roles.php");
        exit();
    }
}

// Получаем все роли
$roles = $db->getRoles();

// Возможные разрешения
$allPermissions = [
    'manage_users' => 'Управление пользователями',
    'manage_equipment' => 'Управление оборудованием',
    'manage_activation' => 'Управление активациями',
    'view_reports' => 'Просмотр отчетов',
    'manage_settings' => 'Управление настройками'
];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление ролями - <?= APP_NAME ?></title>
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

        .role-card {
            height: 100%;
            border-left: 4px solid var(--accent);
            transition: all 0.3s ease;
        }

        .role-card:hover {
            border-left: 4px solid var(--highlight);
        }

        .role-card-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--highlight);
        }

        .role-meta {
            font-size: 0.9rem;
            color: #a3b1c6;
            margin-bottom: 1rem;
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

        .note-card {
            background: rgba(69, 123, 157, 0.2);
            border-left: 4px solid var(--secondary);
            padding: 1.5rem;
            border-radius: 8px;
            margin: 2rem 0;
        }

        .note-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.8rem;
            color: var(--highlight);
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
    </style>
</head>
<body>
<?php include HEADER_FILE; ?>

<div class="container py-5">
    <div class="glass-card p-4 p-lg-5 mb-5">
        <div class="role-header rounded">
            <h1 class="role-title">Управление ролями</h1>
            <p class="role-subtitle">Администрирование ролей и прав доступа системы</p>

            <div class="d-flex flex-wrap gap-3">
                    <span class="stats-badge">
                        <i class="bi bi-people-fill"></i>
                        <?= count($roles) ?> ролей
                    </span>
                <span class="stats-badge">
                        <i class="bi bi-shield-lock"></i>
                        <?= count($allPermissions) ?> типов прав
                    </span>
            </div>
        </div>

        <div class="divider"></div>

        <div class="note-card">
            <h3 class="note-title">Примечание</h3>
            <p>Возможно, что выходная рота должна быть использована в соответствии с учётом организации.</p>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <button class="btn-modern" data-bs-toggle="modal" data-bs-target="#roleModal">
                <i class="bi bi-plus-lg"></i> Добавить роль
            </button>
        </div>

        <?php if (isset($_GET['edit'])):
            $roleId = (int)$_GET['edit'];
            $role = $db->getRole($roleId);
            $rolePermissions = $role ? explode(',', $role['permissions']) : [];
            ?>
            <div class="glass-card p-4 mb-5">
                <h3 class="section-title">Редактирование роли: <?= htmlspecialchars($role['name'] ?? '') ?></h3>

                <form method="POST">
                    <input type="hidden" name="role_id" value="<?= $roleId ?>">

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="roleName" class="form-label">Название роли</label>
                            <input type="text" class="form-control" id="roleName" name="role_name"
                                   value="<?= htmlspecialchars($role['name'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Права доступа</label>
                        <div class="row">
                            <?php foreach ($allPermissions as $key => $label): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="permissions[]" value="<?= $key ?>"
                                               id="perm-<?= $key ?>"
                                            <?= in_array($key, $rolePermissions) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="perm-<?= $key ?>">
                                            <?= $label ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <button type="submit" name="update_role" class="btn-modern">
                            <i class="bi bi-save me-1"></i> Сохранить
                        </button>
                        <a href="admin_roles.php" class="btn-modern btn-modern-outline">
                            <i class="bi bi-x-lg me-1"></i> Отмена
                        </a>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <h3 class="section-title">Список ролей</h3>

        <div class="row g-4">
            <?php foreach ($roles as $role):
                $rolePermissions = $role['permissions'] ? explode(',', $role['permissions']) : [];
                $permissionCount = count($rolePermissions);
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="glass-card role-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="role-card-title"><?= htmlspecialchars($role['name']) ?></h5>
                                    <div class="role-meta">
                                        Создана: <?= date('d.m.Y', strtotime($role['created_at'])) ?>
                                    </div>
                                </div>
                                <span class="badge bg-primary">ID: <?= $role['id'] ?></span>
                            </div>

                            <div class="d-flex gap-2 mb-4">
                                    <span class="stats-badge">
                                        <i class="bi bi-shield-check"></i>
                                        <?= $permissionCount ?> прав
                                    </span>
                                <span class="stats-badge">
                                        <i class="bi bi-person-check"></i>
                                        <?= $db->countUsersByRoleName($role['name']) ?> пользоват.
                                    </span>
                            </div>

                            <?php if ($permissionCount > 0): ?>
                                <h6 class="mt-4 mb-3">Основные права:</h6>
                                <ul class="permissions-list">
                                    <?php
                                    $displayed = 0;
                                    foreach ($rolePermissions as $permKey):
                                        if ($displayed < 3 && isset($allPermissions[$permKey])):
                                            ?>
                                            <li>
                                                <i class="bi bi-check2-circle"></i>
                                                <?= $allPermissions[$permKey] ?>
                                            </li>
                                            <?php
                                            $displayed++;
                                        endif;
                                    endforeach;
                                    ?>
                                    <?php if ($permissionCount > 3): ?>
                                        <li class="text-muted">
                                            + ещё <?= $permissionCount - 3 ?> прав
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            <?php else: ?>
                                <div class="alert alert-dark mt-3">
                                    <i class="bi bi-info-circle me-2"></i> Нет назначенных прав
                                </div>
                            <?php endif; ?>

                            <div class="d-flex gap-2 mt-4">
                                <a href="admin_roles.php?edit=<?= $role['id'] ?>" class="btn-modern btn-modern-outline">
                                    <i class="bi bi-pencil"></i> Изменить
                                </a>
                                <?php if (!in_array($role['name'], ['director', 'manager', 'specialist'])): ?>
                                    <a href="#" class="btn-modern btn-modern-outline text-danger"
                                       data-bs-toggle="modal" data-bs-target="#deleteModal"
                                       data-roleid="<?= $role['id'] ?>">
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

<!-- Модальное окно добавления роли -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalLabel">Добавить новую роль</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label for="newRoleName" class="form-label">Название роли</label>
                        <input type="text" class="form-control" id="newRoleName" name="role_name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Права доступа</label>
                        <div class="row">
                            <?php foreach ($allPermissions as $key => $label): ?>
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="permissions[]" value="<?= $key ?>"
                                               id="newPerm-<?= $key ?>">
                                        <label class="form-check-label" for="newPerm-<?= $key ?>">
                                            <?= $label ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modern btn-modern-outline" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" name="create_role" class="btn-modern">Создать роль</button>
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
                <p>Вы уверены, что хотите удалить эту роль?</p>
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
        const roleId = button.getAttribute('data-roleid');
        const confirmDelete = document.getElementById('confirmDelete');
        confirmDelete.href = `admin_roles.php?delete=${roleId}`;
    });
</script>
</body>
</html>