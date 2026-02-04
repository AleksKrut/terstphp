<?php
require_once __DIR__ . '/../paths.php';
require CONFIG_FILE;
require DB_FILE;

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/login/login.php");
    exit();
}

// Проверяем, открыт ли виджет
$isWidget = isset($_GET['widget']) && $_GET['widget'] == '1';

// Режим виджета
if ($isWidget) {
    // Минимальный HTML для виджета
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Виджет календаря - <?= APP_NAME ?></title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/ru.min.js"></script>
        <style>
            body {
                margin: 0;
                padding: 0;
                background: transparent;
                overflow: hidden;
                font-family: 'Inter', sans-serif;
            }

            #calendar-container {
                padding: 15px;
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(10px);
                border-radius: 12px;
                box-shadow: 0 4px 25px rgba(0, 0, 0, 0.2);
                width: 350px;
                max-height: 450px;
                overflow: hidden;
            }

            #calendar {
                background: transparent !important;
                border: none !important;
                width: 100%;
                height: 100%;
            }

            .fc .fc-toolbar {
                padding: 0;
                margin-bottom: 10px;
            }

            .fc .fc-toolbar-title {
                font-size: 1.1rem;
                color: #333;
                margin: 0;
            }

            .fc .fc-button {
                padding: 3px 6px !important;
                font-size: 0.8rem;
                background: transparent !important;
                border: none !important;
            }

            .fc .fc-button:hover {
                background: rgba(0,0,0,0.05) !important;
            }

            .fc .fc-daygrid-day {
                border: 1px solid rgba(0,0,0,0.05);
            }

            .fc .fc-daygrid-day-number {
                font-size: 0.9rem;
                padding: 3px;
            }

            .fc-event {
                font-size: 0.75rem;
                padding: 1px 3px;
                margin: 1px 0;
            }
        </style>
    </head>
    <body>
    <div id="calendar-container">
        <div id="calendar"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'ru',
                headerToolbar: {
                    left: 'prev',
                    center: 'title',
                    right: 'next'
                },
                height: 'auto',
                fixedWeekCount: false,
                events: 'get_events.php',
                eventDisplay: 'list-item',
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                eventContent: function(arg) {
                    return {
                        html: `<div class="fc-event-title">${arg.event.title}</div>`
                    };
                },
                dayMaxEventRows: 2,
                dayHeaderFormat: { weekday: 'short' }
            });
            calendar.render();

            // Обновляем размер при изменении месяца
            calendar.on('datesSet', function() {
                setTimeout(() => {
                    const container = document.getElementById('calendar-container');
                    const contentHeight = calendarEl.querySelector('.fc-view-harness').offsetHeight;
                    container.style.height = (contentHeight + 40) + 'px';
                }, 100);
            });
        });
    </script>
    </body>
    </html>
    <?php
    exit;
}

