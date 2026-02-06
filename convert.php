<?php
require __DIR__ . '/auth.php';
$pageTitle = 'Конвертация';
$activePage = 'convert';
$extraStyles = ['/converter/assets/style.css'];
$extraScripts = ['/converter/assets/app.js'];
require __DIR__ . '/partials/layout-start.php';
require __DIR__ . '/frontend/convert.php';
require __DIR__ . '/partials/layout-end.php';
