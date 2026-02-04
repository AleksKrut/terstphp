<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $events = $db->get_events();

    // Форматируем события для FullCalendar
    $formattedEvents = [];
    foreach ($events as $event) {
        $formattedEvents[] = [
            'id' => $event['id'],
            'title' => $event['title'],
            'start' => $event['start'],
            'end' => $event['end'],
            'description' => $event['description'],
            'assigned_to' => $event['assigned_to'],
            'extendedProps' => [
                'assigned_name' => $event['assigned_name']
            ]
        ];
    }

    echo json_encode($formattedEvents);
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}