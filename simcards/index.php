<?php
require_once __DIR__ . '/../paths.php';
require_once CONFIG_FILE;
require_once DB_FILE;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/login/login.php");
    exit();
}

// Проверка прав доступа (ИСПРАВЛЕННЫЙ ВАРИАНТ)
$allowed_roles = ['director', 'manager', 'admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Доступ запрещен!");
}

require_once __DIR__ . '/functions.php';

$current_tab = $_GET['tab'] ?? 'Все';
$search = $_GET['search'] ?? '';

try {
    $sim_cards = getSimCards($current_tab, $search);
    $tabs = getAvailableTabs();
} catch (PDOException $e) {
    die("Ошибка при загрузке данных: " . $e->getMessage());
}

require_once HEADER_FILE;
?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-3">
                    <div class="container-fluid">
                        <a class="navbar-brand" href="index.php">Учет SIM-карт</a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav me-auto">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="mtsDropdown" role="button" data-bs-toggle="dropdown">
                                        МТС
                                    </a>
                                    <div class="dropdown-menu">
                                        <?php foreach ($tabs_structure['МТС'] as $mtstab): ?>
                                            <a class="dropdown-item <?= $mtstab === $current_tab ? 'active' : '' ?>"
                                               href="?tab=<?= urlencode($mtstab) ?>&search=<?= urlencode($search) ?>">
                                                <?= htmlspecialchars($mtstab) ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </li>

                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="tele2Dropdown" role="button" data-bs-toggle="dropdown">
                                        Tele2
                                    </a>
                                    <div class="dropdown-menu">
                                        <?php foreach ($tabs_structure['Tele2'] as $tele2tab): ?>
                                            <a class="dropdown-item <?= $tele2tab === $current_tab ? 'active' : '' ?>"
                                               href="?tab=<?= urlencode($tele2tab) ?>&search=<?= urlencode($search) ?>">
                                                <?= htmlspecialchars($tele2tab) ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </li>

                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="legendDropdown" role="button" data-bs-toggle="dropdown">
                                        Легенда
                                    </a>
                                    <div class="dropdown-menu p-3" style="min-width: 250px;">
                                        <div class="legend-item mb-2">
                                            <span class="legend-box installed-legend"></span> Установлены
                                        </div>
                                        <div class="legend-item mb-2">
                                            <span class="legend-box free-legend"></span> Свободны
                                        </div>
                                        <div class="legend-item">
                                            <span class="legend-box deleted-legend"></span> Удалено
                                        </div>
                                    </div>
                                </li>
                            </ul>

                            <div class="d-flex">
                                <a href="add.php" class="btn btn-light btn-sm me-2">
                                    <i class="bi bi-plus-circle"></i> Добавить
                                </a>
                                <a href="import.php" class="btn btn-warning btn-sm me-2">
                                    <i class="bi bi-upload"></i> Импорт
                                </a>
                                <a href="export.php?tab=<?= urlencode($current_tab) ?>&search=<?= urlencode($search) ?>"
                                   class="btn btn-info btn-sm">
                                    <i class="bi bi-download"></i> Экспорт
                                </a>
                            </div>
                        </div>
                    </div>
                </nav>

                <div class="card">
                    <div class="card-body">
                        <form method="get" class="mb-3">
                            <input type="hidden" name="tab" value="<?= htmlspecialchars($current_tab) ?>">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Поиск по номеру, клиенту, гос. номеру, терминалу..."
                                       value="<?= htmlspecialchars($search) ?>">
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="table-light">
                                <tr>
                                    <th>Номер SIM</th>
                                    <th>Клиент</th>
                                    <th class="d-none d-sm-table-cell">Гос. номер</th>
                                    <th class="d-none d-md-table-cell">Терминал</th>
                                    <th class="d-none d-lg-table-cell">Система</th>
                                    <th>Статус</th>
                                    <th class="d-none d-sm-table-cell">Оператор</th>
                                    <th class="d-none d-md-table-cell">Вкладка</th>
                                    <th>Действия</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($sim_cards as $sim): ?>
                                    <tr class="<?= getStatusClass($sim['status']) ?>">
                                        <td><?= htmlspecialchars($sim['number']) ?></td>
                                        <td><?= htmlspecialchars($sim['client']) ?></td>
                                        <td class="d-none d-sm-table-cell"><?= htmlspecialchars($sim['car_number']) ?></td>
                                        <td class="d-none d-md-table-cell"><?= htmlspecialchars($sim['terminal']) ?></td>
                                        <td class="d-none d-lg-table-cell"><?= htmlspecialchars($sim['system']) ?></td>
                                        <td>
                                            <span class="d-inline d-md-none"><?= mb_substr($sim['status'], 0, 3) ?>.</span>
                                            <span class="d-none d-md-inline"><?= htmlspecialchars($sim['status']) ?></span>
                                        </td>
                                        <td class="d-none d-sm-table-cell"><?= htmlspecialchars($sim['operator']) ?></td>
                                        <td class="d-none d-md-table-cell"><?= htmlspecialchars($sim['tab']) ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="edit.php?id=<?= $sim['id'] ?>" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="delete.php?id=<?= $sim['id'] ?>" class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Вы уверены?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light d-flex flex-wrap justify-content-between align-items-center">
                        <div class="mb-2 mb-md-0">
                            <span class="badge bg-primary rounded-pill"><?= count($sim_cards) ?></span> записей
                        </div>
                        <div class="text-muted small">
                            Вкладка: <span class="fw-bold"><?= htmlspecialchars($current_tab) ?></span>
                            <?php if (!empty($search)): ?>
                                | Поиск: "<span class="fw-bold"><?= htmlspecialchars($search) ?></span>"
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once FOOTER_FILE; ?>