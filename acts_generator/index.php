<?php
// Шаблоны документов
$mt_template = <<<'EOD'
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Акт диагностики МТ</title>
    <style>
        body { font-family: Times New Roman, serif; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .content { width: 100%; border-collapse: collapse; }
        .content td, .content th { border: 1px solid #000; padding: 8px; }
        .footer { margin-top: 30px; width: 100%; }
        .signature { width: 50%; float: left; }
        .underline { text-decoration: underline; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class='header'>
        <div class='bold'>«{mt_date}» {mt_month} 2025 г.</div>
        <div class='bold'>АКТ диагностики оборудования №{mt_number}</div>
    </div>
    
    <table class='content'>
        <tr>
            <td>Дата, номер заявки</td>
            <td>{mt_request_date} №{mt_request_number}</td>
        </tr>
        <tr>
            <td>Организация-заказчик</td>
            <td>{mt_organization}</td>
        </tr>
        <tr>
            <td>Адрес проведения диагностики</td>
            <td>{mt_address}</td>
        </tr>
        <tr>
            <td>Марка, модель автомобиля</td>
            <td>{mt_car_model}</td>
        </tr>
        <tr>
            <td>Гос. рег. знак</td>
            <td>{mt_plate}</td>
        </tr>
        <tr>
            <td>ID-номер модуля мониторинга</td>
            <td>{mt_module_id}</td>
        </tr>
        <tr>
            <td>№ датчика уровня топлива (ДУТ)</td>
            <td>{mt_fuel_sensor}</td>
        </tr>
        <tr>
            <td colspan='2' class='bold center'>Результаты осмотра</td>
        </tr>
        <tr>
            <td colspan='2' class='bold'>Состояние, наличие пломб</td>
        </tr>
        <tr>
            <td>Разъем питания</td>
            <td>{mt_power_connector}</td>
        </tr>
        <tr>
            <td>Мобильный терминал (МТ)</td>
            <td>{mt_terminal}</td>
        </tr>
        <tr>
            <td>Антенна GPS/GSM</td>
            <td>{mt_antenna}</td>
        </tr>
        <tr>
            <td>Датчик уровня топлива (ДУТ)</td>
            <td>{mt_fuel_sensor_state}</td>
        </tr>
        <tr>
            <td>Другое</td>
            <td>{mt_other}</td>
        </tr>
        <tr>
            <td colspan='2' class='bold'>Физические повреждения</td>
        </tr>
        <tr>
            <td>Разъем питания</td>
            <td>{mt_power_damage}</td>
        </tr>
        <tr>
            <td>Мобильный терминал (МТ)</td>
            <td>{mt_terminal_damage}</td>
        </tr>
        <tr>
            <td>Антенна GPS/GSM</td>
            <td>{mt_antenna_damage}</td>
        </tr>
        <tr>
            <td>Датчик уровня топлива (ДУТ)</td>
            <td>{mt_fuel_sensor_damage}</td>
        </tr>
        <tr>
            <td>Другое</td>
            <td>{mt_other_damage}</td>
        </tr>
        <tr>
            <td>SIM карта</td>
            <td>{mt_sim}</td>
        </tr>
        <tr>
            <td colspan='2'>Примечания: {mt_notes}</td>
        </tr>
    </table>
    
    <div class='bold'>Выполнены работы: {mt_work_done}</div>
    <div class='bold'>Заключение: {mt_conclusion}</div>
    <div class='bold'>Основание признания случая не гарантийным: {mt_non_warranty}</div>
    
    <div class='footer'>
        <div class='signature'>
            <div>Исполнитель монтажных работ:</div>
            <div class='underline'>{mt_installer}</div>
            <div>Подпись (ФИО)</div>
        </div>
        <div class='signature'>
            <div>Представитель Заказчика:</div>
            <div>Должность {mt_client_position}</div>
            <div>Фамилия И.О. <span class='underline'>{mt_client_name}</span></div>
            <div>Подпись</div>
        </div>
    </div>
</body>
</html>
EOD;

$taho_template = <<<'EOD'
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Акт диагностики тахографа</title>
    <style>
        body { font-family: Times New Roman, serif; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .content { width: 100%; border-collapse: collapse; }
        .content td, .content th { border: 1px solid #000; padding: 8px; }
        .footer { margin-top: 30px; width: 100%; }
        .signature { width: 50%; float: left; }
        .underline { text-decoration: underline; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class='header'>
        <div class='bold'>«{taho_date}» {taho_month} 2025 г.</div>
        <div class='bold'>АКТ диагностики тахографа №{taho_number}</div>
    </div>
    
    <table class='content'>
        <tr>
            <td colspan='2' class='bold center'>Общие сведения</td>
        </tr>
        <tr>
            <td>Организация-заказчик</td>
            <td>{taho_organization}</td>
        </tr>
        <tr>
            <td>Адрес диагностики:</td>
            <td>{taho_address}</td>
        </tr>
        <tr>
            <td>Марка автомобиля</td>
            <td>{taho_car_model}</td>
        </tr>
        <tr>
            <td>Гос. номер автомобиля</td>
            <td>{taho_plate}</td>
        </tr>
        <tr>
            <td>Заводской номер тахографа</td>
            <td>{taho_factory_number}</td>
        </tr>
        <tr>
            <td>Номер блока СКЗИ (НКМ)</td>
            <td>{taho_skzi}</td>
        </tr>
        <tr>
            <td colspan='2' class='bold center'>Результаты осмотра</td>
        </tr>
        <tr>
            <td colspan='2' class='bold'>Состояние пломб</td>
        </tr>
        <tr>
            <td>Разъем питания</td>
            <td>{taho_power}</td>
        </tr>
        <tr>
            <td>Тахограф</td>
            <td>{taho_tachograph}</td>
        </tr>
        <tr>
            <td>Антенна GPS/GSM</td>
            <td>{taho_antenna}</td>
        </tr>
        <tr>
            <td>ДС</td>
            <td>{taho_ds}</td>
        </tr>
        <tr>
            <td>ППС</td>
            <td>{taho_pps}</td>
        </tr>
        <tr>
            <td>Другое</td>
            <td>{taho_other}</td>
        </tr>
        <tr>
            <td colspan='2' class='bold'>Физические повреждения</td>
        </tr>
        <tr>
            <td>Разъем питания</td>
            <td>{taho_power_damage}</td>
        </tr>
        <tr>
            <td>Тахограф</td>
            <td>{taho_tachograph_damage}</td>
        </tr>
        <tr>
            <td>Антенна GPS/GSM</td>
            <td>{taho_antenna_damage}</td>
        </tr>
        <tr>
            <td>ДС</td>
            <td>{taho_ds_damage}</td>
        </tr>
        <tr>
            <td>ППС</td>
            <td>{taho_pps_damage}</td>
        </tr>
        <tr>
            <td>Другое</td>
            <td>{taho_other_damage}</td>
        </tr>
        <tr>
            <td colspan='2'>Прочее: {taho_notes}</td>
        </tr>
    </table>
    
    <div class='bold'>Произведена: {taho_work_done}</div>
    <div class='bold'>Заключение: {taho_conclusion}</div>
    <div class='bold'>Рекомендация: Замена тахографа</div>
    
    <div class='footer'>
        <div class='signature'>
            <div>Исполнитель:</div>
            <div class='underline'>{taho_installer}</div>
            <div>Подпись (ФИО)</div>
        </div>
        <div class='signature'>
            <div>Представитель Заказчика:</div>
            <div>Должность {taho_client_position}</div>
            <div>Фамилия И.О. <span class='underline'>{taho_client_name}</span></div>
            <div>Подпись</div>
        </div>
    </div>
</body>
</html>
EOD;

// Обработка отправки формы
$saved = false;
$docType = 'mt';
$documentContent = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $docType = $_POST['doc_type'] ?? 'mt';
    $formData = $_POST;

    // Подготовка данных для замены
    $replacements = [];
    foreach ($formData as $key => $value) {
        $replacements["{{$key}}"] = htmlspecialchars($value);
    }

    // Генерация документа в зависимости от типа
    if ($docType === 'mt') {
        $documentContent = str_replace(array_keys($replacements), array_values($replacements), $mt_template);
    } else {
        $documentContent = str_replace(array_keys($replacements), array_values($replacements), $taho_template);
    }

    $saved = true;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Генератор актов диагностики</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #1a2a6c);
            color: #333;
            line-height: 1.6;
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        header {
            background: linear-gradient(to right, #1a2a6c, #2a4ba0);
            color: white;
            text-align: center;
            padding: 30px 20px;
            position: relative;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 800px;
            margin: 0 auto;
        }

        .tabs {
            display: flex;
            background: linear-gradient(to right, #2a4ba0, #1a2a6c);
            border-bottom: 2px solid #fff;
        }

        .tab {
            flex: 1;
            text-align: center;
            padding: 20px;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            color: white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .tab:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .tab.active {
            background: white;
            color: #1a2a6c;
            box-shadow: 0 -3px 10px rgba(0, 0, 0, 0.1);
        }

        .tab-content {
            display: none;
            padding: 30px;
        }

        .tab-content.active {
            display: block;
        }

        .form-container {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #1a2a6c;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eaeaea;
            font-size: 1.8rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1a2a6c;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #2a4ba0;
            outline: none;
            box-shadow: 0 0 0 3px rgba(42, 75, 160, 0.2);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .btn-container {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            gap: 20px;
        }

        .btn {
            padding: 15px 40px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(to right, #1a2a6c, #2a4ba0);
            color: white;
            box-shadow: 0 5px 15px rgba(26, 42, 108, 0.4);
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #2a4ba0, #1a2a6c);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(26, 42, 108, 0.6);
        }

        .btn-secondary {
            background: linear-gradient(to right, #b21f1f, #d62828);
            color: white;
            box-shadow: 0 5px 15px rgba(178, 31, 31, 0.4);
        }

        .btn-secondary:hover {
            background: linear-gradient(to right, #d62828, #b21f1f);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(178, 31, 31, 0.6);
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 20px 30px;
            background: linear-gradient(to right, #4CAF50, #2E7D32);
            color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transform: translateX(150%);
            transition: transform 0.5s ease;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .notification.show {
            transform: translateX(0);
        }

        .preview-container {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-top: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            display: none;
        }

        .preview-container.active {
            display: block;
        }

        .preview-content {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            max-height: 400px;
            overflow-y: auto;
            background: #f9f9f9;
        }

        footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            color: #6c757d;
            border-top: 1px solid #eaeaea;
            margin-top: 30px;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            h1 {
                font-size: 2rem;
            }

            .tabs {
                flex-direction: column;
            }

            .btn-container {
                flex-direction: column;
                gap: 15px;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1><i class="fas fa-file-contract"></i> Генератор актов диагностики</h1>
        <p class="subtitle">Создавайте профессиональные акты диагностики для мобильных терминалов и тахографов</p>
    </header>

    <div class="tabs">
        <div class="tab active" data-tab="mt">Акт диагностики МТ</div>
        <div class="tab" data-tab="taho">Акт диагностики ТАХО</div>
    </div>

    <form method="post" class="tab-content active" id="mt-form">
        <input type="hidden" name="doc_type" value="mt">
        <div class="form-container">
            <h2><i class="fas fa-mobile-alt"></i> Акт диагностики МТ</h2>

            <div class="form-grid">
                <div class="form-group">
                    <label for="mt_date">Дата (число)</label>
                    <input type="text" id="mt_date" name="mt_date" value="19" required>
                </div>

                <div class="form-group">
                    <label for="mt_month">Месяц</label>
                    <select id="mt_month" name="mt_month" required>
                        <option value="Января">Января</option>
                        <option value="Февраля">Февраля</option>
                        <option value="Марта">Марта</option>
                        <option value="Апреля">Апреля</option>
                        <option value="Мая" selected>Мая</option>
                        <option value="Июня">Июня</option>
                        <option value="Июля">Июля</option>
                        <option value="Августа">Августа</option>
                        <option value="Сентября">Сентября</option>
                        <option value="Октября">Октября</option>
                        <option value="Ноября">Ноября</option>
                        <option value="Декабря">Декабря</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="mt_number">Номер акта</label>
                    <input type="text" id="mt_number" name="mt_number" value="MT-2025-001" required>
                </div>

                <div class="form-group">
                    <label for="mt_request_date">Дата заявки</label>
                    <input type="date" id="mt_request_date" name="mt_request_date" value="2025-05-18" required>
                </div>

                <div class="form-group">
                    <label for="mt_request_number">Номер заявки</label>
                    <input type="text" id="mt_request_number" name="mt_request_number" value="З-2025-0057" required>
                </div>

                <div class="form-group">
                    <label for="mt_organization">Организация-заказчик</label>
                    <input type="text" id="mt_organization" name="mt_organization" value="ООО 'ТрансЛогистик'" required>
                </div>

                <div class="form-group">
                    <label for="mt_address">Адрес проведения диагностики</label>
                    <input type="text" id="mt_address" name="mt_address" value="г. Москва, ул. Промышленная, д. 15" required>
                </div>

                <div class="form-group">
                    <label for="mt_car_model">Марка, модель автомобиля</label>
                    <input type="text" id="mt_car_model" name="mt_car_model" value="КАМАЗ-65801" required>
                </div>

                <div class="form-group">
                    <label for="mt_plate">Гос. рег. знак</label>
                    <input type="text" id="mt_plate" name="mt_plate" value="А123ВС77" required>
                </div>

                <div class="form-group">
                    <label for="mt_module_id">ID-номер модуля мониторинга</label>
                    <input type="text" id="mt_module_id" name="mt_module_id" value="MT-887654" required>
                </div>

                <div class="form-group">
                    <label for="mt_fuel_sensor">№ датчика уровня топлива (ДУТ)</label>
                    <input type="text" id="mt_fuel_sensor" name="mt_fuel_sensor" value="ДУТ-5573" required>
                </div>
            </div>

            <h3><i class="fas fa-search"></i> Результаты осмотра</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="mt_power_connector">Разъем питания</label>
                    <input type="text" id="mt_power_connector" name="mt_power_connector" value="Удовлетворительное" required>
                </div>

                <div class="form-group">
                    <label for="mt_terminal">Мобильный терминал (МТ)</label>
                    <input type="text" id="mt_terminal" name="mt_terminal" value="Удовлетворительное" required>
                </div>

                <div class="form-group">
                    <label for="mt_antenna">Антенна GPS/GSM</label>
                    <input type="text" id="mt_antenna" name="mt_antenna" value="Удовлетворительное" required>
                </div>

                <div class="form-group">
                    <label for="mt_fuel_sensor_state">Датчик уровня топлива (ДУТ)</label>
                    <input type="text" id="mt_fuel_sensor_state" name="mt_fuel_sensor_state" value="Удовлетворительное" required>
                </div>

                <div class="form-group">
                    <label for="mt_other">Другое</label>
                    <input type="text" id="mt_other" name="mt_other" value="Нет замечаний">
                </div>
            </div>

            <h3><i class="fas fa-tools"></i> Физические повреждения</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="mt_power_damage">Разъем питания</label>
                    <input type="text" id="mt_power_damage" name="mt_power_damage" value="Отсутствуют" required>
                </div>

                <div class="form-group">
                    <label for="mt_terminal_damage">Мобильный терминал (МТ)</label>
                    <input type="text" id="mt_terminal_damage" name="mt_terminal_damage" value="Отсутствуют" required>
                </div>

                <div class="form-group">
                    <label for="mt_antenna_damage">Антенна GPS/GSM</label>
                    <input type="text" id="mt_antenna_damage" name="mt_antenna_damage" value="Отсутствуют" required>
                </div>

                <div class="form-group">
                    <label for="mt_fuel_sensor_damage">Датчик уровня топлива (ДУТ)</label>
                    <input type="text" id="mt_fuel_sensor_damage" name="mt_fuel_sensor_damage" value="Отсутствуют" required>
                </div>

                <div class="form-group">
                    <label for="mt_other_damage">Другое</label>
                    <input type="text" id="mt_other_damage" name="mt_other_damage" value="Нет">
                </div>

                <div class="form-group">
                    <label for="mt_sim">SIM карта</label>
                    <input type="text" id="mt_sim" name="mt_sim" value="Активна, номер: +7(999)123-45-67" required>
                </div>

                <div class="form-group full-width">
                    <label for="mt_notes">Примечания</label>
                    <textarea id="mt_notes" name="mt_notes">Установлено обновление ПО до версии 2.4. Проведена диагностика кабельных соединений.</textarea>
                </div>

                <div class="form-group full-width">
                    <label for="mt_work_done">Выполнены работы</label>
                    <input type="text" id="mt_work_done" name="mt_work_done" value="Диагностика МТ, Обновление ПО, Диагностика кабельных соединений (2шт), Замена антенн (GPS, GNSS с использованием материалов исполнителя)" required>
                </div>

                <div class="form-group full-width">
                    <label for="mt_conclusion">Заключение</label>
                    <textarea id="mt_conclusion" name="mt_conclusion" required>Оборудование функционирует нормально, после обновления ПО и замены антенн показания стабильные. Рекомендуется повторная диагностика через 6 месяцев.</textarea>
                </div>

                <div class="form-group full-width">
                    <label for="mt_non_warranty">Основание признания случая не гарантийным</label>
                    <textarea id="mt_non_warranty" name="mt_non_warranty">Повреждения оборудования вызваны внешними факторами (механическое воздействие), не связанными с производственным браком.</textarea>
                </div>

                <div class="form-group">
                    <label for="mt_installer">Исполнитель монтажных работ</label>
                    <input type="text" id="mt_installer" name="mt_installer" value="Петров И.С." required>
                </div>

                <div class="form-group">
                    <label for="mt_client_position">Должность представителя заказчика</label>
                    <input type="text" id="mt_client_position" name="mt_client_position" value="Начальник транспортного отдела" required>
                </div>

                <div class="form-group">
                    <label for="mt_client_name">ФИО представителя заказчика</label>
                    <input type="text" id="mt_client_name" name="mt_client_name" value="Сидоров А.В." required>
                </div>
            </div>

            <div class="btn-container">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Сохранить документ
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Сбросить форму
                </button>
            </div>
        </div>
    </form>

    <form method="post" class="tab-content" id="taho-form">
        <input type="hidden" name="doc_type" value="taho">
        <div class="form-container">
            <h2><i class="fas fa-tachometer-alt"></i> Акт диагностики ТАХО</h2>

            <div class="form-grid">
                <div class="form-group">
                    <label for="taho_date">Дата (число)</label>
                    <input type="text" id="taho_date" name="taho_date" value="19" required>
                </div>

                <div class="form-group">
                    <label for="taho_month">Месяц</label>
                    <select id="taho_month" name="taho_month" required>
                        <option value="Января">Января</option>
                        <option value="Февраля">Февраля</option>
                        <option value="Марта">Марта</option>
                        <option value="Апреля">Апреля</option>
                        <option value="Мая" selected>Мая</option>
                        <option value="Июня">Июня</option>
                        <option value="Июля">Июля</option>
                        <option value="Августа">Августа</option>
                        <option value="Сентября">Сентября</option>
                        <option value="Октября">Октября</option>
                        <option value="Ноября">Ноября</option>
                        <option value="Декабря">Декабря</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="taho_number">Номер акта</label>
                    <input type="text" id="taho_number" name="taho_number" value="ТХ-2025-015" required>
                </div>

                <div class="form-group">
                    <label for="taho_organization">Организация-заказчик</label>
                    <input type="text" id="taho_organization" name="taho_organization" value="ООО 'ГрузАвто'" required>
                </div>

                <div class="form-group">
                    <label for="taho_address">Адрес диагностики</label>
                    <input type="text" id="taho_address" name="taho_address" value="г. Москва, ул. Автозаводская, д. 23" required>
                </div>

                <div class="form-group">
                    <label for="taho_car_model">Марка автомобиля</label>
                    <input type="text" id="taho_car_model" name="taho_car_model" value="Volvo FH16" required>
                </div>

                <div class="form-group">
                    <label for="taho_plate">Гос. номер автомобиля</label>
                    <input type="text" id="taho_plate" name="taho_plate" value="Х987УН77" required>
                </div>

                <div class="form-group">
                    <label for="taho_factory_number">Заводской номер тахографа</label>
                    <input type="text" id="taho_factory_number" name="taho_factory_number" value="ТХ-774411" required>
                </div>

                <div class="form-group">
                    <label for="taho_skzi">Номер блока СКЗИ (НКМ)</label>
                    <input type="text" id="taho_skzi" name="taho_skzi" value="СКЗИ-2025-044" required>
                </div>
            </div>

            <h3><i class="fas fa-search"></i> Результаты осмотра</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="taho_power">Разъем питания</label>
                    <input type="text" id="taho_power" name="taho_power" value="Удов." required>
                </div>

                <div class="form-group">
                    <label for="taho_tachograph">Тахограф</label>
                    <input type="text" id="taho_tachograph" name="taho_tachograph" value="Удов." required>
                </div>

                <div class="form-group">
                    <label for="taho_antenna">Антенна GPS/GSM</label>
                    <input type="text" id="taho_antenna" name="taho_antenna" value="Удов." required>
                </div>

                <div class="form-group">
                    <label for="taho_ds">ДС</label>
                    <input type="text" id="taho_ds" name="taho_ds" value="Удов." required>
                </div>

                <div class="form-group">
                    <label for="taho_pps">ППС</label>
                    <input type="text" id="taho_pps" name="taho_pps" value="-">
                </div>

                <div class="form-group">
                    <label for="taho_other">Другое</label>
                    <input type="text" id="taho_other" name="taho_other" value="-">
                </div>
            </div>

            <h3><i class="fas fa-tools"></i> Физические повреждения</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="taho_power_damage">Разъем питания</label>
                    <input type="text" id="taho_power_damage" name="taho_power_damage" value="Нет." required>
                </div>

                <div class="form-group">
                    <label for="taho_tachograph_damage">Тахограф</label>
                    <input type="text" id="taho_tachograph_damage" name="taho_tachograph_damage" value="Нет." required>
                </div>

                <div class="form-group">
                    <label for="taho_antenna_damage">Антенна GPS/GSM</label>
                    <input type="text" id="taho_antenna_damage" name="taho_antenna_damage" value="Нет." required>
                </div>

                <div class="form-group">
                    <label for="taho_ds_damage">ДС</label>
                    <input type="text" id="taho_ds_damage" name="taho_ds_damage" value="-">
                </div>

                <div class="form-group">
                    <label for="taho_pps_damage">ППС</label>
                    <input type="text" id="taho_pps_damage" name="taho_pps_damage" value="-">
                </div>

                <div class="form-group">
                    <label for="taho_other_damage">Другое</label>
                    <input type="text" id="taho_other_damage" name="taho_other_damage" value="-">
                </div>

                <div class="form-group full-width">
                    <label for="taho_notes">Прочее</label>
                    <textarea id="taho_notes" name="taho_notes">Обнаружены ошибки в работе модуля СКЗИ. Требуется дополнительная диагностика.</textarea>
                </div>

                <div class="form-group full-width">
                    <label for="taho_work_done">Выполнены работы</label>
                    <input type="text" id="taho_work_done" name="taho_work_done" value="Диагностика тахографа, замена элемента питания" required>
                </div>

                <div class="form-group full-width">
                    <label for="taho_conclusion">Заключение</label>
                    <textarea id="taho_conclusion" name="taho_conclusion" required>Тахограф требует замены из-за неисправности модуля СКЗИ и устаревшего программного обеспечения. Рекомендуется установка новой модели.</textarea>
                </div>

                <div class="form-group">
                    <label for="taho_installer">Исполнитель</label>
                    <input type="text" id="taho_installer" name="taho_installer" value="Иванов П.К." required>
                </div>

                <div class="form-group">
                    <label for="taho_client_position">Должность представителя заказчика</label>
                    <input type="text" id="taho_client_position" name="taho_client_position" value="Менеджер автопарка" required>
                </div>

                <div class="form-group">
                    <label for="taho_client_name">ФИО представителя заказчика</label>
                    <input type="text" id="taho_client_name" name="taho_client_name" value="Кузнецов В.М." required>
                </div>
            </div>

            <div class="btn-container">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Сохранить документ
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Сбросить форму
                </button>
            </div>
        </div>
    </form>

    <?php if ($saved): ?>
        <div class="preview-container active">
            <h2><i class="fas fa-file-alt"></i> Предпросмотр документа</h2>
            <div class="preview-content">
                <?= $documentContent ?>
            </div>
            <div class="btn-container">
                <a href="data:application/msword;base64,<?= base64_encode($documentContent) ?>" download="<?= ($docType === 'mt' ? 'Акт_диагностики_МТ' : 'Акт_диагностики_ТАХО') ?>.doc" class="btn btn-primary">
                    <i class="fas fa-download"></i> Скачать DOC файл
                </a>
            </div>
        </div>
    <?php endif; ?>

    <footer>
        <p>&copy; 2025 Система генерации актов диагностики | Версия 1.0</p>
    </footer>
</div>

<?php if ($saved): ?>
    <div class="notification show">
        <i class="fas fa-check-circle fa-2x"></i>
        <div>
            <h3>Документ успешно создан!</h3>
            <p><?= ($docType === 'mt' ? 'Акт диагностики МТ' : 'Акт диагностики ТАХО') ?> готов к скачиванию.</p>
        </div>
    </div>
<?php endif; ?>

<script>
    // Переключение вкладок
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', () => {
            // Удаляем активный класс у всех вкладок и контента
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

            // Добавляем активный класс текущей вкладке
            tab.classList.add('active');

            // Показываем соответствующий контент
            const tabId = tab.getAttribute('data-tab');
            document.getElementById(`${tabId}-form`).classList.add('active');
        });
    });

    // Автоматическое скрытие уведомления
    <?php if ($saved): ?>
    setTimeout(() => {
        document.querySelector('.notification').classList.remove('show');
    }, 5000);
    <?php endif; ?>
</script>
</body>
</html>