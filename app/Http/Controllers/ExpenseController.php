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

        DB::transaction(function () use ($validated) {
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
        });

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
            'name' => 'required|string|max:100|unique:expense_categories,name',
            'description' => 'nullable|string|max:255',
        ]);

        $category = ExpenseCategory::create([
            'name' => trim($validated['name']),
            'description' => $validated['description'] ?? null,
            'status' => 'active',
        ]);

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
        $categories = ExpenseCategory::where('status', 'active')->orderBy('name')->get();
        $banks = Bank::where('is_active', true)->orderBy('bank_name')->get();
        $orders = Order::with(['customer', 'event'])->latest('id')->get();
        $events = Event::orderBy('event_name')->get();

        return view('expenses.edit', compact('expense', 'categories', 'banks', 'orders', 'events'));
    }

    public function update(Request $request, Expense $expense)
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

        if ($validated['type'] === 'direct' && !empty($validated['order_id'])) {
            $order = Order::find($validated['order_id']);
            if ($order && $order->event_id) {
                $validated['event_id'] = $order->event_id;
            }
        }

        $expense->update($validated);
        return redirect()->route('reports.expenses')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->back()->with('success', 'Expense deleted successfully.');
    }
}
