<?php
if (!isset($pageTitle)) {
    $pageTitle = 'WAS Portal';
}
if (!isset($activePage)) {
    $activePage = '';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES); ?></title>
    <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand" data-profile>
            <div class="brand__avatar" data-avatar>
                <span>WP</span>
            </div>
            <div class="brand__text">
                <span class="brand__pixel" data-title>WAS Portal</span>
                <span class="brand__label">Secure Console</span>
            </div>
        </div>
        <nav class="nav">
            <a class="nav-link<?php echo $activePage === 'home' ? ' is-active' : ''; ?>" href="/index.php" data-icon="home">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 11L12 4l8 7v9a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1z"/></svg>
                <span>главная</span>
            </a>
            <a class="nav-link<?php echo $activePage === 'search' ? ' is-active' : ''; ?>" href="/search.php" data-icon="search">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 3a7 7 0 1 1 0 14 7 7 0 0 1 0-14zm9.5 16.5-4.2-4.2"/></svg>
                <span>поиск</span>
            </a>
            <a class="nav-link<?php echo $activePage === 'convert' ? ' is-active' : ''; ?>" href="/convert.php" data-icon="convert">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 4h10v4m0 12H7v-4m10-8-3-3m3 3-3 3M7 16l3 3m-3-3 3-3"/></svg>
                <span>конвертация</span>
            </a>
            <a class="nav-link<?php echo $activePage === 'passwords' ? ' is-active' : ''; ?>" href="/passwords.php" data-icon="passwords">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 11V8a5 5 0 0 1 10 0v3M6 11h12v9H6z"/></svg>
                <span>пароли</span>
            </a>
            <button class="nav-toggle" type="button" data-toggle="settings">
                <span class="nav-toggle__label">настройки</span>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
            </button>
            <div class="nav-submenu" data-submenu="settings">
                <a class="nav-link<?php echo $activePage === 'settings' ? ' is-active' : ''; ?>" href="/settings.php" data-icon="settings">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 8a4 4 0 1 1 0 8 4 4 0 0 1 0-8zm9 4-2.2.8.4 2.3-2 1.2-1.5-1.8-2 .8-1-2.1-2.2-.1-1 2.1-2-.8-1.5 1.8-2-1.2.4-2.3L3 12l2.2-.8-.4-2.3 2-1.2 1.5 1.8 2-.8 1-2.1 2.2.1 1 2.1 2-.8 1.5-1.8 2 1.2-.4 2.3z"/></svg>
                    <span>профиль</span>
                </a>
                <a class="nav-link<?php echo $activePage === 'integrations' ? ' is-active' : ''; ?>" href="/integrations.php" data-icon="integrations">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 7h4v4H7zM13 13h4v4h-4zM13 7h4v4h-4zM7 13h4v4H7z"/></svg>
                    <span>интеграции</span>
                </a>
            </div>
            <button class="nav-toggle" type="button" data-toggle="admin">
                <span class="nav-toggle__label">администрирование</span>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
            </button>
            <div class="nav-submenu" data-submenu="admin">
                <a class="nav-link<?php echo $activePage === 'admin' ? ' is-active' : ''; ?>" href="/admin.php" data-icon="admin">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2 3 5v6c0 5 3.8 9.7 9 11 5.2-1.3 9-6 9-11V5l-9-3z"/></svg>
                    <span>доступы</span>
                </a>
                <a class="nav-link<?php echo $activePage === 'reports' ? ' is-active' : ''; ?>" href="/reports.php" data-icon="reports">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h12v18H6zM8 7h8M8 11h8M8 15h5"/></svg>
                    <span>отчеты</span>
                </a>
                <a class="nav-link<?php echo $activePage === 'stats' ? ' is-active' : ''; ?>" href="/statistics.php" data-icon="stats">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 19V9m7 10V5m7 14v-6"/></svg>
                    <span>статистика</span>
                </a>
                <a class="nav-link<?php echo $activePage === 'ldap' ? ' is-active' : ''; ?>" href="/ldap.php" data-icon="ldap">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                    <span>ldap</span>
                </a>
                <a class="nav-link<?php echo $activePage === 'smtp' ? ' is-active' : ''; ?>" href="/smtp.php" data-icon="smtp">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16v12H4zM4 7l8 5 8-5"/></svg>
                    <span>smtp</span>
                </a>
            </div>
        </nav>
        <div class="sidebar-footer">
            <a class="logout" href="/logout.php">выйти</a>
        </div>
    </aside>
    <main class="content">
        <header class="page-header">
            <h1><?php echo htmlspecialchars($pageTitle, ENT_QUOTES); ?></h1>
            <div class="header-meta">
                <span class="status-dot"></span>
                <span>онлайн</span>
            </div>
        </header>
        <div class="page-body">
