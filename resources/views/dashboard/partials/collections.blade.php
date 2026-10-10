<section id="payment-collections" data-total="{{ number_format($collectionTotal, 2, '.', '') }}" class="mt-7 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="font-semibold text-slate-900">Payment collections</h2>
                <p class="mt-1 text-sm text-slate-500">Track actual payments received, not invoice totals. Defaults to month to date.</p>
            </div>
            <div class="rounded-lg bg-emerald-50 px-3.5 py-2 text-right">
                <p class="text-xs font-medium text-emerald-700">Collected</p>
                <p class="mt-0.5 text-xl font-bold text-emerald-800">${{ number_format($collectionTotal, 2) }}</p>
            </div>
        </div>
        <form method="GET" action="{{ route('dashboard') }}" class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6">
            <div>
                <label for="collection-from" class="block text-xs font-medium text-slate-600">From</label>
                <input id="collection-from" type="date" name="from" value="{{ $from }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm">
            </div>
            <div>
                <label for="collection-to" class="block text-xs font-medium text-slate-600">To</label>
                <input id="collection-to" type="date" name="to" value="{{ $to }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm">
            </div>
            <div>
                <label for="collection-method" class="block text-xs font-medium text-slate-600">Payment method</label>
                <select id="collection-method" name="payment_method" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm">
                    <option value="">All methods</option>
                    @foreach (['check' => 'Check', 'charge' => 'Charge', 'card' => 'Card', 'cash' => 'Cash', 'other' => 'Other'] as $method => $label)
                        <option value="{{ $method }}" @selected(($filters['payment_method'] ?? '') === $method)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="collection-status" class="block text-xs font-medium text-slate-600">Job payment status</label>
                <select id="collection-status" name="collection_status" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm">
                    <option value="">All statuses</option>
                    <option value="outstanding" @selected(($filters['collection_status'] ?? '') === 'outstanding')>Balance due</option>
                    <option value="paid" @selected(($filters['collection_status'] ?? '') === 'paid')>Fully paid</option>
                </select>
            </div>
            <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-2">
                <button class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500">Apply filters</button>
                <a href="{{ route('dashboard', ['all_dates' => 1, 'payment_method' => $filters['payment_method'] ?? null, 'collection_status' => $filters['collection_status'] ?? null]) }}" data-collection-all-dates class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">All dates</a>
            </div>
        </form>
        <p data-collection-error role="alert" class="mt-3 hidden text-sm font-medium text-rose-700"></p>
    </div>

    <div class="grid grid-cols-1 gap-3 border-b border-slate-100 bg-slate-50/70 px-5 py-4 sm:grid-cols-2 sm:px-6">
        <div><p class="text-xs font-medium text-slate-500">Payments in this result</p><p class="mt-1 text-lg font-semibold text-slate-900">{{ $collectionCount }}</p></div>
        <div><p class="text-xs font-medium text-slate-500">Filtered collection total</p><p class="mt-1 text-lg font-semibold text-emerald-700">${{ number_format($collectionTotal, 2) }}</p></div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr><th class="px-5 py-3 sm:px-6">Date / receipt</th><th class="px-5 py-3">Job / customer</th><th class="px-5 py-3">Method</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right sm:px-6">Collected</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($payments as $payment)
                    <tr class="hover:bg-slate-50/70">
                        <td class="whitespace-nowrap px-5 py-4 sm:px-6"><span class="font-medium text-slate-900">{{ $payment->transaction_date->format('M j, Y') }}</span><p class="mt-1 text-xs text-slate-500">{{ $payment->receipt_no }}</p></td>
                        <td class="px-5 py-4"><a href="{{ $payment->job->trashed() ? route('jobs.deleted.show', $payment->job) : route('jobs.show', $payment->job) }}" class="font-semibold text-indigo-700 hover:text-indigo-900">{{ $payment->job->job_number ?: $payment->job->quote_estimate_number ?: 'Job #' . $payment->job->id }}</a><p class="mt-1 text-xs text-slate-500">{{ $payment->job->customer?->full_name ?? $payment->job->site_full_name ?? 'No customer' }}</p>@if ($payment->job->trashed())<p class="mt-1 text-xs font-medium text-amber-700">Archived</p>@endif</td>
                        <td class="whitespace-nowrap px-5 py-4 capitalize text-slate-600">{{ $payment->payment_method }}</td>
                        <td class="whitespace-nowrap px-5 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $payment->job->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ $payment->job->status === 'paid' ? 'Fully paid' : 'Balance due' }}</span></td>
                        <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-slate-900 sm:px-6">${{ number_format((float) $payment->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">No payments match these filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($payments->hasPages())
        <div class="border-t border-slate-100 px-5 py-4 sm:px-6">{{ $payments->links() }}</div>
    @endif
</section>
