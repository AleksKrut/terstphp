<?php
require_once __DIR__ . '/../db.php';

function getSimCards($tab = 'Все', $search = '') {
    global $db;

    $sql = "SELECT * FROM sim_cards";
    $params = [];
    $where = [];

    if ($tab != 'Все') {
        $where[] = "tab = ?";
        $params[] = $tab;
    }

    if ($search) {
        $where[] = "(number LIKE ? OR client LIKE ? OR car_number LIKE ? OR terminal LIKE ?)";
        $searchTerm = "%$search%";
        array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    }

    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $stmt = $db->query($sql, $params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAvailableTabs() {
    global $db;
    global $tabs_structure;

    try {
        $stmt = $db->query("SELECT DISTINCT tab FROM sim_cards WHERE tab IS NOT NULL AND tab != ''");
        $db_tabs = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $all_tabs = ['Все'];
        foreach ($tabs_structure as $operator => $tabs) {
            $all_tabs = array_merge($all_tabs, $tabs);
        }

        return array_values(array_intersect($all_tabs, array_merge(['Все'], $db_tabs)));
    } catch (PDOException $e) {
        return array_merge(['Все'], ...array_values($tabs_structure));
    }
}

function getSimCard($id) {
    global $db;
    $stmt = $db->query("SELECT * FROM sim_cards WHERE id = ?", [$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function importSimCards($rows) {
    global $db;

    $imported = 0;
    $updated = 0;
    $tabsCreated = 0;
    $existingTabs = [];

    try {
        $stmt = $db->query("SELECT DISTINCT tab FROM sim_cards");
        $existingTabs = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $db->beginTransaction();

        foreach ($rows as $row) {
            if (empty($row[0])) continue;

            $data = [
                ':number' => $row[0],
                ':client' => $row[1] ?? '',
                ':car_number' => $row[2] ?? '',
                ':terminal' => $row[3] ?? '',
                ':system' => $row[4] ?? '',
                ':status' => normalizeStatus($row[5] ?? 'Установлены'),
                ':operator' => $row[6] ?? 'МТС',
                ':tab' => $row[7] ?? 'Основная'
            ];

            if (!in_array($data[':tab'], $existingTabs)) {
                $existingTabs[] = $data[':tab'];
                $tabsCreated++;
            }

            $stmt = $db->query("SELECT id FROM sim_cards WHERE number = :number", [':number' => $data[':number']]);
            $existing = $stmt->fetch();

            if ($existing) {
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
                $data[':id'] = $existing['id'];

                $stmt = $db->query($sql, $data);
                $updated++;
            } else {
                $sql = "INSERT INTO sim_cards 
                        (number, client, car_number, terminal, system, status, operator, tab) 
                        VALUES (:number, :client, :car_number, :terminal, :system, :status, :operator, :tab)";

                $stmt = $db->query($sql, $data);
                $imported++;
            }
        }

        $db->commit();

        return [
            'imported' => $imported,
            'updated' => $updated,
            'tabs_created' => $tabsCreated
        ];
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}

function getStatusClass($status) {
    if (empty($status)) return '';
    $status = mb_strtolower($status);
    if (strpos($status, 'установ') !== false) return 'installed';
    if (strpos($status, 'свобод') !== false) return 'free';
    if (strpos($status, 'удал') !== false) return 'deleted';
    return '';
}

function normalizeStatus($status) {
    $status = mb_strtolower(trim($status));
    if (strpos($status, 'установ') !== false) return 'Установлены';
    if (strpos($status, 'свобод') !== false) return 'Свободны';
    if (strpos($status, 'удал') !== false) return 'Удалено';
    return 'Установлены';
}

function deleteSimCard($id) {
    global $db;
    try {
        $stmt = $db->query("DELETE FROM sim_cards WHERE id = ?", [$id]);
        return $stmt->rowCount() > 0;
    } catch(PDOException $e) {
        error_log("Ошибка удаления: " . $e->getMessage());
        return false;
    }
}
?>