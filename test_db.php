<?php
echo "<h3>Проверка расширений PHP:</h3>";

// Проверим доступные расширения
$extensions = get_loaded_extensions();
sort($extensions);

echo "<strong>Загруженные расширения:</strong><br>";
foreach ($extensions as $ext) {
    echo "- $ext<br>";
}

echo "<br><strong>Проверка конкретных расширений:</strong><br>";
$required = ['mysqli', 'pdo_mysql', 'pdo', 'mbstring'];
foreach ($required as $ext) {
    echo $ext . ": " . (extension_loaded($ext) ? "✅" : "❌") . "<br>";
}

echo "<br><strong>PDO драйверы:</strong><br>";
if (extension_loaded('pdo')) {
    $drivers = PDO::getAvailableDrivers();
    echo empty($drivers) ? "❌ Нет драйверов" : "✅ " . implode(", ", $drivers);
} else {
    echo "❌ PDO не загружен";
}
?>