<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">Job calendar</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $month->format('F Y') }} - {{ $monthJobCount }} {{ \Illuminate\Support\Str::plural('job', $monthJobCount) }} scheduled</p>
            </div>
            <a href="{{ route('jobs.create') }}" class="inline-flex w-fit items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Create job</a>
        </div>
    </x-slot>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col justify-between gap-4 border-b border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:px-6">
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('jobs.calendar') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Today</a>
                <div class="flex overflow-hidden rounded-lg border border-slate-300">
                    <a href="{{ route('jobs.calendar', ['month' => $previousMonth]) }}" class="border-r border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" aria-label="Previous month">&larr;</a>
                    <a href="{{ route('jobs.calendar', ['month' => $nextMonth]) }}" class="px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" aria-label="Next month">&rarr;</a>
                </div>
                <h2 class="text-lg font-semibold text-slate-900 sm:text-xl">{{ $month->format('F Y') }}</h2>
            </div>
            <div class="w-fit rounded-full bg-indigo-50 px-3 py-1.5 text-sm font-semibold text-indigo-700">{{ $monthJobCount }} {{ \Illuminate\Support\Str::plural('job', $monthJobCount) }} this month</div>
        </div>

        <div class="overflow-x-auto">
            <div class="min-w-[760px]">
                <div class="grid grid-cols-7 gap-px border-b border-slate-200 bg-slate-200 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                    @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
                        <div class="bg-slate-50 px-3 py-3">{{ $dayName }}</div>
                    @endforeach
                </div>

                <div class="grid grid-cols-7 gap-px bg-slate-200">
                    @foreach ($weeks as $week)
                        @foreach ($week as $day)
                            <div class="min-h-36 bg-white p-2 {{ $day['is_current_month'] ? '' : 'bg-slate-50/70' }}">
                                <div class="mb-2 flex items-center justify-between">
                                    <time datetime="{{ $day['date']->toDateString() }}" class="flex h-8 min-w-8 items-center justify-center rounded-full px-2 text-sm font-semibold {{ $day['is_today'] ? 'bg-indigo-600 text-white' : ($day['is_current_month'] ? 'text-slate-800' : 'text-slate-400') }}">
                                        {{ $day['date']->day }}
                                    </time>
                                    @if ($day['count'] > 0)
                                        <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-700">{{ $day['count'] }}</span>
                                    @endif
                                </div>

                                <div class="space-y-1">
                                    @forelse ($day['jobs']->take(3) as $job)
                                        <a href="{{ route('jobs.show', $job) }}" class="flex min-w-0 items-center gap-1.5 rounded-md border border-indigo-100 bg-indigo-50 px-1.5 py-1 text-[11px] leading-4 text-indigo-950 transition hover:border-indigo-300 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" title="Open {{ $job->customer?->full_name ?? $job->site_full_name ?? 'job' }}">
                                            <span class="shrink-0 font-semibold">{{ $job->scheduled_time ?: 'Any time' }}</span>
                                            <span class="truncate">{{ $job->customer?->full_name ?? $job->site_full_name ?? 'No customer' }}</span>
                                        </a>
                                    @empty
                                        <p class="px-1 py-1 text-xs text-slate-400">No jobs</p>
                                    @endforelse

                                    @if ($day['count'] > 3)
                                        <details class="group pt-0.5">
                                            <summary class="cursor-pointer list-none rounded px-1.5 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                <span class="group-open:hidden">+{{ $day['count'] - 3 }} more</span>
                                                <span class="hidden group-open:inline">Hide extra jobs</span>
                                            </summary>
                                            <div class="mt-1 space-y-1 border-l-2 border-indigo-100 pl-1.5">
                                                @foreach ($day['jobs']->slice(3) as $job)
                                                    <a href="{{ route('jobs.show', $job) }}" class="flex min-w-0 items-center gap-1.5 rounded-md border border-slate-200 bg-white px-1.5 py-1 text-[11px] leading-4 text-slate-700 transition hover:border-indigo-300 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500" title="Open {{ $job->customer?->full_name ?? $job->site_full_name ?? 'job' }}">
                                                        <span class="shrink-0 font-semibold">{{ $job->scheduled_time ?: 'Any time' }}</span>
                                                        <span class="truncate">{{ $job->customer?->full_name ?? $job->site_full_name ?? 'No customer' }}</span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </details>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
