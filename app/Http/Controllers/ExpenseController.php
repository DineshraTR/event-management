<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Event;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Bank;
use App\Models\BankTransaction;
use App\Models\CashTransaction;
use App\Models\Cheque;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    /**
     * Display the expenses entry terminal directly on /expenses
     */
    public function index(Request $request)
    {
        return $this->create($request);
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create(Request $request)
    {
        // Seed default categories if none exist
        if (ExpenseCategory::count() === 0) {
            $defaultCategories = [
                'Office Rent' => 'Office space lease and building rent',
                'Electricity & Water' => 'Monthly utilities bills',
                'Transport & Fuel' => 'Vehicle fuel and logistics',
                'Staff Wages & Overtime' => 'Operational staff daily/weekly payouts',
                'Food & Refreshments' => 'Staff tea, snacks, and meals',
                'Stationery & Printing' => 'Paper, toner, and office stationery',
                'Equipment Maintenance' => 'Repair and servicing of equipment',
                'Advertising & Marketing' => 'Social media promotions and printing ads',
                'Internet & Phone' => 'Broadband and mobile phone charges',
                'Miscellaneous' => 'General minor overhead expenses'
            ];

            foreach ($defaultCategories as $name => $desc) {
                ExpenseCategory::firstOrCreate(['name' => $name], ['description' => $desc, 'status' => 'active']);
            }
        }

        $categories = ExpenseCategory::where('status', 'active')->orderBy('name')->get();
        $banks = Bank::where('is_active', true)->orderBy('bank_name')->get();

        // Fetch customers and approved orders for Direct Expenses
        $customers = Customer::where('is_active', true)->orderBy('name')->get(['id', 'name', 'phone']);
        $orders = Order::with(['customer', 'event'])
            ->whereHas('customer')
            ->latest('id')
            ->get();

        $today = Carbon::today()->format('Y-m-d');
        $preselectedCustomerId = $request->get('customer_id');
        $preselectedOrderId = $request->get('order_id');

        return view('expenses.create', compact(
            'categories',
            'banks',
            'customers',
            'orders',
            'today',
            'preselectedCustomerId',
            'preselectedOrderId'
        ));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:direct,normal',
            'category' => 'nullable|string|max:100',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,cheque',
            'bank_name' => 'nullable|string|max:255',
            'cheque_no' => 'nullable|string|max:100',
            'order_id' => 'nullable|exists:orders,id',
            'event_id' => 'nullable|exists:events,id',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        // If direct expense with order_id, automatically resolve event_id
        if ($validated['type'] === 'direct' && !empty($validated['order_id'])) {
            $order = Order::find($validated['order_id']);
            if ($order && $order->event_id) {
                $validated['event_id'] = $order->event_id;
            }
        }

        $expense = DB::transaction(function () use ($validated) {
            $expense = Expense::create($validated);

            if ($validated['payment_method'] === 'cheque') {
                // Cheque expense: record as Outward Cheque (Issued)
                // Bank balance will NOT be deducted immediately.
                // Balance is only deducted when cheque is marked as Realized / Cleared in Cheques Management!
                $targetBank = null;
                if (!empty($validated['bank_name'])) {
                    $targetBank = Bank::where('bank_name', 'like', "%{$validated['bank_name']}%")->first();
                }

                $chequeNo = !empty($validated['cheque_no']) ? trim($validated['cheque_no']) : ('CHQ-' . str_pad($expense->id, 6, '0', STR_PAD_LEFT));

                Cheque::create([
                    'type' => 'OUT',
                    'cheque_number' => $chequeNo,
                    'cheque_date' => $validated['expense_date'],
                    'received_or_issued_date' => $validated['expense_date'],
                    'amount' => $validated['amount'],
                    'payee_name' => $validated['description'],
                    'expense_id' => $expense->id,
                    'bank_id' => $targetBank?->id,
                    'status' => 'Issued',
                    'notes' => $validated['notes'] ?: "Cheque issued for {$validated['type']} expense: {$validated['description']}",
                ]);
            } else {
                // Cash or Bank Transfer: record in respective table
                if ($validated['payment_method'] === 'cash') {
                    CashTransaction::create([
                        'type' => 'expense',
                        'amount' => $validated['amount'],
                        'description' => ($validated['type'] === 'direct' ? 'Direct Event Expense: ' : 'Expense: ') . $validated['description'],
                        'reference_no' => $expense->expense_no,
                        'order_id' => $validated['order_id'] ?? null,
                        'expense_id' => $expense->id,
                        'transaction_date' => $validated['expense_date'],
                        'notes' => $validated['notes'] ?? null,
                    ]);
                } else {
                    $accountType = $validated['bank_name'] ?? 'bank';
                    $targetBank = null;
                    if ($validated['payment_method'] === 'bank_transfer' && !empty($validated['bank_name'])) {
                        $targetBank = Bank::where('bank_name', 'like', "%{$validated['bank_name']}%")->first();
                        if ($targetBank) {
                            $targetBank->decrement('current_balance', $validated['amount']);
                        }
                    }

                    BankTransaction::create([
                        'type' => 'expense',
                        'transaction_method' => $validated['payment_method'],
                        'account_type' => $accountType,
                        'bank_id' => $targetBank?->id,
                        'order_id' => $validated['order_id'] ?? null,
                        'expense_id' => $expense->id,
                        'amount' => $validated['amount'],
                        'description' => ($validated['type'] === 'direct' ? 'Direct Event Expense: ' : 'Expense: ') . $validated['description'],
                        'reference_no' => $expense->expense_no,
                        'cheque_no' => null,
                        'bank_name' => $validated['bank_name'] ?? null,
                        'transaction_date' => $validated['expense_date'],
                        'running_balance' => $targetBank?->current_balance ?? 0,
                        'notes' => $validated['notes'] ?? null,
                    ]);
                }
            }

            return $expense;
        });

        \App\Services\AuditLogger::log(
            'created',
            'Expense',
            "Expense #{$expense->expense_no} - Rs. " . number_format($expense->amount, 2),
            "Recorded expense #{$expense->expense_no} ({$expense->category}) of Rs. " . number_format($expense->amount, 2) . " via {$expense->payment_method}",
            $expense->id,
            null,
            $expense->toArray()
        );

        $successMsg = ($validated['payment_method'] === 'cheque')
            ? 'Expense recorded and Cheque saved to Cheque Management (Issued).'
            : 'Expense recorded successfully and ledger updated.';

        return redirect()->route('expenses.index')->with('success', $successMsg);
    }

    /**
     * Quick store for Expense Category via Modal AJAX
     */
    public function quickStoreCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        $name = trim($validated['name']);

        $category = ExpenseCategory::firstOrCreate(
            ['name' => $name],
            [
                'description' => $validated['description'] ?? null,
                'status' => 'active',
            ]
        );

        if ($category->status !== 'active') {
            $category->update(['status' => 'active']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Expense category created successfully.',
            'category' => $category,
        ]);
    }

    public function show(Expense $expense)
    {
        $expense->load(['event', 'order.customer']);
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $cheque = $expense->cheque ?: Cheque::where('expense_id', $expense->id)->first();
        if ($cheque && in_array(strtolower($cheque->status), ['realized', 'issued', 'released', 'cleared', 'deposited'])) {
            return redirect()->route('reports.expenses')->with('error', "මෙම වියදමට අදාළ චෙක්පත (#{$cheque->cheque_number}) දැනටමත් '{$cheque->status}' තත්වයේ පවතින බැවින් සංශෝධනය (Edit) කිරීමට නොහැක. (Cheque #{$cheque->cheque_number} is {$cheque->status} - editing is locked).");
        }

        $categories = ExpenseCategory::where('status', 'active')->orderBy('name')->get();
        $banks = Bank::where('is_active', true)->orderBy('bank_name')->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $orders = Order::with(['customer', 'event'])->latest('id')->get();
        $events = Event::orderBy('event_name')->get();

        return view('expenses.edit', compact('expense', 'categories', 'banks', 'customers', 'orders', 'events'));
    }

    public function update(Request $request, Expense $expense)
    {
        $cheque = $expense->cheque ?: Cheque::where('expense_id', $expense->id)->first();
        if ($cheque && in_array(strtolower($cheque->status), ['realized', 'issued', 'released', 'cleared', 'deposited'])) {
            return redirect()->route('reports.expenses')->with('error', "මෙම වියදමට අදාළ චෙක්පත (#{$cheque->cheque_number}) දැනටමත් '{$cheque->status}' තත්වයේ පවතින බැවින් සංශෝධනය (Edit) කිරීමට නොහැක. (Cheque #{$cheque->cheque_number} is {$cheque->status} - editing is locked).");
        }

        $validated = $request->validate([
            'type' => 'required|in:direct,normal',
            'category' => 'nullable|string|max:100',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,cheque',
            'bank_name' => 'nullable|string|max:255',
            'bank_id' => 'nullable|exists:banks,id',
            'cheque_no' => 'nullable|string|max:100',
            'order_id' => 'nullable|exists:orders,id',
            'event_id' => 'nullable|exists:events,id',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validated['type'] === 'direct' && !empty($validated['order_id'])) {
            $order = Order::find($validated['order_id']);
            if ($order && $order->event_id) {
                $validated['event_id'] = $order->event_id;
            }
        } elseif ($validated['type'] === 'normal') {
            $validated['order_id'] = null;
            $validated['event_id'] = null;
        }

        $oldExpenseValues = $expense->toArray();

        DB::transaction(function () use ($expense, $validated) {
            // STEP 1: REVERSE PREVIOUS LEDGER & BANK IMPACT
            if ($expense->payment_method === 'bank_transfer') {
                $oldBank = $expense->bank_id ? Bank::find($expense->bank_id) : null;
                if (!$oldBank && !empty($expense->bank_name)) {
                    foreach (Bank::all() as $b) {
                        if (str_contains($expense->bank_name, (string)$b->account_number) || str_contains($expense->bank_name, (string)$b->bank_name)) {
                            $oldBank = $b;
                            break;
                        }
                    }
                }
                if ($oldBank) {
                    $oldBank->increment('current_balance', $expense->amount);
                }
            }

            // Delete old transaction rows linked to this expense
            BankTransaction::where('expense_id', $expense->id)->delete();
            CashTransaction::where('expense_id', $expense->id)->delete();

            // Reverse and remove old cheques linked to this expense
            $oldCheques = Cheque::where('expense_id', $expense->id)->get();
            foreach ($oldCheques as $c) {
                if ($c->status === 'Cleared' && $c->bank_id) {
                    $cBank = Bank::find($c->bank_id);
                    if ($cBank) {
                        $cBank->increment('current_balance', $c->amount);
                    }
                }
                $c->delete();
            }

            // STEP 2: RESOLVE TARGET BANK FOR NEW DATA
            $targetBank = null;
            if (!empty($validated['bank_id'])) {
                $targetBank = Bank::find($validated['bank_id']);
            }
            if (!$targetBank && !empty($validated['bank_name'])) {
                foreach (Bank::where('is_active', true)->get() as $b) {
                    if (str_contains($validated['bank_name'], (string)$b->account_number) || str_contains($validated['bank_name'], (string)$b->bank_name)) {
                        $targetBank = $b;
                        break;
                    }
                }
                if (!$targetBank) {
                    $targetBank = Bank::where('bank_name', 'like', "%{$validated['bank_name']}%")->first();
                }
            }
            if ($targetBank) {
                $validated['bank_id'] = $targetBank->id;
            }

            // STEP 3: UPDATE EXPENSE RECORD
            $expense->update($validated);

            // STEP 4: CREATE REPLACEMENT LEDGER / BANK TRANSACTION
            if ($validated['payment_method'] === 'cheque') {
                $chequeNo = !empty($validated['cheque_no']) ? trim($validated['cheque_no']) : ('CHQ-' . str_pad($expense->id, 6, '0', STR_PAD_LEFT));

                Cheque::create([
                    'type' => 'OUT',
                    'cheque_number' => $chequeNo,
                    'cheque_date' => $validated['expense_date'],
                    'received_or_issued_date' => $validated['expense_date'],
                    'amount' => $validated['amount'],
                    'payee_name' => $validated['description'],
                    'expense_id' => $expense->id,
                    'bank_id' => $targetBank?->id,
                    'status' => 'Issued',
                    'notes' => $validated['notes'] ?: "Cheque issued for {$validated['type']} expense: {$validated['description']}",
                ]);
            } elseif ($validated['payment_method'] === 'cash') {
                CashTransaction::create([
                    'type' => 'expense',
                    'amount' => $validated['amount'],
                    'description' => ($validated['type'] === 'direct' ? 'Direct Event Expense: ' : 'Expense: ') . $validated['description'],
                    'reference_no' => $expense->expense_no,
                    'order_id' => $validated['order_id'] ?? null,
                    'expense_id' => $expense->id,
                    'transaction_date' => $validated['expense_date'],
                    'notes' => $validated['notes'] ?? null,
                ]);
            } else { // bank_transfer
                if ($targetBank) {
                    $targetBank->decrement('current_balance', $validated['amount']);
                }

                BankTransaction::create([
                    'type' => 'expense',
                    'transaction_method' => 'bank_transfer',
                    'account_type' => $validated['bank_name'] ?? 'bank',
                    'bank_id' => $targetBank?->id,
                    'order_id' => $validated['order_id'] ?? null,
                    'expense_id' => $expense->id,
                    'amount' => $validated['amount'],
                    'description' => ($validated['type'] === 'direct' ? 'Direct Event Expense: ' : 'Expense: ') . $validated['description'],
                    'reference_no' => $expense->expense_no,
                    'cheque_no' => null,
                    'bank_name' => $validated['bank_name'] ?? null,
                    'transaction_date' => $validated['expense_date'],
                    'running_balance' => $targetBank?->current_balance ?? 0,
                    'notes' => $validated['notes'] ?? null,
                ]);
            }
        });

        \App\Services\AuditLogger::log(
            'updated',
            'Expense',
            "Expense #{$expense->expense_no}",
            "Updated expense voucher #{$expense->expense_no} (Amount: Rs. " . number_format($expense->amount, 2) . ")",
            $expense->id,
            $oldExpenseValues,
            $expense->fresh()->toArray()
        );

        return redirect()->route('reports.expenses')->with('success', 'Expense updated and financial ledgers synced successfully.');
    }

    public function destroy(Expense $expense)
    {
        $hasCheque = ($expense->payment_method === 'cheque') || Cheque::where('expense_id', $expense->id)->exists();
        if ($hasCheque) {
            return redirect()->back()->with('error', "චෙක්පත් (Cheque) මගින් සිදුකරන ලද වියදම් Delete කිරීමට අවසර නැත. (Expenses made via Cheque cannot be deleted. Please manage or cancel the cheque in Cheques Management).");
        }

        DB::transaction(function () use ($expense) {
            $today = now()->format('Y-m-d');
            $customerId = $expense->order?->customer_id ?? $expense->event?->customer_id ?? null;

            // 1. If it was Bank Transfer:
            if ($expense->payment_method === 'bank_transfer') {
                $targetBank = $expense->bank_id ? Bank::find($expense->bank_id) : null;
                if (!$targetBank && !empty($expense->bank_name)) {
                    foreach (Bank::all() as $b) {
                        if (str_contains($expense->bank_name, (string)$b->account_number) || str_contains($expense->bank_name, (string)$b->bank_name)) {
                            $targetBank = $b;
                            break;
                        }
                    }
                }

                // Increment bank balance back
                if ($targetBank) {
                    $targetBank->increment('current_balance', $expense->amount);
                }

                // Keep original BankTransaction(s) intact, update their notes to indicate void/cancelled
                BankTransaction::where('expense_id', $expense->id)->update([
                    'notes' => DB::raw("CONCAT(COALESCE(notes, ''), ' [CANCELLED: Expense {$expense->expense_no} deleted]')")
                ]);

                // Create a REVERSAL entry in bank_transactions (type: income = Inflow/Refund)
                BankTransaction::create([
                    'type' => 'income',
                    'transaction_method' => 'bank_transfer',
                    'account_type' => $targetBank?->bank_name ?? 'bank',
                    'bank_id' => $targetBank?->id,
                    'customer_id' => $customerId,
                    'order_id' => $expense->order_id,
                    'expense_id' => null,
                    'amount' => $expense->amount,
                    'description' => "Reversal: Cancelled Expense #{$expense->expense_no} - {$expense->description}",
                    'reference_no' => 'REV-' . $expense->expense_no,
                    'cheque_no' => null,
                    'bank_name' => $targetBank?->bank_name ?? $expense->bank_name,
                    'transaction_date' => $today,
                    'running_balance' => $targetBank?->current_balance ?? 0,
                    'notes' => "Reversal entry for deleted expense voucher #{$expense->expense_no}",
                ]);
            }
            // 2. If it was Cash:
            elseif ($expense->payment_method === 'cash') {
                // Keep original CashTransaction(s) intact, update their notes
                CashTransaction::where('expense_id', $expense->id)->update([
                    'notes' => DB::raw("CONCAT(COALESCE(notes, ''), ' [CANCELLED: Expense {$expense->expense_no} deleted]')")
                ]);

                // Create a REVERSAL entry in cash_transactions (type: income = Cash Inflow/Refund)
                CashTransaction::create([
                    'type' => 'income',
                    'amount' => $expense->amount,
                    'description' => "Reversal: Cancelled Expense #{$expense->expense_no} - {$expense->description}",
                    'reference_no' => 'REV-' . $expense->expense_no,
                    'customer_id' => $customerId,
                    'order_id' => $expense->order_id,
                    'expense_id' => null,
                    'transaction_date' => $today,
                    'notes' => "Reversal entry for deleted cash expense voucher #{$expense->expense_no}",
                ]);
            }
            // 3. If it was Cheque:
            elseif ($expense->payment_method === 'cheque') {
                $cheques = Cheque::where('expense_id', $expense->id)->get();
                foreach ($cheques as $cheque) {
                    if ($cheque->status === 'Cleared' && $cheque->bank_id) {
                        $chequeBank = Bank::find($cheque->bank_id);
                        if ($chequeBank) {
                            $chequeBank->increment('current_balance', $cheque->amount);

                            // Create a bank reversal entry for the cleared cheque
                            BankTransaction::create([
                                'type' => 'income',
                                'transaction_method' => 'cheque',
                                'account_type' => $chequeBank->bank_name,
                                'bank_id' => $chequeBank->id,
                                'customer_id' => $customerId,
                                'order_id' => $expense->order_id,
                                'amount' => $cheque->amount,
                                'description' => "Reversal: Cancelled Cleared Cheque Expense #{$expense->expense_no} - {$expense->description}",
                                'reference_no' => 'REV-' . $expense->expense_no,
                                'cheque_no' => $cheque->cheque_number,
                                'bank_name' => $chequeBank->bank_name,
                                'transaction_date' => $today,
                                'running_balance' => $chequeBank->current_balance,
                                'notes' => "Reversal entry for cleared cheque #{$cheque->cheque_number} upon expense deletion",
                            ]);
                        }
                    }

                    // Mark the cheque as Cancelled
                    $cheque->update([
                        'status' => 'Cancelled',
                        'notes' => ($cheque->notes ? $cheque->notes . ' ' : '') . "[Cancelled on expense #{$expense->expense_no} deletion]",
                    ]);
                }
            }

            // 4. Delete the expense record itself
            $expenseTitle = "Expense #{$expense->expense_no} - Rs. " . number_format($expense->amount, 2);
            $oldValues = $expense->toArray();

            $expense->delete();

            \App\Services\AuditLogger::log(
                'deleted',
                'Expense',
                $expenseTitle,
                "Deleted expense voucher {$expenseTitle}",
                $expense->id,
                $oldValues,
                null
            );
        });

        return redirect()->back()->with('success', 'Expense deleted successfully and an official reversal entry has been added to the ledger.');
    }
}
