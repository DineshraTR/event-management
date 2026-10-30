<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventService;
use App\Models\EventOtherCharge;
use App\Models\EventCategory;
use App\Models\EventSubCategory;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index()
    {
        $search = request('search');
        $customerId = request('customer_id');
        $eventId = request('event_id');
        $subCategoryId = request('sub_category_id');
        $fromDate = request('from_date');
        $toDate = request('to_date');
        $status = request('status');
        $tab = request('tab', 'all');

        $baseQuery = Event::with(['customer', 'subCategory.category', 'order'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('event_name', 'like', "%{$search}%")
                        ->orWhere('event_no', 'like', "%{$search}%")
                        ->orWhere('venue', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"));
                });
            })
            ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
            ->when($eventId, fn($q) => $q->where('id', $eventId))
            ->when($subCategoryId, fn($q) => $q->where('event_sub_category_id', $subCategoryId))
            ->when($fromDate, fn($q) => $q->whereDate('event_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('event_date', '<=', $toDate))
            ->when($status, fn($q) => $q->where('status', $status));

        // Tab counts based on current filters
        $countAll = (clone $baseQuery)->count();
        $countOrdered = (clone $baseQuery)->has('orders')->count();
        $countNoOrder = (clone $baseQuery)->doesntHave('orders')->count();

        // Apply tab filter
        $query = clone $baseQuery;
        if ($tab === 'ordered') {
            $query->has('orders');
        } elseif ($tab === 'no_order') {
            $query->doesntHave('orders');
        }

        $events = $query->latest('event_date')->paginate(25)->withQueryString();

        $customers = Customer::where('is_active', true)->orderBy('name')->get(['id', 'name', 'phone']);
        $subCategories = EventSubCategory::with('category')->where('is_active', true)->orderBy('name')->get();
        $eventList = Event::orderBy('event_name')->get(['id', 'event_name', 'event_no']);

        return view('events.index', compact(
            'events',
            'customers',
            'subCategories',
            'eventList',
            'tab',
            'countAll',
            'countOrdered',
            'countNoOrder'
        ));
    }

    public function create(Request $request)
    {
        $categories = EventCategory::where('is_active', true)->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $services = Service::where('is_active', true)->get();
        $today = Carbon::today()->format('Y-m-d');

        $quotation = null;
        $initialSubCategories = collect();

        if ($request->filled('quotation_id')) {
            $quotation = Quotation::with([
                'customer',
                'subCategory.category',
                'subCategory.subCategoryServices.service',
                'services.service',
                'otherCharges'
            ])->find($request->input('quotation_id'));

            if ($quotation && $quotation->subCategory) {
                $initialSubCategories = EventSubCategory::where('event_category_id', $quotation->subCategory->event_category_id)
                    ->where('is_active', true)
                    ->get();
            }
        }

        return view('events.create', compact('categories', 'customers', 'services', 'today', 'quotation', 'initialSubCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'event_sub_category_id' => 'required|exists:event_sub_categories,id',
            'event_name' => 'required|string|max:255',
            'event_date' => 'required|date',
            'today_date' => 'required|date',
            'venue' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'package_sell_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $subCategory = EventSubCategory::findOrFail($validated['event_sub_category_id']);

        $validated['package_cost_price'] = $subCategory->cost_price;
        $validated['discount'] = $validated['discount'] ?? 0;
        $validated['advance_payment'] = $validated['advance_payment'] ?? 0;

        $createdOrder = null;
        DB::transaction(function () use ($request, $validated, &$event, &$createdOrder) {
            $event = Event::create($validated);

            // Add additional services
            if ($request->has('additional_services')) {
                foreach ($request->additional_services as $svc) {
                    if (!empty($svc['description'])) {
                        EventService::create([
                            'event_id' => $event->id,
                            'service_id' => $svc['service_id'] ?? null,
                            'description' => $svc['description'],
                            'qty' => $svc['qty'] ?? 1,
                            'unit_price' => $svc['unit_price'],
                            'total_price' => ($svc['unit_price'] ?? 0) * ($svc['qty'] ?? 1),
                        ]);
                    }
                }
            }

            // Add other charges
            if ($request->has('other_charges')) {
                foreach ($request->other_charges as $charge) {
                    if (!empty($charge['description'])) {
                        EventOtherCharge::create([
                            'event_id' => $event->id,
                            'description' => $charge['description'],
                            'amount' => $charge['amount'] ?? 0,
                        ]);
                    }
                }
            }

            $event->recalculateTotals();

            // Auto-create order if advance payment > 0
            if ($validated['advance_payment'] > 0) {
                $createdOrder = $this->autoCreateOrder($event, $validated['advance_payment']);
            }
        });

        if ($createdOrder) {
            return redirect()->route('orders.print', ['order' => $createdOrder->id, 'auto_print' => 1])
                ->with('success', 'Event registered, Order created with advance payment, and Invoice is ready to print!');
        }

        return redirect()->route('events.show', $event)->with('success', 'Event registered successfully!');
    }

    public function show(Event $event)
    {
        $event->load([
            'customer',
            'subCategory.category',
            'subCategory.subCategoryServices.service',
            'additionalServices.service',
            'otherCharges',
            'orders.payments'
        ]);
        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $categories = EventCategory::where('is_active', true)->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $services = Service::where('is_active', true)->get();
        $event->load(['subCategory.category', 'subCategory.subCategoryServices.service', 'additionalServices.service', 'otherCharges']);
        return view('events.edit', compact('event', 'categories', 'customers', 'services'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'event_sub_category_id' => 'required|exists:event_sub_categories,id',
            'event_name' => 'required|string|max:255',
            'event_date' => 'required|date',
            'today_date' => 'required|date',
            'venue' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'package_sell_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $subCategory = EventSubCategory::findOrFail($validated['event_sub_category_id']);
        $validated['package_cost_price'] = $subCategory->cost_price;

        DB::transaction(function () use ($request, $validated, $event) {
            $event->update($validated);

            // Rebuild additional services
            $event->additionalServices()->delete();
            if ($request->has('additional_services')) {
                foreach ($request->additional_services as $svc) {
                    if (!empty($svc['description'])) {
                        EventService::create([
                            'event_id' => $event->id,
                            'service_id' => $svc['service_id'] ?? null,
                            'description' => $svc['description'],
                            'qty' => $svc['qty'] ?? 1,
                            'unit_price' => $svc['unit_price'],
                            'total_price' => ($svc['unit_price'] ?? 0) * ($svc['qty'] ?? 1),
                        ]);
                    }
                }
            }

            // Rebuild other charges
            $event->otherCharges()->delete();
            if ($request->has('other_charges')) {
                foreach ($request->other_charges as $charge) {
                    if (!empty($charge['description'])) {
                        EventOtherCharge::create([
                            'event_id' => $event->id,
                            'description' => $charge['description'],
                            'amount' => $charge['amount'] ?? 0,
                        ]);
                    }
                }
            }

            $event->recalculateTotals();
        });

        return redirect()->route('events.show', $event)->with('success', 'Event updated successfully!');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted.');
    }

    public function createOrder(Event $event)
    {
        $existingOrder = $event->orders()->where('is_auto_created', false)->first();
        if ($existingOrder) {
            return redirect()->route('orders.show', $existingOrder)
                ->with('info', 'An order already exists for this event.');
        }
        return redirect()->route('orders.create', ['event_id' => $event->id]);
    }

    private function autoCreateOrder(Event $event, float $advanceAmount): Order
    {
        $event->load(['customer', 'subCategory.category', 'subCategory.subCategoryServices.service', 'additionalServices', 'otherCharges']);

        $packageName = $event->subCategory?->name ?? 'Base Event Package';

        $order = Order::create([
            'event_id' => $event->id,
            'customer_id' => $event->customer_id,
            'event_sub_category_id' => $event->event_sub_category_id,
            'package_name' => $packageName,
            'package_cost_price' => $event->package_cost_price ?? 0,
            'package_sell_price' => $event->package_sell_price ?? 0,
            'additional_services_total' => $event->additional_services_total ?? 0,
            'other_charges_total' => $event->other_charges_total ?? 0,
            'order_date' => $event->today_date,
            'sub_total' => $event->sub_total,
            'discount' => $event->discount ?? 0,
            'total_amount' => $event->total_amount,
            'paid_amount' => $advanceAmount,
            'balance' => max(0, $event->total_amount - $advanceAmount),
            'payment_status' => $advanceAmount >= $event->total_amount ? 'paid' : ($advanceAmount > 0 ? 'partial' : 'pending'),
            'notes' => 'Auto-created from event registration advance payment.',
            'is_auto_created' => true,
        ]);

        // 1. Add base package item as top line item
        if ($event->package_sell_price > 0 || $event->subCategory) {
            \App\Models\OrderService::create([
                'order_id' => $order->id,
                'service_id' => null,
                'is_package_item' => true,
                'description' => $packageName . ' (Base Package)',
                'qty' => 1,
                'unit_price' => $event->package_sell_price,
                'total_price' => $event->package_sell_price,
            ]);
        }

        // 2. Copy event additional services to order
        foreach ($event->additionalServices as $svc) {
            \App\Models\OrderService::create([
                'order_id' => $order->id,
                'service_id' => $svc->service_id,
                'description' => $svc->description,
                'qty' => $svc->qty,
                'unit_price' => $svc->unit_price,
                'total_price' => $svc->total_price,
            ]);
        }

        // 3. Copy event other charges to order
        foreach ($event->otherCharges as $charge) {
            \App\Models\OrderOtherCharge::create([
                'order_id' => $order->id,
                'description' => $charge->description,
                'amount' => $charge->amount,
            ]);
        }

        // 4. Record advance payment
        $payment = \App\Models\Payment::create([
            'order_id' => $order->id,
            'customer_id' => $event->customer_id,
            'amount' => $advanceAmount,
            'payment_type' => 'advance',
            'payment_method' => 'cash',
            'payment_date' => $event->today_date,
            'notes' => 'Advance payment during event registration.',
        ]);

        // 5. Log to CashTransaction / Cash Book
        \App\Models\CashTransaction::create([
            'type' => 'income',
            'amount' => $advanceAmount,
            'description' => 'Advance payment - Event ' . $event->event_no . ' (Order ' . $order->order_no . ')',
            'transaction_date' => $event->today_date,
            'reference_no' => $payment->payment_no,
            'customer_id' => $event->customer_id,
            'order_id' => $order->id,
            'payment_id' => $payment->id,
            'notes' => "Event: {$event->event_no} - Package: {$packageName}",
        ]);

        return $order;
    }
}
