<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventPackage;
use App\Models\EventService;
use App\Models\EventOtherCharge;
use App\Models\EventCategory;
use App\Models\EventSubCategory;
use App\Models\Customer;
use App\Models\Bank;
use App\Models\Order;
use App\Models\OrderPackage;
use App\Models\OrderService;
use App\Models\OrderOtherCharge;
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

        $baseQuery = Event::with(['customer', 'packages', 'subCategory.category', 'order'])
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
                'packages.subCategory.category',
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

        $banks = Bank::where('is_active', true)->orderBy('bank_name')->get();

        return view('events.create', compact('categories', 'customers', 'services', 'today', 'quotation', 'initialSubCategories', 'banks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'event_sub_category_id' => 'nullable|exists:event_sub_categories,id',
            'event_name' => 'required|string|max:255',
            'event_date' => 'required|date',
            'today_date' => 'required|date',
            'venue' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'package_sell_price' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'packages' => 'nullable|array',
            'payment_method' => 'nullable|in:cash,bank_transfer,cheque',
            'bank_name' => 'nullable|string|max:255',
            'cheque_no' => 'nullable|string|max:100',
            'cheque_date' => 'nullable|date',
            'payment_date' => 'nullable|date',
            'payment_notes' => 'nullable|string',
        ]);

        $packagesData = $request->input('packages', []);
        $totalPkgSell = 0;
        $totalPkgCost = 0;
        $firstSubCatId = $validated['event_sub_category_id'] ?? null;
        $firstPkgName = null;

        if (!empty($packagesData)) {
            foreach ($packagesData as $p) {
                $qty = (int)($p['qty'] ?? 1);
                $sell = (float)($p['sell_price'] ?? 0);
                $cost = (float)($p['cost_price'] ?? 0);
                $totalPkgSell += ($sell * $qty);
                $totalPkgCost += ($cost * $qty);
                if (!$firstSubCatId && !empty($p['event_sub_category_id'])) {
                    $firstSubCatId = $p['event_sub_category_id'];
                }
                if (!$firstPkgName && !empty($p['package_name'])) {
                    $firstPkgName = $p['package_name'];
                }
            }
        } elseif (!empty($validated['event_sub_category_id'])) {
            $subCategory = EventSubCategory::find($validated['event_sub_category_id']);
            $totalPkgCost = $subCategory ? (float)$subCategory->cost_price : 0;
            $totalPkgSell = (float)($validated['package_sell_price'] ?? ($subCategory ? $subCategory->sell_price : 0));
            $firstPkgName = $subCategory?->name;
        }

        $validated['event_sub_category_id'] = $firstSubCatId;
        $validated['package_name'] = $firstPkgName;
        $validated['package_sell_price'] = $totalPkgSell;
        $validated['package_cost_price'] = $totalPkgCost;
        $validated['discount'] = $validated['discount'] ?? 0;
        $validated['advance_payment'] = $validated['advance_payment'] ?? 0;

        $createdOrder = null;
        DB::transaction(function () use ($request, $validated, $packagesData, &$event, &$createdOrder) {
            $event = Event::create($validated);

            // Save event_packages
            if (!empty($packagesData)) {
                foreach ($packagesData as $p) {
                    if (!empty($p['package_name']) || !empty($p['event_sub_category_id'])) {
                        $qty = (int)($p['qty'] ?? 1);
                        $sell = (float)($p['sell_price'] ?? 0);
                        EventPackage::create([
                            'event_id' => $event->id,
                            'event_sub_category_id' => !empty($p['event_sub_category_id']) ? $p['event_sub_category_id'] : null,
                            'package_name' => $p['package_name'] ?? 'Event Package',
                            'cost_price' => (float)($p['cost_price'] ?? 0),
                            'sell_price' => $sell,
                            'qty' => $qty,
                            'total_price' => (float)($p['total_price'] ?? ($sell * $qty)),
                        ]);
                    }
                }
            } elseif ($event->subCategory || $event->package_sell_price > 0) {
                EventPackage::create([
                    'event_id' => $event->id,
                    'event_sub_category_id' => $event->event_sub_category_id,
                    'package_name' => $event->package_name ?? $event->subCategory?->name ?? 'Base Package',
                    'cost_price' => $event->package_cost_price ?? 0,
                    'sell_price' => $event->package_sell_price ?? 0,
                    'qty' => 1,
                    'total_price' => $event->package_sell_price ?? 0,
                ]);
            }

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
                $paymentDetails = [
                    'payment_method' => $request->input('payment_method', 'cash'),
                    'bank_name' => $request->input('bank_name'),
                    'cheque_no' => $request->input('cheque_no'),
                    'cheque_date' => $request->input('cheque_date'),
                    'payment_date' => $request->input('payment_date', $event->today_date),
                    'payment_notes' => $request->input('payment_notes'),
                ];
                $createdOrder = $this->autoCreateOrder($event, (float)$validated['advance_payment'], $paymentDetails);
            }
        });

        \App\Services\AuditLogger::log(
            'created',
            'Event',
            "Event #{$event->event_no} - {$event->event_name}",
            "Registered new event #{$event->event_no} ({$event->event_name}) for customer " . ($event->customer?->name ?? 'N/A'),
            $event->id,
            null,
            $event->toArray()
        );

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
            'packages.subCategory.category',
            'packages.subCategory.subCategoryServices.service',
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
        $event->load([
            'subCategory.category',
            'subCategory.subCategoryServices.service',
            'packages.subCategory.category',
            'packages.subCategory.subCategoryServices.service',
            'additionalServices.service',
            'otherCharges'
        ]);
        return view('events.edit', compact('event', 'categories', 'customers', 'services'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'event_sub_category_id' => 'nullable|exists:event_sub_categories,id',
            'event_name' => 'required|string|max:255',
            'event_date' => 'required|date',
            'today_date' => 'required|date',
            'venue' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'package_sell_price' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'packages' => 'nullable|array',
            'payment_method' => 'nullable|in:cash,bank_transfer,cheque',
            'bank_name' => 'nullable|string|max:255',
            'cheque_no' => 'nullable|string|max:100',
            'cheque_date' => 'nullable|date',
            'payment_date' => 'nullable|date',
            'payment_notes' => 'nullable|string',
        ]);

        $packagesData = $request->input('packages', []);
        $totalPkgSell = 0;
        $totalPkgCost = 0;
        $firstSubCatId = $validated['event_sub_category_id'] ?? null;
        $firstPkgName = null;

        if (!empty($packagesData)) {
            foreach ($packagesData as $p) {
                $qty = (int)($p['qty'] ?? 1);
                $sell = (float)($p['sell_price'] ?? 0);
                $cost = (float)($p['cost_price'] ?? 0);
                $totalPkgSell += ($sell * $qty);
                $totalPkgCost += ($cost * $qty);
                if (!$firstSubCatId && !empty($p['event_sub_category_id'])) {
                    $firstSubCatId = $p['event_sub_category_id'];
                }
                if (!$firstPkgName && !empty($p['package_name'])) {
                    $firstPkgName = $p['package_name'];
                }
            }
        } elseif (!empty($validated['event_sub_category_id'])) {
            $subCategory = EventSubCategory::find($validated['event_sub_category_id']);
            $totalPkgCost = $subCategory ? (float)$subCategory->cost_price : 0;
            $totalPkgSell = (float)($validated['package_sell_price'] ?? ($subCategory ? $subCategory->sell_price : 0));
            $firstPkgName = $subCategory?->name;
        }

        $validated['event_sub_category_id'] = $firstSubCatId;
        $validated['package_name'] = $firstPkgName;
        $validated['package_sell_price'] = $totalPkgSell;
        $validated['package_cost_price'] = $totalPkgCost;

        $oldValues = $event->toArray();

        DB::transaction(function () use ($request, $validated, $packagesData, $event) {
            $event->update($validated);

            // Rebuild event_packages
            $event->packages()->delete();
            if (!empty($packagesData)) {
                foreach ($packagesData as $p) {
                    if (!empty($p['package_name']) || !empty($p['event_sub_category_id'])) {
                        $qty = (int)($p['qty'] ?? 1);
                        $sell = (float)($p['sell_price'] ?? 0);
                        EventPackage::create([
                            'event_id' => $event->id,
                            'event_sub_category_id' => !empty($p['event_sub_category_id']) ? $p['event_sub_category_id'] : null,
                            'package_name' => $p['package_name'] ?? 'Event Package',
                            'cost_price' => (float)($p['cost_price'] ?? 0),
                            'sell_price' => $sell,
                            'qty' => $qty,
                            'total_price' => (float)($p['total_price'] ?? ($sell * $qty)),
                        ]);
                    }
                }
            } elseif ($event->subCategory || $event->package_sell_price > 0) {
                EventPackage::create([
                    'event_id' => $event->id,
                    'event_sub_category_id' => $event->event_sub_category_id,
                    'package_name' => $event->package_name ?? $event->subCategory?->name ?? 'Base Package',
                    'cost_price' => $event->package_cost_price ?? 0,
                    'sell_price' => $event->package_sell_price ?? 0,
                    'qty' => 1,
                    'total_price' => $event->package_sell_price ?? 0,
                ]);
            }

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

            // Sync with linked orders if any exist
            $orders = $event->orders;
            if ($orders && $orders->isNotEmpty()) {
                $freshPackages = $event->packages()->get();
                foreach ($orders as $order) {
                    // 1. Sync order_packages
                    $order->packages()->delete();
                    foreach ($freshPackages as $epkg) {
                        OrderPackage::create([
                            'order_id' => $order->id,
                            'event_package_id' => $epkg->id,
                            'event_sub_category_id' => $epkg->event_sub_category_id,
                            'package_name' => $epkg->package_name,
                            'cost_price' => $epkg->cost_price,
                            'sell_price' => $epkg->sell_price,
                            'qty' => $epkg->qty,
                            'total_price' => $epkg->total_price,
                        ]);
                    }

                    // 2. Sync package line items in OrderService
                    $order->services()->where('is_package_item', true)->delete();
                    foreach ($freshPackages as $epkg) {
                        OrderService::create([
                            'order_id' => $order->id,
                            'service_id' => null,
                            'is_package_item' => true,
                            'description' => $epkg->package_name . ' (Package)',
                            'qty' => $epkg->qty,
                            'unit_price' => $epkg->sell_price,
                            'total_price' => $epkg->total_price,
                        ]);
                    }

                    // 3. Update order package info and recalculate
                    $order->update([
                        'event_sub_category_id' => $event->event_sub_category_id,
                        'package_name' => $event->package_name,
                        'package_cost_price' => $event->package_cost_price,
                        'package_sell_price' => $event->package_sell_price,
                    ]);
                    $order->recalculateTotals();
                }
            }
        });

        \App\Services\AuditLogger::log(
            'updated',
            'Event',
            "Event #{$event->event_no} - {$event->event_name}",
            "Updated event #{$event->event_no} ({$event->event_name})",
            $event->id,
            $oldValues,
            $event->fresh()->toArray()
        );

        return redirect()->route('events.show', $event)->with('success', 'Event updated successfully!');
    }

    public function destroy(Event $event)
    {
        $hasPayments = $event->hasPayments();

        if ($hasPayments) {
            $paidTotal = (float)$event->orders()->sum('paid_amount');
            return redirect()->back()->with('error', "Cannot delete Event #{$event->event_no} because payment records (Rs. " . number_format($paidTotal, 2) . ") have already been made towards its orders. Deletion is strictly prevented to protect accounting integrity.");
        }

        $eventTitle = "Event #{$event->event_no} - {$event->event_name}";
        $oldValues = $event->toArray();
        $deletedOrderNos = [];

        DB::transaction(function () use ($event, $eventTitle, $oldValues, &$deletedOrderNos) {
            // Delete all associated orders and their line items
            $orders = $event->orders()->get();
            foreach ($orders as $order) {
                $orderTitle = "Order #{$order->order_no}";
                $orderOldValues = $order->toArray();
                $deletedOrderNos[] = $order->order_no;

                $order->packages()->delete();
                $order->services()->delete();
                $order->otherCharges()->delete();
                $order->delete();

                \App\Services\AuditLogger::log(
                    'deleted',
                    'Order',
                    $orderTitle,
                    "Deleted associated order {$orderTitle} because linked {$eventTitle} was deleted.",
                    $order->id,
                    $orderOldValues,
                    null
                );
            }

            // Delete event packages, additional services, and other charges
            $event->packages()->delete();
            $event->additionalServices()->delete();
            $event->otherCharges()->delete();

            // Delete the event
            $event->delete();
        });

        \App\Services\AuditLogger::log('deleted', 'Event', $eventTitle, "Deleted event {$eventTitle}", $event->id, $oldValues, null);

        $msg = "Event #{$event->event_no} deleted successfully.";
        if (!empty($deletedOrderNos)) {
            $msg = "Event #{$event->event_no} and its associated order(s) (" . implode(', ', $deletedOrderNos) . ") were deleted successfully.";
        }

        return redirect()->route('events.index')->with('success', $msg);
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

    private function autoCreateOrder(Event $event, float $advanceAmount, array $paymentDetails = []): Order
    {
        $event->load(['customer', 'packages.subCategory.category', 'subCategory.category', 'subCategory.subCategoryServices.service', 'additionalServices', 'otherCharges']);

        $packageName = $event->package_name ?? $event->subCategory?->name ?? 'Base Event Package';

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

        // 1. Add packages into order_packages and order_services
        $eventPackages = $event->packages;
        if ($eventPackages && $eventPackages->isNotEmpty()) {
            foreach ($eventPackages as $epkg) {
                OrderPackage::create([
                    'order_id' => $order->id,
                    'event_package_id' => $epkg->id,
                    'event_sub_category_id' => $epkg->event_sub_category_id,
                    'package_name' => $epkg->package_name,
                    'cost_price' => $epkg->cost_price,
                    'sell_price' => $epkg->sell_price,
                    'qty' => $epkg->qty,
                    'total_price' => $epkg->total_price,
                ]);

                OrderService::create([
                    'order_id' => $order->id,
                    'service_id' => null,
                    'is_package_item' => true,
                    'description' => $epkg->package_name . ' (Package)',
                    'qty' => $epkg->qty,
                    'unit_price' => $epkg->sell_price,
                    'total_price' => $epkg->total_price,
                ]);
            }
        } elseif ($event->package_sell_price > 0 || $event->subCategory) {
            OrderPackage::create([
                'order_id' => $order->id,
                'event_package_id' => null,
                'event_sub_category_id' => $event->event_sub_category_id,
                'package_name' => $packageName,
                'cost_price' => $event->package_cost_price ?? 0,
                'sell_price' => $event->package_sell_price ?? 0,
                'qty' => 1,
                'total_price' => $event->package_sell_price ?? 0,
            ]);

            OrderService::create([
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

        // 4. Record advance payment with chosen payment method
        $paymentMethod = $paymentDetails['payment_method'] ?? 'cash';
        $bankName = $paymentDetails['bank_name'] ?? null;
        $chequeNo = $paymentDetails['cheque_no'] ?? null;
        $chequeDate = $paymentDetails['cheque_date'] ?? null;
        $paymentDate = $paymentDetails['payment_date'] ?? $event->today_date;
        $paymentNotes = $paymentDetails['payment_notes'] ?? 'Advance payment during event registration.';

        $payment = \App\Models\Payment::create([
            'order_id' => $order->id,
            'customer_id' => $event->customer_id,
            'amount' => $advanceAmount,
            'payment_type' => 'advance',
            'payment_method' => $paymentMethod,
            'cheque_no' => $chequeNo,
            'bank_name' => $bankName,
            'payment_date' => $paymentDate,
            'notes' => $paymentNotes,
        ]);

        // 5. Handle method-specific ledger records (Cheque, Bank Transfer, Cash)
        if ($paymentMethod === 'cheque') {
            $chequeNoVal = !empty($chequeNo) ? trim($chequeNo) : ('CHQ-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT));
            \App\Models\Cheque::create([
                'type' => 'IN',
                'cheque_number' => $chequeNoVal,
                'cheque_date' => $chequeDate ?: $paymentDate,
                'received_or_issued_date' => $paymentDate,
                'amount' => $advanceAmount,
                'payer_bank' => $bankName,
                'customer_id' => $event->customer_id,
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'status' => 'In Hand',
                'notes' => $paymentNotes ?: "Customer Cheque Advance for Event #{$event->event_no} (Order #{$order->order_no})",
            ]);
        } elseif ($paymentMethod === 'bank_transfer') {
            $targetBank = null;
            if (!empty($bankName)) {
                $targetBank = \App\Models\Bank::where('bank_name', 'like', "%{$bankName}%")->first();
                if ($targetBank) {
                    $targetBank->increment('current_balance', $advanceAmount);
                }
            }

            \App\Models\BankTransaction::create([
                'type' => 'income',
                'transaction_method' => 'bank_transfer',
                'account_type' => $bankName ?? 'bank',
                'bank_id' => $targetBank?->id,
                'customer_id' => $event->customer_id,
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'amount' => $advanceAmount,
                'description' => 'Advance payment - Event ' . $event->event_no . ' (Order ' . $order->order_no . ')',
                'cheque_no' => null,
                'bank_name' => $bankName,
                'transaction_date' => $paymentDate,
                'reference_no' => $payment->payment_no,
                'running_balance' => $targetBank?->current_balance ?? 0,
                'notes' => $paymentNotes ?: "Event: {$event->event_no} - Package: {$packageName}",
            ]);
        } else {
            // Cash
            \App\Models\CashTransaction::create([
                'type' => 'income',
                'amount' => $advanceAmount,
                'description' => 'Advance payment - Event ' . $event->event_no . ' (Order ' . $order->order_no . ')',
                'transaction_date' => $paymentDate,
                'reference_no' => $payment->payment_no,
                'customer_id' => $event->customer_id,
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'notes' => $paymentNotes ?: "Event: {$event->event_no} - Package: {$packageName}",
            ]);
        }

        \App\Services\AuditLogger::log(
            'payment_received',
            'Payment',
            "Receipt #{$payment->payment_no} - Rs. " . number_format($payment->amount, 2),
            "Recorded advance payment of Rs. " . number_format($payment->amount, 2) . " via {$paymentMethod} for Event #{$event->event_no} (Order #{$order->order_no})",
            $payment->id,
            null,
            $payment->toArray()
        );

        // Send customer SMS notifications via Fit SMS (Mask: CEYLON WEDS)
        \App\Services\FitSmsService::sendOrderCreatedSms($order);
        \App\Services\FitSmsService::sendPaymentReceivedSms($payment);

        return $order;
    }
}
