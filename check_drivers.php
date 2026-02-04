<?php
echo "<h3>Проверка PHP конфигурации:</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Loaded php.ini: " . php_ini_loaded_file() . "<br><br>";

echo "<h3>PDO драйверы:</h3>";
$drivers = PDO::getAvailableDrivers();
echo empty($drivers) ? "❌ Нет драйверов PDO!" : "✅ " . implode(", ", $drivers);
echo "<br><br>";

echo "<h3>Расширения MySQL:</h3>";
echo "MySQLi: " . (extension_loaded('mysqli') ? "✅ Загружено" : "❌ Не загружено") . "<br>";
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? "✅ Загружено" : "❌ Не загружено") . "<br><br>";

echo "<h3>Проверка подключения к БД:</h3>";
try {
    $pdo = new PDO('mysql:host=localhost;charset=utf8', 'root', '');
    echo "✅ Подключение к MySQL успешно!";
} catch (PDOException $e) {
    echo "❌ Ошибка подключения: " . $e->getMessage();
}
?>