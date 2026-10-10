<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">Edit customer</h1>
                <p class="mt-1 text-sm text-slate-500">Update details for {{ $customer->full_name }}.</p>
            </div>
            <a href="{{ route('customers.index') }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back to customers</a>
        </div>
    </x-slot>

    @if ($errors->any())
        <div role="alert" class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <ul class="list-inside list-disc space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <section class="mx-auto max-w-3xl rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
        <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @foreach ([
                    'first_name' => ['First name', true],
                    'last_name' => ['Last name', false],
                    'phone' => ['Phone', true],
                    'business' => ['Business', false],
                    'title' => ['Title', false],
                    'address' => ['Address', false],
                    'city' => ['City', false],
                    'state' => ['State', false],
                    'postal_code' => ['Postal code', false],
                ] as $field => [$label, $required])
                    <div class="{{ $field === 'address' ? 'md:col-span-2' : '' }}">
                        <label for="{{ $field }}" class="block text-sm font-medium text-slate-700">{{ $label }}</label>
                        <input
                            id="{{ $field }}"
                            type="text"
                            name="{{ $field }}"
                            value="{{ old($field, $customer->{$field}) }}"
                            @if ($required) required @endif
                            class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('customers.index') }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500">Save changes</button>
            </div>
        </form>
    </section>
</x-app-layout>
