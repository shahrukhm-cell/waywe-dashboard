<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create service job / quote</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('jobs.store') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="customer_id" value="{{ $customer?->id ?? old('customer_id') }}">

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Customer / site lookup</h3>
                        <a href="{{ route('customers.create') }}" class="text-sm font-medium text-indigo-600">Create customer</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Phone lookup</label>
                            <div class="mt-1 flex gap-3">
                                <input type="text" id="lookup-phone" value="{{ $phone ?? old('site_telephone') ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <a href="{{ route('jobs.create') }}" id="lookup-link" class="inline-flex items-center rounded-md bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Lookup</a>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Full name</label>
                            <input type="text" name="site_full_name" value="{{ $customer?->full_name ?? old('site_full_name') ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Telephone</label>
                            <input type="text" name="site_telephone" value="{{ $customer?->phone ?? old('site_telephone') ?? '' }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Business</label>
                            <input type="text" name="site_business" value="{{ $customer?->business ?? old('site_business') ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" name="site_title" value="{{ $customer?->title ?? old('site_title') ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Address</label>
                            <input type="text" name="site_address" value="{{ $customer?->address ?? old('site_address') ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">City</label>
                            <input type="text" name="site_city" value="{{ $customer?->city ?? old('site_city') ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">State</label>
                            <input type="text" name="site_state" value="{{ $customer?->state ?? old('site_state') ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Postal code</label>
                            <input type="text" name="site_postal_code" value="{{ $customer?->postal_code ?? old('site_postal_code') ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Quote / estimate number</label>
                            <input type="text" name="quote_estimate_number" value="{{ old('quote_estimate_number') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">PO #</label>
                            <input type="text" name="po_number" value="{{ old('po_number') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Job type</label>
                            <select name="job_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="estimate" {{ old('job_type', 'estimate') === 'estimate' ? 'selected' : '' }}>Estimate</option>
                                <option value="job" {{ old('job_type') === 'job' ? 'selected' : '' }}>Job</option>
                                <option value="redo" {{ old('job_type') === 'redo' ? 'selected' : '' }}>Redo</option>
                                <option value="pending" {{ old('job_type') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="adjustment" {{ old('job_type') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                                <option value="delivery" {{ old('job_type') === 'delivery' ? 'selected' : '' }}>Delivery</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <div class="mt-1 rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-800">New jobs start as a quote</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Confirmed date</label>
                            <input type="date" name="confirmed_date" value="{{ old('confirmed_date', today()->toDateString()) }}" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Booked date / work date</label>
                            <input type="date" name="booked_date" value="{{ old('booked_date') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">This will also be saved as the scheduled date.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Service line items</h3>
                        <button type="button" id="add-item-row" class="rounded-md border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">Add row</button>
                    </div>

                    @php
                        $items = old('items', [[
                            'area_item' => '',
                            'length' => '',
                            'width' => '',
                            'description' => '',
                            'unit' => '1',
                            'quantity' => '1',
                            'time' => '',
                            'amount' => '',
                        ]]);
                    @endphp

                    <div class="overflow-x-auto">
                        <table class="min-w-full border text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="border px-2 py-2 text-left">Area/Item</th>
                                    <th class="border px-2 py-2 text-left">Length</th>
                                    <th class="border px-2 py-2 text-left">Width</th>
                                    <th class="border px-2 py-2 text-left">Description</th>
                                    <th class="border px-2 py-2 text-left">Unit</th>
                                    <th class="border px-2 py-2 text-left">Qty</th>
                                    <th class="border px-2 py-2 text-left">Scheduled time</th>
                                    <th class="border px-2 py-2 text-left">Amount</th>
                                    <th class="border px-2 py-2 text-left">Action</th>
                                </tr>
                            </thead>
                            <tbody id="item-rows">
                                @foreach ($items as $index => $item)
                                    <tr class="item-row">
                                        <td class="border px-2 py-2"><input type="text" name="items[{{ $index }}][area_item]" value="{{ $item['area_item'] ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm"></td>
                                        <td class="border px-2 py-2"><input type="number" step="0.01" name="items[{{ $index }}][length]" value="{{ $item['length'] ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm"></td>
                                        <td class="border px-2 py-2"><input type="number" step="0.01" name="items[{{ $index }}][width]" value="{{ $item['width'] ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm"></td>
                                        <td class="border px-2 py-2"><input type="text" name="items[{{ $index }}][description]" value="{{ $item['description'] ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm"></td>
                                        <td class="border px-2 py-2"><input type="text" name="items[{{ $index }}][unit]" value="{{ $item['unit'] ?? '1' }}" class="w-full rounded-md border-gray-300 shadow-sm"></td>
                                        <td class="border px-2 py-2"><input type="number" step="0.01" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" class="quantity-field w-full rounded-md border-gray-300 bg-gray-50 shadow-sm" {{ !empty($item['length']) && !empty($item['width']) ? 'readonly' : '' }}></td>
                                        <td class="border px-2 py-2"><input type="time" name="items[{{ $index }}][time]" value="{{ $item['time'] ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm"></td>
                                        <td class="border px-2 py-2"><input type="number" step="0.01" name="items[{{ $index }}][amount]" value="{{ $item['amount'] ?? '' }}" class="amount-field w-full rounded-md border-gray-300 shadow-sm"></td>
                                        <td class="border px-2 py-2"><button type="button" class="remove-item-row rounded-md bg-red-50 px-2 py-1 text-xs font-semibold text-red-700 hover:bg-red-100">Remove</button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Crew / route</label>
                            <input type="text" name="crew_route" value="{{ old('crew_route') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Best time</label>
                            <input type="time" name="best_time" value="{{ old('best_time') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Source</label>
                            <input type="text" name="source" value="{{ old('source') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Agent</label>
                            <input type="text" name="agent" value="{{ old('agent') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Billing details</h3>
                        <p class="mt-1 text-sm text-gray-500">The invoice address starts with the site address and can be changed later.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Billing name</label>
                            <input type="text" name="billing_full_name" value="{{ old('billing_full_name', $customer?->full_name ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Billing phone</label>
                            <input type="text" name="billing_telephone" value="{{ old('billing_telephone', $customer?->phone ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Billing address</label>
                            <input type="text" name="billing_address" value="{{ old('billing_address', $customer?->address ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Billing city</label>
                            <input type="text" name="billing_city" value="{{ old('billing_city', $customer?->city ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Billing state</label>
                            <input type="text" name="billing_state" value="{{ old('billing_state', $customer?->state ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Billing postal code</label>
                            <input type="text" name="billing_postal_code" value="{{ old('billing_postal_code', $customer?->postal_code ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Other notes / internal notes</label>
                            <textarea name="notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                        </div>
                        <div>
                            <div class="rounded-lg border border-gray-200 bg-slate-50 p-4">
                                <div class="grid grid-cols-2 gap-2 text-sm text-gray-700">
                                    <div>Subtotal</div>
                                    <div class="text-right"><span id="subtotal-display">$0.00</span></div>
                                    <div>Tax</div>
                                    <div><input type="number" step="0.01" name="tax" value="{{ old('tax', 0) }}" class="w-full rounded-md border-gray-300 text-right"></div>
                                    <div>Other tax</div>
                                    <div><input type="number" step="0.01" name="other_tax" value="{{ old('other_tax', 0) }}" class="w-full rounded-md border-gray-300 text-right"></div>
                                    <div class="font-semibold">Total</div>
                                    <div class="text-right font-semibold"><span id="total-display">$0.00</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('jobs.index') }}" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Save quote</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const itemTable = document.getElementById('item-rows');
        const addItemButton = document.getElementById('add-item-row');
        const lookupLink = document.getElementById('lookup-link');
        const lookupInput = document.getElementById('lookup-phone');

        if (lookupLink && lookupInput) {
            lookupLink.href = '{{ route('jobs.create') }}?phone=' + encodeURIComponent(lookupInput.value || '');
            lookupInput.addEventListener('input', function() {
                lookupLink.href = '{{ route('jobs.create') }}?phone=' + encodeURIComponent(this.value);
            });
        }

        function updateSummary() {
            const amounts = [...document.querySelectorAll('.amount-field')].map((input) => {
                const value = Number.parseFloat(input.value || 0);
                return Number.isNaN(value) ? 0 : value;
            });
            const subtotal = amounts.reduce((total, value) => total + value, 0);
            const tax = Number.parseFloat(document.querySelector('input[name="tax"]').value || 0);
            const otherTax = Number.parseFloat(document.querySelector('input[name="other_tax"]').value || 0);
            const total = subtotal + tax + otherTax;
            const money = (value) => '$' + value.toFixed(2);
            document.getElementById('subtotal-display').textContent = money(subtotal);
            document.getElementById('total-display').textContent = money(total);
        }

        let itemIndex = Math.max(-1, ...[...itemTable.querySelectorAll('[name^="items["]')].map((field) => Number(field.name.match(/^items\[(\d+)\]/)?.[1] ?? -1))) + 1;

        itemTable.addEventListener('input', (event) => {
            const row = event.target.closest('.item-row');
            if (row && (event.target.name.endsWith('[length]') || event.target.name.endsWith('[width]'))) {
                const length = Number.parseFloat(row.querySelector('[name$="[length]"]').value);
                const width = Number.parseFloat(row.querySelector('[name$="[width]"]').value);
                const quantity = row.querySelector('.quantity-field');
                if (row.querySelector('[name$="[length]"]').value !== '' && row.querySelector('[name$="[width]"]').value !== '') {
                    quantity.value = (length * width).toFixed(2);
                    quantity.readOnly = true;
                } else {
                    quantity.readOnly = false;
                }
            }
            if (event.target.matches('.amount-field')) updateSummary();
        });

        document.querySelectorAll('.amount-field, input[name="tax"], input[name="other_tax"]').forEach((field) => {
            field.addEventListener('input', updateSummary);
        });

        addItemButton.addEventListener('click', function () {
            const rowCount = itemIndex++;
            const row = document.createElement('tr');
            row.className = 'item-row';
            row.innerHTML = `
                <td class="border px-2 py-2"><input type="text" name="items[${rowCount}][area_item]" value="" class="w-full rounded-md border-gray-300 shadow-sm"></td>
                <td class="border px-2 py-2"><input type="number" step="0.01" name="items[${rowCount}][length]" value="" class="w-full rounded-md border-gray-300 shadow-sm"></td>
                <td class="border px-2 py-2"><input type="number" step="0.01" name="items[${rowCount}][width]" value="" class="w-full rounded-md border-gray-300 shadow-sm"></td>
                <td class="border px-2 py-2"><input type="text" name="items[${rowCount}][description]" value="" class="w-full rounded-md border-gray-300 shadow-sm"></td>
                <td class="border px-2 py-2"><input type="text" name="items[${rowCount}][unit]" value="1" class="w-full rounded-md border-gray-300 shadow-sm"></td>
                <td class="border px-2 py-2"><input type="number" step="0.01" name="items[${rowCount}][quantity]" value="1" class="quantity-field w-full rounded-md border-gray-300 bg-gray-50 shadow-sm"></td>
                <td class="border px-2 py-2"><input type="time" name="items[${rowCount}][time]" value="" class="w-full rounded-md border-gray-300 shadow-sm"></td>
                <td class="border px-2 py-2"><input type="number" step="0.01" name="items[${rowCount}][amount]" value="" class="amount-field w-full rounded-md border-gray-300 shadow-sm"></td>
                <td class="border px-2 py-2"><button type="button" class="remove-item-row rounded-md bg-red-50 px-2 py-1 text-xs font-semibold text-red-700 hover:bg-red-100">Remove</button></td>
            `;
            itemTable.appendChild(row);
            row.querySelector('.remove-item-row').addEventListener('click', function () {
                row.remove();
                updateSummary();
            });
        });

        document.querySelectorAll('.remove-item-row').forEach((button) => {
            button.addEventListener('click', function () {
                this.closest('tr').remove();
                updateSummary();
            });
        });

        updateSummary();
    </script>
</x-app-layout>
