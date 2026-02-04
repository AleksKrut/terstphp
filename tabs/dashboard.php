<div class="row mb-4">
    <div class="col-md-3">
        <div class="quick-stats">
            <div class="stat-number"><?= $stats['total_equipment'] ?? 0 ?></div>
            <div class="stat-label">Всего оборудования</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="quick-stats">
            <div class="stat-number"><?= $stats['active_events'] ?? 0 ?></div>
            <div class="stat-label">Активных задач</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="quick-stats">
            <div class="stat-number">5</div>
            <div class="stat-label">Актов за неделю</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="quick-stats">
            <div class="stat-number"><?= $stats['online_users'] ?? 1 ?></div>
            <div class="stat-label">Сейчас онлайн</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="module-card p-4">
            <h5 class="d-flex align-items-center">
                <i class="bi bi-clock-history me-2"></i>Последние действия
            </h5>
            <div class="mt-3">
                <?php foreach ($recent_activities as $activity): ?>
                    <div class="activity-item">
                        <div class="d-flex justify-content-between">
                            <strong><?= htmlspecialchars($activity['fullname'] ?? $activity['username'] ?? 'Система') ?></strong>
                            <small class="text-muted"><?= date('H:i', strtotime($activity['created_at'])) ?></small>
                        </div>
                        <div class="small text-muted"><?= htmlspecialchars($activity['action']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="module-card p-4">
            <h5 class="d-flex align-items-center">
                <i class="bi bi-calendar-check me-2"></i>Ближайшие события
            </h5>
            <div class="mt-3">
                <div class="activity-item">
                    <div class="d-flex justify-content-between">
                        <strong>Техническое обслуживание</strong>
                        <small class="text-muted">Завтра, 10:00</small>
                    </div>
                    <div class="small text-muted">Плановое ТО серверного оборудования</div>
                </div>
                <div class="activity-item">
                    <div class="d-flex justify-content-between">
                        <strong>Замена SIM-карт</strong>
                        <small class="text-muted">15.03.2024</small>
                    </div>
                    <div class="small text-muted">Ротация SIM-карт в терминалах Tele2</div>
                </div>
            </div>
        </div>

        <div class="module-card p-4 mt-4">
            <h5 class="d-flex align-items-center">
                <i class="bi bi-lightning me-2"></i>Быстрый доступ
            </h5>
            <div class="row mt-3">
                <div class="col-6 mb-2">
                    <a href="?tab=acts" class="btn btn-outline-primary w-100 btn-sm">Создать акт</a>
                </div>
                <div class="col-6 mb-2">
                    <a href="?tab=forum" class="btn btn-outline-success w-100 btn-sm">Новая тема</a>
                </div>
                <div class="col-6 mb-2">
                    <a href="?tab=equipment" class="btn btn-outline-info w-100 btn-sm">Оборудование</a>
                </div>
                <div class="col-6 mb-2">
                    <a href="?tab=calendar" class="btn btn-outline-warning w-100 btn-sm">Календарь</a>
                </div>
            </div>
        </div>
    </div>
</div>