<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'WAYWE Service') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .datatable-wrapper{min-width:100%;display:grid;gap:.75rem}
        .datatable-toolbar{display:flex;flex-direction:column;gap:.75rem}
        @media (min-width:640px){.datatable-toolbar{flex-direction:row;align-items:flex-end;justify-content:space-between}}
        .datatable-search,.datatable-length{display:flex;flex-direction:column;gap:.25rem;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#64748b}
        .datatable-search input,.datatable-length select{border:1px solid #cbd5e1;border-radius:.5rem;color:#0f172a;font-size:.875rem;font-weight:400;letter-spacing:0;text-transform:none;box-shadow:0 1px 2px rgb(15 23 42 / .05)}
        .datatable-search input{width:100%;max-width:18rem}
        .datatable-length select{width:7rem}
        .datatable-table th.datatable-sortable{cursor:pointer;user-select:none}
        .datatable-table th.datatable-sortable::after{content:" \2195";color:#94a3b8}
        .datatable-footer{display:flex;flex-direction:column;gap:.75rem;color:#64748b;font-size:.875rem}
        @media (min-width:640px){.datatable-footer{flex-direction:row;align-items:center;justify-content:space-between}}
        .datatable-pagination{display:flex;align-items:center;gap:.5rem}
        .datatable-pagination button{border:1px solid #e2e8f0;border-radius:.5rem;background:#fff;padding:.375rem .75rem;font-size:.875rem;font-weight:700;color:#334155}
        .datatable-pagination button:hover{background:#f8fafc}
        .datatable-pagination button:disabled{cursor:not-allowed;opacity:.5}
        .datatable-pagination span{font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#64748b}
    </style>
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="flex min-h-screen">
        @include('layouts.navigation')

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur">
                <div class="flex min-h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <a href="{{ route('dashboard') }}" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-600 font-bold text-white lg:hidden">W</a>
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-slate-900">Service workspace</div>
                            <div class="hidden text-xs text-slate-500 sm:block">{{ now()->format('l, F j, Y') }}</div>
                        </div>
                    </div>
                    <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                        <span class="hidden text-sm text-slate-500 sm:inline">{{ auth()->user()->name }}</span>
                        <a href="{{ route('jobs.create') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:px-4">
                            <span class="sm:hidden">+ Job</span><span class="hidden sm:inline">+ New job</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                            @csrf
                            <button type="submit" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Sign out</button>
                        </form>
                    </div>
                </div>
                <nav class="flex flex-wrap gap-1 border-t border-slate-100 px-3 py-2 lg:hidden">
                    <a href="{{ route('dashboard') }}" class="whitespace-nowrap rounded-md px-3 py-1.5 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50' }}">Overview</a>
                    <a href="{{ route('jobs.index') }}" class="whitespace-nowrap rounded-md px-3 py-1.5 text-sm font-medium {{ request()->routeIs('jobs.*') && ! request()->routeIs('jobs.deleted') && ! request()->routeIs('jobs.calendar') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50' }}">Jobs</a>
                    <a href="{{ route('jobs.calendar') }}" class="whitespace-nowrap rounded-md px-3 py-1.5 text-sm font-medium {{ request()->routeIs('jobs.calendar') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50' }}">Calendar</a>
                    <a href="{{ route('customers.index') }}" class="whitespace-nowrap rounded-md px-3 py-1.5 text-sm font-medium {{ request()->routeIs('customers.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50' }}">Customers</a>
                    <a href="{{ route('jobs.deleted') }}" class="whitespace-nowrap rounded-md px-3 py-1.5 text-sm font-medium {{ request()->routeIs('jobs.deleted') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50' }}">Deleted</a>
                    <form method="POST" action="{{ route('logout') }}" class="sm:hidden">
                        @csrf
                        <button type="submit" class="whitespace-nowrap rounded-md px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-50">Sign out</button>
                    </form>
                </nav>
            </header>

            <main class="w-full flex-1 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                <div class="mx-auto w-full max-w-7xl">
                    @isset($header)
                        <div class="mb-6">{{ $header }}</div>
                    @endisset
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
    <script>
        (() => {
            const enhanceDataTables = (scope = document) => {
                scope.querySelectorAll('table:not([data-no-datatable])').forEach((table) => {
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
                    toolbar.innerHTML = '<label class="datatable-search"><span>Search</span><input type="search" placeholder="Search table"></label><label class="datatable-length"><span>Rows</span><select><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="all">All</option></select></label>';

                    const footer = document.createElement('div');
                    footer.className = 'datatable-footer';
                    footer.innerHTML = '<p class="datatable-info"></p><div class="datatable-pagination"><button type="button" data-page-action="previous">Previous</button><span></span><button type="button" data-page-action="next">Next</button></div>';

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

                    const filteredRows = () => {
                        const query = searchInput.value.trim().toLowerCase();
                        let rows = query === '' ? [...allRows] : allRows.filter((row) => row.textContent.toLowerCase().includes(query));

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

                        info.textContent = `Showing ${rows.length === 0 ? 0 : start + 1}-${Math.min(start + pageSize, rows.length)} of ${rows.length} rows`;
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
            };

            document.addEventListener('DOMContentLoaded', () => enhanceDataTables());
            new MutationObserver(() => enhanceDataTables()).observe(document.body, { childList: true, subtree: true });
        })();
    </script>
</body>
</html>
