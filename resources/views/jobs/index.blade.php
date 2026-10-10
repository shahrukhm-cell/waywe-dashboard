<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Jobs</h2>
            <div class="space-x-3">
                <a href="{{ route('jobs.calendar') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Calendar</a>
                <a href="{{ route('jobs.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Create job</a>
                <a href="{{ route('jobs.deleted') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Deleted jobs</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr class="text-left text-gray-600">
                                <th class="pb-3 pr-4">Job #</th>
                                <th class="pb-3 pr-4">Customer</th>
                                <th class="pb-3 pr-4">Type</th>
                                <th class="pb-3 pr-4">Status</th>
                                <th class="pb-3 pr-4">Total</th>
                                <th class="pb-3 pr-4">Date</th>
                                <th class="pb-3 pr-4">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($jobs as $job)
                                <tr>
                                    <td class="py-3 pr-4 font-medium text-slate-800">{{ $job->job_number ?: $job->quote_estimate_number ?: '—' }}</td>
                                    <td class="py-3 pr-4">{{ $job->customer?->full_name ?? 'No customer' }}</td>
                                    <td class="py-3 pr-4">{{ ucfirst($job->job_type) }}</td>
                                    <td class="py-3 pr-4"><span class="inline-flex rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">{{ ucfirst($job->status) }}</span></td>
                                    <td class="py-3 pr-4">${{ number_format((float) $job->total, 2) }}</td>
                                    <td class="py-3 pr-4">{{ $job->created_at->format('M d, Y') }}</td>
                                    <td class="py-3 pr-4">
                                        <a href="{{ route('jobs.show', $job) }}" class="text-indigo-600 hover:text-indigo-800">Open</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-gray-500">No jobs available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
