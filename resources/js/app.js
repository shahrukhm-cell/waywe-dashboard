import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const collectionCurrency = new Intl.NumberFormat(undefined, {
    style: 'currency',
    currency: 'USD',
});

async function refreshCollections(url, updateHistory = true) {
    const currentSection = document.getElementById('payment-collections');

    if (!currentSection) {
        return;
    }

    const errorMessage = currentSection.querySelector('[data-collection-error]');
    const applyButton = currentSection.querySelector('form button[type="submit"], form button:not([type])');
    currentSection.setAttribute('aria-busy', 'true');
    if (applyButton) {
        applyButton.disabled = true;
        applyButton.textContent = 'Loading…';
    }
    if (errorMessage) {
        errorMessage.classList.add('hidden');
        errorMessage.textContent = '';
    }

    try {
        const response = await fetch(url, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            if (response.headers.get('content-type')?.includes('application/json')) {
                const result = await response.json();
                const messages = Object.values(result.errors ?? {}).flat();
                throw new Error(messages.join(' ') || 'Unable to load payment collections.');
            }

            throw new Error('Unable to load payment collections.');
        }

        const html = await response.text();
        const documentFragment = new DOMParser().parseFromString(html, 'text/html');
        const updatedSection = documentFragment.getElementById('payment-collections');

        if (!updatedSection) {
            throw new Error('The payment collections response was incomplete.');
        }

        currentSection.replaceWith(updatedSection);
        enhanceDataTables(updatedSection);
        const summary = document.querySelector('[data-collection-summary-value]');
        if (summary) {
            summary.textContent = collectionCurrency.format(Number(updatedSection.dataset.total));
        }
        if (updateHistory) {
            window.history.pushState({}, '', url);
        }
    } catch (error) {
        const messageTarget = currentSection.querySelector('[data-collection-error]');
        if (messageTarget) {
            messageTarget.textContent = error instanceof Error ? error.message : 'Unable to load payment collections.';
            messageTarget.classList.remove('hidden');
        }
    } finally {
        const section = document.getElementById('payment-collections');
        const button = section?.querySelector('form button[type="submit"], form button:not([type])');
        section?.removeAttribute('aria-busy');
        if (button) {
            button.disabled = false;
            button.textContent = 'Apply filters';
        }
    }
}

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement) || !form.closest('#payment-collections')) {
        return;
    }

    event.preventDefault();
    const url = new URL(form.action, window.location.origin);
    new FormData(form).forEach((value, key) => {
        if (typeof value === 'string' && value !== '') {
            url.searchParams.set(key, value);
        }
    });
    url.searchParams.delete('all_dates');
    url.searchParams.delete('page');
    refreshCollections(url, true);
});

document.addEventListener('click', (event) => {
    const link = event.target instanceof Element ? event.target.closest('#payment-collections a') : null;

    if (!link) {
        return;
    }

    const isAllDates = link.hasAttribute('data-collection-all-dates');
    const isPagination = Boolean(link.closest('#payment-collections nav') && new URL(link.href).searchParams.has('page'));

    if (!isAllDates && !isPagination) {
        return;
    }

    event.preventDefault();
    refreshCollections(link.href, true);
});

window.addEventListener('popstate', () => {
    if (document.getElementById('payment-collections')) {
        refreshCollections(window.location.href, false);
    }
});

