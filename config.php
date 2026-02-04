<?php
// Защита от прямого доступа
//if (!defined('ROOT_DIR')) die('Доступ запрещен!');

// Динамический BASE_URL
if (!defined('APP_NAME')) define('APP_NAME', 'Smart-ITS');
if (!defined('BASE_URL')) {
    define('BASE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/Smart-ITS');
}

// Фиксированная соль для безопасности
if (!defined('SALT')) {
    define('SALT', 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6');
}

// Настройки базы данных
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'smartact_db');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');

// Роли
$roles = [];
if (!defined('ROLES')) {
    define('ROLES', $roles ?: [
        'manager' => 'Менеджер',
        'specialist' => 'Специалист монтажных работ',
        'director' => 'Администратор'
    ]);
}

// ДОБАВЛЕНЫ НАСТРОЙКИ ВКЛАДОК ДЛЯ SIM-КАРТ
$tabs_structure = [
    'МТС' => ['МТС Конорыгин', 'МТС Компании', 'Личные МТС', 'Простой МТС', 'Не найденные МТС'],
    'Tele2' => ['Tele2 Основная', 'Tele2 Архив']
];

// Логирование ошибок
if (!defined('LOG_FILE')) define('LOG_FILE', __DIR__ . '/logs/errors.log');
ini_set('log_errors', 1);
ini_set('error_log', LOG_FILE);

if (!defined('FULLCALENDAR_CSS')) define('FULLCALENDAR_CSS', BASE_URL . '/assets/fullcalendar/main.min.css');
if (!defined('FULLCALENDAR_JS')) define('FULLCALENDAR_JS', BASE_URL . '/assets/fullcalendar/main.min.js');
?>