<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold tracking-tight text-slate-900">Overview</h1>
        <p class="mt-1 text-sm text-slate-500">Your service business at a glance.</p>
    </x-slot>

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <section class="mb-7 flex flex-col justify-between gap-5 rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-950 p-6 text-white shadow-sm sm:p-8 md:flex-row md:items-center">
        <div>
            <p class="text-sm font-medium text-indigo-200">{{ now()->format('l, F j') }}</p>
            <h1 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">Good day, {{ auth()->user()->name }}</h1>
            <p class="mt-2 max-w-xl text-sm leading-6 text-slate-300">Your service operations at a glance. Keep quotes, schedules, and payments moving.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('jobs.create') }}" class="inline-flex items-center justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 shadow-sm hover:bg-indigo-50">Create job / quote</a>
            <a href="{{ route('customers.create') }}" class="inline-flex items-center justify-center rounded-lg border border-white/25 px-4 py-2.5 text-sm font-semibold text-white hover:bg-white/10">Add customer</a>
        </div>
    </section>

    <section aria-label="Job summary" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @php
            $cards = [
                ['label' => 'Open quotes', 'value' => $stats['quotes'], 'hint' => 'Waiting for approval', 'icon' => 'Q', 'color' => 'bg-amber-50 text-amber-700'],
                ['label' => 'Scheduled jobs', 'value' => $stats['scheduled'], 'hint' => 'Ready for the team', 'icon' => 'S', 'color' => 'bg-sky-50 text-sky-700'],
                ['label' => 'Completed', 'value' => $stats['completed'], 'hint' => 'Work finished', 'icon' => 'C', 'color' => 'bg-emerald-50 text-emerald-700'],
                ['label' => 'Outstanding balance', 'value' => '$' . number_format($stats['outstanding'], 2), 'hint' => 'Payments to collect', 'icon' => '$', 'color' => 'bg-violet-50 text-violet-700'],
                ['label' => 'Collected', 'value' => '$' . number_format($stats['collected'], 2), 'hint' => 'For the selected period and filters', 'icon' => '+', 'color' => 'bg-indigo-50 text-indigo-700'],
            ];
        @endphp
        @foreach ($cards as $card)
            <article @if ($card['label'] === 'Collected') data-collection-summary @endif class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">{{ $card['label'] }}</p>
                        <p @if ($card['label'] === 'Collected') data-collection-summary-value @endif class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ $card['value'] }}</p>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl text-sm font-bold {{ $card['color'] }}">{{ $card['icon'] }}</span>
                </div>
                <p class="mt-3 text-xs text-slate-500">{{ $card['hint'] }}</p>
            </article>
        @endforeach
    </section>

    @include('dashboard.partials.collections')

    <section class="mt-7 grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1.65fr)_minmax(300px,0.85fr)]">
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-6">
                <div>
                    <h2 class="font-semibold text-slate-900">Recent jobs</h2>
                    <p class="mt-1 text-xs text-slate-500">The latest quotes and service work</p>
                </div>
                <a href="{{ route('jobs.index') }}" class="shrink-0 text-sm font-semibold text-indigo-600 hover:text-indigo-800">All jobs <span aria-hidden="true">→</span></a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 sm:px-6">Job / Quote</th>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right sm:px-6">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($jobs as $job)
                            <tr class="hover:bg-slate-50/70">
                                <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                                    <a href="{{ route('jobs.show', $job) }}" class="font-semibold text-slate-900 hover:text-indigo-700">{{ $job->job_number ?: $job->quote_estimate_number ?: 'Job #' . $job->id }}</a>
                                    <p class="mt-1 text-xs text-slate-500">{{ ucfirst($job->job_type) }} · {{ $job->created_at->format('M j, Y') }}</p>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $job->customer?->full_name ?? $job->site_full_name ?? 'No customer' }}</td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                                        {{ $job->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : ($job->status === 'scheduled' ? 'bg-sky-50 text-sky-700' : ($job->status === 'quote' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-700')) }}">
                                        {{ ucfirst($job->status) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-slate-900 sm:px-6">${{ number_format((float) $job->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center">
                                <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-slate-100 text-slate-500">▤</div>
                                <p class="mt-3 font-medium text-slate-900">No jobs yet</p>
                                <p class="mt-1 text-sm text-slate-500">Create your first quote to get started.</p>
                                <a href="{{ route('jobs.create') }}" class="mt-4 inline-flex rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Create a job</a>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <aside class="space-y-6">
            <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <div>
                        <h2 class="font-semibold text-slate-900">Today’s schedule</h2>
                        <p class="mt-1 text-xs text-slate-500">{{ $todayJobs->count() }} {{ \Illuminate\Support\Str::plural('job', $todayJobs->count()) }} booked</p>
                    </div>
                    <span class="rounded-lg bg-indigo-50 px-2.5 py-1.5 text-xs font-semibold text-indigo-700">{{ now()->format('M j') }}</span>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($todayJobs as $job)
                        <a href="{{ route('jobs.show', $job) }}" class="flex items-center gap-3 px-5 py-4 hover:bg-slate-50">
                            <span class="w-14 shrink-0 text-xs font-semibold text-slate-500">{{ $job->scheduled_time ?: '—' }}</span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-semibold text-slate-900">{{ $job->customer?->full_name ?? $job->site_full_name ?? 'No customer' }}</span>
                                <span class="mt-1 block truncate text-xs text-slate-500">{{ $job->crew_route ?: 'Route not set' }}</span>
                            </span>
                            <span aria-hidden="true" class="text-slate-400">›</span>
                        </a>
                    @empty
                        <div class="px-5 py-8 text-center">
                            <p class="text-sm font-medium text-slate-700">Nothing scheduled today</p>
                            <p class="mt-1 text-xs text-slate-500">Add a job to your schedule when it is approved.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-semibold text-slate-900">Shortcuts</h2>
                <div class="mt-4 grid grid-cols-1 gap-2">
                    <a href="{{ route('jobs.create') }}" class="flex items-center justify-between rounded-lg border border-slate-200 px-3.5 py-3 text-sm font-medium text-slate-700 hover:border-indigo-200 hover:bg-indigo-50/50">
                        <span>Start a quote</span><span class="text-indigo-600" aria-hidden="true">↗</span>
                    </a>
                    <a href="{{ route('customers.index') }}" class="flex items-center justify-between rounded-lg border border-slate-200 px-3.5 py-3 text-sm font-medium text-slate-700 hover:border-indigo-200 hover:bg-indigo-50/50">
                        <span>Find a customer</span><span class="text-indigo-600" aria-hidden="true">↗</span>
                    </a>
                    <a href="{{ route('jobs.deleted') }}" class="flex items-center justify-between rounded-lg border border-slate-200 px-3.5 py-3 text-sm font-medium text-slate-700 hover:border-indigo-200 hover:bg-indigo-50/50">
                        <span>Review deleted jobs</span><span class="text-indigo-600" aria-hidden="true">↗</span>
                    </a>
                </div>
            </section>
        </aside>
    </section>
</x-app-layout>
