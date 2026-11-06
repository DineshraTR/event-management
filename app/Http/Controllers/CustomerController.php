<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $hasEvents = $request->input('has_events');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $query = Customer::withCount(['events', 'orders']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('phone2', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nic', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($status !== null && $status !== '') {
            $query->where('is_active', $status === '1' || $status === 'active');
        }

        if ($hasEvents === 'yes') {
            $query->has('events');
        } elseif ($hasEvents === 'no') {
            $query->doesntHave('events');
        }

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $customers = $query->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:25',
            'phone2' => 'nullable|string|max:25',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'nic' => 'required|string|max:30',
            'dob' => 'nullable|date',
            'lead_source' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;
        $customer = Customer::create($validated);

        \App\Services\AuditLogger::log(
            'created',
            'Customer',
            $customer->name,
            "Added new customer {$customer->name} ({$customer->phone})",
            $customer->id,
            null,
            $customer->toArray()
        );

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'customer' => $customer]);
        }
        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['events.subCategory', 'orders.payments']);
        $totalBilled = $customer->orders()->sum('total_amount');
        $totalPaid = $customer->payments()->sum('amount');
        $totalBalance = $totalBilled - $totalPaid;
        return view('customers.show', compact('customer', 'totalBilled', 'totalPaid', 'totalBalance'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:25',
            'phone2' => 'nullable|string|max:25',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'nic' => 'required|string|max:30',
            'dob' => 'nullable|date',
            'lead_source' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;
        $oldValues = $customer->toArray();
        $customer->update($validated);

        \App\Services\AuditLogger::log(
            'updated',
            'Customer',
            $customer->name,
            "Updated profile details for customer {$customer->name}",
            $customer->id,
            $oldValues,
            $customer->fresh()->toArray()
        );

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $oldValues = $customer->toArray();
        $customer->update(['is_active' => false]);

        \App\Services\AuditLogger::log(
            'deactivated',
            'Customer',
            $customer->name,
            "Deactivated customer {$customer->name}",
            $customer->id,
            $oldValues,
            ['is_active' => false]
        );

        return redirect()->route('customers.index')->with('success', 'Customer deactivated successfully.');
    }

    public function toggleStatus(Customer $customer)
    {
        $customer->update(['is_active' => !$customer->is_active]);
        $status = $customer->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Customer {$status} successfully.");
    }

    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'company_name' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'nic' => 'nullable|string|max:50',
        ]);
        $validated['is_active'] = true;
        $customer = Customer::create($validated);
        return response()->json([
            'success' => true,
            'customer' => $customer,
            'message' => 'Customer created successfully.'
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $customers = Customer::where('name', 'like', "%$query%")
            ->orWhere('phone', 'like', "%$query%")
            ->limit(10)
            ->get(['id', 'name', 'phone', 'email']);
        return response()->json($customers);
    }
}
