<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">{{ $customer->full_name }}</h1>
                <p class="mt-1 text-sm text-slate-500">Customer details and service job history</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('jobs.create', ['phone' => $customer->phone]) }}" class="rounded-lg bg-indigo-600 px-3.5 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Create job</a>
                <a href="{{ route('customers.edit', $customer) }}" class="rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Edit customer</a>
                <a href="{{ route('customers.index') }}" class="rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back to customers</a>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="space-y-6">
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="font-semibold text-slate-900">Customer detail</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $customer->business ?: 'Individual customer' }}</p>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $customer->jobs->count() }} {{ \Illuminate\Support\Str::plural('job', $customer->jobs->count()) }}</span>
            </div>

            <dl class="mt-5 grid grid-cols-1 gap-x-8 gap-y-4 text-sm sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <dt class="text-slate-500">Name</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ $customer->full_name }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Phone</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ $customer->phone ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Business</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ $customer->business ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Title</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ $customer->title ?: '—' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-slate-500">Address</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ trim(collect([$customer->address, $customer->city, $customer->state, $customer->postal_code])->filter()->implode(', ')) ?: '—' }}</dd>
                </div>
            </dl>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:px-6">
                <div>
                    <h2 class="font-semibold text-slate-900">Customer jobs</h2>
                    <p class="mt-1 text-sm text-slate-500">Click a job to open the full job detail.</p>
                </div>
                <a href="{{ route('jobs.create', ['phone' => $customer->phone]) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">New job for this customer</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Job</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Scheduled</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3">Created</th>
                            <th class="px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($customer->jobs as $job)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium">
                                    <a href="{{ route('jobs.show', $job) }}" class="text-slate-900 hover:text-indigo-700">
                                        {{ $job->job_number ?: $job->quote_estimate_number ?: 'Job #' . $job->id }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ ucfirst($job->job_type) }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">{{ ucfirst($job->status) }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $job->scheduled_date?->format('M j, Y') ?: '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right font-medium text-slate-900">${{ number_format((float) $job->total, 2) }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $job->created_at->format('M j, Y') }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('jobs.show', $job) }}" class="font-medium text-indigo-600 hover:text-indigo-800">Open detail</a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7" class="bg-slate-50 px-4 py-4">
                                    <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(280px,0.45fr)]">
                                        <div class="space-y-4">
                                            <dl class="grid grid-cols-1 gap-x-6 gap-y-3 text-sm sm:grid-cols-2 lg:grid-cols-3">
                                                <div>
                                                    <dt class="text-slate-500">Site customer</dt>
                                                    <dd class="mt-1 font-medium text-slate-900">{{ $job->site_full_name ?: $customer->full_name }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-slate-500">Site phone</dt>
                                                    <dd class="mt-1 font-medium text-slate-900">{{ $job->site_telephone ?: $customer->phone }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-slate-500">Business</dt>
                                                    <dd class="mt-1 font-medium text-slate-900">{{ $job->site_business ?: '—' }}</dd>
                                                </div>
                                                <div class="sm:col-span-2">
                                                    <dt class="text-slate-500">Site address</dt>
                                                    <dd class="mt-1 font-medium text-slate-900">{{ trim(collect([$job->site_address, $job->site_city, $job->site_state, $job->site_postal_code])->filter()->implode(', ')) ?: '—' }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-slate-500">PO / quote</dt>
                                                    <dd class="mt-1 font-medium text-slate-900">{{ collect([$job->po_number ? 'PO ' . $job->po_number : null, $job->quote_estimate_number ? 'Quote ' . $job->quote_estimate_number : null])->filter()->implode(' · ') ?: '—' }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-slate-500">Route</dt>
                                                    <dd class="mt-1 font-medium text-slate-900">{{ $job->crew_route ?: '—' }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-slate-500">Arrival / best time</dt>
                                                    <dd class="mt-1 font-medium text-slate-900">{{ collect([$job->scheduled_time, $job->best_time ? 'Best ' . $job->best_time : null])->filter()->implode(' · ') ?: '—' }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-slate-500">Invoice</dt>
                                                    <dd class="mt-1 font-medium text-slate-900">{{ $job->invoice_number ?: '—' }}</dd>
                                                </div>
                                            </dl>

                                            <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                                                <div class="border-b border-slate-100 px-4 py-3 text-sm font-semibold text-slate-900">Service lines</div>
                                                <div class="overflow-x-auto">
                                                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                                                        <thead class="bg-white text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                            <tr>
                                                                <th class="px-4 py-2">Area / item</th>
                                                                <th class="px-4 py-2">Dimensions</th>
                                                                <th class="px-4 py-2">Description</th>
                                                                <th class="px-4 py-2">Qty</th>
                                                                <th class="px-4 py-2 text-right">Amount</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-slate-100">
                                                            @forelse ($job->lineItems as $line)
                                                                <tr>
                                                                    <td class="px-4 py-2 font-medium text-slate-900">{{ $line->area_item ?: '—' }}</td>
                                                                    <td class="whitespace-nowrap px-4 py-2 text-slate-600">{{ $line->length }} × {{ $line->width }}</td>
                                                                    <td class="px-4 py-2 text-slate-600">{{ $line->description ?: '—' }}</td>
                                                                    <td class="px-4 py-2 text-slate-600">{{ rtrim(rtrim(number_format((float) $line->quantity, 2), '0'), '.') }}</td>
                                                                    <td class="whitespace-nowrap px-4 py-2 text-right font-medium text-slate-900">${{ number_format((float) $line->amount, 2) }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="5" class="px-4 py-3 text-center text-slate-500">No service lines added.</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="space-y-4">
                                            <div class="rounded-lg border border-slate-200 bg-white p-4">
                                                <h3 class="text-sm font-semibold text-slate-900">Totals</h3>
                                                <dl class="mt-3 space-y-2 text-sm">
                                                    <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd class="font-medium text-slate-900">${{ number_format((float) $job->subtotal, 2) }}</dd></div>
                                                    <div class="flex justify-between"><dt class="text-slate-500">Tax</dt><dd class="font-medium text-slate-900">${{ number_format((float) $job->tax + (float) $job->other_tax, 2) }}</dd></div>
                                                    <div class="flex justify-between border-t border-slate-100 pt-2"><dt class="font-semibold text-slate-700">Total</dt><dd class="font-semibold text-slate-900">${{ number_format((float) $job->total, 2) }}</dd></div>
                                                    <div class="flex justify-between"><dt class="text-slate-500">Paid</dt><dd class="font-medium text-emerald-700">${{ number_format((float) $job->amount_paid, 2) }}</dd></div>
                                                    <div class="flex justify-between"><dt class="text-slate-500">Balance</dt><dd class="font-medium text-slate-900">${{ number_format((float) $job->balance, 2) }}</dd></div>
                                                </dl>
                                            </div>

                                            <div class="rounded-lg border border-slate-200 bg-white p-4">
                                                <h3 class="text-sm font-semibold text-slate-900">Technicians</h3>
                                                <ul class="mt-3 space-y-2 text-sm text-slate-600">
                                                    @forelse ($job->technicians as $technician)
                                                        <li>{{ $technician->technician_name }}{{ $technician->time ? ' · ' . $technician->time : '' }}{{ (float) $technician->amount > 0 ? ' · $' . number_format((float) $technician->amount, 2) : '' }}</li>
                                                    @empty
                                                        <li>No technicians assigned.</li>
                                                    @endforelse
                                                </ul>
                                            </div>

                                            <div class="rounded-lg border border-slate-200 bg-white p-4">
                                                <h3 class="text-sm font-semibold text-slate-900">Payments and notes</h3>
                                                <div class="mt-3 space-y-3 text-sm text-slate-600">
                                                    @forelse ($job->payments as $payment)
                                                        <p>{{ $payment->receipt_no }} · {{ $payment->transaction_date?->format('M j, Y') }} · {{ ucfirst($payment->payment_method) }} · ${{ number_format((float) $payment->amount, 2) }}</p>
                                                    @empty
                                                        <p>No payments recorded.</p>
                                                    @endforelse

                                                    @if ($job->notes)
                                                        <p class="border-t border-slate-100 pt-3 whitespace-pre-line">{{ $job->notes }}</p>
                                                    @endif

                                                    @foreach ($job->reminders->take(3) as $reminder)
                                                        <p class="border-t border-slate-100 pt-3 whitespace-pre-line">{{ $reminder->note }}</p>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-slate-500">No jobs found for this customer.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