function enhanceDataTables(scope = document) {
    const tables = scope.querySelectorAll('table:not([data-no-datatable])');

    tables.forEach((table) => {
        if (table.dataset.datatableInitialized === 'true' || table.closest('form') || !table.tHead || !table.tBodies.length) {
            return;
        }

        const tbody = table.tBodies[0];
        const allRows = Array.from(tbody.rows);

        if (allRows.length <= 1) {
            return;
        }

        table.dataset.datatableInitialized = 'true';
        table.classList.add('datatable-table');

        const wrapper = document.createElement('div');
        wrapper.className = 'datatable-wrapper';
        table.parentNode.insertBefore(wrapper, table);

        const toolbar = document.createElement('div');
        toolbar.className = 'datatable-toolbar';
        toolbar.innerHTML = `
            <label class="datatable-search">
                <span>Search</span>
                <input type="search" placeholder="Search table">
            </label>
            <label class="datatable-length">
                <span>Rows</span>
                <select>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="all">All</option>
                </select>
            </label>
        `;

        const footer = document.createElement('div');
        footer.className = 'datatable-footer';
        footer.innerHTML = `
            <p class="datatable-info"></p>
            <div class="datatable-pagination">
                <button type="button" data-page-action="previous">Previous</button>
                <span></span>
                <button type="button" data-page-action="next">Next</button>
            </div>
        `;

        wrapper.appendChild(toolbar);
        wrapper.appendChild(table);
        wrapper.appendChild(footer);

        const searchInput = toolbar.querySelector('input');
        const lengthSelect = toolbar.querySelector('select');
        const info = footer.querySelector('.datatable-info');
        const pageLabel = footer.querySelector('.datatable-pagination span');
        const previousButton = footer.querySelector('[data-page-action="previous"]');
        const nextButton = footer.querySelector('[data-page-action="next"]');
        let currentPage = 1;
        let sortColumn = null;
        let sortDirection = 'asc';

        const rowText = (row) => row.textContent.toLowerCase();
        const filteredRows = () => {
            const query = searchInput.value.trim().toLowerCase();
            let rows = query === '' ? [...allRows] : allRows.filter((row) => rowText(row).includes(query));

            if (sortColumn !== null) {
                rows.sort((first, second) => {
                    const firstValue = first.cells[sortColumn]?.textContent.trim() ?? '';
                    const secondValue = second.cells[sortColumn]?.textContent.trim() ?? '';
                    const result = firstValue.localeCompare(secondValue, undefined, { numeric: true, sensitivity: 'base' });

                    return sortDirection === 'asc' ? result : -result;
                });
            }

            return rows;
        };

        const render = () => {
            const rows = filteredRows();
            const pageSize = lengthSelect.value === 'all' ? rows.length || 1 : Number(lengthSelect.value);
            const pageCount = Math.max(Math.ceil(rows.length / pageSize), 1);
            currentPage = Math.min(currentPage, pageCount);
            const start = (currentPage - 1) * pageSize;
            const visibleRows = rows.slice(start, start + pageSize);

            allRows.forEach((row) => {
                row.hidden = true;
                tbody.appendChild(row);
            });
            visibleRows.forEach((row) => {
                row.hidden = false;
                tbody.appendChild(row);
            });

            const firstVisible = rows.length === 0 ? 0 : start + 1;
            const lastVisible = Math.min(start + pageSize, rows.length);
            info.textContent = `Showing ${firstVisible}-${lastVisible} of ${rows.length} rows`;
            pageLabel.textContent = `Page ${currentPage} of ${pageCount}`;
            previousButton.disabled = currentPage === 1;
            nextButton.disabled = currentPage === pageCount;
        };

        Array.from(table.tHead.rows[0].cells).forEach((cell, index) => {
            cell.classList.add('datatable-sortable');
            cell.tabIndex = 0;
            cell.addEventListener('click', () => {
                if (sortColumn === index) {
                    sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    sortColumn = index;
                    sortDirection = 'asc';
                }
                currentPage = 1;
                render();
            });
            cell.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    cell.click();
                }
            });
        });

        searchInput.addEventListener('input', () => {
            currentPage = 1;
            render();
        });
        lengthSelect.addEventListener('change', () => {
            currentPage = 1;
            render();
        });
        previousButton.addEventListener('click', () => {
            currentPage -= 1;
            render();
        });
        nextButton.addEventListener('click', () => {
            currentPage += 1;
            render();
        });

        render();
    });
}

document.addEventListener('DOMContentLoaded', () => enhanceDataTables());
document.addEventListener('datatable:refresh', (event) => enhanceDataTables(event.target));
