<?php
require_once __DIR__ . '/paths.php';
require CONFIG_FILE;

class Database {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
                DB_USER,
                DB_PASS
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->create_tables();
        } catch (PDOException $e) {
            die("Ошибка подключения: " . $e->getMessage());
        }
    }

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    private function create_tables() {
        // Таблица пользователей
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                fullname VARCHAR(100) NOT NULL,
                role VARCHAR(20) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        // Таблица оборудования
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS equipment (
                id INT AUTO_INCREMENT PRIMARY KEY,
                type VARCHAR(100) NOT NULL,
                name VARCHAR(100) NOT NULL,
                serial_number VARCHAR(50) UNIQUE,
                location VARCHAR(100),
                status ENUM('active', 'inactive', 'maintenance') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        // Таблица ролей
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS roles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL UNIQUE,
                permissions TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        // Таблица событий
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS events (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        // ДОБАВЛЕНА ТАБЛИЦА ДЛЯ SIM-КАРТ
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS sim_cards (
                id INT AUTO_INCREMENT PRIMARY KEY,
                number VARCHAR(50) UNIQUE,
                client VARCHAR(100),
                car_number VARCHAR(20),
                terminal VARCHAR(50),
                system VARCHAR(50),
                status VARCHAR(50),
                operator VARCHAR(50),
                tab VARCHAR(50) DEFAULT 'Основная'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");


        // Таблица логов
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                action VARCHAR(255) NOT NULL,
                details TEXT,
                ip_address VARCHAR(45) NOT NULL,
                user_agent TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        // Добавляем базовые роли, если их нет
        $defaultRoles = ['director', 'manager', 'specialist'];
        foreach ($defaultRoles as $role) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM roles WHERE name = ?");
            $stmt->execute([$role]);
            if ($stmt->fetchColumn() == 0) {
                $this->pdo->prepare("INSERT INTO roles (name) VALUES (?)")->execute([$role]);
            }
        }
    }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // Пользователи
    public function get_user($username) {
        $stmt = $this->query("SELECT * FROM users WHERE username = ?", [$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create_user($username, $password, $fullname, $role) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $this->query(
            "INSERT INTO users (username, password, fullname, role) VALUES (?, ?, ?, ?)",
            [$username, $hashed_password, $fullname, $role]
        );
        return $this->lastInsertId();
    }

    public function get_all_users() {
        $stmt = $this->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Оборудование
    public function add_equipment($data) {
        $sql = "INSERT INTO equipment (type, name, serial_number, location, status) 
                VALUES (:type, :name, :serial, :location, :status)";
        $this->query($sql, [
            ':type' => $data['type'],
            ':name' => $data['name'],
            ':serial' => $data['serial_number'],
            ':location' => $data['location'],
            ':status' => $data['status'] ?? 'active'
        ]);
        return $this->lastInsertId();
    }

    public function get_equipment_by_type($type) {
        $stmt = $this->query("SELECT * FROM equipment WHERE type = ?", [$type]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update_equipment($id, $data) {
        $sql = "UPDATE equipment SET 
                type = :type, 
                name = :name, 
                serial_number = :serial, 
                location = :location, 
                status = :status 
                WHERE id = :id";
        $this->query($sql, [
            ':type' => $data['type'],
            ':name' => $data['name'],
            ':serial' => $data['serial_number'],
            ':location' => $data['location'],
            ':status' => $data['status'],
            ':id' => $id
        ]);
        return true;
    }

    public function delete_equipment($id) {
        $this->query("DELETE FROM equipment WHERE id = ?", [$id]);
        return true;
    }

    public function get_equipment($id) {
        $stmt = $this->query("SELECT * FROM equipment WHERE id = ?", [$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_all_equipment($filters = [], $limit = 100, $offset = 0) {
        $where = [];
        $params = [];

        if (!empty($filters['type'])) {
            $where[] = "type LIKE :type";
            $params[':type'] = '%' . $filters['type'] . '%';
        }
        if (!empty($filters['status'])) {
            $where[] = "status = :status";
            $params[':status'] = $filters['status'];
        }

        $where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT * FROM equipment $where_clause LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count_equipment($filters = []) {
        $where = [];
        $params = [];

        if (!empty($filters['type'])) {
            $where[] = "type LIKE :type";
            $params[':type'] = '%' . $filters['type'] . '%';
        }
        if (!empty($filters['status'])) {
            $where[] = "status = :status";
            $params[':status'] = $filters['status'];
        }

        $where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT COUNT(*) as total FROM equipment $where_clause";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Роли
    public function getRoles() {
        $stmt = $this->query("SELECT * FROM roles");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createRole($name, $permissions = '') {
        $this->query(
            "INSERT INTO roles (name, permissions) VALUES (?, ?)",
            [$name, $permissions]
        );
        return $this->lastInsertId();
    }

    public function updateRole($id, $name, $permissions = '') {
        $this->query(
            "UPDATE roles SET name = ?, permissions = ? WHERE id = ?",
            [$name, $permissions, $id]
        );
        return true;
    }

    public function deleteRole($id) {
        $this->query("DELETE FROM roles WHERE id = ?", [$id]);
        return true;
    }

    public function getRole($id) {
        $stmt = $this->query("SELECT * FROM roles WHERE id = ?", [$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // События
    public function get_events() {
        $query = "SELECT 
                e.id, 
                e.title, 
                e.start, 
                e.end, 
                e.description,
                e.assigned_to,
                u.fullname AS assigned_name
            FROM events e
            LEFT JOIN users u ON e.assigned_to = u.id";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add_event($title, $start, $end, $description, $assigned_to, $user_id) {
        $sql = "INSERT INTO events (title, start, end, description, assigned_to, user_id) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $this->query($sql, [
            $title,
            $start,
            $end,
            $description,
            $assigned_to,
            $user_id
        ]);
        return $this->lastInsertId();
    }

    public function update_event($id, $title, $start, $end, $description, $assigned_to) {
        $sql = "UPDATE events SET 
                title = ?,
                start = ?,
                end = ?,
                description = ?,
                assigned_to = ?
                WHERE id = ?";
        $this->query($sql, [
            $title,
            $start,
            $end,
            $description,
            $assigned_to,
            $id
        ]);
        return true;
    }

    public function delete_event($id) {
        $this->query("DELETE FROM events WHERE id = ?", [$id]);
        return true;
    }

    public function countUsersByRoleName($roleName) {
        $stmt = $this->query("SELECT COUNT(*) as count FROM users WHERE role = ?", [$roleName]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    // Логирование
    public function add_log($user_id, $action, $details, $ip_address, $user_agent) {
        $sql = "INSERT INTO logs (user_id, action, details, ip_address, user_agent) 
                VALUES (:user_id, :action, :details, :ip_address, :user_agent)";

        $this->query($sql, [
            ':user_id' => $user_id,
            ':action' => $action,
            ':details' => $details,
            ':ip_address' => $ip_address,
            ':user_agent' => $user_agent
        ]);
        return $this->lastInsertId();
    }

    public function get_logs($limit = 100) {
        $sql = "SELECT l.*, u.username 
                FROM logs l 
                LEFT JOIN users u ON l.user_id = u.id 
                ORDER BY l.created_at DESC 
                LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count_logs($filters = []) {
        $where = [];
        $params = [];

        if (!empty($filters['action'])) {
            $where[] = "action = :action";
            $params[':action'] = $filters['action'];
        }
        if (!empty($filters['user_id'])) {
            $where[] = "user_id = :user_id";
            $params[':user_id'] = $filters['user_id'];
        }

        $where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT COUNT(*) as total FROM logs $where_clause";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}

// Инициализация базы данных
$db = new Database();

// Создание пользователя-администратора по умолчанию
$admin = $db->get_user('admin');
if (!$admin) {
    $db->create_user('admin', 'admin123', 'Администратор системы', 'director');
}
?>