// Получаем список пользователей для назначения событий
$db = new Database();
$users = $db->get_all_users();
$users_json = json_encode($users);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Календарь событий - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Современная светлая цветовая схема */
        :root {
            --primary: #4361ee;
            --primary-light: #eef2ff;
            --secondary: #3f37c9;
            --accent: #f72585;
            --success: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --border: #dee2e6;
            --card-shadow: 0 8px 20px rgba(0,0,0,0.06);
            --transition: all 0.3s ease;
        }

        body {
            background-color: #f5f7fb;
            color: var(--dark);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            line-height: 1.6;
        }

        .header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .dashboard-card {
            background: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            border: none;
            overflow: hidden;
            transition: var(--transition);
        }

        .dashboard-card:hover {
            box-shadow: 0 12px 30px rgba(0,0,0,0.1);
            transform: translateY(-3px);
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 20px 25px;
            border-radius: 12px 12px 0 0 !important;
        }

        .calendar-container {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border);
        }

        /* Стили для календаря */
        #calendar {
            min-height: 70vh;
            font-family: 'Inter', sans-serif;
        }

        .fc .fc-toolbar {
            flex-wrap: wrap;
            padding: 15px 0;
            margin-bottom: 15px;
        }

        .fc .fc-toolbar-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin: 10px 0;
        }

        .fc .fc-button {
            background: white;
            border: 1px solid var(--border);
            color: var(--dark);
            padding: 8px 15px;
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: 8px;
            transition: var(--transition);
            text-transform: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .fc .fc-button:hover {
            background: var(--primary-light);
            color: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 4px 8px rgba(67, 97, 238, 0.15);
        }

        .fc .fc-button-primary:not(:disabled).fc-button-active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .fc .fc-col-header-cell {
            background: var(--primary-light);
            color: var(--primary);
            padding: 10px 0;
            border: 1px solid var(--border);
            font-weight: 600;
        }

        .fc .fc-daygrid-day {
            border: 1px solid var(--border);
            transition: var(--transition);
        }

        .fc .fc-daygrid-day:hover {
            background: var(--primary-light);
        }

        .fc .fc-daygrid-day.fc-day-today {
            background: rgba(76, 201, 240, 0.1);
        }

        .fc .fc-daygrid-day-number {
            color: var(--dark);
            font-weight: 500;
            padding: 8px;
        }

        .fc .fc-event {
            background: var(--primary);
            border: none;
            box-shadow: 0 2px 8px rgba(67, 97, 238, 0.2);
            border-radius: 6px;
            padding: 5px 8px;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .fc .fc-event:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }

        .fc .fc-event-main {
            padding: 3px 0;
        }

        .fc .fc-popover {
            background: white;
            border: 1px solid var(--border);
            box-shadow: var(--card-shadow);
            border-radius: 12px;
            overflow: hidden;
        }

        .fc .fc-popover-header {
            background: var(--primary-light);
            color: var(--primary);
            border-bottom: 1px solid var(--border);
            font-weight: 600;
        }

        .fc .fc-popover-close {
            color: var(--gray);
        }

        .fc-event-title {
            font-weight: 500;
            margin-bottom: 2px;
        }

        .fc-event-assigned {
            font-size: 0.8rem;
            opacity: 0.9;
            display: block;
            margin-top: 3px;
        }

        /* Модальное окно */
        .modal-content {
            background: white;
            color: var(--dark);
            border: none;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-bottom: none;
            border-radius: 12px 12px 0 0 !important;
            padding: 20px 25px;
        }

        .modal-title {
            font-weight: 700;
        }

        .btn-close {
            filter: invert(1);
        }

        .modal-body {
            padding: 25px;
        }

        .modal-footer {
            border-top: 1px solid var(--border);
            padding: 15px 25px;
        }

        .form-control, .form-select {
            background: white;
            border: 1px solid var(--border);
            color: var(--dark);
            border-radius: 8px;
            padding: 10px 15px;
            transition: var(--transition);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.15);
        }

        .form-label {
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-primary:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }

        .btn-secondary {
            background: white;
            border: 1px solid var(--border);
            color: var(--dark);
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-secondary:hover {
            background: var(--light-gray);
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #f8f9fa;
            border: 1px solid var(--border);
            color: #dc3545;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-danger:hover {
            background: #fff5f5;
            color: #b02a37;
            transform: translateY(-2px);
        }

        /* Кнопка нового события */
        #newEventBtn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            font-weight: 500;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
            transition: var(--transition);
        }

        #newEventBtn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.3);
        }

        /* Заголовок страницы */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px 0;
            border-bottom: 1px solid var(--border);
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
            margin: 0;
        }

        /* Адаптивность */
        @media (max-width: 768px) {
            .fc .fc-toolbar {
                flex-direction: column;
                gap: 10px;
            }

            .fc .fc-toolbar-title {
                font-size: 1.3rem;
            }

            .modal-body .row > div {
                width: 100%;
                margin-bottom: 15px;
            }

            .calendar-container {
                padding: 15px;
                margin: 0 -15px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            #newEventBtn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
<?php include HEADER_FILE; ?>

<div class="container py-4">
    <div class="page-header">
        <h1 class="page-title">Календарь событий</h1>
        <button id="newEventBtn" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Новое событие
        </button>
    </div>

    <div class="dashboard-card">
        <div class="calendar-container">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Модальное окно для событий -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Создать событие</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="eventForm">
                    <input type="hidden" id="eventId">
                    <div class="mb-4">
                        <label for="eventTitle" class="form-label">Название события</label>
                        <input type="text" class="form-control" id="eventTitle" required placeholder="Введите название события">
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="eventStart" class="form-label">Дата и время начала</label>
                            <input type="datetime-local" class="form-control" id="eventStart" required>
                        </div>
                        <div class="col-md-6">
                            <label for="eventEnd" class="form-label">Дата и время окончания</label>
                            <input type="datetime-local" class="form-control" id="eventEnd" placeholder="Необязательно">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="eventDescription" class="form-label">Описание</label>
                        <textarea class="form-control" id="eventDescription" rows="3" placeholder="Добавьте описание события"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="eventAssigned", class="form-label">Назначить на</label>
                        <select class="form-select" id="eventAssigned">
                            <option value="">Выберите пользователя</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['fullname']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger me-auto" id="deleteBtn" style="display:none;">
                    <i class="bi bi-trash me-1"></i> Удалить
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" id="saveBtn">
                    <i class="bi bi-check-circle me-1"></i> Сохранить
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/ru.min.js"></script>
<script>
    // Получаем список пользователей из PHP
    const users = <?= $users_json ?>;
    let currentEvent = null;
    const calendarEl = document.getElementById('calendar');

    // Инициализация календаря
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'ru',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: 'get_events.php',
        editable: true,
        eventResizableFromStart: true,
        eventDurationEditable: true,
        eventClick: function(info) {
            currentEvent = info.event;
            openEventModal(currentEvent);
        },
        eventDrop: function(info) {
            updateEvent(info.event);
        },
        eventResize: function(info) {
            updateEvent(info.event);
        },
        eventContent: function(arg) {
            const assignedUserId = arg.event.extendedProps.assigned_to;
            const assignedUser = users.find(u => u.id == assignedUserId);
            const userName = assignedUser ? assignedUser.fullname : 'Не назначено';

            return {
                html: `
                    <div class="fc-event-main-frame">
                        <div class="fc-event-title-container">
                            <div class="fc-event-title">${arg.event.title}</div>
                            <div class="fc-event-assigned">${userName}</div>
                        </div>
                    </div>
                `
            };
        },
        eventClassNames: function(arg) {
            // Добавляем классы для разных типов событий
            const eventTypes = {
                'Важное': 'fc-event-important',
                'Встреча': 'fc-event-meeting',
                'Задача': 'fc-event-task'
            };

            const typeClass = eventTypes[arg.event.title.split(' ')[0]] || '';
            return [typeClass];
        }
    });

    calendar.render();

    // Открытие модального окна
    function openEventModal(event = null) {
        const modal = new bootstrap.Modal(document.getElementById('eventModal'));
        const form = document.getElementById('eventForm');

        if (event) {
            document.getElementById('modalTitle').textContent = 'Редактировать событие';
            document.getElementById('eventId').value = event.id;
            document.getElementById('eventTitle').value = event.title;
            document.getElementById('eventStart').value = formatDateTimeLocal(event.start);
            document.getElementById('eventEnd').value = event.end ? formatDateTimeLocal(event.end) : '';
            document.getElementById('eventDescription').value = event.extendedProps.description || '';
            document.getElementById('eventAssigned').value = event.extendedProps.assigned_to || '';
            document.getElementById('deleteBtn').style.display = 'block';
        } else {
            document.getElementById('modalTitle').textContent = 'Создать событие';
            form.reset();
            document.getElementById('deleteBtn').style.display = 'none';
            // Установка текущего времени по умолчанию
            const now = new Date();
            document.getElementById('eventStart').value = formatDateTimeLocal(now);
        }

        modal.show();
    }

    // Форматирование даты для input
    function formatDateTimeLocal(date) {
        date = new Date(date);
        const offset = date.getTimezoneOffset() * 60000;
        const localISOTime = (new Date(date - offset)).toISOString().slice(0, 16);
        return localISOTime;
    }

    // Обновление события при перетаскивании
    function updateEvent(event) {
        const eventData = {
            id: event.id,
            start: event.start.toISOString(),
            end: event.end ? event.end.toISOString() : null
        };

        saveEvent(eventData);
    }

    // Сохранение события
    function saveEvent(data) {
        fetch('save_event.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    calendar.refetchEvents();
                    // Закрываем модальное окно
                    bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
                } else {
                    alert(result.error || 'Ошибка сохранения');
                }
            });
    }

    // Обработчики кнопок
    document.getElementById('newEventBtn').addEventListener('click', () => openEventModal());

    document.getElementById('saveBtn').addEventListener('click', () => {
        const eventData = {
            id: document.getElementById('eventId').value,
            title: document.getElementById('eventTitle').value,
            start: document.getElementById('eventStart').value,
            end: document.getElementById('eventEnd').value,
            description: document.getElementById('eventDescription').value,
            assigned_to: document.getElementById('eventAssigned').value
        };

        saveEvent(eventData);
    });

    document.getElementById('deleteBtn').addEventListener('click', () => {
        if (confirm('Вы уверены, что хотите удалить это событие?')) {
            fetch('delete_event.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: currentEvent.id })
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        currentEvent.remove();
                        bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
                    } else {
                        alert(result.error || 'Ошибка удаления');
                    }
                });
        }
    });
</script>
</body>
</html>