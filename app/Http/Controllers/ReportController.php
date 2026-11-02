<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Customer;
use App\Models\Event;
use App\Models\Bank;
use App\Models\BankTransaction;
use App\Models\CashTransaction;
use App\Models\Cheque;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function orderPL(Request $request)
    {
        $orderId = $request->get('order_id');
        $customerId = $request->get('customer_id');
        $orderNo = $request->get('order_no');
        $eventName = $request->get('event_name') ?: $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // All Customers for customer dropdown filter
        $allCustomers = Customer::orderBy('name')->get(['id', 'name', 'phone']);

        // Quick list of all orders for the selectable order dropdown
        $allOrdersQuickList = Order::with(['customer', 'event'])
            ->latest('order_date')
            ->get()
            ->map(function ($o) {
                return [
                    'id' => $o->id,
                    'order_no' => $o->order_no,
                    'customer_id' => $o->customer_id,
                    'customer_name' => $o->customer?->name ?? 'Walk-in Customer',
                    'event_name' => $o->event?->event_name ?? 'Direct Order',
                ];
            });

        // If a specific order is selected to view its formal statement
        $singleOrder = null;
        $singleOrderData = null;
        if ($orderId) {
            $singleOrder = Order::with([
                'customer',
                'event.subCategory.category',
                'event.additionalServices',
                'event.otherCharges',
                'event.expenses',
                'services.service',
                'otherCharges',
                'payments',
                'expenses',
            ])->find($orderId);

            if ($singleOrder) {
                $rev = (float) $singleOrder->total_amount;
                $paid = (float) $singleOrder->paid_amount;
                $due = max(0, $rev - $paid);

                // Package direct cost from event if available
                $pkgCost = $singleOrder->event ? (float) $singleOrder->event->package_cost_price : 0;

                // Direct expense records linked to this order or its event
                $directExpenses = Expense::where(function ($q) use ($singleOrder) {
                    $q->where('order_id', $singleOrder->id);
                    if ($singleOrder->event_id) {
                        $q->orWhere('event_id', $singleOrder->event_id);
                    }
                })->orderBy('expense_date')->get();

                $totalDirectVouchers = (float) $directExpenses->sum('amount');
                $totalDirectCost = $pkgCost + $totalDirectVouchers;
                $netProfitOrLoss = $rev - $totalDirectCost;
                $isProfit = $netProfitOrLoss >= 0;
                $profitMarginPct = $rev > 0 ? round(($netProfitOrLoss / $rev) * 100, 1) : 0;
                $cashProfitOrLoss = $paid - $totalDirectCost;

                $singleOrderData = [
                    'order' => $singleOrder,
                    'revenue' => $rev,
                    'paid' => $paid,
                    'due' => $due,
                    'package_cost' => $pkgCost,
                    'direct_expenses' => $directExpenses,
                    'total_direct_vouchers' => $totalDirectVouchers,
                    'total_direct_cost' => $totalDirectCost,
                    'net_profit_loss' => $netProfitOrLoss,
                    'is_profit' => $isProfit,
                    'profit_margin' => $profitMarginPct,
                    'cash_profit_loss' => $cashProfitOrLoss,
                ];
            }
        }

        // Default date range for overview table if not searching specific order and no range specified
        if (!$singleOrder && !$dateFrom && !$dateTo && !$orderNo && !$eventName && !$customerId) {
            $dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
            $dateTo = Carbon::today()->format('Y-m-d');
        }

        // Build query for overview list
        $query = Order::with(['event.additionalServices', 'event.subCategory', 'event.expenses', 'services', 'payments', 'customer', 'expenses']);

        if ($dateFrom) {
            $query->whereDate('order_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('order_date', '<=', $dateTo);
        }
        if ($customerId) {
            $query->where('customer_id', $customerId);
        }
        if ($orderNo) {
            $query->where('order_no', 'like', "%{$orderNo}%");
        }
        if ($eventName) {
            $query->where(function ($q) use ($eventName) {
                $q->whereHas('event', fn($e) => $e->where('event_name', 'like', "%{$eventName}%"))
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$eventName}%"));
            });
        }

        $ordersCollection = $query->latest('order_date')->latest('id')->get();

        $orders = $ordersCollection->map(function ($order) {
            $revenue = (float) $order->total_amount;
            $packageCost = $order->event ? (float) $order->event->package_cost_price : 0;

            // Direct expenses
            $allDirectExpenses = (float) Expense::where(function ($q) use ($order) {
                $q->where('order_id', $order->id);
                if ($order->event_id) {
                    $q->orWhere('event_id', $order->event_id);
                }
            })->sum('amount');

            if ($allDirectExpenses <= 0 && $order->event) {
                $allDirectExpenses = (float) $order->event->expenses()->sum('amount');
            }

            $totalCost = $packageCost + $allDirectExpenses;
            $profit = $revenue - $totalCost;
            $margin = $revenue > 0 ? round(($profit / $revenue) * 100, 1) : 0;
            $paid = (float) $order->paid_amount;
            $due = max(0, $revenue - $paid);

            return [
                'order' => $order,
                'sub_total' => (float) $order->sub_total,
                'discount' => (float) $order->discount,
                'revenue' => $revenue,
                'paid' => $paid,
                'due' => $due,
                'cost' => $totalCost,
                'profit' => $profit,
                'margin' => $margin,
                'is_profit' => $profit >= 0,
            ];
        });

        $totalSubTotal = $orders->sum('sub_total');
        $totalDiscount = $orders->sum('discount');
        $totalRevenue = $orders->sum('revenue');
        $totalPaid = $orders->sum('paid');
        $totalDue = $orders->sum('due');
        $totalCost = $orders->sum('cost');
        $totalProfit = $orders->sum('profit');

        return view('reports.order-pl', compact(
            'orders',
            'totalSubTotal',
            'totalDiscount',
            'totalRevenue',
            'totalPaid',
            'totalDue',
            'totalCost',
            'totalProfit',
            'dateFrom',
            'dateTo',
            'customerId',
            'orderNo',
            'eventName',
            'allCustomers',
            'allOrdersQuickList',
            'singleOrder',
            'singleOrderData'
        ));
    }

    public function monthlyPL(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $year = (int) $request->get('year', Carbon::now()->year);

        // If specific start and end dates not given, default to current month
        if (!$startDate && !$endDate) {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::today()->format('Y-m-d');
        } elseif ($startDate && !$endDate) {
            $endDate = Carbon::today()->format('Y-m-d');
        } elseif (!$startDate && $endDate) {
            $startDate = Carbon::parse($endDate)->startOfMonth()->format('Y-m-d');
        }

        $periodLabel = Carbon::parse($startDate)->format('d M Y') . ' to ' . Carbon::parse($endDate)->format('d M Y');

        // 1. Orders registered in this period
        $orders = Order::with(['customer', 'event.subCategory'])
            ->whereBetween('order_date', [$startDate, $endDate])
            ->orderBy('order_date')
            ->get();
        $totalOrdersCount = $orders->count();
        $totalOrderValuation = (float) $orders->sum('total_amount');

        // 2. Payments collected in this period
        $payments = Payment::with(['customer', 'order.event'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', '!=', 'Returned');
            })
            ->orderBy('payment_date')
            ->get();
        $totalPaymentsReceived = (float) $payments->sum('amount');
        $totalPaymentsCount = $payments->count();

        // Total Period Revenue
        $totalRevenue = $totalOrderValuation > 0 ? $totalOrderValuation : $totalPaymentsReceived;

        // 3. Direct Costs (Cost of Sales):
        // Package base costs from events registered in period
        $packageCostsTotal = (float) $orders->sum(fn($o) => $o->event?->package_cost_price ?? 0);

        // Direct expense vouchers recorded in this period
        $directExpenses = Expense::where(function ($q) {
                $q->where('type', 'direct')->orWhereNotNull('order_id')->orWhereNotNull('event_id');
            })
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date')
            ->get();
        $totalDirectVouchers = (float) $directExpenses->sum('amount');
        $totalDirectCost = $packageCostsTotal + $totalDirectVouchers;

        // Gross Profit (Total Revenue - Total Direct Cost)
        $grossProfit = $totalRevenue - $totalDirectCost;
        $isGrossProfit = ($grossProfit >= 0);
        $grossProfitMargin = ($totalRevenue > 0) ? round(($grossProfit / $totalRevenue) * 100, 1) : 0.0;

        // 4. Operating / Normal Expenses (Overheads):
        $operatingExpenses = Expense::where('type', 'normal')
            ->whereNull('order_id')
            ->whereNull('event_id')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date')
            ->get();
        $totalOperatingCost = (float) $operatingExpenses->sum('amount');

        // Net Profit or Loss (Gross Profit - Operating Expenses)
        $netProfitOrLoss = $grossProfit - $totalOperatingCost;
        $isNetProfit = ($netProfitOrLoss >= 0);
        $netProfitMargin = ($totalRevenue > 0) ? round(($netProfitOrLoss / $totalRevenue) * 100, 1) : 0.0;

        // 5. 12-Month Comparative Overview for the Selected Year
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $mStart = Carbon::createFromDate($year, $m, 1)->startOfMonth()->format('Y-m-d');
            $mEnd = Carbon::createFromDate($year, $m, 1)->endOfMonth()->format('Y-m-d');

            $mOrderRev = (float) Order::whereBetween('order_date', [$mStart, $mEnd])->sum('total_amount');
            $mPayRev = (float) Payment::whereBetween('payment_date', [$mStart, $mEnd])->sum('amount');
            $mRev = $mOrderRev > 0 ? $mOrderRev : $mPayRev;

            $mPkgCost = (float) Order::whereBetween('order_date', [$mStart, $mEnd])
                ->with('event')
                ->get()
                ->sum(fn($o) => $o->event?->package_cost_price ?? 0);

            $mDirExp = (float) Expense::where(function ($q) {
                    $q->where('type', 'direct')->orWhereNotNull('order_id')->orWhereNotNull('event_id');
                })
                ->whereBetween('expense_date', [$mStart, $mEnd])
                ->sum('amount');
            $mDirectTotal = $mPkgCost + $mDirExp;

            $mNormExp = (float) Expense::where('type', 'normal')
                ->whereNull('order_id')
                ->whereNull('event_id')
                ->whereBetween('expense_date', [$mStart, $mEnd])
                ->sum('amount');

            $mTotalExp = $mDirectTotal + $mNormExp;
            $mProfit = $mRev - $mTotalExp;

            $months[] = [
                'month' => Carbon::createFromDate($year, $m, 1)->format('F'),
                'month_num' => $m,
                'revenue' => round($mRev, 2),
                'direct_cost' => round($mDirectTotal, 2),
                'operating_cost' => round($mNormExp, 2),
                'expenses' => round($mTotalExp, 2),
                'profit' => round($mProfit, 2),
                'margin' => $mRev > 0 ? round(($mProfit / $mRev) * 100, 1) : 0,
            ];
        }

        $yearTotalRevenue = array_sum(array_column($months, 'revenue'));
        $yearTotalExpenses = array_sum(array_column($months, 'expenses'));
        $yearTotalProfit = $yearTotalRevenue - $yearTotalExpenses;

        return view('reports.monthly-pl', compact(
            'startDate',
            'endDate',
            'periodLabel',
            'year',
            'orders',
            'totalOrdersCount',
            'totalOrderValuation',
            'payments',
            'totalPaymentsReceived',
            'totalPaymentsCount',
            'totalRevenue',
            'packageCostsTotal',
            'directExpenses',
            'totalDirectVouchers',
            'totalDirectCost',
            'grossProfit',
            'isGrossProfit',
            'grossProfitMargin',
            'operatingExpenses',
            'totalOperatingCost',
            'netProfitOrLoss',
            'isNetProfit',
            'netProfitMargin',
            'months',
            'yearTotalRevenue',
            'yearTotalExpenses',
            'yearTotalProfit'
        ));
    }

    public function customerCredit(Request $request)
    {
        $search = $request->get('search');

        $customers = Customer::with(['orders', 'payments'])
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->get()
            ->map(function ($customer) {
                $totalBilled = $customer->orders->sum('total_amount');
                $totalPaid = $customer->payments->sum('amount');
                $balance = $totalBilled - $totalPaid;
                return [
                    'customer' => $customer,
                    'total_billed' => $totalBilled,
                    'total_paid' => $totalPaid,
                    'balance' => $balance,
                    'status' => $balance <= 0 ? 'cleared' : 'outstanding',
                ];
            })
            ->filter(fn($c) => $c['total_billed'] > 0);

        $page = $request->get('page', 1);
        $perPage = 25;
        $paginatedCustomers = new \Illuminate\Pagination\LengthAwarePaginator(
            $customers->forPage($page, $perPage),
            $customers->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('reports.customer-credit', ['customers' => $paginatedCustomers, 'search' => $search]);
    }

    public function orderHistory(Request $request)
    {
        $search = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $status = $request->get('status');
        $customerId = $request->get('customer_id');

        $orders = Order::with(['customer', 'event.subCategory.category', 'payments'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('order_no', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"))
                        ->orWhereHas('event', fn($e) => $e->where('event_name', 'like', "%{$search}%")->orWhere('event_no', 'like', "%{$search}%"));
                });
            })
            ->when($dateFrom, fn($q) => $q->where('order_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->where('order_date', '<=', $dateTo))
            ->when($status, fn($q) => $q->where('payment_status', $status))
            ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
            ->latest('order_date')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $customers = Customer::orderBy('name')->get(['id', 'name']);

        return view('reports.order-history', compact('orders', 'customers', 'search', 'dateFrom', 'dateTo', 'status', 'customerId'));
    }

    public function paymentsReport(Request $request)
    {
        $search = $request->get('search');
        $customerId = $request->get('customer_id');
        $paymentMethod = $request->get('payment_method');
        $paymentType = $request->get('payment_type');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = Payment::with(['customer', 'order.event'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('payment_no', 'like', "%{$search}%")
                        ->orWhere('cheque_no', 'like', "%{$search}%")
                        ->orWhere('bank_name', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"))
                        ->orWhereHas('order', fn($o) => $o->where('order_no', 'like', "%{$search}%"));
                });
            })
            ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
            ->when($paymentMethod, fn($q) => $q->where('payment_method', $paymentMethod))
            ->when($paymentType, fn($q) => $q->where('payment_type', $paymentType))
            ->when($dateFrom, fn($q) => $q->whereDate('payment_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('payment_date', '<=', $dateTo));

        // Summary KPI statistics based on current filtered query
        $totalCollected = (clone $query)->sum('amount');
        $cashTotal = (clone $query)->where('payment_method', 'cash')->sum('amount');
        $bankTotal = (clone $query)->where('payment_method', 'bank_transfer')->sum('amount');
        $chequeTotal = (clone $query)->where('payment_method', 'cheque')->sum('amount');
        $totalReceipts = (clone $query)->count();

        $payments = $query->latest('payment_date')->latest('id')->paginate(25)->withQueryString();

        $customers = Customer::orderBy('name')->get(['id', 'name', 'phone']);

        return view('reports.payments', compact(
            'payments',
            'customers',
            'search',
            'customerId',
            'paymentMethod',
            'paymentType',
            'dateFrom',
            'dateTo',
            'totalCollected',
            'cashTotal',
            'bankTotal',
            'chequeTotal',
            'totalReceipts'
        ));
    }

    public function expensesReport(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('category');
        $customerId = $request->get('customer_id');
        $paymentMethod = $request->get('payment_method');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $activeTab = $request->get('tab', 'direct'); // 'direct' or 'normal'

        // Base Query
        $baseQuery = Expense::with(['event', 'order.customer', 'cheque'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('expense_no', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('reference', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%")
                        ->orWhereHas('event', fn($e) => $e->where('event_name', 'like', "%{$search}%"))
                        ->orWhereHas('order.customer', fn($c) => $c->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($category, fn($q) => $q->where('category', $category))
            ->when($paymentMethod, fn($q) => $q->where('payment_method', $paymentMethod))
            ->when($dateFrom, fn($q) => $q->whereDate('expense_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('expense_date', '<=', $dateTo));

        // Summary Statistics
        $totalAmount = (clone $baseQuery)->sum('amount');
        $directTotal = (clone $baseQuery)->where('type', 'direct')->sum('amount');
        $normalTotal = (clone $baseQuery)->where('type', 'normal')->sum('amount');
        $monthTotal = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        // Direct Expenses Query (Filtered by customer if provided)
        $directQuery = (clone $baseQuery)->where('type', 'direct')
            ->when($customerId, function ($q) use ($customerId) {
                $q->where(function ($sub) use ($customerId) {
                    $sub->whereHas('order', fn($o) => $o->where('customer_id', $customerId))
                        ->orWhereHas('event', fn($e) => $e->where('customer_id', $customerId));
                });
            });

        $directExpenses = $directQuery->latest('expense_date')->latest('id')->paginate(20, ['*'], 'direct_page')->withQueryString();
        $directCount = (clone $directQuery)->count();

        // Normal Expenses Query
        $normalQuery = (clone $baseQuery)->where('type', 'normal');
        $normalExpenses = $normalQuery->latest('expense_date')->latest('id')->paginate(20, ['*'], 'normal_page')->withQueryString();
        $normalCount = (clone $normalQuery)->count();

        // Filter Dropdowns
        $categories = Expense::whereNotNull('category')->where('category', '!=', '')->distinct()->pluck('category');
        $customers = Customer::orderBy('name')->get(['id', 'name', 'phone']);
        $banks = Bank::where('is_active', true)->orderBy('bank_name')->get();
        $orders = Order::with(['customer', 'event'])->latest('id')->get();

        return view('reports.expenses', compact(
            'directExpenses',
            'normalExpenses',
            'categories',
            'customers',
            'banks',
            'orders',
            'search',
            'category',
            'customerId',
            'paymentMethod',
            'dateFrom',
            'dateTo',
            'activeTab',
            'totalAmount',
            'directTotal',
            'normalTotal',
            'monthTotal',
            'directCount',
            'normalCount'
        ));
    }

    public function transactionsReport(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::today()->format('Y-m-d'));
        $type = $request->get('type', 'all');
        $method = $request->get('method', 'all');
        $bankFilter = $request->get('bank');
        $search = $request->get('search');

        // Cash Query Base
        $cashBase = CashTransaction::query()
            ->when($dateFrom, fn($q) => $q->whereDate('transaction_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('transaction_date', '<=', $dateTo));

        // Bank Query Base
        $bankBase = BankTransaction::query()
            ->when($dateFrom, fn($q) => $q->whereDate('transaction_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('transaction_date', '<=', $dateTo));

        // Overall Period KPI Statistics
        $cashInTotal = (float) (clone $cashBase)->where('type', 'income')->sum('amount');
        $cashOutTotal = (float) (clone $cashBase)->where('type', 'expense')->sum('amount');
        $bankInTotal = (float) (clone $bankBase)->where('type', 'income')->sum('amount');
        $bankOutTotal = (float) (clone $bankBase)->where('type', 'expense')->sum('amount');

        $periodIncomeTotal = $cashInTotal + $bankInTotal;
        $periodExpenseTotal = $cashOutTotal + $bankOutTotal;
        $totalTransactionsCount = (clone $cashBase)->count() + (clone $bankBase)->count();
        $netFlow = $periodIncomeTotal - $periodExpenseTotal;

        // Query Builder for table
        $cashSelect = DB::table('cash_transactions')
            ->select([
                'id',
                'transaction_no',
                'type',
                DB::raw("'cash' as transaction_method"),
                DB::raw("'cash' as account_type"),
                'amount',
                'description',
                'reference_no',
                DB::raw("NULL as cheque_no"),
                DB::raw("NULL as bank_name"),
                'transaction_date',
                'running_balance',
                'notes',
                'created_at',
                'updated_at'
            ])
            ->when($dateFrom, fn($q) => $q->whereDate('transaction_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('transaction_date', '<=', $dateTo))
            ->when($type && in_array($type, ['income', 'expense']), fn($q) => $q->where('type', $type))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('description', 'like', "%{$search}%")
                        ->orWhere('reference_no', 'like', "%{$search}%")
                        ->orWhere('transaction_no', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            });

        $bankSelect = DB::table('bank_transactions')
            ->select([
                'id',
                'transaction_no',
                'type',
                'transaction_method',
                'account_type',
                'amount',
                'description',
                'reference_no',
                'cheque_no',
                'bank_name',
                'transaction_date',
                'running_balance',
                'notes',
                'created_at',
                'updated_at'
            ])
            ->when($dateFrom, fn($q) => $q->whereDate('transaction_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('transaction_date', '<=', $dateTo))
            ->when($type && in_array($type, ['income', 'expense']), fn($q) => $q->where('type', $type))
            ->when($bankFilter, fn($q) => $q->where('bank_name', 'like', "%{$bankFilter}%"))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('description', 'like', "%{$search}%")
                        ->orWhere('reference_no', 'like', "%{$search}%")
                        ->orWhere('cheque_no', 'like', "%{$search}%")
                        ->orWhere('bank_name', 'like', "%{$search}%")
                        ->orWhere('transaction_no', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            });

        if ($method === 'cash') {
            $tableQuery = $cashSelect;
        } elseif ($method === 'bank_transfer' || $method === 'cheque') {
            $tableQuery = $bankSelect->where('transaction_method', $method);
        } else {
            $tableQuery = $cashSelect->unionAll($bankSelect);
        }

        $transactions = $tableQuery->orderBy('transaction_date', 'desc')->orderBy('id', 'desc')->paginate(25)->withQueryString();

        $transactions->getCollection()->transform(function ($item) {
            if (is_string($item->transaction_date)) {
                $item->transaction_date = Carbon::parse($item->transaction_date);
            }
            return $item;
        });

        $banks = Bank::orderBy('bank_name')->get();

        return view('reports.transactions', compact(
            'transactions',
            'dateFrom',
            'dateTo',
            'type',
            'method',
            'bankFilter',
            'search',
            'periodIncomeTotal',
            'periodExpenseTotal',
            'netFlow',
            'totalTransactionsCount',
            'cashInTotal',
            'cashOutTotal',
            'bankInTotal',
            'bankOutTotal',
            'banks'
        ));
    }

    /**
     * Cheques Audit & Clearance Ledger Report
     */
    public function chequesReport(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::today()->format('Y-m-d'));
        $type = $request->get('type', 'all');
        $status = $request->get('status', 'all');
        $bankId = $request->get('bank_id');
        $search = $request->get('search');

        $baseQuery = Cheque::with(['customer', 'order.event', 'bank', 'payment', 'expense'])
            ->whereBetween('received_or_issued_date', [$dateFrom, $dateTo]);

        // Totals across the period
        $periodChequesCount = (clone $baseQuery)->count();
        $inTotal = (float) (clone $baseQuery)->where('type', 'IN')->sum('amount');
        $outTotal = (float) (clone $baseQuery)->where('type', 'OUT')->sum('amount');
        $realizedTotal = (float) (clone $baseQuery)->where('status', 'Realized')->sum('amount');
        $pendingTotal = (float) (clone $baseQuery)->whereIn('status', ['In Hand', 'Deposited', 'Issued'])->sum('amount');
        $returnedTotal = (float) (clone $baseQuery)->where('status', 'Returned')->sum('amount');

        // Filtered query for display
        $query = (clone $baseQuery);

        if ($type && in_array(strtoupper($type), ['IN', 'OUT'])) {
            $query->where('type', strtoupper($type));
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($bankId) {
            $query->where('bank_id', $bankId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('cheque_number', 'like', "%{$search}%")
                  ->orWhere('payer_bank', 'like', "%{$search}%")
                  ->orWhere('payee_name', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('order', fn($o) => $o->where('order_no', 'like', "%{$search}%"));
            });
        }

        $cheques = $query->orderBy('received_or_issued_date', 'desc')->orderBy('id', 'desc')->paginate(25)->withQueryString();
        $banks = Bank::orderBy('bank_name')->get();

        return view('reports.cheques', compact(
            'cheques',
            'dateFrom',
            'dateTo',
            'type',
            'status',
            'bankId',
            'search',
            'periodChequesCount',
            'inTotal',
            'outTotal',
            'realizedTotal',
            'pendingTotal',
            'returnedTotal',
            'banks'
        ));
    }
}
