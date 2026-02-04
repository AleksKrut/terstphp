<?php
echo "<h3>Текущий PHP:</h3>";
echo "Версия: " . phpversion() . "<br>";
echo "Путь: " . PHP_BINARY . "<br>";
echo "php.ini: " . php_ini_loaded_file() . "<br>";

echo "<h3>Драйверы PDO:</h3>";
$drivers = PDO::getAvailableDrivers();
echo empty($drivers) ? "❌ Нет драйверов" : "✅ " . implode(", ", $drivers);

echo "<h3>Рекомендация:</h3>";
echo "Используйте установщик с SQLite или настройте XAMPP правильно";
?><?php
