<?php
require __DIR__ . '/auth.php';
$pageTitle = 'Настройки';
$activePage = 'settings';
$extraHead = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" referrerpolicy="no-referrer">';
$extraScripts = '<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js" referrerpolicy="no-referrer"></script>';
require __DIR__ . '/partials/layout-start.php';
?>
<section class="grid">
    <div class="panel">
        <h2>Профиль</h2>
        <p>Измените название портала и загрузите логотип в формате PNG или JPG.</p>
        <form class="settings-form" data-profile-form>
            <label>
                <span>Название портала</span>
                <input type="text" name="portalTitle" placeholder="WAS Portal">
            </label>
            <button type="submit" class="primary-btn">сохранить</button>
        </form>
        <div class="logo-uploader" data-logo-uploader>
            <div class="logo-preview" data-logo-preview>
                <img src="/assets/logo-placeholder.svg" alt="Логотип">
            </div>
            <div class="logo-actions">
                <label class="logo-input">
                    <span>Выбрать файл</span>
                    <input type="file" name="logo" accept="image/png, image/jpeg" data-logo-input>
                </label>
                <button type="button" class="secondary-btn" data-logo-reset>сбросить</button>
                <button type="button" class="primary-btn" data-logo-save>загрузить</button>
            </div>
            <p class="panel-note">Логотип будет вписан в небольшой блок без выхода за границы. Доступно редактирование кадра.</p>
            <div class="form-feedback" data-logo-feedback></div>
        </div>
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
