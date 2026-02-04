<?php
require __DIR__ . '/auth.php';
$pageTitle = 'Resolve IP';
$activePage = 'resolve-ip';
require __DIR__ . '/partials/layout-start.php';
?>
<section class="panel panel--wide">
    <h2>Resolve IP</h2>
    <p class="panel-note">
        Страница предназначена для быстрых запросов по IP-адресам: домен (reverse DNS), страна, регион,
        провайдер и другие признаки, важные для ИБ. Вставьте один IP или список IP-адресов, затем нажмите
        «Resolve» и получите таблицу для дальнейшей обработки и экспорта.
    </p>
</section>
<section class="panel">
    <h2>Ввод данных</h2>
    <div class="form-grid">
        <div class="form-field">
            <label for="single-ip">Один IP</label>
            <input id="single-ip" type="text" placeholder="Например: 8.8.8.8">
        </div>
        <div class="form-field form-field--full">
            <label for="bulk-ips">Список IP (копировать/вставить)</label>
            <textarea id="bulk-ips" rows="5" placeholder="IP-адреса можно разделять пробелами, запятыми, строками или любыми символами"></textarea>
        </div>
    </div>
    <div class="action-row">
        <button class="btn" id="resolve-btn" type="button">Resolve</button>
        <span class="form-hint">Поддерживаются IPv4 и IPv6.</span>
    </div>
    <div class="form-message" id="resolve-message" role="status" aria-live="polite"></div>
</section>
<section class="panel panel--wide">
    <div class="table-toolbar">
        <div class="table-toolbar__search">
            <label for="resolve-search">Поиск</label>
            <input id="resolve-search" type="search" placeholder="Фильтр по любому столбцу">
        </div>
        <div class="table-toolbar__actions">
            <button class="btn btn-secondary" type="button" id="add-row-btn">Добавить строку</button>
            <button class="btn btn-secondary" type="button" id="add-column-btn">Добавить столбец</button>
            <button class="btn btn-secondary" type="button" id="export-xlsx-btn">Скачать XLSX</button>
            <button class="btn btn-secondary" type="button" id="export-pdf-btn">Скачать PDF</button>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="data-table" id="resolve-table">
            <thead>
            <tr>
                <th data-sortable>IP</th>
                <th data-sortable>Домен</th>
                <th data-sortable>Страна</th>
                <th data-sortable>Регион</th>
                <th data-sortable>Город</th>
                <th data-sortable>Провайдер</th>
                <th data-sortable>Организация</th>
                <th data-sortable>ASN</th>
                <th data-sortable>Статус</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody id="resolve-table-body"></tbody>
        </table>
    </div>
