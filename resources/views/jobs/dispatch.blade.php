<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dispatch preview</h2>
            <a href="{{ route('jobs.dispatch.print', $job) }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Download PDF</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-8">
                <div class="flex items-center justify-between border-b pb-4 mb-4">
                    <div>
                        <div class="text-lg font-bold uppercase tracking-wide">Dispatching List</div>
                        <div class="text-sm text-gray-500">{{ now()->format('M d, Y') }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-600">Job #</div>
                        <div class="text-xl font-bold">{{ $job->job_number ?: $job->quote_estimate_number ?: '—' }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 text-sm text-gray-700">
                    <div>
                        <div class="font-semibold mb-2">Client</div>
                        <div>{{ $job->site_full_name ?: '—' }}</div>
                        <div>{{ $job->site_telephone ?: '—' }}</div>
                        <div>{{ $job->site_address ?: '—' }}</div>
                        <div>{{ trim(($job->site_city ?: '') . ' ' . ($job->site_state ?: '') . ' ' . ($job->site_postal_code ?: '')) }}</div>
                    </div>
                    <div>
                        <div class="font-semibold mb-2">Scheduling</div>
                        <div>Crew / Route: {{ $job->crew_route ?: '—' }}</div>
                        <div>Day: {{ $job->scheduled_day ?: '—' }}</div>
                        <div>Date: {{ $job->scheduled_date?->format('M d, Y') ?: '—' }}</div>
                        <div>Arrival window: {{ $job->scheduled_time ?: '—' }}</div>
                        <div>Best Time: {{ $job->best_time ?: '—' }}</div>
                        <div class="mt-3 font-semibold">Assigned technicians</div>
                        @forelse ($job->technicians as $technician)
                            <div>{{ $technician->technician_name }}{{ $technician->time ? ' · ' . $technician->time : '' }}{{ (float) $technician->amount > 0 ? ' · $' . number_format((float) $technician->amount, 2) : '' }}</div>
                        @empty
                            <div>Not assigned</div>
                        @endforelse
                    </div>
                </div>

                <div class="mt-8 overflow-x-auto">
                    <table class="min-w-full border text-sm">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="border px-2 py-2 text-left">Area/Item</th>
                                <th class="border px-2 py-2 text-left">Description</th>
                                <th class="border px-2 py-2 text-left">Qty</th>
                                <th class="border px-2 py-2 text-left">Time</th>
                                <th class="border px-2 py-2 text-left">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($job->lineItems as $line)
                                <tr>
                                    <td class="border px-2 py-2">{{ $line->area_item ?: '—' }}</td>
                                    <td class="border px-2 py-2">{{ $line->description ?: '—' }}</td>
                                    <td class="border px-2 py-2">{{ $line->quantity }}</td>
                                    <td class="border px-2 py-2">{{ $line->time ?: '—' }}</td>
                                    <td class="border px-2 py-2">${{ number_format((float) $line->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-4 text-sm text-gray-700">
                    <div>
                        <div class="font-semibold mb-2">Additional details</div>
                        <div>{{ $job->notes ?: 'No notes' }}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-semibold mb-2">Report Summary</div>
                        <div>Total Price: ${{ number_format((float) $job->total, 2) }}</div>
                        <div>No. of Service Lines: {{ $job->lineItems->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
