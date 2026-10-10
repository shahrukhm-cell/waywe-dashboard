<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">Quote {{ $job->quote_estimate_number }}</h1>
                <p class="mt-1 text-sm text-slate-500">Customer estimate · Job work has not been dispatched</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('jobs.show', $job) }}" class="rounded-lg border border-slate-200 px-3.5 py-2 text-sm font-semibold text-slate-700">Back to job</a>
                <a href="{{ route('jobs.quote.download', $job) }}" class="rounded-lg bg-indigo-600 px-3.5 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Download quote PDF</a>
            </div>
        </div>
    </x-slot>

    <article class="mx-auto max-w-4xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10">
        <div class="flex flex-wrap justify-between gap-6 border-b border-slate-200 pb-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-indigo-600">Service estimate</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">Quote {{ $job->quote_estimate_number }}</h2>
                <p class="mt-1 text-sm text-slate-500">Prepared {{ $job->created_at->format('F j, Y') }}</p>
            </div>
            <div class="text-sm sm:text-right">
                <p class="font-semibold text-slate-900">{{ $job->site_business ?: config('app.name') }}</p>
                <p class="mt-1 text-slate-600">For: {{ $job->billing_full_name ?: $job->site_full_name ?: $job->customer?->full_name }}</p>
                <p class="text-slate-600">{{ $job->billing_telephone ?: $job->site_telephone }}</p>
                <p class="text-slate-600">{{ trim(collect([$job->billing_address ?: $job->site_address, $job->billing_city ?: $job->site_city, $job->billing_state ?: $job->site_state, $job->billing_postal_code ?: $job->site_postal_code])->filter()->implode(', ')) }}</p>
            </div>
        </div>
        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-slate-200 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr><th class="py-3 pr-4">Area / item</th><th class="py-3 pr-4">Description</th><th class="py-3 pr-4">Dimensions</th><th class="py-3 pr-4">Quantity</th><th class="py-3 text-right">Amount</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($job->lineItems as $line)
                        <tr><td class="py-3 pr-4 font-medium text-slate-900">{{ $line->area_item ?: '—' }}</td><td class="py-3 pr-4 text-slate-600">{{ $line->description ?: '—' }}</td><td class="py-3 pr-4 text-slate-600">{{ $line->length }} × {{ $line->width }}</td><td class="py-3 pr-4 text-slate-600">{{ rtrim(rtrim(number_format((float) $line->quantity, 2), '0'), '.') }}</td><td class="py-3 text-right font-medium text-slate-900">${{ number_format((float) $line->amount, 2) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="ml-auto mt-6 max-w-xs space-y-2 text-sm">
            <div class="flex justify-between text-slate-600"><span>Subtotal</span><span>${{ number_format((float) $job->subtotal, 2) }}</span></div>
            <div class="flex justify-between text-slate-600"><span>Tax</span><span>${{ number_format((float) $job->tax, 2) }}</span></div>
            <div class="flex justify-between text-slate-600"><span>Other tax</span><span>${{ number_format((float) $job->other_tax, 2) }}</span></div>
            <div class="flex justify-between border-t border-slate-200 pt-3 text-base font-bold text-slate-900"><span>Quote total</span><span>${{ number_format((float) $job->total, 2) }}</span></div>
        </div>
        @if ($job->notes)
            <div class="mt-8 rounded-lg bg-slate-50 p-4"><h3 class="text-sm font-semibold text-slate-900">Quote notes</h3><p class="mt-2 whitespace-pre-line text-sm text-slate-600">{{ $job->notes }}</p></div>
        @endif
        <p class="mt-8 border-t border-slate-100 pt-5 text-xs leading-5 text-slate-500">This is a quote / estimate only. The work will be scheduled and dispatched after approval. Payment is not recorded on this document.</p>
    </article>
</x-app-layout>
