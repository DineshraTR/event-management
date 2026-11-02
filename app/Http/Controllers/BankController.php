<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'all');

        $query = Bank::query()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('bank_name', 'like', "%{$search}%")
                        ->orWhere('account_name', 'like', "%{$search}%")
                        ->orWhere('account_number', 'like', "%{$search}%")
                        ->orWhere('branch', 'like', "%{$search}%")
                        ->orWhere('swift_code', 'like', "%{$search}%");
                });
            })
            ->when($status === 'active', fn($q) => $q->where('is_active', true))
            ->when($status === 'inactive', fn($q) => $q->where('is_active', false));

        // Metric Statistics
        $totalBanksCount = Bank::count();
        $activeBanksCount = Bank::where('is_active', true)->count();
        $totalBalance = Bank::where('is_active', true)->sum('current_balance');

        $banks = $query->latest()->paginate(25)->withQueryString();

        return view('banks.index', compact(
            'banks',
            'search',
            'status',
            'totalBanksCount',
            'activeBanksCount',
            'totalBalance'
        ));
    }

    public function create()
    {
        return view('banks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:100',
            'branch' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:50',
            'contact_number' => 'nullable|string|max:50',
            'opening_balance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $openingBalance = (float)($request->input('opening_balance', 0));
        $validated['opening_balance'] = $openingBalance;
        $validated['current_balance'] = $openingBalance;
        $validated['is_active'] = $request->has('is_active');

        Bank::create($validated);

        return redirect()->route('banks.index')->with('success', 'Bank account registered successfully!');
    }

    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:100',
            'branch' => 'nullable|string|max:255',
            'opening_balance' => 'nullable|numeric|min:0',
        ]);

        $openingBalance = (float)($request->input('opening_balance', 0));
        $validated['opening_balance'] = $openingBalance;
        $validated['current_balance'] = $openingBalance;
        $validated['is_active'] = true;

        $bank = Bank::create($validated);

        $branchName = $bank->branch ?: 'Main';
        return response()->json([
            'success' => true,
            'message' => 'Bank account registered successfully.',
            'bank' => $bank,
            'display_value' => "{$bank->bank_name} - {$bank->account_number} ({$branchName})",
            'display_text' => "{$bank->bank_name} • {$bank->account_number} ({$branchName}) - Bal: Rs. " . number_format($bank->current_balance, 2)
        ]);
    }

    public function show(Bank $bank)
    {
        return redirect()->route('banks.edit', $bank);
    }

    public function edit(Bank $bank)
    {
        return view('banks.edit', compact('bank'));
    }

    public function update(Request $request, Bank $bank)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:100',
            'branch' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:50',
            'contact_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $bank->update($validated);

        return redirect()->route('banks.index')->with('success', 'Bank account updated successfully!');
    }

    public function destroy(Bank $bank)
    {
        $bank->delete();
        return redirect()->route('banks.index')->with('success', 'Bank account deleted successfully.');
    }

    public function toggleStatus(Bank $bank)
    {
        $bank->is_active = !$bank->is_active;
        $bank->save();

        $statusText = $bank->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Bank account {$bank->bank_name} has been {$statusText}.");
    }
}
