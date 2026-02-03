<?php
require __DIR__ . '/auth.php';
$pageTitle = 'Настройки';
$activePage = 'settings';
require __DIR__ . '/partials/layout-start.php';
?>
<section class="grid">
    <div class="panel">
        <h2>Профиль</h2>
        <p>Измените название портала и загрузите круглый аватар.</p>
        <form class="settings-form" data-profile-form>
            <label>
                <span>Название портала</span>
                <input type="text" name="portalTitle" placeholder="WAS Portal">
            </label>
            <label>
                <span>URL аватара</span>
                <input type="url" name="portalAvatar" placeholder="https://.../avatar.png">
            </label>
            <button type="submit" class="primary-btn">сохранить</button>
        </form>
    </div>
    <div class="panel">
        <h2>Персонализация</h2>
        <p class="panel-note">Настройте внешний вид глобально.</p>
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
</section>
<?php require __DIR__ . '/partials/layout-end.php'; ?>
