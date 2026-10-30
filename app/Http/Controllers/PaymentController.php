<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Bank;
use App\Models\BankTransaction;
use App\Models\CashTransaction;
use App\Models\Cheque;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        return $this->create($request);
    }

    public function create(Request $request)
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $orders = Order::with(['customer', 'event', 'payments'])->latest()->get();
        $banks = Bank::where('is_active', true)->orderBy('bank_name')->get();
        $selectedOrder = null;
        $selectedCustomer = null;

        if ($request->order_id) {
            $selectedOrder = Order::with(['customer', 'event', 'payments'])->find($request->order_id);
            $selectedCustomer = $selectedOrder?->customer;
        }

        $today = Carbon::today()->format('Y-m-d');
        return view('payments.create', compact('customers', 'orders', 'banks', 'selectedOrder', 'selectedCustomer', 'today'));
    }

    public function createForOrder($orderId)
    {
        return redirect()->route('payments.index', ['order_id' => $orderId]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_type' => 'required|in:advance,partial,full',
            'payment_method' => 'required|in:cash,cheque,bank_transfer',
            'cheque_no' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $payment = null;
        DB::transaction(function () use ($validated, &$payment) {
            $payment = Payment::create($validated);

            if ($validated['payment_method'] === 'cheque') {
                // Cheque payment: Create Cheque record with status 'In Hand'
                // NOTE: Bank balance is NOT updated and BankTransaction is NOT created yet!
                // Balance and BankTransaction are ONLY created when realized in Cheque Management.
                $chequeNo = !empty($validated['cheque_no']) ? trim($validated['cheque_no']) : ('CHQ-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT));
                Cheque::create([
                    'type' => 'IN',
                    'cheque_number' => $chequeNo,
                    'cheque_date' => $validated['payment_date'],
                    'received_or_issued_date' => $validated['payment_date'],
                    'amount' => $validated['amount'],
                    'payer_bank' => $validated['bank_name'] ?? null,
                    'customer_id' => $validated['customer_id'],
                    'order_id' => $validated['order_id'],
                    'payment_id' => $payment->id,
                    'status' => 'In Hand',
                    'notes' => $validated['notes'] ?: "Customer Cheque for Order #{$payment->order->order_no}",
                ]);
            } else {
                // Cash or Bank Transfer: record in respective transactions table
                if ($validated['payment_method'] === 'cash') {
                    CashTransaction::create([
                        'type' => 'income',
                        'amount' => $validated['amount'],
                        'description' => 'Payment received - Order: ' . $payment->order->order_no,
                        'transaction_date' => $validated['payment_date'],
                        'reference_no' => $payment->payment_no,
                        'customer_id' => $validated['customer_id'],
                        'order_id' => $validated['order_id'],
                        'payment_id' => $payment->id,
                        'notes' => $validated['notes'] ?? null,
                    ]);
                } else {
                    $accountType = $validated['bank_name'] ?? 'bank';
                    $targetBank = null;
                    if ($validated['payment_method'] === 'bank_transfer' && !empty($validated['bank_name'])) {
                        $targetBank = Bank::where('bank_name', 'like', "%{$validated['bank_name']}%")->first();
                        if ($targetBank) {
                            $targetBank->increment('current_balance', $validated['amount']);
                        }
                    }

                    BankTransaction::create([
                        'type' => 'income',
                        'transaction_method' => $validated['payment_method'],
                        'account_type' => $accountType,
                        'bank_id' => $targetBank?->id,
                        'customer_id' => $validated['customer_id'],
                        'order_id' => $validated['order_id'],
                        'payment_id' => $payment->id,
                        'amount' => $validated['amount'],
                        'description' => 'Payment received - Order: ' . $payment->order->order_no,
                        'cheque_no' => null,
                        'bank_name' => $validated['bank_name'] ?? null,
                        'transaction_date' => $validated['payment_date'],
                        'reference_no' => $payment->payment_no,
                        'running_balance' => $targetBank?->current_balance ?? 0,
                        'notes' => $validated['notes'] ?? null,
                    ]);
                }
            }
        });

        $successMsg = ($validated['payment_method'] === 'cheque') 
            ? 'Payment recorded and Cheque saved to Cheque Management (In Hand).' 
            : 'Payment recorded successfully!';

        return redirect()->route('orders.print', ['order' => $validated['order_id'], 'auto_print' => 1])
            ->with('success', $successMsg . ' Invoice / Receipt ready to print.');
    }

    public function printReceipt(Payment $payment)
    {
        $order = $payment->order;
        $order->load([
            'customer',
            'event.subCategory.category',
            'event.subCategory.subCategoryServices.service',
            'services.service',
            'otherCharges',
            'payments',
        ]);

        return view('orders.print', [
            'order' => $order,
            'highlightPaymentId' => $payment->id
        ]);
    }

    public function show(Payment $payment)
    {
        $payment->load(['customer', 'order.event']);
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        return view('payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_type' => 'required|in:advance,partial,full',
            'payment_method' => 'required|in:cash,cheque,bank_transfer',
            'cheque_no' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        $payment->update($validated);
        $payment->order->recalculateTotals();
        return redirect()->route('payments.index')->with('success', 'Payment updated successfully!');
    }

    public function destroy(Payment $payment)
    {
        $order = $payment->order;
        $payment->delete();
        $order->recalculateTotals();
        return redirect()->route('payments.index')->with('success', 'Payment deleted.');
    }
}
