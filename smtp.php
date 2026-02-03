<?php
require __DIR__ . '/auth.php';
$pageTitle = 'SMTP';
$activePage = 'smtp';
require __DIR__ . '/partials/layout-start.php';
?>
<section class="panel">
    <h2>SMTP</h2>
    <p>Настройки отправки почтовых уведомлений.</p>
    <div class="placeholder">
        <span>SMTP настройки в очереди</span>
    </div>
</section>
<?php require __DIR__ . '/partials/layout-end.php'; ?>
