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
        <span class="form-hint">До 100 адресов за один запрос. Поддерживаются IPv4 и IPv6.</span>
    </div>
    <div class="form-message" id="resolve-message" role="status" aria-live="polite"></div>
</section>
<section class="panel panel--wide">
    <div class="table-stack" id="resolve-table-stack"></div>
</section>
<template id="resolve-table-template">
    <article class="table-card" draggable="true">
        <div class="table-card__header">
            <div>
                <h3 class="table-card__title">Результаты</h3>
                <p class="table-card__meta"></p>
            </div>
            <div class="table-card__actions">
                <button class="icon-btn table-card__move" type="button" aria-label="Переместить таблицу">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 3 6 6h2v5H3V9l-3 3 3 3v-2h5v5H6l3 3 3-3h-2v-5h5v2l3-3-3-3v2h-5V6h2z"/></svg>
                </button>
                <button class="icon-btn icon-btn--danger table-card__delete" type="button" aria-label="Удалить таблицу">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 7h12l-1 14H7L6 7zm3-3h6l1 2H8l1-2z"/></svg>
                </button>
            </div>
        </div>
        <div class="table-toolbar">
            <div class="table-toolbar__search">
                <label>Поиск</label>
                <input type="search" placeholder="Фильтр по любому столбцу">
            </div>
            <div class="table-toolbar__actions">
                <button class="icon-btn" type="button" data-action="add-row" aria-label="Добавить строку">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 5h2v14h-2zM5 11h14v2H5z"/></svg>
                </button>
                <button class="icon-btn" type="button" data-action="add-column" aria-label="Добавить столбец">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h6v14H4zM14 11h6v2h-6zm3-6h2v14h-2z"/></svg>
                </button>
                <button class="icon-btn" type="button" data-action="export-xls" aria-label="Скачать XLS">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h7l5 5v13H7zM14 3v5h5M9 13l2 2 3-3M9 17l2 2 3-3"/></svg>
                </button>
                <button class="icon-btn" type="button" data-action="export-pdf" aria-label="Скачать PDF">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h9l5 5v13H6zM15 3v5h5M8 17h2v2H8zm4 0h2v2h-2zm4 0h2v2h-2z"/></svg>
                </button>
            </div>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                <tr>
                    <th data-sortable>
                        <button class="sort-btn" type="button" aria-label="Сортировать по IP">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 4 4 4H3l4-4zm10 16-4-4h8l-4 4z"/></svg>
                        </button>
                        <span class="header-label" contenteditable="true">IP</span>
                    </th>
                    <th data-sortable>
                        <button class="sort-btn" type="button" aria-label="Сортировать по домену">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 4 4 4H3l4-4zm10 16-4-4h8l-4 4z"/></svg>
                        </button>
                        <span class="header-label" contenteditable="true">Домен</span>
                    </th>
                    <th data-sortable>
                        <button class="sort-btn" type="button" aria-label="Сортировать по стране">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 4 4 4H3l4-4zm10 16-4-4h8l-4 4z"/></svg>
                        </button>
                        <span class="header-label" contenteditable="true">Страна</span>
                    </th>
                    <th data-sortable>
                        <button class="sort-btn" type="button" aria-label="Сортировать по региону">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 4 4 4H3l4-4zm10 16-4-4h8l-4 4z"/></svg>
                        </button>
                        <span class="header-label" contenteditable="true">Регион</span>
                    </th>
                    <th data-sortable>
                        <button class="sort-btn" type="button" aria-label="Сортировать по городу">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 4 4 4H3l4-4zm10 16-4-4h8l-4 4z"/></svg>
                        </button>
                        <span class="header-label" contenteditable="true">Город</span>
                    </th>
                    <th data-sortable>
                        <button class="sort-btn" type="button" aria-label="Сортировать по провайдеру">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 4 4 4H3l4-4zm10 16-4-4h8l-4 4z"/></svg>
                        </button>
                        <span class="header-label" contenteditable="true">Провайдер</span>
                    </th>
                    <th data-sortable>
                        <button class="sort-btn" type="button" aria-label="Сортировать по организации">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 4 4 4H3l4-4zm10 16-4-4h8l-4 4z"/></svg>
                        </button>
                        <span class="header-label" contenteditable="true">Организация</span>
                    </th>
                    <th data-sortable>
                        <button class="sort-btn" type="button" aria-label="Сортировать по ASN">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 4 4 4H3l4-4zm10 16-4-4h8l-4 4z"/></svg>
                        </button>
                        <span class="header-label" contenteditable="true">ASN</span>
                    </th>
                    <th data-sortable>
                        <button class="sort-btn" type="button" aria-label="Сортировать по статусу">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 4 4 4H3l4-4zm10 16-4-4h8l-4 4z"/></svg>
                        </button>
                        <span class="header-label" contenteditable="true">Статус</span>
                    </th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </article>
