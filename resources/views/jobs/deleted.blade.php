<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">Deleted jobs</h1>
                <p class="mt-1 text-sm text-slate-500">Search, inspect, or restore jobs moved here.</p>
            </div>
            <a href="{{ route('jobs.index') }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back to jobs</a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('jobs.deleted') }}" class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-end sm:p-5">
            <div class="min-w-0 flex-1">
                <label for="deleted-job-search" class="block text-xs font-medium text-slate-600">Search deleted jobs</label>
                <input id="deleted-job-search" type="search" name="search" value="{{ $search }}" placeholder="Job or quote number, customer, business, or phone" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm">
            </div>
            <div class="flex gap-2">
                <button class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500">Search</button>
                @if ($search !== '')
                    <a href="{{ route('jobs.deleted') }}" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Clear</a>
                @endif
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Job / quote</th>
                        <th class="px-5 py-3">Customer</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Deleted</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($jobs as $job)
                        <tr class="hover:bg-slate-50/70">
                            <td class="whitespace-nowrap px-5 py-4">
                                <a href="{{ route('jobs.deleted.show', $job) }}" class="font-semibold text-indigo-700 hover:text-indigo-900">{{ $job->job_number ?: $job->quote_estimate_number ?: 'Job #' . $job->id }}</a>
                                <p class="mt-1 text-xs text-slate-500">{{ ucfirst($job->job_type) }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-slate-900">{{ $job->site_full_name ?: $job->customer?->full_name ?: 'No customer' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $job->site_telephone ?: $job->customer?->phone ?: 'No phone' }}</p>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 capitalize text-slate-600">{{ $job->status }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $job->deleted_at->format('M j, Y') }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('jobs.deleted.show', $job) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">View job</a>
                                    <form method="POST" action="{{ route('jobs.restore', $job) }}" onsubmit="return confirm('Restore this job to the active jobs list?');">
                                        @csrf
                                        <button class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-500">Restore</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <p class="font-medium text-slate-900">{{ $search !== '' ? 'No matching deleted jobs' : 'No deleted jobs' }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $search !== '' ? 'Try a different search.' : 'Jobs you move to deleted will appear here.' }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($jobs->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">{{ $jobs->links() }}</div>
        @endif
    </section>
</x-app-layout>
