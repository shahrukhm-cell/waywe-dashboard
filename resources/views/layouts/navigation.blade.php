<aside class="hidden w-64 shrink-0 border-r border-slate-200 bg-white lg:flex lg:flex-col">
    <a href="{{ route('dashboard') }}" class="flex h-20 items-center gap-3 border-b border-slate-100 px-6">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-lg font-bold text-white">W</span>
        <span>
            <span class="block text-sm font-bold tracking-tight text-slate-900">WAYWE Service</span>
            <span class="block text-xs text-slate-500">Job management</span>
        </span>
    </a>

    <nav class="flex-1 space-y-1 px-3 py-6">
        <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Workspace</p>
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <span aria-hidden="true">▦</span> Overview
        </a>
        <a href="{{ route('jobs.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('jobs.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <span aria-hidden="true">▤</span> Jobs
        </a>
        <a href="{{ route('customers.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('customers.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <span aria-hidden="true">♙</span> Customers
        </a>

        <p class="px-3 pb-2 pt-8 text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Records</p>
        <a href="{{ route('jobs.deleted') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('jobs.deleted') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <span aria-hidden="true">▧</span> Deleted jobs
        </a>
    </nav>

    <div class="border-t border-slate-100 p-4">
        <div class="mb-3 truncate px-2 text-sm font-medium text-slate-700">{{ auth()->user()->name }}</div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-900">Sign out</button>
        </form>
    </div>
</aside>
