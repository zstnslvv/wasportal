<section class="grid converter-grid">
    <div class="panel panel--wide converter-panel">
        <h2>Конвертация файлов</h2>
        <p class="panel-note">LibreOffice, Tabula, OCRmyPDF+Tesseract, mPDF</p>
        <div class="drop-zone" id="drop-zone">
            <p class="drop-title">Перетащите файлы сюда или выберите вручную</p>
            <input type="file" id="file-input" multiple>
        </div>
        <div class="actions">
            <button class="btn" id="upload-btn">Загрузить</button>
            <button class="btn primary" id="convert-btn">Конвертировать</button>
        </div>
    </div>

    <div class="panel panel--wide converter-panel file-list" id="file-list">
        <h2>Очередь файлов</h2>
        <div class="file-list__header">
            <span>Файл</span>
            <span>Размер</span>
            <span>Формат</span>
            <span>Статус</span>
            <span>Прогресс</span>
            <span>Действия</span>
        </div>
        <div class="file-list__body" id="file-list-body"></div>
    </div>

    <div class="panel panel--wide converter-panel merge-section">
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
    </div>
</section>

<div class="toast" id="toast"></div>
