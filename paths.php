<?php
// Файл: /Smart-ITS/paths.php

/**
 * Централизованное хранилище путей для проекта Smart-ITS
 * Все пути указаны относительно корня проекта (/Smart-ITS/)
 */

// Базовые директории
define('ROOT_DIR', __DIR__);
define('EQUIPMENT_DIR', ROOT_DIR . '/equipment/');
define('EVENT_DIR', ROOT_DIR . '/event/');
define('LOGIN_DIR', ROOT_DIR . '/login/');
define('USER_DIR', ROOT_DIR . '/user/');
define('VENDOR_DIR', ROOT_DIR . '/vendor/');
define('TEMPLATES_DIR', ROOT_DIR . '/templates/');

// Основные файлы
define('HEADER_FILE', ROOT_DIR . '/header.php');
define('FOOTER_FILE', ROOT_DIR . '/footer.php');
define('INDEX_FILE', ROOT_DIR . '/index.php');
define('VENDOR_AUTOLOAD', VENDOR_DIR . 'autoload.php');

// События (event)
define('DELETE_EVENTS', EVENT_DIR . 'delete_events.php');
define('GET_EVENTS', EVENT_DIR . 'get_events.php');
define('SAVE_EVENT', EVENT_DIR . 'save_event.php');

// Авторизация (login)
define('LOGGER', LOGIN_DIR . 'logger.php');
define('LOGIN', LOGIN_DIR . 'login.php');
define('LOGOUT', LOGIN_DIR . 'logout.php');

// Пользователи (user)
define('ADMIN_ROLES', USER_DIR . 'admin_roles.php');
define('ADMIN_USERS', USER_DIR . 'admin_users.php');
define('DELETE_USER', USER_DIR . 'delete_user.php');
define('EDIT_USER', USER_DIR . 'edit_user.php');
define('EXPORT_USER', USER_DIR . 'export_user.php');

// Другие важные файлы
define('GENERATE_ACT', ROOT_DIR . '/generate_act.php');
define('TAXO_ACTS', ROOT_DIR . '/taxo_acts.php');
define('DASHBOARD', ROOT_DIR . '/dashboard.php');

define('DB_FILE', ROOT_DIR . '/db.php');
define('CONFIG_FILE', ROOT_DIR . '/config.php');

// activation/
define('ACTIVATION_DIR', ROOT_DIR . '/activation/');
define('ACTIVATION_INDEX', ACTIVATION_DIR . 'index.php');

// equipment/
define('EQUIPMENT_INDEX', EQUIPMENT_DIR . 'index.php');

//simcards
define('SIM_DIR', ROOT_DIR . '/simcards/');
define('SIM_INDEX', SIM_DIR . 'index.php');
define('SIM_ADD', SIM_DIR . 'add.php');
define('SIM_EDIT', SIM_DIR . 'edit.php');

//acts_generator
define('ACTS_GENERATOR_DIR', ROOT_DIR . '/acts_generator/');
define('ACTS_GENERATOR_INDEX', ACTS_GENERATOR_DIR . 'index.php');

function validatePaths() {
    $required = [
        DB_FILE,
        CONFIG_FILE,
        HEADER_FILE,
        FOOTER_FILE,
        VENDOR_AUTOLOAD
    ];

    foreach ($required as $path) {
        if (!file_exists($path)) {
            die("Ошибка: файл $path не найден!");
        }
    }
}

// Включить проверку при необходимости
// validatePaths();
?>