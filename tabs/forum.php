<?php
// Временные данные для форума (пока нет БД)
$forum_topics = [
    [
        'id' => 1,
        'title' => 'Обсуждение проблем с МТ-терминалами',
        'author' => 'Иван Петров',
        'replies' => 24,
        'last_activity' => '5 минут назад',
        'category' => 'Оборудование',
        'pinned' => true
    ],
    [
        'id' => 2,
        'title' => 'Координация работ по SIM-картам Tele2',
        'author' => 'Мария Сидорова',
        'replies' => 12,
        'last_activity' => '1 час назад',
        'category' => 'SIM-карты',
        'pinned' => false
    ]
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Рабочий форум</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTopicModal">
        <i class="bi bi-plus-circle me-2"></i>Новая тема
    </button>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Список тем -->
        <div class="module-card">
            <div class="p-4 border-bottom">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Поиск по форуму...">
                    <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
                </div>
            </div>

            <?php foreach ($forum_topics as $topic): ?>
                <div class="p-4 border-bottom">
                    <div class="d-flex">
                        <?php if ($topic['pinned']): ?>
                            <div class="me-3 text-warning">
                                <i class="bi bi-pin-angle-fill"></i>
                            </div>
                        <?php endif; ?>

                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                <a href="#" class="text-decoration-none"><?= htmlspecialchars($topic['title']) ?></a>
                            </h6>
                            <div class="small text-muted mb-2">
                                <span class="badge bg-secondary me-2"><?= htmlspecialchars($topic['category']) ?></span>
                                Автор: <?= htmlspecialchars($topic['author']) ?>
                            </div>
                        </div>

                        <div class="text-end">
                            <div class="mb-1">
                                <span class="badge bg-primary"><?= $topic['replies'] ?> ответов</span>
                            </div>
                            <div class="small text-muted">
                                <?= $topic['last_activity'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Статистика форума -->
        <div class="module-card p-4 mb-4">
            <h6>Статистика форума</h6>
            <div class="mt-3">
                <div class="d-flex justify-content-between mb-2">
                    <span>Темы:</span>
                    <strong>156</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Сообщения:</span>
                    <strong>842</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Пользователи:</span>
                    <strong>24</strong>
                </div>
            </div>
        </div>

        <!-- Активные пользователи -->
        <div class="module-card p-4">
            <h6>Сейчас онлайн</h6>
            <div class="mt-3">
                <div class="d-flex align-items-center mb-2">
                    <div class="user-avatar me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">И</div>
                    <span>Иван Петров</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <div class="user-avatar me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">М</div>
                    <span>Мария Сидорова</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно новой темы -->
<div class="modal fade" id="newTopicModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Новая тема</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Заголовок темы</label>
                        <input type="text" class="form-control" placeholder="Введите заголовок..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Категория</label>
                        <select class="form-select" required>
                            <option value="">Выберите категорию</option>
                            <option value="equipment">Оборудование</option>
                            <option value="simcards">SIM-карты</option>
                            <option value="acts">Акты работ</option>
                            <option value="general">Общие вопросы</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Сообщение</label>
                        <textarea class="form-control" rows="6" placeholder="Текст сообщения..." required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary">Создать тему</button>
            </div>
        </div>
    </div>
</div>