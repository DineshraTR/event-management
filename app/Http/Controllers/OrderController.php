<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderService;
use App\Models\OrderOtherCharge;
use App\Models\Event;
use App\Models\Customer;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $customerId = $request->input('customer_id');
        $eventId = $request->input('event_id');
        $paymentStatus = $request->input('payment_status', $request->input('status'));
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $query = Order::with(['customer', 'event']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_no', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('event', function ($q3) use ($search) {
                      $q3->where('event_name', 'like', "%{$search}%")
                         ->orWhere('event_no', 'like', "%{$search}%");
                  });
            });
        }

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        if ($eventId) {
            $query->where('event_id', $eventId);
        }

        if ($paymentStatus) {
            $query->where('payment_status', $paymentStatus);
        }

        if ($fromDate) {
            $query->whereDate('order_date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('order_date', '<=', $toDate);
        }

        $orders = $query->latest('order_date')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $events = Event::orderBy('event_name')->get(['id', 'event_no', 'event_name']);

        $totalOrdersCount = Order::count();
        $paidOrdersCount = Order::where('payment_status', 'paid')->count();
        $partialOrdersCount = Order::where('payment_status', 'partial')->count();
        $pendingOrdersCount = Order::where('payment_status', 'pending')->count();
        $totalRevenue = Order::sum('total_amount');
        $totalBalance = Order::sum('balance');

        return view('orders.index', compact(
            'orders', 'customers', 'events',
            'totalOrdersCount', 'paidOrdersCount', 'partialOrdersCount', 'pendingOrdersCount',
            'totalRevenue', 'totalBalance'
        ));
    }

    public function create(Request $request)
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $services = Service::where('is_active', true)->get();
        $events = Event::with('customer')->whereIn('status', ['pending', 'confirmed'])->orderBy('event_date')->get();

        $selectedEvent = null;
        if ($request->event_id) {
            $selectedEvent = Event::with(['customer', 'additionalServices', 'otherCharges', 'subCategory'])->find($request->event_id);
        }

        $today = Carbon::today()->format('Y-m-d');
        return view('orders.create', compact('customers', 'services', 'events', 'selectedEvent', 'today'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'package_sell_price' => 'nullable|numeric|min:0',
            'package_name' => 'nullable|string',
            'services' => 'nullable|array',
            'other_charges' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $validated, &$order) {
            $event = Event::with('subCategory')->find($validated['event_id']);
            $packageSellPrice = (float) $request->input('package_sell_price', $event?->package_sell_price ?? 0);
            $packageName = $request->input('package_name', $event?->subCategory?->name);

            $order = Order::create([
                'event_id' => $validated['event_id'],
                'customer_id' => $validated['customer_id'],
                'event_sub_category_id' => $event?->event_sub_category_id,
                'package_name' => $packageName,
                'package_cost_price' => $event?->package_cost_price ?? 0,
                'package_sell_price' => $packageSellPrice,
                'order_date' => $validated['order_date'],
                'discount' => $validated['discount'] ?? 0,
                'notes' => $validated['notes'] ?? null,
                'sub_total' => 0,
                'total_amount' => 0,
                'paid_amount' => 0,
                'balance' => 0,
            ]);

            // Add package base price as top line item if provided
            if ($packageSellPrice > 0 || $packageName) {
                OrderService::create([
                    'order_id' => $order->id,
                    'service_id' => null,
                    'is_package_item' => true,
                    'description' => ($packageName ?: 'Event Package') . ' (Base Package)',
                    'qty' => 1,
                    'unit_price' => $packageSellPrice,
                    'total_price' => $packageSellPrice,
                ]);
            }

            // Add services
            if ($request->has('services')) {
                foreach ($request->services as $svc) {
                    if (!empty($svc['description'])) {
                        OrderService::create([
                            'order_id' => $order->id,
                            'service_id' => $svc['service_id'] ?? null,
                            'description' => $svc['description'],
                            'qty' => $svc['qty'] ?? 1,
                            'unit_price' => $svc['unit_price'] ?? 0,
                            'total_price' => ($svc['unit_price'] ?? 0) * ($svc['qty'] ?? 1),
                        ]);
                    }
                }
            }

            // Add other charges
            if ($request->has('other_charges')) {
                foreach ($request->other_charges as $charge) {
                    if (!empty($charge['description'])) {
                        OrderOtherCharge::create([
                            'order_id' => $order->id,
                            'description' => $charge['description'],
                            'amount' => $charge['amount'] ?? 0,
                        ]);
                    }
                }
            }

            $order->recalculateTotals();
        });

        return redirect()->route('orders.show', $order)->with('success', 'Order created successfully!');
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'event.subCategory.category', 'event.subCategory.subCategoryServices.service', 'services.service', 'otherCharges', 'payments']);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $events = Event::with('customer')->orderBy('event_date')->get();
        $services = Service::where('is_active', true)->get();
        $order->load(['customer', 'services.service', 'otherCharges', 'event.subCategory.category', 'event.subCategory.subCategoryServices.service']);
        return view('orders.edit', compact('order', 'services', 'customers', 'events'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'event_id' => 'nullable|exists:events,id',
            'order_date' => 'required|date',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'package_sell_price' => 'nullable|numeric|min:0',
            'package_name' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $validated, $order) {
            $event = !empty($validated['event_id']) ? Event::with('subCategory')->find($validated['event_id']) : $order->event;
            $packageSellPrice = (float) $request->input('package_sell_price', $order->package_sell_price ?? 0);
            $packageName = $request->input('package_name', $order->package_name ?? $event?->subCategory?->name);

            $updateData = $validated;
            if ($event) {
                $updateData['event_sub_category_id'] = $event->event_sub_category_id;
                $updateData['package_cost_price'] = $event->package_cost_price ?? 0;
            }
            $updateData['package_name'] = $packageName;
            $updateData['package_sell_price'] = $packageSellPrice;

            $order->update($updateData);

            // Rebuild services
            $order->services()->delete();

            // Add package base price as top line item if provided
            if ($packageSellPrice > 0 || $packageName) {
                OrderService::create([
                    'order_id' => $order->id,
                    'service_id' => null,
                    'is_package_item' => true,
                    'description' => ($packageName ?: 'Event Package') . ' (Base Package)',
                    'qty' => 1,
                    'unit_price' => $packageSellPrice,
                    'total_price' => $packageSellPrice,
                ]);
            }

            if ($request->has('services')) {
                foreach ($request->services as $svc) {
                    if (!empty($svc['description'])) {
                        OrderService::create([
                            'order_id' => $order->id,
                            'service_id' => $svc['service_id'] ?? null,
                            'description' => $svc['description'],
                            'qty' => $svc['qty'] ?? 1,
                            'unit_price' => $svc['unit_price'] ?? 0,
                            'total_price' => ($svc['unit_price'] ?? 0) * ($svc['qty'] ?? 1),
                        ]);
                    }
                }
            }

            // Rebuild other charges
            $order->otherCharges()->delete();
            if ($request->has('other_charges')) {
                foreach ($request->other_charges as $charge) {
                    if (!empty($charge['description'])) {
                        OrderOtherCharge::create([
                            'order_id' => $order->id,
                            'description' => $charge['description'],
                            'amount' => $charge['amount'] ?? 0,
                        ]);
                    }
                }
            }

            $order->recalculateTotals();
        });

        return redirect()->route('orders.show', $order)->with('success', 'Order updated successfully!');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted.');
    }

    public function getByEvent($eventId)
    {
        $orders = Order::with('payments')->where('event_id', $eventId)->get();
        return response()->json($orders);
    }

    public function apiGetEventDetails($eventId)
    {
        $event = Event::with(['customer', 'subCategory.category', 'subCategory.subCategoryServices.service', 'additionalServices.service', 'otherCharges'])
            ->findOrFail($eventId);

        return response()->json([
            'id' => $event->id,
            'event_no' => $event->event_no,
            'event_name' => $event->event_name,
            'customer_id' => $event->customer_id,
            'customer_name' => $event->customer?->name,
            'category_name' => $event->subCategory?->category?->name ?? 'General Category',
            'package_id' => $event->event_sub_category_id,
            'package_name' => $event->subCategory?->name ?? 'Custom Package',
            'package_sell_price' => (float) $event->package_sell_price,
            'package_cost_price' => (float) $event->package_cost_price,
            'discount' => (float) $event->discount,
            'advance_payment' => (float) $event->advance_payment,
            'package_services' => $event->subCategory?->subCategoryServices->map(fn($s) => [
                'service_id' => $s->service_id,
                'name' => $s->service?->name ?? 'Service',
                'description' => $s->service?->name ?? 'Service',
                'qty' => $s->qty,
                'cost_price' => (float) $s->cost_price,
            ]) ?? [],
            'additional_services' => $event->additionalServices->map(fn($s) => [
                'service_id' => $s->service_id,
                'description' => $s->description,
                'qty' => $s->qty,
                'unit_price' => (float) $s->unit_price,
            ]),
            'other_charges' => $event->otherCharges->map(fn($c) => [
                'description' => $c->description,
                'amount' => (float) $c->amount,
            ]),
        ]);
    }

    public function print(Order $order)
    {
        $order->load([
            'customer',
            'event.subCategory.category',
            'event.subCategory.subCategoryServices.service',
            'services.service',
            'otherCharges',
            'payments.bank',
        ]);

        $primaryBank = \App\Models\Bank::where('is_active', true)->first();

        return view('orders.print', compact('order', 'primaryBank'));
    }
}
