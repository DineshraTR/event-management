<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankTransaction;
use App\Models\CashTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BankTransactionController extends Controller
{
    public function index(Request $request)
    {
        return $this->create($request);
    }

    public function create(Request $request = null)
    {
        $today = Carbon::today()->format('Y-m-d');
        $banks = Bank::where('is_active', true)->orderBy('bank_name')->get();
        $recentBank = BankTransaction::latest('transaction_date')->latest('id')->take(4)->get();
        $recentCash = CashTransaction::latest('transaction_date')->latest('id')->take(4)->get();
        $recentTransactions = $recentBank->concat($recentCash)->sortByDesc('transaction_date')->take(6);
        return view('bank-transactions.create', compact('today', 'banks', 'recentTransactions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'transaction_method' => 'required|in:cash,cheque,bank_transfer',
            'account_type' => 'nullable|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'reference_no' => 'nullable|string|max:50',
            'cheque_no' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if ($validated['transaction_method'] === 'cash') {
            CashTransaction::create([
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'description' => $validated['description'],
                'reference_no' => $validated['reference_no'] ?? null,
                'transaction_date' => $validated['transaction_date'],
                'notes' => $validated['notes'] ?? null,
            ]);
            return redirect()->route('cash-book')->with('success', 'Cash transaction recorded into Cash Book successfully.');
        } else {
            $validated['account_type'] = $validated['account_type'] ?? ($validated['bank_name'] ?? 'bank');
            BankTransaction::create($validated);
            return redirect()->route('bank-book')->with('success', 'Bank transaction recorded into Bank Book successfully.');
        }
    }

    public function show($id)
    {
        $transaction = BankTransaction::find($id) ?? CashTransaction::findOrFail($id);
        return view('bank-transactions.show', ['bankTransaction' => $transaction]);
    }

    public function edit($id)
    {
        $transaction = BankTransaction::find($id) ?? CashTransaction::findOrFail($id);
        return view('bank-transactions.edit', ['bankTransaction' => $transaction]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $bankTransaction = BankTransaction::find($id);
        if ($bankTransaction) {
            $bankTransaction->update($validated);
            return redirect()->route('bank-book')->with('success', 'Bank transaction updated.');
        }

        $cashTransaction = CashTransaction::findOrFail($id);
        $cashTransaction->update($validated);
        return redirect()->route('cash-book')->with('success', 'Cash transaction updated.');
    }

    public function destroy($id)
    {
        $bankTransaction = BankTransaction::find($id);
        if ($bankTransaction) {
            $bankTransaction->delete();
            return redirect()->route('bank-book')->with('success', 'Bank transaction deleted.');
        }

        $cashTransaction = CashTransaction::findOrFail($id);
        $cashTransaction->delete();
        return redirect()->route('cash-book')->with('success', 'Cash transaction deleted.');
    }

    public function cashBook()
    {
        $dateFrom = request('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = request('date_to', Carbon::today()->format('Y-m-d'));
        $type = request('type', 'all');
        $search = request('search');

        // Period overall totals (unfiltered by keyword for accurate financial balance)
        $periodQuery = CashTransaction::whereBetween('transaction_date', [$dateFrom, $dateTo]);

        $periodIncomeTotal = (float) (clone $periodQuery)->where('type', 'income')->sum('amount');
        $periodExpenseTotal = (float) (clone $periodQuery)->where('type', 'expense')->sum('amount');
        $totalTransactionsCount = (int) (clone $periodQuery)->count();

        // Filtered transactions for the ledger view
        $query = CashTransaction::whereBetween('transaction_date', [$dateFrom, $dateTo]);

        if ($type && in_array($type, ['income', 'expense'])) {
            $query->where('type', $type);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhere('transaction_no', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $transactions = $query->with(['customer', 'order'])->orderBy('transaction_date', 'asc')->orderBy('id', 'asc')->get();

        $openingBalance = (float) (CashTransaction::where('transaction_date', '<', $dateFrom)
            ->selectRaw('SUM(CASE WHEN type="income" THEN amount ELSE -amount END) as balance')
            ->value('balance') ?? 0);

        return view('bank-transactions.cash-book', compact(
            'transactions',
            'dateFrom',
            'dateTo',
            'openingBalance',
            'periodIncomeTotal',
            'periodExpenseTotal',
            'totalTransactionsCount',
            'type',
            'search'
        ));
    }

    public function bankBook()
    {
        $dateFrom = request('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = request('date_to', Carbon::today()->format('Y-m-d'));
        $type = request('type', 'all');
        $method = request('method', 'all');
        $bankFilter = request('bank');
        $search = request('search');

        $baseQuery = BankTransaction::query();

        if ($bankFilter) {
            $baseQuery->where('bank_name', 'like', "%{$bankFilter}%");
        }

        // Period overall totals (unfiltered by search keyword for accurate period summary)
        $periodQuery = (clone $baseQuery)->whereBetween('transaction_date', [$dateFrom, $dateTo]);

        $periodIncomeTotal = (float) (clone $periodQuery)->where('type', 'income')->sum('amount');
        $periodExpenseTotal = (float) (clone $periodQuery)->where('type', 'expense')->sum('amount');
        $totalTransactionsCount = (int) (clone $periodQuery)->count();

        // Filtered query for display
        $query = (clone $periodQuery);

        if ($type && in_array($type, ['income', 'expense'])) {
            $query->where('type', $type);
        }

        if ($method && in_array($method, ['bank_transfer', 'cheque'])) {
            $query->where('transaction_method', $method);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhere('cheque_no', 'like', "%{$search}%")
                  ->orWhere('bank_name', 'like', "%{$search}%")
                  ->orWhere('transaction_no', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $transactions = $query->with(['customer', 'order', 'cheque', 'bank'])->orderBy('transaction_date', 'asc')->orderBy('id', 'asc')->get();

        $openingBalance = (float) ((clone $baseQuery)
            ->where('transaction_date', '<', $dateFrom)
            ->selectRaw('SUM(CASE WHEN type="income" THEN amount ELSE -amount END) as balance')
            ->value('balance') ?? 0);

        $banks = Bank::orderBy('bank_name')->get();

        return view('bank-transactions.bank-book', compact(
            'transactions',
            'dateFrom',
            'dateTo',
            'openingBalance',
            'periodIncomeTotal',
            'periodExpenseTotal',
            'totalTransactionsCount',
            'type',
            'method',
            'bankFilter',
            'search',
            'banks'
        ));
    }
}

