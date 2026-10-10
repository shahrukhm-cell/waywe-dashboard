<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Customers</h2>
            <a href="{{ route('customers.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                New customer
            </a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('customers.index') }}" class="mb-6 flex flex-col gap-3 md:flex-row">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, business, or phone" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="submit" class="rounded-md bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Search</button>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr class="text-left text-gray-600">
                                <th class="pb-3 pr-4">Customer</th>
                                <th class="pb-3 pr-4">Phone</th>
                                <th class="pb-3 pr-4">Business</th>
                                <th class="pb-3 pr-4">Address</th>
                                <th class="pb-3 pr-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($customers as $customer)
                                <tr>
                                    <td class="py-3 pr-4 font-medium">
                                        <a href="{{ route('customers.show', $customer) }}" class="text-slate-800 hover:text-indigo-700">
                                            {{ $customer->full_name }}
                                        </a>
                                    </td>
                                    <td class="py-3 pr-4">{{ $customer->phone ?: '—' }}</td>
                                    <td class="py-3 pr-4">{{ $customer->business ?: '—' }}</td>
                                    <td class="py-3 pr-4">{{ trim(($customer->address ?: '') . ' ' . ($customer->city ?: '')) ?: '—' }}</td>
                                    <td class="py-3 pr-4">
                                        <div class="flex flex-wrap gap-x-3 gap-y-1">
                                            <a href="{{ route('customers.edit', $customer) }}" class="font-medium text-slate-700 hover:text-indigo-700">Edit</a>
                                            <a href="{{ route('customers.show', $customer) }}" class="text-slate-700 hover:text-indigo-700">View</a>
                                            <a href="{{ route('jobs.create', ['phone' => $customer->phone]) }}" class="text-indigo-600 hover:text-indigo-800">Create job</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-500">No customers found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