</section>
<script>
    const resolvePage = (() => {
        const singleInput = document.getElementById('single-ip');
        const bulkInput = document.getElementById('bulk-ips');
        const resolveButton = document.getElementById('resolve-btn');
        const messageBox = document.getElementById('resolve-message');
        const tableBody = document.getElementById('resolve-table-body');
        const table = document.getElementById('resolve-table');
        const searchInput = document.getElementById('resolve-search');
        const addRowButton = document.getElementById('add-row-btn');
        const addColumnButton = document.getElementById('add-column-btn');
        const exportXlsxButton = document.getElementById('export-xlsx-btn');
        const exportPdfButton = document.getElementById('export-pdf-btn');

        const ipv4Regex = /\b(?:\d{1,3}\.){3}\d{1,3}\b/g;
        const ipv6Regex = /\b(?:[A-Fa-f0-9]{1,4}:){1,7}[A-Fa-f0-9]{0,4}\b/g;

        const showMessage = (text, type = '') => {
            messageBox.textContent = text;
            messageBox.className = 'form-message' + (type ? ` ${type}` : '');
        };

        const extractIps = () => {
            const raw = `${singleInput.value}\n${bulkInput.value}`;
            const ipv4Matches = raw.match(ipv4Regex) || [];
            const ipv6Matches = raw.match(ipv6Regex) || [];
            const all = [...ipv4Matches, ...ipv6Matches]
                .map((item) => item.trim())
                .filter((item) => item.length > 0);
            return [...new Set(all)];
        };

        const createCell = (text = '', editable = true) => {
            const cell = document.createElement('td');
            if (editable) {
                cell.setAttribute('contenteditable', 'true');
            }
            cell.textContent = text;
            return cell;
        };

        const createActionCell = () => {
            const cell = document.createElement('td');
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'btn btn-danger btn-xs';
            removeButton.textContent = 'Удалить';
            removeButton.addEventListener('click', () => {
                cell.closest('tr').remove();
            });
            cell.append(removeButton);
            return cell;
        };

        const renderRows = (rows) => {
            tableBody.innerHTML = '';
            rows.forEach((row) => {
                const tr = document.createElement('tr');
                tr.append(
                    createCell(row.ip, false),
                    createCell(row.domain),
                    createCell(row.country),
                    createCell(row.region),
                    createCell(row.city),
                    createCell(row.isp),
                    createCell(row.org),
                    createCell(row.asn),
                    createCell(row.status, false),
                    createActionCell()
                );
                tableBody.appendChild(tr);
            });
        };

        const applySearch = () => {
            const query = searchInput.value.trim().toLowerCase();
            tableBody.querySelectorAll('tr').forEach((row) => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        };

        const sortByColumn = (index) => {
            const rows = Array.from(tableBody.querySelectorAll('tr'));
            const isAscending = !table.dataset.sortDir || table.dataset.sortDir === 'desc';
            rows.sort((a, b) => {
                const aText = (a.children[index]?.textContent || '').trim();
                const bText = (b.children[index]?.textContent || '').trim();
                return isAscending ? aText.localeCompare(bText, 'ru') : bText.localeCompare(aText, 'ru');
            });
            tableBody.innerHTML = '';
            rows.forEach((row) => tableBody.appendChild(row));
            table.dataset.sortDir = isAscending ? 'asc' : 'desc';
        };

        const addRow = () => {
            const columnCount = table.querySelectorAll('thead th').length;
            const tr = document.createElement('tr');
            for (let i = 0; i < columnCount; i += 1) {
                if (i === columnCount - 1) {
                    tr.appendChild(createActionCell());
                } else {
                    tr.appendChild(createCell(''));
                }
            }
            tableBody.appendChild(tr);
        };

        const addColumn = () => {
            const name = prompt('Название нового столбца');
            if (!name) {
                return;
            }
            const headerRow = table.querySelector('thead tr');
            const actionHeader = headerRow.lastElementChild;
            const newHeader = document.createElement('th');
            newHeader.textContent = name;
            newHeader.dataset.sortable = 'true';
            headerRow.insertBefore(newHeader, actionHeader);
            tableBody.querySelectorAll('tr').forEach((row) => {
                const actionCell = row.lastElementChild;
                const newCell = createCell('');
                row.insertBefore(newCell, actionCell);
            });
            bindSortHandlers();
        };

        const exportTable = (type) => {
            const headers = Array.from(table.querySelectorAll('thead th'))
                .slice(0, -1)
                .map((th) => th.textContent.trim());
            const rows = Array.from(tableBody.querySelectorAll('tr')).map((row) => {
                return Array.from(row.children)
                    .slice(0, -1)
                    .map((cell) => cell.textContent.trim());
            });

            const tableHtml = `<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>Resolve IP</title></head><body>`
                + `<table border="1"><thead><tr>${headers.map((h) => `<th>${h}</th>`).join('')}</tr></thead>`
                + `<tbody>${rows.map((cells) => `<tr>${cells.map((cell) => `<td>${cell}</td>`).join('')}</tr>`).join('')}</tbody>`
                + `</table></body></html>`;

            if (type === 'xlsx') {
                const blob = new Blob([tableHtml], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = 'resolve-ip.xlsx';
                link.click();
                URL.revokeObjectURL(url);
                return;
            }

            const printWindow = window.open('', '_blank');
            if (printWindow) {
                printWindow.document.open();
                printWindow.document.write(tableHtml);
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
            }
        };

        const bindSortHandlers = () => {
            table.querySelectorAll('thead th[data-sortable]').forEach((header, index) => {
                header.style.cursor = 'pointer';
                header.onclick = () => sortByColumn(index);
            });
        };

        resolveButton.addEventListener('click', async () => {
            const ips = extractIps();
            if (!ips.length) {
                showMessage('Добавьте хотя бы один IP-адрес.', 'form-error');
                return;
            }
            showMessage('Запрос выполняется...', 'form-success');
            try {
                const response = await fetch('/resolve-ip-action.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ips })
                });
                const payload = await response.json();
                if (!response.ok || !payload || !Array.isArray(payload.results)) {
                    throw new Error(payload?.message || 'Не удалось получить данные.');
                }
                renderRows(payload.results);
                applySearch();
                showMessage('Готово: данные обновлены.', 'form-success');
            } catch (error) {
                showMessage(error.message || 'Ошибка запроса.', 'form-error');
            }
        });

        searchInput.addEventListener('input', applySearch);
        addRowButton.addEventListener('click', addRow);
        addColumnButton.addEventListener('click', addColumn);
        exportXlsxButton.addEventListener('click', () => exportTable('xlsx'));
        exportPdfButton.addEventListener('click', () => exportTable('pdf'));
        bindSortHandlers();

        return {};
    })();
</script>
<?php require __DIR__ . '/partials/layout-end.php'; ?>
