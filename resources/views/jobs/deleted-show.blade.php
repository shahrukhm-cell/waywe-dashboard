<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">{{ $job->job_number ?: $job->quote_estimate_number ?: 'Job #' . $job->id }}</h1>
                <p class="mt-1 text-sm text-slate-500">Complete archived job details · deleted {{ $job->deleted_at->format('M j, Y') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('jobs.deleted', request()->query()) }}" class="rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back to deleted jobs</a>
                <form method="POST" action="{{ route('jobs.restore', $job) }}" onsubmit="return confirm('Restore this job to the active jobs list?');">
                    @csrf
                    <button class="rounded-lg bg-emerald-600 px-3.5 py-2 text-sm font-semibold text-white hover:bg-emerald-500">Restore job</button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
        This job is in the deleted list. The information below is read-only until it is restored.
    </div>

    <div class="space-y-6">
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="font-semibold text-slate-900">Job and customer</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ ucfirst($job->job_type) }} · {{ ucfirst($job->status) }}</p>
                </div>
                <span class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700">Deleted {{ $job->deleted_at->format('M j, Y g:i A') }}</span>
            </div>
            <dl class="mt-5 grid grid-cols-1 gap-x-8 gap-y-4 text-sm sm:grid-cols-2 lg:grid-cols-3">
                <div><dt class="text-slate-500">Customer</dt><dd class="mt-1 font-medium text-slate-900">{{ $job->site_full_name ?: $job->customer?->full_name ?: '—' }}</dd></div>
                <div><dt class="text-slate-500">Phone</dt><dd class="mt-1 font-medium text-slate-900">{{ $job->site_telephone ?: $job->customer?->phone ?: '—' }}</dd></div>
                <div><dt class="text-slate-500">Business</dt><dd class="mt-1 font-medium text-slate-900">{{ $job->site_business ?: '—' }}</dd></div>
                <div><dt class="text-slate-500">Site address</dt><dd class="mt-1 font-medium text-slate-900">{{ trim(collect([$job->site_address, $job->site_city, $job->site_state, $job->site_postal_code])->filter()->implode(', ')) ?: '—' }}</dd></div>
                <div><dt class="text-slate-500">Billing name</dt><dd class="mt-1 font-medium text-slate-900">{{ $job->billing_full_name ?: '—' }}</dd></div>
                <div><dt class="text-slate-500">Billing address</dt><dd class="mt-1 font-medium text-slate-900">{{ trim(collect([$job->billing_address, $job->billing_city, $job->billing_state, $job->billing_postal_code])->filter()->implode(', ')) ?: '—' }}</dd></div>
                <div><dt class="text-slate-500">Quote / estimate</dt><dd class="mt-1 font-medium text-slate-900">{{ $job->quote_estimate_number ?: '—' }}</dd></div>
                <div><dt class="text-slate-500">Purchase order</dt><dd class="mt-1 font-medium text-slate-900">{{ $job->po_number ?: '—' }}</dd></div>
                <div><dt class="text-slate-500">Invoice</dt><dd class="mt-1 font-medium text-slate-900">{{ $job->invoice_number ?: '—' }}</dd></div>
            </dl>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <h2 class="font-semibold text-slate-900">Service details</h2>
                <p class="mt-1 text-sm text-slate-500">Schedule: {{ $job->scheduled_date?->format('M j, Y') ?: '—' }} {{ $job->scheduled_time ?: '' }} · Best time: {{ $job->best_time ?: '—' }} · Route: {{ $job->crew_route ?: '—' }}</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr><th class="px-4 py-3">Area / item</th><th class="px-4 py-3">Dimensions</th><th class="px-4 py-3">Description</th><th class="px-4 py-3">Qty</th><th class="px-4 py-3">Time</th><th class="px-4 py-3 text-right">Amount</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($job->lineItems as $line)
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $line->area_item ?: '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $line->length ?: '—' }} × {{ $line->width ?: '—' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $line->description ?: '—' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ rtrim(rtrim(number_format((float) $line->quantity, 2), '0'), '.') }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $line->time ?: '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right font-medium text-slate-900">${{ number_format((float) $line->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500">No service lines recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="space-y-2 border-t border-slate-100 px-5 py-4 text-sm sm:ml-auto sm:w-72">
                <div class="flex justify-between text-slate-600"><span>Subtotal</span><span>${{ number_format((float) $job->subtotal, 2) }}</span></div>
                <div class="flex justify-between text-slate-600"><span>Tax</span><span>${{ number_format((float) $job->tax, 2) }}</span></div>
                <div class="flex justify-between text-slate-600"><span>Other tax</span><span>${{ number_format((float) $job->other_tax, 2) }}</span></div>
                <div class="flex justify-between border-t border-slate-200 pt-2 font-semibold text-slate-900"><span>Total</span><span>${{ number_format((float) $job->total, 2) }}</span></div>
                <div class="flex justify-between text-emerald-700"><span>Paid</span><span>${{ number_format((float) $job->amount_paid, 2) }}</span></div>
                <div class="flex justify-between font-semibold text-slate-900"><span>Balance</span><span>${{ number_format((float) $job->balance, 2) }}</span></div>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="font-semibold text-slate-900">Technicians</h2>
                <div class="mt-4 divide-y divide-slate-100">
                    @forelse ($job->technicians as $technician)
                        <div class="flex justify-between gap-3 py-3 text-sm">
                            <span class="font-medium text-slate-900">{{ $technician->technician_name }}</span>
                            <span class="text-slate-600">{{ $technician->time ?: '—' }} · ${{ number_format((float) $technician->amount, 2) }}</span>
                        </div>
                    @empty
                        <p class="mt-3 text-sm text-slate-500">No technicians assigned.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="font-semibold text-slate-900">Job notes and reminders</h2>
                @if ($job->notes)
                    <p class="mt-3 whitespace-pre-wrap text-sm text-slate-700">{{ $job->notes }}</p>
                @endif
                <div class="mt-3 divide-y divide-slate-100">
                    @forelse ($job->reminders as $reminder)
                        <p class="whitespace-pre-wrap py-3 text-sm text-slate-700">{{ $reminder->note }}</p>
                    @empty
                        @unless ($job->notes)
                            <p class="text-sm text-slate-500">No notes or reminders recorded.</p>
                        @endunless
                    @endforelse
                </div>
            </section>
        </div>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <h2 class="font-semibold text-slate-900">Payment history</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr><th class="px-4 py-3">Receipt</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Method</th><th class="px-4 py-3">Explanation / notes</th><th class="px-4 py-3 text-right">Amount</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($job->payments as $payment)
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $payment->receipt_no }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $payment->transaction_date->format('M j, Y') }}</td>
                                <td class="px-4 py-3 capitalize text-slate-600">{{ $payment->payment_method }}{{ $payment->check_number ? ' · Check ' . $payment->check_number : '' }}{{ $payment->card_last_four ? ' · Card •••• ' . $payment->card_last_four : '' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $payment->explanation ?: '—' }}{{ $payment->notes ? ' · ' . $payment->notes : '' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right font-medium text-slate-900">${{ number_format((float) $payment->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-8 text-center text-slate-500">No payments recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
