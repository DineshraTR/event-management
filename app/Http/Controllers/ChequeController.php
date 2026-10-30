<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use App\Models\Bank;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\BankTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChequeController extends Controller
{
    /**
     * Display Cheques Management dashboard and ledger
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'in');
        $search = $request->get('search');
        $statusFilter = $request->get('status');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $bankFilter = $request->get('bank_id');

        // KPI Summary Metrics Calculation
        // 1. Cheque IN KPIs
        $kpiInHand = [
            'cnt' => Cheque::where('type', 'IN')->where('status', 'In Hand')->count(),
            'total' => (float) Cheque::where('type', 'IN')->where('status', 'In Hand')->sum('amount'),
        ];
        $kpiDeposited = [
            'cnt' => Cheque::where('type', 'IN')->where('status', 'Deposited')->count(),
            'total' => (float) Cheque::where('type', 'IN')->where('status', 'Deposited')->sum('amount'),
        ];
        $kpiRealizedIn = [
            'cnt' => Cheque::where('type', 'IN')->where('status', 'Realized')->count(),
            'total' => (float) Cheque::where('type', 'IN')->where('status', 'Realized')->sum('amount'),
        ];
        $kpiReturnedIn = [
            'cnt' => Cheque::where('type', 'IN')->where('status', 'Returned')->count(),
            'total' => (float) Cheque::where('type', 'IN')->where('status', 'Returned')->sum('amount'),
        ];

        // 2. Cheque OUT KPIs
        $kpiIssuedOut = [
            'cnt' => Cheque::where('type', 'OUT')->where('status', 'Issued')->count(),
            'total' => (float) Cheque::where('type', 'OUT')->where('status', 'Issued')->sum('amount'),
        ];
        $kpiRealizedOut = [
            'cnt' => Cheque::where('type', 'OUT')->where('status', 'Realized')->count(),
            'total' => (float) Cheque::where('type', 'OUT')->where('status', 'Realized')->sum('amount'),
        ];
        $kpiReturnedOut = [
            'cnt' => Cheque::where('type', 'OUT')->where('status', 'Returned')->count(),
            'total' => (float) Cheque::where('type', 'OUT')->where('status', 'Returned')->sum('amount'),
        ];

        // Query Builder for Current Tab
        $query = Cheque::with(['customer', 'order.event', 'bank', 'payment', 'expense'])
            ->where('type', strtoupper($activeTab));

        if (!empty($search)) {
            $query->where(function ($q) use ($search, $activeTab) {
                $q->where('cheque_number', 'like', "%{$search}%")
                  ->orWhere('payer_bank', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");

                if ($activeTab === 'in') {
                    $q->orWhereHas('customer', function ($cQ) use ($search) {
                        $cQ->where('name', 'like', "%{$search}%")
                           ->orWhere('phone', 'like', "%{$search}%");
                    })->orWhereHas('order', function ($oQ) use ($search) {
                        $oQ->where('order_no', 'like', "%{$search}%");
                    });
                } else {
                    $q->orWhere('payee_name', 'like', "%{$search}%");
                }
            });
        }

        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        if (!empty($bankFilter)) {
            $query->where('bank_id', $bankFilter);
        }

        if (!empty($fromDate)) {
            $query->where('received_or_issued_date', '>=', $fromDate);
        }

        if (!empty($toDate)) {
            $query->where('received_or_issued_date', '<=', $toDate);
        }

        $cheques = $query->latest('id')->paginate(25)->withQueryString();

        // Support data for modals
        $banks = Bank::where('is_active', true)->orderBy('bank_name')->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $orders = Order::with('customer')->latest('id')->take(50)->get();
        $today = Carbon::today()->format('Y-m-d');

        return view('cheques.index', compact(
            'cheques',
            'activeTab',
            'search',
            'statusFilter',
            'fromDate',
            'toDate',
            'bankFilter',
            'kpiInHand',
            'kpiDeposited',
            'kpiRealizedIn',
            'kpiReturnedIn',
            'kpiIssuedOut',
            'kpiRealizedOut',
            'kpiReturnedOut',
            'banks',
            'customers',
            'orders',
            'today'
        ));
    }

    /**
     * Store Received Inward Cheque (Cheque In)
     */
    public function storeIn(Request $request)
    {
        $validated = $request->validate([
            'cheque_number' => 'required|string|max:100',
            'cheque_date' => 'required|date',
            'received_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'customer_id' => 'required|exists:customers,id',
            'order_id' => 'nullable|exists:orders,id',
            'payer_bank' => 'nullable|string|max:255',
            'payer_branch' => 'nullable|string|max:255',
            'status' => 'required|in:In Hand,Deposited',
            'deposit_bank_id' => 'nullable|required_if:status,Deposited|exists:banks,id',
            'deposit_date' => 'nullable|required_if:status,Deposited|date',
            'notes' => 'nullable|string',
            'create_payment' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $paymentId = null;

            // Optional creation of order payment link
            if (!empty($validated['order_id']) && $request->boolean('create_payment')) {
                $order = Order::findOrFail($validated['order_id']);
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'customer_id' => $validated['customer_id'],
                    'amount' => $validated['amount'],
                    'payment_type' => 'partial',
                    'payment_method' => 'cheque',
                    'status' => 'Paid',
                    'cheque_no' => $validated['cheque_number'],
                    'bank_name' => $validated['payer_bank'] ?? null,
                    'payment_date' => $validated['received_date'],
                    'notes' => $validated['notes'] ?: "Received Cheque #{$validated['cheque_number']}",
                ]);
                $paymentId = $payment->id;
            }

            Cheque::create([
                'type' => 'IN',
                'cheque_number' => trim($validated['cheque_number']),
                'cheque_date' => $validated['cheque_date'],
                'received_or_issued_date' => $validated['received_date'],
                'amount' => $validated['amount'],
                'payer_bank' => $validated['payer_bank'] ?? null,
                'payer_branch' => $validated['payer_branch'] ?? null,
                'customer_id' => $validated['customer_id'],
                'order_id' => $validated['order_id'] ?? null,
                'payment_id' => $paymentId,
                'bank_id' => ($validated['status'] === 'Deposited') ? ($validated['deposit_bank_id'] ?? null) : null,
                'deposit_date' => ($validated['status'] === 'Deposited') ? ($validated['deposit_date'] ?? null) : null,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // NOTE: Bank balance is NOT touched and NO BankTransaction is created!
            // Balance is ONLY updated when explicitly Realized in Cheques Management.
        });

        return redirect()->route('cheques.index', ['tab' => 'in'])
            ->with('success', "Received Cheque #{$validated['cheque_number']} recorded successfully ({$validated['status']}).");
    }

    /**
     * Store Issued Outward Cheque (Cheque Out)
     */
    public function storeOut(Request $request)
    {
        $validated = $request->validate([
            'cheque_number' => 'required|string|max:100',
            'cheque_date' => 'required|date',
            'issue_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payee_name' => 'required|string|max:255',
            'bank_id' => 'required|exists:banks,id',
            'expense_id' => 'nullable|exists:expenses,id',
            'notes' => 'nullable|string',
        ]);

        Cheque::create([
            'type' => 'OUT',
            'cheque_number' => trim($validated['cheque_number']),
            'cheque_date' => $validated['cheque_date'],
            'received_or_issued_date' => $validated['issue_date'],
            'amount' => $validated['amount'],
            'payee_name' => trim($validated['payee_name']),
            'bank_id' => $validated['bank_id'],
            'expense_id' => $validated['expense_id'] ?? null,
            'status' => 'Issued',
            'notes' => $validated['notes'] ?? null,
        ]);

        // NOTE: Bank balance is NOT deducted yet. It will only be deducted when Realized / Cleared.
        return redirect()->route('cheques.index', ['tab' => 'out'])
            ->with('success', "Issued Cheque #{$validated['cheque_number']} to {$validated['payee_name']} recorded successfully (Issued).");
    }

    /**
     * Deposit Cheque: In Hand -> Deposited (Sent to Bank for Clearing)
     * Still does NOT touch bank balance (funds are in clearance pipeline).
     */
    public function deposit(Request $request, Cheque $cheque)
    {
        if ($cheque->type !== 'IN') {
            return redirect()->back()->with('error', 'Only inward cheques can be marked as Deposited.');
        }

        $validated = $request->validate([
            'deposit_bank_id' => 'required|exists:banks,id',
            'deposit_date' => 'required|date',
        ]);

        $cheque->update([
            'status' => 'Deposited',
            'bank_id' => $validated['deposit_bank_id'],
            'deposit_date' => $validated['deposit_date'],
        ]);

        return redirect()->route('cheques.index', ['tab' => 'in'])
            ->with('success', "Cheque #{$cheque->cheque_number} has been marked as Deposited in bank clearing pipeline.");
    }

    /**
     * Realize Cheque: Mark Realized / Cleared
     * HERE AND ONLY HERE the Bank Balance is actually updated!
     * Credit bank for IN, Debit bank for OUT, and create a ledger BankTransaction entry.
     */
    public function realize(Request $request, Cheque $cheque)
    {
        $validated = $request->validate([
            'clearance_date' => 'required|date',
            'bank_id' => 'nullable|exists:banks,id',
        ]);

        if ($cheque->status === 'Realized') {
            return redirect()->back()->with('error', "Cheque #{$cheque->cheque_number} is already marked as Realized.");
        }

        DB::transaction(function () use ($validated, $cheque) {
            $clearanceDate = $validated['clearance_date'];
            $targetBankId = $validated['bank_id'] ?: ($cheque->bank_id ?: Bank::where('is_active', true)->value('id'));

            if (!$targetBankId) {
                throw new \Exception("Please select a valid Company Bank Account.");
            }

            $bank = Bank::lockForUpdate()->findOrFail($targetBankId);

            if ($cheque->type === 'IN') {
                // 1. Credit the bank current_balance
                $bank->increment('current_balance', $cheque->amount);

                // 2. Create BankTransaction credit entry
                BankTransaction::create([
                    'type' => 'income',
                    'transaction_method' => 'cheque',
                    'account_type' => $bank->bank_name,
                    'bank_id' => $bank->id,
                    'customer_id' => $cheque->customer_id,
                    'order_id' => $cheque->order_id,
                    'payment_id' => $cheque->payment_id,
                    'cheque_id' => $cheque->id,
                    'bank_name' => $bank->bank_name,
                    'amount' => $cheque->amount,
                    'reference_no' => 'CHQ-IN-' . $cheque->cheque_number,
                    'cheque_no' => $cheque->cheque_number,
                    'transaction_date' => $clearanceDate,
                    'description' => "Realized Cheque #{$cheque->cheque_number} from " . ($cheque->customer?->name ?? 'Customer') . ($cheque->order ? " (Order: {$cheque->order->order_no})" : ''),
                    'running_balance' => $bank->current_balance,
                ]);

                // 3. Update Cheque status
                $cheque->update([
                    'status' => 'Realized',
                    'clearance_date' => $clearanceDate,
                    'bank_id' => $targetBankId,
                ]);
            } else { // OUT
                // 1. Debit the bank current_balance
                $bank->decrement('current_balance', $cheque->amount);

                // 2. Create BankTransaction debit entry
                BankTransaction::create([
                    'type' => 'expense',
                    'transaction_method' => 'cheque',
                    'account_type' => $bank->bank_name,
                    'bank_id' => $bank->id,
                    'order_id' => $cheque->order_id,
                    'expense_id' => $cheque->expense_id,
                    'cheque_id' => $cheque->id,
                    'bank_name' => $bank->bank_name,
                    'amount' => $cheque->amount,
                    'reference_no' => 'CHQ-OUT-' . $cheque->cheque_number,
                    'cheque_no' => $cheque->cheque_number,
                    'transaction_date' => $clearanceDate,
                    'description' => "Cleared Issued Cheque #{$cheque->cheque_number} for " . ($cheque->payee_name ?? 'Payee') . ($cheque->expense ? " (EXP: {$cheque->expense->expense_no})" : ''),
                    'running_balance' => $bank->current_balance,
                ]);

                // 3. Update Cheque status
                $cheque->update([
                    'status' => 'Realized',
                    'clearance_date' => $clearanceDate,
                    'bank_id' => $targetBankId,
                ]);
            }
        });

        $tab = strtolower($cheque->type);
        return redirect()->route('cheques.index', ['tab' => $tab])
            ->with('success', "Cheque #{$cheque->cheque_number} marked as Realized / Cleared successfully! Bank balance updated.");
    }

    /**
     * Return Cheque: Mark Bounced / Returned
     * If previously Realized, reverses the bank balance and logs reversal BankTransaction.
     * If linked to an Order payment, marks the payment as Returned and restores the Order balance!
     */
    public function returnCheque(Request $request, Cheque $cheque)
    {
        $validated = $request->validate([
            'return_date' => 'required|date',
            'return_reason' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $cheque) {
            $previousStatus = $cheque->status;
            $returnDate = $validated['return_date'];
            $returnReason = $validated['return_reason'];

            // 1. Reverse bank funds ONLY IF the cheque was actually Realized into bank before!
            if ($previousStatus === 'Realized' && $cheque->bank_id) {
                $bank = Bank::lockForUpdate()->find($cheque->bank_id);
                if ($bank) {
                    if ($cheque->type === 'IN') {
                        // Was credited before; now deduct it
                        $bank->decrement('current_balance', $cheque->amount);
                        BankTransaction::create([
                            'type' => 'expense',
                            'transaction_method' => 'cheque',
                            'account_type' => $bank->bank_name,
                            'bank_name' => $bank->bank_name,
                            'amount' => $cheque->amount,
                            'reference_no' => 'CHQ-RET-' . $cheque->cheque_number,
                            'cheque_no' => $cheque->cheque_number,
                            'transaction_date' => $returnDate,
                            'description' => "Cheque Return Reversal: #{$cheque->cheque_number} - {$returnReason}",
                            'running_balance' => $bank->current_balance,
                        ]);
                    } else { // OUT
                        // Was debited before; now restore it
                        $bank->increment('current_balance', $cheque->amount);
                        BankTransaction::create([
                            'type' => 'income',
                            'transaction_method' => 'cheque',
                            'account_type' => $bank->bank_name,
                            'bank_name' => $bank->bank_name,
                            'amount' => $cheque->amount,
                            'reference_no' => 'CHQ-RET-' . $cheque->cheque_number,
                            'cheque_no' => $cheque->cheque_number,
                            'transaction_date' => $returnDate,
                            'description' => "Cheque Return Reversal: #{$cheque->cheque_number} - {$returnReason}",
                            'running_balance' => $bank->current_balance,
                        ]);
                    }
                }
            }

            // 2. If Cheque IN is linked to an order payment, update payment status to Returned
            // This immediately causes Order::recalculateTotals() to restore the due balance for customer!
            if ($cheque->type === 'IN' && $cheque->payment_id) {
                $payment = Payment::find($cheque->payment_id);
                if ($payment) {
                    $noteAppend = " [CHEQUE RETURNED on {$returnDate}: {$returnReason}]";
                    $payment->update([
                        'status' => 'Returned',
                        'notes' => trim(($payment->notes ?? '') . $noteAppend),
                    ]);
                    $payment->order?->recalculateTotals();
                }
            }

            // 3. Update Cheque record to Returned
            $cheque->update([
                'status' => 'Returned',
                'return_date' => $returnDate,
                'return_reason' => $returnReason,
            ]);
        });

        $tab = strtolower($cheque->type);
        return redirect()->route('cheques.index', ['tab' => $tab])
            ->with('success', "Cheque #{$cheque->cheque_number} marked as Returned. Any ledger and customer due balances have been accurately restored.");
    }

    /**
     * Destroy a Cheque record
     */
    public function destroy(Cheque $cheque)
    {
        if ($cheque->status === 'Realized') {
            return redirect()->back()->with('error', 'Cannot delete a Realized cheque directly. Please mark it as Returned or Cancelled first to ensure ledger consistency.');
        }

        $tab = strtolower($cheque->type);
        $cheque->delete();

        return redirect()->route('cheques.index', ['tab' => $tab])
            ->with('success', 'Cheque record removed successfully.');
    }
}
