<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign in · WAYWE Service</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-900 antialiased">
    <div class="grid min-h-screen lg:grid-cols-2">
        <section class="relative hidden overflow-hidden bg-gradient-to-br from-indigo-600 via-indigo-700 to-slate-900 p-12 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full border border-white/10"></div>
            <div class="absolute -right-12 -top-12 h-72 w-72 rounded-full border border-white/10"></div>
            <a href="{{ route('login') }}" class="relative flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-lg font-bold text-indigo-700">W</span>
                <span class="text-base font-bold tracking-tight">WAYWE Service</span>
            </a>
            <div class="relative max-w-lg pb-10">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-200">Service operations</p>
                <h1 class="mt-5 text-4xl font-bold leading-tight tracking-tight xl:text-5xl">Your workday, organized.</h1>
                <p class="mt-5 max-w-md text-base leading-7 text-indigo-100">Keep customer details, quotes, schedules, dispatches, and payments together in one clear workspace.</p>
            </div>
            <p class="relative text-xs text-indigo-200">Private workspace for your office team</p>
        </section>

        <main class="flex min-h-screen items-center justify-center bg-slate-50 px-5 py-12 sm:px-8">
            <div class="w-full max-w-md">
                <a href="{{ route('login') }}" class="mb-10 flex items-center justify-center gap-3 lg:hidden">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-lg font-bold text-white">W</span>
                    <span class="text-base font-bold tracking-tight text-slate-900">WAYWE Service</span>
                </a>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-9">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Welcome back</h2>
                        <p class="mt-2 text-sm text-slate-500">Sign in to continue to your workspace.</p>
                    </div>

                    @if ($errors->any())
                        <div role="alert" class="mt-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.store') }}" class="mt-7 space-y-5">
                        @csrf
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700">Email address</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="mt-2 block w-full rounded-lg border-slate-300 px-3.5 py-3 text-sm shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500" placeholder="you@business.com">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                            <input id="password" type="password" name="password" required autocomplete="current-password" class="mt-2 block w-full rounded-lg border-slate-300 px-3.5 py-3 text-sm shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Enter your password">
                        </div>
                        <label for="remember" class="flex items-center gap-2 text-sm text-slate-600">
                            <input id="remember" type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            Remember me
                        </label>
                        <button type="submit" class="flex w-full justify-center rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Sign in</button>
                    </form>
                </div>
                <p class="mt-6 text-center text-xs text-slate-500">Secure access for your service business</p>
            </div>
        </main>
    </div>
</body>
</html>
