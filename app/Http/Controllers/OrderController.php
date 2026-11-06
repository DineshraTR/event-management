<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderPackage;
use App\Models\OrderService;
use App\Models\OrderOtherCharge;
use App\Models\Event;
use App\Models\EventPackage;
use App\Models\EventCategory;
use App\Models\EventSubCategory;
use App\Models\EventService;
use App\Models\EventOtherCharge;
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

        $query = Order::with(['customer', 'packages', 'event.packages', 'event.subCategory']);

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
        $categories = EventCategory::where('is_active', true)->get();
        $services = Service::where('is_active', true)->get();
        $events = Event::with('customer')->whereIn('status', ['pending', 'confirmed'])->orderBy('event_date')->get();

        $selectedEvent = null;
        if ($request->event_id) {
            $selectedEvent = Event::with([
                'customer',
                'packages.subCategory.category',
                'packages.subCategory.subCategoryServices.service',
                'additionalServices.service',
                'otherCharges',
                'subCategory.category',
                'subCategory.subCategoryServices.service'
            ])->find($request->event_id);
        }

        $today = Carbon::today()->format('Y-m-d');
        return view('orders.create', compact('customers', 'categories', 'services', 'events', 'selectedEvent', 'today'));
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
            'packages' => 'nullable|array',
            'services' => 'nullable|array',
            'other_charges' => 'nullable|array',
            'removed_event_package_ids' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $validated, &$order) {
            $event = Event::with(['subCategory', 'additionalServices', 'otherCharges', 'packages'])->find($validated['event_id']);

            // 1. Delete removed event packages if user removed them on the order form
            if ($request->filled('removed_event_package_ids') && $event) {
                EventPackage::where('event_id', $event->id)
                    ->whereIn('id', $request->input('removed_event_package_ids'))
                    ->delete();
            }

            // 2. Compute package totals
            $packagesData = $request->input('packages', []);
            $totalPkgSell = 0;
            $totalPkgCost = 0;
            $firstSubCatId = $event?->event_sub_category_id;
            $firstPkgName = $event?->package_name;

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
            } else {
                $totalPkgSell = (float) $request->input('package_sell_price', $event?->package_sell_price ?? 0);
                $totalPkgCost = (float) ($event?->package_cost_price ?? 0);
                $firstPkgName = $request->input('package_name', $event?->package_name ?? $event?->subCategory?->name);
            }

            // 3. Create Order
            $order = Order::create([
                'event_id' => $validated['event_id'],
                'customer_id' => $validated['customer_id'],
                'event_sub_category_id' => $firstSubCatId,
                'package_name' => $firstPkgName,
                'package_cost_price' => $totalPkgCost,
                'package_sell_price' => $totalPkgSell,
                'order_date' => $validated['order_date'],
                'discount' => $validated['discount'] ?? 0,
                'notes' => $validated['notes'] ?? null,
                'sub_total' => 0,
                'total_amount' => 0,
                'paid_amount' => 0,
                'balance' => 0,
            ]);

            // 4. Save Order Packages and sync new packages to Event
            if (!empty($packagesData)) {
                foreach ($packagesData as $p) {
                    if (!empty($p['package_name']) || !empty($p['event_sub_category_id'])) {
                        $epkgId = !empty($p['event_package_id']) ? $p['event_package_id'] : null;
                        $subCatId = !empty($p['event_sub_category_id']) ? $p['event_sub_category_id'] : null;
                        $pName = $p['package_name'] ?? 'Package';
                        $qty = (int)($p['qty'] ?? 1);
                        $sell = (float)($p['sell_price'] ?? 0);
                        $cost = (float)($p['cost_price'] ?? 0);
                        $lineTotal = (float)($p['total_price'] ?? ($sell * $qty));

                        // Sync newly added package to event
                        if (!$epkgId && $event) {
                            $newEventPkg = EventPackage::create([
                                'event_id' => $event->id,
                                'event_sub_category_id' => $subCatId,
                                'package_name' => $pName,
                                'cost_price' => $cost,
                                'sell_price' => $sell,
                                'qty' => $qty,
                                'total_price' => $lineTotal,
                            ]);
                            $epkgId = $newEventPkg->id;
                        }

                        OrderPackage::create([
                            'order_id' => $order->id,
                            'event_package_id' => $epkgId,
                            'event_sub_category_id' => $subCatId,
                            'package_name' => $pName,
                            'cost_price' => $cost,
                            'sell_price' => $sell,
                            'qty' => $qty,
                            'total_price' => $lineTotal,
                        ]);

                        OrderService::create([
                            'order_id' => $order->id,
                            'service_id' => null,
                            'is_package_item' => true,
                            'description' => $pName . ' (Package)',
                            'qty' => $qty,
                            'unit_price' => $sell,
                            'total_price' => $lineTotal,
                        ]);
                    }
                }
            } elseif ($totalPkgSell > 0 || $firstPkgName) {
                OrderPackage::create([
                    'order_id' => $order->id,
                    'event_package_id' => null,
                    'event_sub_category_id' => $firstSubCatId,
                    'package_name' => $firstPkgName ?: 'Event Package',
                    'cost_price' => $totalPkgCost,
                    'sell_price' => $totalPkgSell,
                    'qty' => 1,
                    'total_price' => $totalPkgSell,
                ]);

                OrderService::create([
                    'order_id' => $order->id,
                    'service_id' => null,
                    'is_package_item' => true,
                    'description' => ($firstPkgName ?: 'Event Package') . ' (Base Package)',
                    'qty' => 1,
                    'unit_price' => $totalPkgSell,
                    'total_price' => $totalPkgSell,
                ]);
            }

            // 5. Save Services & sync new services to Event
            if ($request->has('services')) {
                foreach ($request->services as $svc) {
                    if (!empty($svc['description'])) {
                        $qty = (int)($svc['qty'] ?? 1);
                        $uPrice = (float)($svc['unit_price'] ?? 0);
                        $tPrice = (float)($svc['total_price'] ?? ($uPrice * $qty));

                        OrderService::create([
                            'order_id' => $order->id,
                            'service_id' => $svc['service_id'] ?? null,
                            'description' => $svc['description'],
                            'qty' => $qty,
                            'unit_price' => $uPrice,
                            'total_price' => $tPrice,
                        ]);

                        // Sync new service to event if not already present
                        if ($event) {
                            $existsOnEvent = $event->additionalServices()
                                ->where(function($q) use ($svc) {
                                    if (!empty($svc['service_id'])) {
                                        $q->where('service_id', $svc['service_id']);
                                    } else {
                                        $q->where('description', $svc['description']);
                                    }
                                })->exists();

                            if (!$existsOnEvent) {
                                EventService::create([
                                    'event_id' => $event->id,
                                    'service_id' => $svc['service_id'] ?? null,
                                    'description' => $svc['description'],
                                    'qty' => $qty,
                                    'unit_price' => $uPrice,
                                    'total_price' => $tPrice,
                                ]);
                            }
                        }
                    }
                }
            }

            // 6. Save Other Charges & sync new charges to Event
            if ($request->has('other_charges')) {
                foreach ($request->other_charges as $charge) {
                    if (!empty($charge['description'])) {
                        $amt = (float)($charge['amount'] ?? 0);
                        OrderOtherCharge::create([
                            'order_id' => $order->id,
                            'description' => $charge['description'],
                            'amount' => $amt,
                        ]);

                        // Sync new charge to event if not already present
                        if ($event) {
                            $existsOnEvent = $event->otherCharges()->where('description', $charge['description'])->exists();
                            if (!$existsOnEvent) {
                                EventOtherCharge::create([
                                    'event_id' => $event->id,
                                    'description' => $charge['description'],
                                    'amount' => $amt,
                                ]);
                            }
                        }
                    }
                }
            }

            if ($event) {
                $event->recalculateTotals();
            }
            $order->recalculateTotals();
        });

        \App\Services\AuditLogger::log(
            'created',
            'Order',
            "Order #{$order->order_no}",
            "Created order #{$order->order_no} for customer " . ($order->customer?->name ?? 'N/A') . " (Total: Rs. " . number_format($order->total_amount, 2) . ")",
            $order->id,
            null,
            $order->toArray()
        );

        // Send customer SMS with invoice details via Fit SMS (Mask: CEYLON WEDS)
        \App\Services\FitSmsService::sendOrderCreatedSms($order);

        return redirect()->route('orders.show', $order)->with('success', 'Order created successfully!');
    }

    public function show(Order $order)
    {
        $order->load([
            'customer',
            'packages.subCategory.category',
            'packages.subCategory.subCategoryServices.service',
            'event.subCategory.category',
            'event.subCategory.subCategoryServices.service',
            'event.packages.subCategory.category',
            'event.packages.subCategory.subCategoryServices.service',
            'services.service',
            'otherCharges',
            'payments'
        ]);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $categories = EventCategory::where('is_active', true)->get();
        $events = Event::with('customer')->orderBy('event_date')->get();
        $services = Service::where('is_active', true)->get();
        $order->load([
            'customer',
            'packages.subCategory.category',
            'packages.subCategory.subCategoryServices.service',
            'services.service',
            'otherCharges',
            'event.subCategory.category',
            'event.subCategory.subCategoryServices.service',
            'event.packages.subCategory.category',
            'event.packages.subCategory.subCategoryServices.service'
        ]);
        return view('orders.edit', compact('order', 'categories', 'services', 'customers', 'events'));
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
            'packages' => 'nullable|array',
            'services' => 'nullable|array',
            'other_charges' => 'nullable|array',
            'removed_event_package_ids' => 'nullable|array',
        ]);

        $oldValues = $order->toArray();

        DB::transaction(function () use ($request, $validated, $order) {
            $event = !empty($validated['event_id']) ? Event::with(['subCategory', 'additionalServices', 'otherCharges', 'packages'])->find($validated['event_id']) : $order->event;

            // 1. Delete removed event packages
            if ($request->filled('removed_event_package_ids') && $event) {
                EventPackage::where('event_id', $event->id)
                    ->whereIn('id', $request->input('removed_event_package_ids'))
                    ->delete();
            }

            // 2. Compute package totals
            $packagesData = $request->input('packages', []);
            $totalPkgSell = 0;
            $totalPkgCost = 0;
            $firstSubCatId = $event?->event_sub_category_id;
            $firstPkgName = $event?->package_name;

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
            } else {
                $totalPkgSell = (float) $request->input('package_sell_price', $order->package_sell_price ?? 0);
                $totalPkgCost = (float) ($order->package_cost_price ?? 0);
                $firstPkgName = $request->input('package_name', $order->package_name ?? $event?->subCategory?->name);
            }

            $updateData = $validated;
            if ($firstSubCatId) {
                $updateData['event_sub_category_id'] = $firstSubCatId;
            }
            $updateData['package_name'] = $firstPkgName;
            $updateData['package_cost_price'] = $totalPkgCost;
            $updateData['package_sell_price'] = $totalPkgSell;

            $order->update($updateData);

            // 3. Rebuild order_packages & package line items
            $order->packages()->delete();
            $order->services()->delete();

            if (!empty($packagesData)) {
                foreach ($packagesData as $p) {
                    if (!empty($p['package_name']) || !empty($p['event_sub_category_id'])) {
                        $epkgId = !empty($p['event_package_id']) ? $p['event_package_id'] : null;
                        $subCatId = !empty($p['event_sub_category_id']) ? $p['event_sub_category_id'] : null;
                        $pName = $p['package_name'] ?? 'Package';
                        $qty = (int)($p['qty'] ?? 1);
                        $sell = (float)($p['sell_price'] ?? 0);
                        $cost = (float)($p['cost_price'] ?? 0);
                        $lineTotal = (float)($p['total_price'] ?? ($sell * $qty));

                        // Sync newly added package to event
                        if (!$epkgId && $event) {
                            $newEventPkg = EventPackage::create([
                                'event_id' => $event->id,
                                'event_sub_category_id' => $subCatId,
                                'package_name' => $pName,
                                'cost_price' => $cost,
                                'sell_price' => $sell,
                                'qty' => $qty,
                                'total_price' => $lineTotal,
                            ]);
                            $epkgId = $newEventPkg->id;
                        }

                        OrderPackage::create([
                            'order_id' => $order->id,
                            'event_package_id' => $epkgId,
                            'event_sub_category_id' => $subCatId,
                            'package_name' => $pName,
                            'cost_price' => $cost,
                            'sell_price' => $sell,
                            'qty' => $qty,
                            'total_price' => $lineTotal,
                        ]);

                        OrderService::create([
                            'order_id' => $order->id,
                            'service_id' => null,
                            'is_package_item' => true,
                            'description' => $pName . ' (Package)',
                            'qty' => $qty,
                            'unit_price' => $sell,
                            'total_price' => $lineTotal,
                        ]);
                    }
                }
            } elseif ($totalPkgSell > 0 || $firstPkgName) {
                OrderPackage::create([
                    'order_id' => $order->id,
                    'event_package_id' => null,
                    'event_sub_category_id' => $firstSubCatId,
                    'package_name' => $firstPkgName ?: 'Event Package',
                    'cost_price' => $totalPkgCost,
                    'sell_price' => $totalPkgSell,
                    'qty' => 1,
                    'total_price' => $totalPkgSell,
                ]);

                OrderService::create([
                    'order_id' => $order->id,
                    'service_id' => null,
                    'is_package_item' => true,
                    'description' => ($firstPkgName ?: 'Event Package') . ' (Base Package)',
                    'qty' => 1,
                    'unit_price' => $totalPkgSell,
                    'total_price' => $totalPkgSell,
                ]);
            }

            // 4. Rebuild services & sync new to Event
            if ($request->has('services')) {
                foreach ($request->services as $svc) {
                    if (!empty($svc['description'])) {
                        $qty = (int)($svc['qty'] ?? 1);
                        $uPrice = (float)($svc['unit_price'] ?? 0);
                        $tPrice = (float)($svc['total_price'] ?? ($uPrice * $qty));

                        OrderService::create([
                            'order_id' => $order->id,
                            'service_id' => $svc['service_id'] ?? null,
                            'description' => $svc['description'],
                            'qty' => $qty,
                            'unit_price' => $uPrice,
                            'total_price' => $tPrice,
                        ]);

                        if ($event) {
                            $existsOnEvent = $event->additionalServices()
                                ->where(function($q) use ($svc) {
                                    if (!empty($svc['service_id'])) {
                                        $q->where('service_id', $svc['service_id']);
                                    } else {
                                        $q->where('description', $svc['description']);
                                    }
                                })->exists();

                            if (!$existsOnEvent) {
                                EventService::create([
                                    'event_id' => $event->id,
                                    'service_id' => $svc['service_id'] ?? null,
                                    'description' => $svc['description'],
                                    'qty' => $qty,
                                    'unit_price' => $uPrice,
                                    'total_price' => $tPrice,
                                ]);
                            }
                        }
                    }
                }
            }

            // 5. Rebuild other charges & sync new to Event
            $order->otherCharges()->delete();
            if ($request->has('other_charges')) {
                foreach ($request->other_charges as $charge) {
                    if (!empty($charge['description'])) {
                        $amt = (float)($charge['amount'] ?? 0);
                        OrderOtherCharge::create([
                            'order_id' => $order->id,
                            'description' => $charge['description'],
                            'amount' => $amt,
                        ]);

                        if ($event) {
                            $existsOnEvent = $event->otherCharges()->where('description', $charge['description'])->exists();
                            if (!$existsOnEvent) {
                                EventOtherCharge::create([
                                    'event_id' => $event->id,
                                    'description' => $charge['description'],
                                    'amount' => $amt,
                                ]);
                            }
                        }
                    }
                }
            }

            if ($event) {
                $event->recalculateTotals();
            }
            $order->recalculateTotals();
        });

        \App\Services\AuditLogger::log(
            'updated',
            'Order',
            "Order #{$order->order_no}",
            "Updated order #{$order->order_no} (Total: Rs. " . number_format($order->total_amount, 2) . ")",
            $order->id,
            $oldValues,
            $order->fresh()->toArray()
        );

        return redirect()->route('orders.show', $order)->with('success', 'Order updated successfully!');
    }

    public function destroy(Order $order)
    {
        $hasPayments = $order->payments()->exists() || (float)$order->paid_amount > 0;

        if ($hasPayments) {
            return redirect()->back()->with('error', "Cannot delete Order #{$order->order_no} because payments totaling Rs. " . number_format($order->paid_amount, 2) . " have already been made towards it. Deletion is strictly prevented to protect accounting integrity.");
        }

        $orderTitle = "Order #{$order->order_no}";
        $oldValues = $order->toArray();

        DB::transaction(function () use ($order) {
            $order->packages()->delete();
            $order->services()->delete();
            $order->otherCharges()->delete();
            $order->delete();
        });

        \App\Services\AuditLogger::log('deleted', 'Order', $orderTitle, "Deleted order {$orderTitle}", $order->id, $oldValues, null);

        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }

    public function getByEvent($eventId)
    {
        $orders = Order::with('payments')->where('event_id', $eventId)->get();
        return response()->json($orders);
    }

    public function apiGetEventDetails($eventId)
    {
        $event = Event::with([
            'customer',
            'subCategory.category',
            'subCategory.subCategoryServices.service',
            'packages.subCategory.category',
            'packages.subCategory.subCategoryServices.service',
            'additionalServices.service',
            'otherCharges'
        ])->findOrFail($eventId);

        // Build list of event packages
        $packagesList = [];
        if ($event->packages && $event->packages->isNotEmpty()) {
            foreach ($event->packages as $pkg) {
                $subCat = $pkg->subCategory;
                $packagesList[] = [
                    'event_package_id' => $pkg->id,
                    'event_sub_category_id' => $pkg->event_sub_category_id,
                    'category_name' => $subCat?->category?->name ?? 'General Category',
                    'package_name' => $pkg->package_name,
                    'cost_price' => (float) $pkg->cost_price,
                    'sell_price' => (float) $pkg->sell_price,
                    'qty' => (int) $pkg->qty,
                    'total_price' => (float) $pkg->total_price,
                    'services' => $subCat?->subCategoryServices->map(fn($s) => [
                        'service_id' => $s->service_id,
                        'name' => $s->service?->name ?? 'Service',
                        'qty' => $s->qty,
                    ])->toArray() ?? [],
                ];
            }
        } elseif ($event->event_sub_category_id || $event->package_sell_price > 0) {
            // Fallback for legacy single-package events
            $subCat = $event->subCategory;
            $packagesList[] = [
                'event_package_id' => null,
                'event_sub_category_id' => $event->event_sub_category_id,
                'category_name' => $subCat?->category?->name ?? 'General Category',
                'package_name' => $event->package_name ?? $subCat?->name ?? 'Base Package',
                'cost_price' => (float) $event->package_cost_price,
                'sell_price' => (float) $event->package_sell_price,
                'qty' => 1,
                'total_price' => (float) $event->package_sell_price,
                'services' => $subCat?->subCategoryServices->map(fn($s) => [
                    'service_id' => $s->service_id,
                    'name' => $s->service?->name ?? 'Service',
                    'qty' => $s->qty,
                ])->toArray() ?? [],
            ];
        }

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
            'packages' => $packagesList,
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
