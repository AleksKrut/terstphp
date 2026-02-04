<?php
// В самом начале файла - старт сессии и проверка авторизации
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../config.php';

// Проверка авторизации ДО любого вывода
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'director') {
    header('Location: ../dashboard.php');
    exit;
}

// Только после проверки подключаем header.php
require_once __DIR__ . '/../header.php';

$user_id = $_GET['id'] ?? 0;
$db = new Database();
$user = $db->query("SELECT * FROM users WHERE id = ?", [$user_id])->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: ../user/admin_users.php');  // Добавил ../ для корректного пути
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $role = $_POST['role'] ?? '';
    $new_password = trim($_POST['new_password'] ?? '');

    if (empty($fullname) || empty($role)) {
        $error = "Обязательные поля не заполнены";
    } else {
        $params = [$fullname, $role, $user_id];
        $sql = "UPDATE users SET fullname = ?, role = ?";

        if (!empty($new_password)) {
            $sql .= ", password = ?";
            $params[] = password_hash($new_password, PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = ?";
        $db->query($sql, $params);
        $success = "Пользователь успешно обновлен!";
        $user = $db->query("SELECT * FROM users WHERE id = ?", [$user_id])->fetch(PDO::FETCH_ASSOC);
    }
}
?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card dashboard-card">
                    <div class="card-header card-header-custom">
                        <h4 class="mb-0">Редактирование пользователя</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Логин</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Полное имя</label>
                                <input type="text" name="fullname" class="form-control"
                                       value="<?= htmlspecialchars($user['fullname']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Должность</label>
                                <select name="role" class="form-select" required>
                                    <?php foreach (ROLES as $key => $value): ?>
                                        <option value="<?= $key ?>" <?= $key === $user['role'] ? 'selected' : '' ?>>
                                            <?= $value ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Новый пароль</label>
                                <input type="password" name="new_password" class="form-control">
                                <small class="text-muted">Оставьте пустым, если не нужно менять</small>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Сохранить изменения</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/../footer.php'; ?>