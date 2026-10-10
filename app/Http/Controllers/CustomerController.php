<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::query();

        if ($request->filled('phone')) {
            $customers->where('phone', 'like', '%' . $request->phone . '%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $customers->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('business', 'like', "%{$search}%");
            });
        }

        return view('customers.index', [
            'customers' => $customers->latest()->paginate(15),
            'search' => $request->search ?? '',
        ]);
    }

    public function create()
    {
        return view('customers.create');
    }

    public function show(Customer $customer)
    {
        $customer->load([
            'jobs' => fn ($query) => $query
                ->with(['lineItems', 'technicians', 'payments', 'reminders'])
                ->latest(),
        ]);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', Rule::unique('customers', 'phone')],
            'business' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:50'],
        ]);

        $customer = Customer::create($validated);

        return redirect()->route('jobs.create', ['phone' => $customer->phone])->with('success', 'Customer saved successfully.');
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', Rule::unique('customers', 'phone')->ignore($customer->id)],
            'business' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:50'],
        ]);

        $customer->update($validated);

        return redirect()->route('customers.show', $customer)->with('success', 'Customer updated successfully.');
    }
}
