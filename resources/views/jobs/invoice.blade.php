<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div><h1 class="text-xl font-semibold text-slate-900">Invoice {{ $job->invoice_number }}</h1><p class="mt-1 text-sm text-slate-500">Current invoice balance and complete payment history</p></div>
            <div class="flex gap-2"><a href="{{ route('jobs.show', $job) }}" class="rounded-lg border border-slate-200 px-3.5 py-2 text-sm font-semibold text-slate-700">Back to job</a><a href="{{ route('jobs.invoice.download', $job) }}" class="rounded-lg bg-indigo-600 px-3.5 py-2 text-sm font-semibold text-white">Download invoice PDF</a></div>
        </div>
    </x-slot>
    <article class="mx-auto max-w-4xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10">
        <div class="flex flex-wrap justify-between gap-6 border-b border-slate-200 pb-6">
            <div><p class="text-xs font-semibold uppercase tracking-widest text-indigo-600">Invoice</p><h2 class="mt-2 text-2xl font-bold text-slate-900">{{ $job->invoice_number }}</h2><p class="mt-1 text-sm text-slate-500">Job #{{ $job->job_number }} · {{ now()->format('F j, Y') }}</p></div>
            <div class="text-sm sm:text-right"><p class="font-semibold text-slate-900">{{ $job->site_business ?: config('app.name') }}</p><p class="mt-1 text-slate-600">Bill to: {{ $job->billing_full_name ?: $job->site_full_name ?: $job->customer?->full_name }}</p><p class="text-slate-600">{{ $job->billing_telephone ?: $job->site_telephone }}</p><p class="text-slate-600">{{ trim(collect([$job->billing_address ?: $job->site_address, $job->billing_city ?: $job->site_city, $job->billing_state ?: $job->site_state, $job->billing_postal_code ?: $job->site_postal_code])->filter()->implode(', ')) }}</p></div>
        </div>
        <h3 class="mt-7 font-semibold text-slate-900">Work completed</h3>
        <div class="mt-3 overflow-x-auto">
            <table class="min-w-full text-sm"><thead class="border-b border-slate-200 text-left text-xs font-semibold uppercase text-slate-500"><tr><th class="py-3 pr-4">Area / item</th><th class="py-3 pr-4">Description</th><th class="py-3 pr-4">Qty</th><th class="py-3 text-right">Amount</th></tr></thead><tbody class="divide-y divide-slate-100">
                @foreach ($job->lineItems as $line)<tr><td class="py-3 pr-4">{{ $line->area_item ?: '—' }}</td><td class="py-3 pr-4">{{ $line->description ?: '—' }}</td><td class="py-3 pr-4">{{ rtrim(rtrim(number_format((float) $line->quantity, 2), '0'), '.') }}</td><td class="py-3 text-right">${{ number_format((float) $line->amount, 2) }}</td></tr>@endforeach
            </tbody></table>
        </div>
        <h3 class="mt-8 font-semibold text-slate-900">Payment history and receipts</h3>
        <div class="mt-3 overflow-x-auto">
            <table class="min-w-full text-sm"><thead class="border-b border-slate-200 text-left text-xs font-semibold uppercase text-slate-500"><tr><th class="py-3 pr-4">Receipt no.</th><th class="py-3 pr-4">Date</th><th class="py-3 pr-4">Method / reference</th><th class="py-3 pr-4">Explanation / notes</th><th class="py-3 text-right">Payment</th><th class="py-3 text-right">Balance after payment</th></tr></thead><tbody class="divide-y divide-slate-100">
                @php($runningPaid = 0)
                @foreach ($job->payments as $payment)
                    @php($runningPaid += (float) $payment->amount)
                    <tr>
                        <td class="py-3 pr-4 font-medium">{{ $payment->receipt_no }}</td>
                        <td class="py-3 pr-4">{{ $payment->transaction_date->format('M j, Y') }}</td>
                        <td class="py-3 pr-4 capitalize">
                            {{ $payment->payment_method }}{{ $payment->check_number ? ' · Check ' . $payment->check_number : '' }}{{ $payment->check_date ? ' · Check date ' . $payment->check_date->format('M j, Y') : '' }}{{ $payment->card_last_four ? ' · Card •••• ' . $payment->card_last_four : '' }}{{ $payment->card_expiry_month && $payment->card_expiry_year ? ' · Exp ' . sprintf('%02d/%04d', $payment->card_expiry_month, $payment->card_expiry_year) : '' }}{{ $payment->account_reference ? ' · ' . $payment->account_reference : '' }}
                        </td>
                        <td class="py-3 pr-4"><div>{{ $payment->explanation ?: '—' }}</div>@if ($payment->notes)<div class="mt-1 text-xs text-slate-500">Notes: {{ $payment->notes }}</div>@endif</td>
                        <td class="py-3 text-right">${{ number_format((float) $payment->amount, 2) }}</td>
                        <td class="py-3 text-right">${{ number_format(max((float) $job->total - $runningPaid, 0), 2) }}</td>
                    </tr>
                @endforeach
            </tbody></table>
        </div>
        <div class="ml-auto mt-6 max-w-sm space-y-2 border-t border-slate-200 pt-4 text-sm">
            <div class="flex justify-between"><span>Invoice total</span><span>${{ number_format((float) $job->total, 2) }}</span></div>
            <div class="flex justify-between text-emerald-700"><span>Total paid</span><span>− ${{ number_format((float) $job->amount_paid, 2) }}</span></div>
            <div class="flex justify-between border-t border-slate-200 pt-3 text-base font-bold"><span>Balance due</span><span>${{ number_format((float) $job->balance, 2) }}</span></div>
        </div>
        <p class="mt-8 border-t border-slate-100 pt-5 text-xs text-slate-500">This invoice reflects all payments recorded for the job. Each installment has its own receipt number.</p>
    </article>
</x-app-layout>
