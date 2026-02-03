<?php
session_start();

if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    header('Location: /index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if ($username === 'admin' && $password === 'admin') {
        $_SESSION['authenticated'] = true;
        $_SESSION['user'] = 'admin';
        header('Location: /index.php');
        exit;
    }

    $error = 'Неверные учетные данные. Попробуйте admin / admin.';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Аутентификация</title>
    <link rel="stylesheet" href="/assets/styles.css">
</head>
<body class="auth-body">
<div class="auth-card">
    <div class="auth-header">
        <span class="auth-logo">WAS</span>
        <h1>Аутентификация</h1>
        <p>Вход в защищенный контур WAS Portal.</p>
    </div>
    <form class="auth-form" method="post" action="/login.php">
        <label>
            <span>Логин</span>
            <input type="text" name="username" placeholder="admin" required>
        </label>
        <label>
            <span>Пароль</span>
            <input type="password" name="password" placeholder="admin" required>
        </label>
        <?php if ($error !== ''): ?>
            <div class="auth-error"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></div>
        <?php endif; ?>
        <button type="submit" class="primary-btn">войти</button>
    </form>
    <div class="auth-footer">
        <span class="pixel-note">Доступ по уровню 01</span>
    </div>
</div>
</body>
</html>
