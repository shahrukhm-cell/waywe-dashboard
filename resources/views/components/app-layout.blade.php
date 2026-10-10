<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'WAYWE Service') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                    <a href="{{ route('jobs.index') }}" class="whitespace-nowrap rounded-md px-3 py-1.5 text-sm font-medium {{ request()->routeIs('jobs.*') && ! request()->routeIs('jobs.deleted') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50' }}">Jobs</a>
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
</body>
</html>
