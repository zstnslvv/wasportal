<?php
require __DIR__ . '/auth.php';
$pageTitle = 'Пароли';
$activePage = 'passwords';
require __DIR__ . '/partials/layout-start.php';
?>
<section class="panel">
    <h2>Пароли</h2>
    <p>Хранилище секретов будет подключено далее.</p>
    <div class="placeholder">
        <span>Безопасное хранение и аудит</span>
    </div>
</section>
<?php require __DIR__ . '/partials/layout-end.php'; ?>
