<?php
require __DIR__ . '/auth.php';
$pageTitle = 'Главная';
$activePage = 'home';
require __DIR__ . '/partials/layout-start.php';
?>
<section class="grid">
    <div class="panel panel--wide">
        <h2>Схема работы приложения</h2>
        <div class="schema">
            <div class="schema-item">
                <span class="schema-label">Наименование сервера</span>
                <span class="schema-value"><?php echo htmlspecialchars(php_uname('n'), ENT_QUOTES); ?></span>
            </div>
            <div class="schema-item">
                <span class="schema-label">Папка проекта</span>
                <span class="schema-value"><?php echo htmlspecialchars(basename(__DIR__), ENT_QUOTES); ?></span>
            </div>
            <div class="schema-item">
                <span class="schema-label">Время</span>
                <span class="schema-value"><?php echo date('H:i:s'); ?></span>
            </div>
            <div class="schema-item">
                <span class="schema-label">Пользователь</span>
                <span class="schema-value"><?php echo htmlspecialchars(get_current_user(), ENT_QUOTES); ?></span>
            </div>
        </div>
    </div>
    <div class="panel">
        <h2>Кастомизация ресурса</h2>
        <p class="panel-note">Акцент, режим и плотность интерфейса сохраняются.</p>
        <div class="theme-picker" data-theme-picker>
            <button class="theme-swatch" type="button" data-accent="#3b7cff" aria-label="синий"></button>
            <button class="theme-swatch" type="button" data-accent="#57a0ff" aria-label="голубой"></button>
            <button class="theme-swatch" type="button" data-accent="#2b5fd8" aria-label="индиго"></button>
            <button class="theme-swatch" type="button" data-accent="#8ab8ff" aria-label="лед"></button>
        </div>
        <div class="toggle-row">
            <button class="toggle-pill" type="button" data-toggle-theme="contrast">контраст</button>
            <button class="toggle-pill" type="button" data-toggle-theme="compact">компакт</button>
        </div>
    </div>
    <div class="panel">
        <h2>Интерфейс</h2>
        <p>Минималистичный пиксельный UI с активными иконками.</p>
        <div class="icon-row">
            <button class="icon-button" type="button" aria-label="иконка 1">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 5h14v14H5z"/></svg>
            </button>
            <button class="icon-button" type="button" aria-label="иконка 2">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4 20 20H4z"/></svg>
            </button>
            <button class="icon-button" type="button" aria-label="иконка 3">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8"/></svg>
            </button>
        </div>
    </div>
</section>
<?php require __DIR__ . '/partials/layout-end.php'; ?>
