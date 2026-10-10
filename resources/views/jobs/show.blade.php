<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">{{ $job->job_number ?: $job->quote_estimate_number ?: 'Service job' }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $job->site_full_name ?: $job->customer?->full_name ?: 'No customer' }} · {{ ucfirst($job->job_type) }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('jobs.quote', $job) }}" class="rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">View quote</a>
                <a href="{{ route('jobs.quote.download', $job) }}" class="rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Download quote</a>
                <a href="{{ route('jobs.edit', $job) }}" class="rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Edit</a>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div role="alert" class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <ul class="list-inside list-disc space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1.55fr)_minmax(320px,0.85fr)]">
        <div class="space-y-6">
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="font-semibold text-slate-900">Job details</h2>
                        <p class="mt-1 text-sm text-slate-500">Customer and service information</p>
                    </div>
                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">{{ ucfirst($job->status) }}</span>
                </div>
                <dl class="mt-5 grid grid-cols-1 gap-x-8 gap-y-4 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-500">Customer</dt><dd class="mt-1 font-medium text-slate-900">{{ $job->site_full_name ?: $job->customer?->full_name ?: '—' }}</dd></div>
                    <div><dt class="text-slate-500">Phone</dt><dd class="mt-1 font-medium text-slate-900">{{ $job->site_telephone ?: $job->customer?->phone ?: '—' }}</dd></div>
                    <div><dt class="text-slate-500">Business</dt><dd class="mt-1 font-medium text-slate-900">{{ $job->site_business ?: '—' }}</dd></div>
                    <div><dt class="text-slate-500">Job number</dt><dd class="mt-1 font-medium text-slate-900">{{ $job->job_number ?: 'Assigned after approval' }}</dd></div>
                    <div><dt class="text-slate-500">Site</dt><dd class="mt-1 font-medium text-slate-900">{{ trim(collect([$job->site_address, $job->site_city, $job->site_state, $job->site_postal_code])->filter()->implode(', ')) ?: '—' }}</dd></div>
                    <div><dt class="text-slate-500">Billing</dt><dd class="mt-1 font-medium text-slate-900">{{ $job->billing_full_name ?: $job->site_full_name ?: '—' }}</dd></div>
                    <div><dt class="text-slate-500">Quote / estimate</dt><dd class="mt-1 font-medium text-slate-900">{{ $job->quote_estimate_number ?: '—' }}</dd></div>
                    <div><dt class="text-slate-500">Purchase order</dt><dd class="mt-1 font-medium text-slate-900">{{ $job->po_number ?: '—' }}</dd></div>
                </dl>
            </section>

            <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <h2 class="font-semibold text-slate-900">Service details</h2>
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
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $line->length }} × {{ $line->width }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $line->description ?: '—' }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ rtrim(rtrim(number_format((float) $line->quantity, 2), '0'), '.') }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $line->time ?: '—' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-medium text-slate-900">${{ number_format((float) $line->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500">No service lines added.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="space-y-2 border-t border-slate-100 px-5 py-4 text-sm sm:ml-auto sm:w-72">
                    <div class="flex justify-between text-slate-600"><span>Subtotal</span><span>${{ number_format((float) $job->subtotal, 2) }}</span></div>
                    <div class="flex justify-between text-slate-600"><span>Tax</span><span>${{ number_format((float) $job->tax, 2) }}</span></div>
                    <div class="flex justify-between text-slate-600"><span>Other tax</span><span>${{ number_format((float) $job->other_tax, 2) }}</span></div>
                    <div class="flex justify-between border-t border-slate-200 pt-2 font-semibold text-slate-900"><span>Quote total</span><span>${{ number_format((float) $job->total, 2) }}</span></div>
                </div>
            </section>

            @if ($job->payments->isNotEmpty())
                <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
                        <div>
                            <h2 class="font-semibold text-slate-900">Invoice and payments</h2>
                            <p class="mt-1 text-sm text-slate-500">Invoice {{ $job->invoice_number }} · payments are recorded individually</p>
                        </div>
                        <a href="{{ route('jobs.invoice.download', $job) }}" class="rounded-lg bg-indigo-600 px-3.5 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Download invoice</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 text-sm">
                            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <tr><th class="px-4 py-3">Receipt</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Method</th><th class="px-4 py-3">Details</th><th class="px-4 py-3 text-right">Amount</th></tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($job->payments as $payment)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-slate-900">{{ $payment->receipt_no }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $payment->transaction_date->format('M j, Y') }}</td>
                                        <td class="px-4 py-3 capitalize text-slate-600">{{ $payment->payment_method }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $payment->check_number ? 'Check ' . $payment->check_number . ($payment->check_date ? ' · ' . $payment->check_date->format('M j, Y') : '') : ($payment->card_last_four ? 'Card •••• ' . $payment->card_last_four . ($payment->card_expiry_month && $payment->card_expiry_year ? ' · Exp ' . sprintf('%02d/%04d', $payment->card_expiry_month, $payment->card_expiry_year) : '') : ($payment->account_reference ?: '—')) }}{{ $payment->explanation ? ' · ' . $payment->explanation : '' }}{{ $payment->notes ? ' · Notes: ' . $payment->notes : '' }}</td>
                                        <td class="px-4 py-3 text-right font-medium text-slate-900">${{ number_format((float) $payment->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="grid grid-cols-3 gap-3 border-t border-slate-100 bg-slate-50 px-5 py-4 text-sm">
                        <div><p class="text-xs text-slate-500">Invoice total</p><p class="mt-1 font-semibold text-slate-900">${{ number_format((float) $job->total, 2) }}</p></div>
                        <div><p class="text-xs text-slate-500">Total paid</p><p class="mt-1 font-semibold text-emerald-700">${{ number_format((float) $job->amount_paid, 2) }}</p></div>
                        <div><p class="text-xs text-slate-500">Remaining</p><p class="mt-1 font-semibold text-slate-900">${{ number_format((float) $job->balance, 2) }}</p></div>
                    </div>
                </section>
            @endif

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <div>
                    <h2 class="font-semibold text-slate-900">Job reminders</h2>
                    <p class="mt-1 text-sm text-slate-500">Add as many private notes and reminders as needed.</p>
                </div>
                <form method="POST" action="{{ route('jobs.notes.store', $job) }}" class="mt-4 space-y-3">
                    @csrf
                    <textarea name="note" rows="3" required maxlength="5000" placeholder="Add a call-back reminder, access detail, or internal note…" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('note') }}</textarea>
                    <div class="flex justify-end"><button class="rounded-lg border border-slate-200 px-3.5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Add reminder</button></div>
                </form>
                <ol class="mt-5 space-y-3">
                    @forelse ($job->reminders->sortByDesc('created_at') as $reminder)
                        <li class="rounded-lg bg-slate-50 p-3.5">
                            <p class="whitespace-pre-line text-sm text-slate-800">{{ $reminder->note }}</p>
                            <p class="mt-2 text-xs text-slate-500">{{ $reminder->created_at->format('M j, Y g:i A') }}</p>
                        </li>
                    @empty
                        <li class="text-sm text-slate-500">No reminders added yet.</li>
                    @endforelse
                </ol>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="font-semibold text-slate-900">Workflow</h2>
                <p class="mt-1 text-sm text-slate-500">Quote approval → schedule → dispatch → complete → payments.</p>

                @if (in_array($job->status, ['draft', 'quote'], true))
                    <form method="POST" action="{{ route('jobs.confirm', $job) }}" class="mt-5">
                        @csrf
                        <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-500">Mark quote approved</button>
                    </form>
                @elseif (in_array($job->status, ['approved', 'scheduled', 'dispatched', 'completed', 'paid'], true))
                    <div class="mt-5 rounded-lg bg-emerald-50 px-3.5 py-3 text-sm font-medium text-emerald-800">Quote approved · Job #{{ $job->job_number }}</div>
                @endif

                @if (in_array($job->status, ['approved', 'scheduled'], true))
                    <form method="POST" action="{{ route('jobs.schedule', $job) }}" class="mt-5 space-y-3 border-t border-slate-100 pt-5">
                        @csrf
                        <h3 class="text-sm font-semibold text-slate-900">Schedule and assign team</h3>
                        <div>
                            <label for="crew_route" class="block text-xs font-medium text-slate-600">Crew / route</label>
                            <input id="crew_route" type="text" name="crew_route" value="{{ old('crew_route', $job->crew_route) }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm">
                        </div>
                        <div>
                            <label for="scheduled_date" class="block text-xs font-medium text-slate-600">Job date</label>
                            <input id="scheduled_date" type="date" name="scheduled_date" required value="{{ old('scheduled_date', $job->scheduled_date?->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label for="scheduled_time" class="block text-xs font-medium text-slate-600">Arrival window</label><input id="scheduled_time" type="time" name="scheduled_time" value="{{ old('scheduled_time', $job->scheduled_time) }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm"></div>
                            <div><label for="best_time" class="block text-xs font-medium text-slate-600">Best time</label><input id="best_time" type="time" name="best_time" value="{{ old('best_time', $job->best_time) }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm"></div>
                        </div>
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <h4 class="text-xs font-semibold text-slate-700">Technicians</h4>
                                <button type="button" id="add-technician" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">+ Add technician</button>
                            </div>
                            <div id="technician-rows" class="space-y-2">
                                @forelse ($job->technicians as $index => $technician)
                                    <div class="technician-row grid grid-cols-[minmax(0,1fr)_auto_auto_auto] gap-2">
                                        <input name="technicians[{{ $index }}][technician_name]" value="{{ $technician->technician_name }}" aria-label="Technician name" placeholder="Name" class="min-w-0 rounded-lg border-slate-300 text-sm shadow-sm">
                                        <input type="time" name="technicians[{{ $index }}][time]" value="{{ $technician->time }}" aria-label="Technician time" class="w-28 rounded-lg border-slate-300 text-sm shadow-sm">
                                        <input type="number" step="0.01" min="0" name="technicians[{{ $index }}][amount]" value="{{ $technician->amount }}" aria-label="Technician amount" placeholder="$" class="w-20 rounded-lg border-slate-300 text-sm shadow-sm">
                                        <button type="button" class="remove-technician rounded-lg px-2 text-xs font-semibold text-rose-600 hover:bg-rose-50">×</button>
                                    </div>
                                @empty
                                    <div class="technician-row grid grid-cols-[minmax(0,1fr)_auto_auto_auto] gap-2">
                                        <input name="technicians[0][technician_name]" aria-label="Technician name" placeholder="Technician name" class="min-w-0 rounded-lg border-slate-300 text-sm shadow-sm">
                                        <input type="time" name="technicians[0][time]" aria-label="Technician time" class="w-28 rounded-lg border-slate-300 text-sm shadow-sm">
                                        <input type="number" step="0.01" min="0" name="technicians[0][amount]" aria-label="Technician amount" placeholder="$" class="w-20 rounded-lg border-slate-300 text-sm shadow-sm">
                                        <button type="button" class="remove-technician rounded-lg px-2 text-xs font-semibold text-rose-600 hover:bg-rose-50">×</button>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500">Save schedule</button>
                    </form>
                @endif

                @if (in_array($job->status, ['scheduled', 'dispatched', 'completed', 'paid'], true))
                    <div class="mt-5 space-y-2 border-t border-slate-100 pt-5">
                        <a href="{{ route('jobs.dispatch', $job) }}" class="block w-full rounded-lg border border-slate-200 px-4 py-2.5 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Preview dispatch sheet</a>
                        <a href="{{ route('jobs.dispatch.print', $job) }}" class="block w-full rounded-lg border border-slate-200 px-4 py-2.5 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Download dispatch PDF</a>
                    </div>
                @endif
                @if ($job->status === 'scheduled')
                    <form method="POST" action="{{ route('jobs.dispatch.mark', $job) }}" class="mt-3">
                        @csrf
                        <button class="w-full rounded-lg bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-sky-500">Mark as dispatched</button>
                    </form>
                @endif
                @if (in_array($job->status, ['scheduled', 'dispatched'], true))
                    <form method="POST" action="{{ route('jobs.complete', $job) }}" class="mt-3">
                        @csrf
                        <button class="w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-500">Mark work complete</button>
                    </form>
                @endif
            </section>

            @if (in_array($job->status, ['completed', 'paid'], true) && (float) $job->balance > 0)
                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="font-semibold text-slate-900">Record a payment</h2>
                    <p class="mt-1 text-sm text-slate-500">Remaining balance: <span class="font-semibold text-slate-900">${{ number_format((float) $job->balance, 2) }}</span>. Each payment gets its own receipt and appears on the invoice.</p>
                    <form method="POST" action="{{ route('jobs.payment', $job) }}" class="mt-5 space-y-3">
                        @csrf
                        <div>
                            <label for="payment_method" class="block text-xs font-medium text-slate-600">Payment method</label>
                            <select id="payment_method" name="payment_method" required class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm">
                                <option value="">Select a method</option><option value="check">Check</option><option value="charge">Charge</option><option value="card">Card</option><option value="cash">Cash</option><option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label for="payment_amount" class="block text-xs font-medium text-slate-600">Amount received</label>
                            <input id="payment_amount" type="number" name="amount" min="0.01" max="{{ number_format((float) $job->balance, 2, '.', '') }}" step="0.01" required value="{{ number_format((float) $job->balance, 2, '.', '') }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm">
                        </div>
                        <div>
                            <label for="transaction_date" class="block text-xs font-medium text-slate-600">Payment date</label>
                            <input id="transaction_date" type="date" name="transaction_date" required value="{{ now()->toDateString() }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm">
                        </div>
                        <div id="check-fields" class="hidden space-y-3 rounded-lg bg-slate-50 p-3">
                            <div><label class="block text-xs font-medium text-slate-600">Check number</label><input name="check_number" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm"></div>
                            <div><label class="block text-xs font-medium text-slate-600">Check date</label><input type="date" name="check_date" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm"></div>
                        </div>
                        <div id="account-fields" class="hidden">
                            <label class="block text-xs font-medium text-slate-600">Account reference / last 4 digits</label>
                            <input name="account_reference" maxlength="100" autocomplete="off" placeholder="Reference or last four digits only" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm">
                        </div>
                        <div id="card-fields" class="hidden space-y-3 rounded-lg bg-slate-50 p-3">
                            <div><label class="block text-xs font-medium text-slate-600">Card last 4 digits only</label><input name="card_last_four" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" autocomplete="off" placeholder="1234" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm"></div>
                            <div class="grid grid-cols-2 gap-2">
                                <div><label class="block text-xs font-medium text-slate-600">Expiry month</label><input type="number" name="card_expiry_month" min="1" max="12" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm"></div>
                                <div><label class="block text-xs font-medium text-slate-600">Expiry year</label><input type="number" name="card_expiry_year" min="{{ now()->year }}" max="{{ now()->year + 20 }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm"></div>
                            </div>
                            <p class="text-xs text-slate-500">Never enter or store the full card number or security code.</p>
                        </div>
                        <div><label class="block text-xs font-medium text-slate-600">Explanation</label><input name="explanation" maxlength="2000" placeholder="Optional payment explanation" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm"></div>
                        <div><label class="block text-xs font-medium text-slate-600">Payment notes</label><textarea name="notes" rows="2" class="mt-1 w-full rounded-lg border-slate-300 text-sm shadow-sm"></textarea></div>
                        <button class="w-full rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-400">Save payment and create receipt</button>
                    </form>
                </section>
            @endif

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="font-semibold text-slate-900">Payment status</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Invoice total</dt><dd class="font-medium text-slate-900">${{ number_format((float) $job->total, 2) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Total received</dt><dd class="font-medium text-emerald-700">${{ number_format((float) $job->amount_paid, 2) }}</dd></div>
                    <div class="flex justify-between border-t border-slate-100 pt-3"><dt class="font-semibold text-slate-700">Balance due</dt><dd class="font-semibold text-slate-900">${{ number_format((float) $job->balance, 2) }}</dd></div>
                </dl>
            </section>

            <form method="POST" action="{{ route('jobs.destroy', $job) }}" onsubmit="return confirm('Move this job to deleted jobs?');">
                @csrf
                @method('DELETE')
                <button class="w-full rounded-lg border border-rose-200 bg-white px-4 py-2.5 text-sm font-semibold text-rose-700 hover:bg-rose-50">Move to deleted jobs</button>
            </form>
        </aside>
    </div>

    <script>
        const addTechnicianButton = document.getElementById('add-technician');
        const technicianRows = document.getElementById('technician-rows');
        let technicianIndex = technicianRows ? technicianRows.querySelectorAll('.technician-row').length : 0;

        addTechnicianButton?.addEventListener('click', () => {
            const row = document.createElement('div');
            row.className = 'technician-row grid grid-cols-[minmax(0,1fr)_auto_auto_auto] gap-2';
            row.innerHTML = `<input name="technicians[${technicianIndex}][technician_name]" aria-label="Technician name" placeholder="Technician name" class="min-w-0 rounded-lg border-slate-300 text-sm shadow-sm"><input type="time" name="technicians[${technicianIndex}][time]" aria-label="Technician time" class="w-28 rounded-lg border-slate-300 text-sm shadow-sm"><input type="number" step="0.01" min="0" name="technicians[${technicianIndex}][amount]" aria-label="Technician amount" placeholder="$" class="w-20 rounded-lg border-slate-300 text-sm shadow-sm"><button type="button" class="remove-technician rounded-lg px-2 text-xs font-semibold text-rose-600 hover:bg-rose-50">×</button>`;
            technicianRows.appendChild(row);
            technicianIndex++;
        });

        technicianRows?.addEventListener('click', (event) => {
            if (event.target.closest('.remove-technician')) {
                event.target.closest('.technician-row').remove();
            }
        });

        const methodSelect = document.getElementById('payment_method');
        methodSelect?.addEventListener('change', () => {
            document.getElementById('check-fields').classList.toggle('hidden', methodSelect.value !== 'check');
            document.getElementById('card-fields').classList.toggle('hidden', methodSelect.value !== 'card' && methodSelect.value !== 'charge');
            document.getElementById('account-fields').classList.toggle('hidden', !['check', 'charge', 'card'].includes(methodSelect.value));
        });
    </script>
</x-app-layout>
