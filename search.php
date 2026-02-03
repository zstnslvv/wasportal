<?php
require __DIR__ . '/auth.php';
$pageTitle = 'Поиск';
$activePage = 'search';
require __DIR__ . '/partials/layout-start.php';
?>
<section class="panel">
    <h2>Поиск</h2>
    <p>Секция готова для подключения движка поиска.</p>
    <div class="placeholder">
        <span>Поиск по Docker, времени, месту, пользователю</span>
    </div>
</section>
<?php require __DIR__ . '/partials/layout-end.php'; ?>