</template>
<script>
    (() => {
        const singleInput = document.getElementById('single-ip');
        const bulkInput = document.getElementById('bulk-ips');
        const resolveButton = document.getElementById('resolve-btn');
        const messageBox = document.getElementById('resolve-message');
        const tableStack = document.getElementById('resolve-table-stack');
        const tableTemplate = document.getElementById('resolve-table-template');
        const maxIps = 100;

        const ipv4Regex = /\b(?:\d{1,3}\.){3}\d{1,3}\b/g;
        const ipv6Regex = /\b(?:[A-Fa-f0-9]{1,4}:){1,7}[A-Fa-f0-9]{0,4}\b/g;

        const showMessage = (text, type = '') => {
            messageBox.textContent = text;
            messageBox.className = 'form-message' + (type ? ` ${type}` : '');
        };

        const extractIps = () => {
            const singleRaw = singleInput.value;
            const bulkRaw = bulkInput.value;
            const singleMatches = [
                ...(singleRaw.match(ipv4Regex) || []),
                ...(singleRaw.match(ipv6Regex) || [])
            ]
                .map((item) => item.trim())
                .filter((item) => item.length > 0);

            if (singleMatches.length > 1) {
                return { error: 'Поле "Один IP" должно содержать только один адрес.' };
            }

            const bulkMatches = [
                ...(bulkRaw.match(ipv4Regex) || []),
                ...(bulkRaw.match(ipv6Regex) || [])
            ]
                .map((item) => item.trim())
                .filter((item) => item.length > 0);

            const all = [...singleMatches, ...bulkMatches];
            return { ips: [...new Set(all)] };
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

        const applySearch = (tableCard) => {
            const input = tableCard.querySelector('input[type="search"]');
            const tbody = tableCard.querySelector('tbody');
            const query = input.value.trim().toLowerCase();
            tbody.querySelectorAll('tr').forEach((row) => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        };

        const sortByColumn = (tableCard, index) => {
            const tbody = tableCard.querySelector('tbody');
            const table = tableCard.querySelector('table');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const isAscending = !table.dataset.sortDir || table.dataset.sortDir === 'desc';
            rows.sort((a, b) => {
                const aText = (a.children[index]?.textContent || '').trim();
                const bText = (b.children[index]?.textContent || '').trim();
                return isAscending ? aText.localeCompare(bText, 'ru') : bText.localeCompare(aText, 'ru');
            });
            tbody.innerHTML = '';
            rows.forEach((row) => tbody.appendChild(row));
            table.dataset.sortDir = isAscending ? 'asc' : 'desc';
        };

        const bindSortHandlers = (tableCard) => {
            tableCard.querySelectorAll('thead th[data-sortable]').forEach((header, index) => {
                const sortButton = header.querySelector('.sort-btn');
                if (sortButton) {
                    sortButton.onclick = (event) => {
                        event.stopPropagation();
                        sortByColumn(tableCard, index);
                    };
                }
            });
        };

        const addRow = (tableCard) => {
            const table = tableCard.querySelector('table');
            const tbody = tableCard.querySelector('tbody');
            const columnCount = table.querySelectorAll('thead th').length;
            const tr = document.createElement('tr');
            for (let i = 0; i < columnCount; i += 1) {
                if (i === columnCount - 1) {
                    tr.appendChild(createActionCell());
                } else {
                    tr.appendChild(createCell(''));
                }
            }
            tbody.appendChild(tr);
        };

        const buildSortableHeader = (labelText) => {
            const header = document.createElement('th');
            header.dataset.sortable = 'true';

            const sortButton = document.createElement('button');
            sortButton.type = 'button';
            sortButton.className = 'sort-btn';
            sortButton.setAttribute('aria-label', 'Сортировать столбец');
            sortButton.innerHTML = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 4 4 4H3l4-4zm10 16-4-4h8l-4 4z"/></svg>';

            const label = document.createElement('span');
            label.className = 'header-label';
            label.textContent = labelText;
            label.setAttribute('contenteditable', 'true');

            header.append(sortButton, label);
            return header;
        };

        const addColumn = (tableCard) => {
            const table = tableCard.querySelector('table');
            const headerRow = table.querySelector('thead tr');
            const actionHeader = headerRow.lastElementChild;
            const newHeader = buildSortableHeader('Новый столбец');
            headerRow.insertBefore(newHeader, actionHeader);
            table.querySelectorAll('tbody tr').forEach((row) => {
                const actionCell = row.lastElementChild;
                const newCell = createCell('');
                row.insertBefore(newCell, actionCell);
            });
            bindSortHandlers(tableCard);
            const label = newHeader.querySelector('.header-label');
            if (label) {
                label.focus();
                const range = document.createRange();
                range.selectNodeContents(label);
                const selection = window.getSelection();
                selection.removeAllRanges();
                selection.addRange(range);
            }
        };

        const exportTable = (tableCard, type) => {
            const table = tableCard.querySelector('table');
            const headers = Array.from(table.querySelectorAll('thead th'))
                .slice(0, -1)
                .map((th) => {
                    const label = th.querySelector('.header-label');
                    return label ? label.textContent.trim() : th.textContent.trim();
                });
            const rows = Array.from(table.querySelectorAll('tbody tr')).map((row) => {
                return Array.from(row.children)
                    .slice(0, -1)
                    .map((cell) => cell.textContent.trim());
            });

            const tableHtml = `<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>Resolve IP</title></head><body>`
                + `<table border="1"><thead><tr>${headers.map((h) => `<th>${h}</th>`).join('')}</tr></thead>`
                + `<tbody>${rows.map((cells) => `<tr>${cells.map((cell) => `<td>${cell}</td>`).join('')}</tr>`).join('')}</tbody>`
                + `</table></body></html>`;

            if (type === 'xls') {
                const blob = new Blob([tableHtml], { type: 'application/vnd.ms-excel' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = 'resolve-ip.xls';
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

        const createTableCard = (tableId, results) => {
            const card = tableTemplate.content.firstElementChild.cloneNode(true);
            const meta = card.querySelector('.table-card__meta');
            const title = card.querySelector('.table-card__title');
            const tbody = card.querySelector('tbody');
            const searchInput = card.querySelector('input[type="search"]');
            const deleteButton = card.querySelector('.table-card__delete');
            const moveHandle = card.querySelector('.table-card__move');

            card.dataset.tableId = tableId;
            const order = tableStack.querySelectorAll('.table-card').length + 1;
            title.textContent = `Результаты #${order}`;
            meta.textContent = `Создано: ${new Date().toLocaleString('ru-RU')}. Адресов: ${results.length}.`;

            results.forEach((row) => {
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
                tbody.appendChild(tr);
            });

            card.querySelectorAll('[data-action]').forEach((button) => {
                const action = button.dataset.action;
                if (action === 'add-row') {
                    button.addEventListener('click', () => addRow(card));
                }
                if (action === 'add-column') {
                    button.addEventListener('click', () => addColumn(card));
                }
                if (action === 'export-xls') {
                    button.addEventListener('click', () => exportTable(card, 'xls'));
                }
                if (action === 'export-pdf') {
                    button.addEventListener('click', () => exportTable(card, 'pdf'));
                }
            });

            searchInput.addEventListener('input', () => applySearch(card));
            deleteButton.addEventListener('click', async () => {
                const response = await fetch('/resolve-ip-action.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete', tableId })
                });
                if (response.ok) {
                    card.remove();
                } else {
                    showMessage('Не удалось удалить таблицу на сервере.', 'form-error');
                }
            });

            moveHandle.addEventListener('mousedown', () => {
                card.dataset.allowDrag = 'true';
            });
            moveHandle.addEventListener('mouseup', () => {
                card.dataset.allowDrag = '';
            });
            document.addEventListener('mouseup', () => {
                card.dataset.allowDrag = '';
            });

            card.addEventListener('dragstart', (event) => {
                if (card.dataset.allowDrag !== 'true') {
                    event.preventDefault();
                    return;
                }
                card.classList.add('is-dragging');
            });

            card.addEventListener('dragend', () => {
                card.classList.remove('is-dragging');
                card.dataset.allowDrag = '';
            });

            tbody.addEventListener('click', (event) => {
                const row = event.target.closest('tr');
                if (!row) {
                    return;
                }
                row.classList.toggle('is-selected');
            });

            bindSortHandlers(card);
            return card;
        };

        tableStack.addEventListener('dragover', (event) => {
            event.preventDefault();
            const dragging = tableStack.querySelector('.is-dragging');
            if (!dragging) {
                return;
            }
            const afterElement = Array.from(tableStack.querySelectorAll('.table-card:not(.is-dragging)'))
                .find((card) => event.clientY <= card.getBoundingClientRect().top + card.offsetHeight / 2);
            if (afterElement) {
                tableStack.insertBefore(dragging, afterElement);
            } else {
                tableStack.appendChild(dragging);
            }
        });

        resolveButton.addEventListener('click', async () => {
            const extracted = extractIps();
            if (extracted.error) {
                showMessage(extracted.error, 'form-error');
                return;
            }
            const ips = extracted.ips || [];
            if (!ips.length) {
                showMessage('Добавьте хотя бы один IP-адрес.', 'form-error');
                return;
            }
            if (ips.length > maxIps) {
                showMessage(`Слишком много адресов: максимум ${maxIps}.`, 'form-error');
                return;
            }

            resolveButton.disabled = true;
            showMessage('Запрос выполняется...', 'form-success');

            const results = [];
            for (const ip of ips) {
                showMessage(`Резолвим: ${ip}`, 'form-success');
                try {
                    const response = await fetch('/resolve-ip-action.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'resolve', ip })
                    });
                    const payload = await response.json();
                    if (!response.ok || !payload || !payload.result) {
                        throw new Error(payload?.message || 'Ошибка запроса');
                    }
                    results.push(payload.result);
                } catch (error) {
                    results.push({
                        ip,
                        domain: '—',
                        country: '—',
                        region: '—',
                        city: '—',
                        isp: '—',
                        org: '—',
                        asn: '—',
                        status: 'error'
                    });
                }
            }

            try {
                const response = await fetch('/resolve-ip-action.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'store', results })
                });
                const payload = await response.json();
                if (!response.ok || !payload || !payload.tableId) {
                    throw new Error(payload?.message || 'Не удалось сохранить таблицу.');
                }
                const card = createTableCard(payload.tableId, results);
                tableStack.prepend(card);
                showMessage('Готово: данные обновлены.', 'form-success');
            } catch (error) {
                showMessage(error.message || 'Ошибка сохранения данных.', 'form-error');
            } finally {
                resolveButton.disabled = false;
            }
        });
    })();
</script>
<?php require __DIR__ . '/partials/layout-end.php'; ?>
