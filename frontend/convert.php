<?php
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Конвертация файлов (Free engines)</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <main class="container">
        <header class="header">
            <h1>Конвертация файлов (Free engines)</h1>
            <p class="subtitle">LibreOffice, Tabula, OCRmyPDF+Tesseract, mPDF</p>
        </header>

        <section class="upload-section">
            <div class="drop-zone" id="drop-zone">
                <p class="drop-title">Перетащите файлы сюда или выберите вручную</p>
                <input type="file" id="file-input" multiple>
            </div>
            <div class="actions">
                <button class="btn" id="upload-btn">Загрузить</button>
                <button class="btn primary" id="convert-btn">Конвертировать</button>
            </div>
        </section>

        <section class="file-list" id="file-list">
            <div class="file-list__header">
                <span>Файл</span>
                <span>Размер</span>
                <span>Формат</span>
                <span>Статус</span>
                <span>Прогресс</span>
                <span>Действия</span>
            </div>
            <div class="file-list__body" id="file-list-body"></div>
        </section>

        <section class="merge-section">
            <h2>Собрать один PDF из изображений</h2>
            <div class="merge-controls">
                <input type="file" id="merge-input" multiple accept="image/png,image/jpeg">
                <label class="checkbox">
                    <input type="checkbox" id="merge-separate" checked>
                    <span>каждое изображение на отдельной странице</span>
                </label>
                <button class="btn primary" id="merge-btn">Собрать PDF</button>
            </div>
            <div class="merge-status" id="merge-status"></div>
        </section>
    </main>

    <div class="toast" id="toast"></div>

    <script src="/assets/app.js"></script>
</body>
</html>
