<?php
require __DIR__ . '/auth.php';
$pageTitle = 'Пользователи';
$activePage = 'admin-users';
require __DIR__ . '/partials/layout-start.php';
?>
<section class="grid">
    <div class="panel panel--wide">
        <h2>Создание пользователя</h2>
        <p class="panel-note">Подготовка к ролям и доступам — поля роли появятся в следующем обновлении.</p>
        <form class="form-grid" data-users-form>
            <label class="form-field">
                <span>Login <strong>*</strong></span>
                <input type="text" name="login" placeholder="user.login" required>
            </label>
            <label class="form-field">
                <span>ФИО</span>
                <input type="text" name="fullName" placeholder="Иванов Иван Иванович">
            </label>
            <label class="form-field">
                <span>Почта</span>
                <input type="email" name="email" placeholder="user@company.ru">
            </label>
            <label class="form-field">
                <span>Пароль <strong>*</strong></span>
                <input type="password" name="password" autocomplete="new-password" required>
            </label>
            <label class="form-field">
                <span>Повтор пароля <strong>*</strong></span>
                <input type="password" name="confirmPassword" autocomplete="new-password" required>
            </label>
            <div class="form-hint">
                Пароль должен состоять из 16 символов и содержать буквы, цифры и спецсимволы.
            </div>
            <div class="form-feedback" data-users-feedback></div>
            <button type="submit" class="primary-btn">создать</button>
        </form>
    </div>
    <div class="panel">
        <h2>Активные пользователи</h2>
        <div class="user-list">
            <div class="user-card">
                <div>
                    <div class="user-name">Security Admin</div>
                    <div class="user-meta">admin • root@was.local</div>
                </div>
                <span class="user-tag">системный</span>
            </div>
            <div class="user-card">
                <div>
                    <div class="user-name">Служба интеграций</div>
                    <div class="user-meta">integration.bot • bot@was.local</div>
                </div>
                <span class="user-tag">технический</span>
            </div>
            <div class="user-card user-card--placeholder">
                <span>Новые пользователи будут появляться здесь после добавления ролей.</span>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/partials/layout-end.php'; ?>
