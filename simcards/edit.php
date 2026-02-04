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

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];
$sim_card = getSimCard($id);

if (!$sim_card) {
    header('Location: index.php');
    exit;
}

$operators = ['МТС', 'Мегафон', 'Билайн', 'Tele2'];
$statuses = ['Установлены', 'Свободны', 'Удалено'];
$tabs = getAvailableTabs();
array_shift($tabs);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        ':number' => $_POST['number'],
        ':client' => $_POST['client'],
        ':car_number' => $_POST['car_number'],
        ':terminal' => $_POST['terminal'],
        ':system' => $_POST['system'],
        ':status' => $_POST['status'],
        ':operator' => $_POST['operator'],
        ':tab' => $_POST['tab'],
        ':id' => $id
    ];

    try {
        $sql = "UPDATE sim_cards SET 
                number = :number,
                client = :client, 
                car_number = :car_number, 
                terminal = :terminal, 
                system = :system, 
                status = :status, 
                operator = :operator,
                tab = :tab
                WHERE id = :id";

        $db->query($sql, $data);

        header('Location: index.php?updated=1');
        exit;
    } catch(PDOException $e) {
        $error = "Ошибка: " . $e->getMessage();
    }
}

require_once HEADER_FILE;
?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title"><i class="bi bi-pencil-square me-2"></i>Редактировать SIM-карту</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="mb-3">
                                <label for="number" class="form-label">Номер SIM*</label>
                                <input type="text" class="form-control" id="number" name="number"
                                       value="<?= htmlspecialchars($sim_card['number']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="client" class="form-label">Клиент</label>
                                <input type="text" class="form-control" id="client" name="client"
                                       value="<?= htmlspecialchars($sim_card['client']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="car_number" class="form-label">Гос. номер</label>
                                <input type="text" class="form-control" id="car_number" name="car_number"
                                       value="<?= htmlspecialchars($sim_card['car_number']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="terminal" class="form-label">Терминал</label>
                                <input type="text" class="form-control" id="terminal" name="terminal"
                                       value="<?= htmlspecialchars($sim_card['terminal']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="system" class="form-label">Система</label>
                                <input type="text" class="form-control" id="system" name="system"
                                       value="<?= htmlspecialchars($sim_card['system']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Статус</label>
                                <select class="form-select" id="status" name="status" required>
                                    <?php foreach($statuses as $status): ?>
                                        <option value="<?= $status ?>" <?= $status === $sim_card['status'] ? 'selected' : '' ?>>
                                            <?= $status ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="operator" class="form-label">Оператор</label>
                                <select class="form-select" id="operator" name="operator" required>
                                    <?php foreach($operators as $operator): ?>
                                        <option value="<?= $operator ?>" <?= $operator === $sim_card['operator'] ? 'selected' : '' ?>>
                                            <?= $operator ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="tab" class="form-label">Вкладка журнала*</label>
                                <select class="form-select" id="tab" name="tab" required>
                                    <?php foreach($tabs as $tab): ?>
                                        <option value="<?= $tab ?>" <?= $tab === $sim_card['tab'] ? 'selected' : '' ?>>
                                            <?= $tab ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> Сохранить
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i> Отмена
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once FOOTER_FILE; ?>