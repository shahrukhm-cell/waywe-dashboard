<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit service job</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('jobs.update', $job) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Quote / estimate number</label>
                            <input type="text" name="quote_estimate_number" value="{{ old('quote_estimate_number', $job->quote_estimate_number) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">PO #</label>
                            <input type="text" name="po_number" value="{{ old('po_number', $job->po_number) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Job type</label>
                            <select name="job_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="estimate" {{ old('job_type', $job->job_type) === 'estimate' ? 'selected' : '' }}>Estimate</option>
                                <option value="job" {{ old('job_type', $job->job_type) === 'job' ? 'selected' : '' }}>Job</option>
                                <option value="redo" {{ old('job_type', $job->job_type) === 'redo' ? 'selected' : '' }}>Redo</option>
                                <option value="pending" {{ old('job_type', $job->job_type) === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="adjustment" {{ old('job_type', $job->job_type) === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                                <option value="delivery" {{ old('job_type', $job->job_type) === 'delivery' ? 'selected' : '' }}>Delivery</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tax</label>
                            <input type="number" step="0.01" name="tax" value="{{ old('tax', $job->tax) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Other tax</label>
                            <input type="number" step="0.01" name="other_tax" value="{{ old('other_tax', $job->other_tax) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Full name</label>
                            <input type="text" name="site_full_name" value="{{ old('site_full_name', $job->site_full_name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Telephone</label>
                            <input type="text" name="site_telephone" value="{{ old('site_telephone', $job->site_telephone) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Business</label>
                            <input type="text" name="site_business" value="{{ old('site_business', $job->site_business) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" name="site_title" value="{{ old('site_title', $job->site_title) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Address</label>
                            <input type="text" name="site_address" value="{{ old('site_address', $job->site_address) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">City</label>
                            <input type="text" name="site_city" value="{{ old('site_city', $job->site_city) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">State</label>
                            <input type="text" name="site_state" value="{{ old('site_state', $job->site_state) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Postal code</label>
                            <input type="text" name="site_postal_code" value="{{ old('site_postal_code', $job->site_postal_code) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Billing name</label>
                            <input type="text" name="billing_full_name" value="{{ old('billing_full_name', $job->billing_full_name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Billing phone</label>
                            <input type="text" name="billing_telephone" value="{{ old('billing_telephone', $job->billing_telephone) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Billing address</label>
                            <input type="text" name="billing_address" value="{{ old('billing_address', $job->billing_address) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Billing city</label>
                            <input type="text" name="billing_city" value="{{ old('billing_city', $job->billing_city) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Billing state</label>
                            <input type="text" name="billing_state" value="{{ old('billing_state', $job->billing_state) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Billing postal code</label>
                            <input type="text" name="billing_postal_code" value="{{ old('billing_postal_code', $job->billing_postal_code) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Service line items</h3>
                    </div>

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
                                    <th class="border px-2 py-2 text-left">Time</th>
                                    <th class="border px-2 py-2 text-left">Amount</th>
                                    <th class="border px-2 py-2 text-left"><button type="button" id="add-item-row" class="text-indigo-700">Add row</button></th>
                                </tr>
                            </thead>
                            <tbody id="item-rows">
                                @foreach ($job->lineItems as $item)
                                    <tr class="item-row">
                                        <td class="border px-2 py-2"><input type="text" name="items[{{ $loop->index }}][area_item]" value="{{ $item->area_item }}" class="w-full rounded-md border-gray-300 shadow-sm"></td>
                                        <td class="border px-2 py-2"><input type="number" step="0.01" name="items[{{ $loop->index }}][length]" value="{{ $item->length }}" class="w-full rounded-md border-gray-300 shadow-sm"></td>
                                        <td class="border px-2 py-2"><input type="number" step="0.01" name="items[{{ $loop->index }}][width]" value="{{ $item->width }}" class="w-full rounded-md border-gray-300 shadow-sm"></td>
                                        <td class="border px-2 py-2"><input type="text" name="items[{{ $loop->index }}][description]" value="{{ $item->description }}" class="w-full rounded-md border-gray-300 shadow-sm"></td>
                                        <td class="border px-2 py-2"><input type="text" name="items[{{ $loop->index }}][unit]" value="{{ $item->unit }}" class="w-full rounded-md border-gray-300 shadow-sm"></td>
                                        <td class="border px-2 py-2"><input type="number" step="0.01" name="items[{{ $loop->index }}][quantity]" value="{{ $item->quantity }}" class="quantity-field w-full rounded-md border-gray-300 shadow-sm" {{ (float) $item->length > 0 && (float) $item->width > 0 ? 'readonly' : '' }}></td>
                                        <td class="border px-2 py-2"><input type="time" name="items[{{ $loop->index }}][time]" value="{{ $item->time }}" class="w-full rounded-md border-gray-300 shadow-sm"></td>
                                        <td class="border px-2 py-2"><input type="number" step="0.01" name="items[{{ $loop->index }}][amount]" value="{{ $item->amount }}" class="w-full rounded-md border-gray-300 shadow-sm"></td>
                                        <td class="border px-2 py-2"><button type="button" class="remove-item-row text-rose-600">Remove</button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <label class="block text-sm font-medium text-gray-700">Internal notes</label>
                    <textarea name="notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('notes', $job->notes) }}</textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('jobs.show', $job) }}" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Update job</button>
                </div>
            </form>
        </div>
    </div>
<script>
    const itemRows = document.getElementById('item-rows');
    const addRowButton = document.getElementById('add-item-row');
    let lineIndex = Math.max(-1, ...[...itemRows.querySelectorAll('[name^="items["]')].map((field) => Number(field.name.match(/^items\[(\d+)\]/)?.[1] ?? -1))) + 1;

    itemRows.addEventListener('input', (event) => {
        const row = event.target.closest('.item-row');
        if (!row || (!event.target.name.endsWith('[length]') && !event.target.name.endsWith('[width]'))) return;
        const lengthInput = row.querySelector('[name$="[length]"]');
        const widthInput = row.querySelector('[name$="[width]"]');
        const quantity = row.querySelector('.quantity-field');
        if (lengthInput.value !== '' && widthInput.value !== '') {
            quantity.value = (Number(lengthInput.value) * Number(widthInput.value)).toFixed(2);
            quantity.readOnly = true;
        } else {
            quantity.readOnly = false;
        }
    });

    itemRows.addEventListener('click', (event) => {
        if (event.target.closest('.remove-item-row')) event.target.closest('.item-row').remove();
    });

    addRowButton.addEventListener('click', () => {
        const index = lineIndex++;
        const row = document.createElement('tr');
        row.className = 'item-row';
        row.innerHTML = `<td class="border px-2 py-2"><input name="items[${index}][area_item]" class="w-full rounded-md border-gray-300 shadow-sm"></td><td class="border px-2 py-2"><input type="number" step="0.01" name="items[${index}][length]" class="w-full rounded-md border-gray-300 shadow-sm"></td><td class="border px-2 py-2"><input type="number" step="0.01" name="items[${index}][width]" class="w-full rounded-md border-gray-300 shadow-sm"></td><td class="border px-2 py-2"><input name="items[${index}][description]" class="w-full rounded-md border-gray-300 shadow-sm"></td><td class="border px-2 py-2"><input name="items[${index}][unit]" value="1" class="w-full rounded-md border-gray-300 shadow-sm"></td><td class="border px-2 py-2"><input type="number" step="0.01" name="items[${index}][quantity]" value="1" class="quantity-field w-full rounded-md border-gray-300 shadow-sm"></td><td class="border px-2 py-2"><input type="time" name="items[${index}][time]" class="w-full rounded-md border-gray-300 shadow-sm"></td><td class="border px-2 py-2"><input type="number" step="0.01" name="items[${index}][amount]" class="w-full rounded-md border-gray-300 shadow-sm"></td><td class="border px-2 py-2"><button type="button" class="remove-item-row text-rose-600">Remove</button></td>`;
        itemRows.appendChild(row);
    });
</script>
</x-app-layout>
