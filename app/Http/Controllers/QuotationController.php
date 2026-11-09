<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationPackage;
use App\Models\QuotationService;
use App\Models\QuotationOtherCharge;
use App\Models\Customer;
use App\Models\EventSubCategory;
use App\Models\EventCategory;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $customerId = $request->input('customer_id');
        $subCategoryId = $request->input('sub_category_id');
        $status = $request->input('status');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $query = Quotation::with(['customer', 'packages', 'subCategory.category']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('quotation_no', 'like', "%{$search}%")
                  ->orWhere('event_name', 'like', "%{$search}%")
                  ->orWhere('venue', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        if ($subCategoryId) {
            $query->where('event_sub_category_id', $subCategoryId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($fromDate) {
            $query->whereDate('quotation_date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('quotation_date', '<=', $toDate);
        }

        $quotations = $query->latest('quotation_date')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $subCategories = EventSubCategory::with('category')->where('is_active', true)->orderBy('name')->get();

        $totalQuotationsCount = Quotation::count();
        $draftCount = Quotation::where('status', 'draft')->count();
        $sentCount = Quotation::where('status', 'sent')->count();
        $acceptedCount = Quotation::where('status', 'accepted')->count();
        $rejectedCount = Quotation::where('status', 'rejected')->count();
        $totalQuotedValue = Quotation::sum('total_amount');

        return view('quotations.index', compact(
            'quotations',
            'customers',
            'subCategories',
            'totalQuotationsCount',
            'draftCount',
            'sentCount',
            'acceptedCount',
            'rejectedCount',
            'totalQuotedValue'
        ));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $categories = EventCategory::where('is_active', true)->get();
        $services = Service::where('is_active', true)->get();
        $today = Carbon::today()->format('Y-m-d');
        return view('quotations.create', compact('customers', 'categories', 'services', 'today'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'event_sub_category_id' => 'nullable|exists:event_sub_categories,id',
            'package_sell_price' => 'nullable|numeric|min:0',
            'quotation_date' => 'required|date',
            'valid_until' => 'nullable|date',
            'event_name' => 'nullable|string|max:255',
            'event_date' => 'nullable|date',
            'venue' => 'nullable|string|max:255',
            'discount' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
            'packages' => 'nullable|array',
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
        $validated['package_cost_price'] = $totalPkgCost;
        $validated['package_sell_price'] = $totalPkgSell;

        DB::transaction(function () use ($request, $validated, $packagesData, &$quotation) {
            $quotation = Quotation::create($validated);

            // Save quotation_packages
            if (!empty($packagesData)) {
                foreach ($packagesData as $p) {
                    if (!empty($p['package_name']) || !empty($p['event_sub_category_id'])) {
                        $qty = (int)($p['qty'] ?? 1);
                        $sell = (float)($p['sell_price'] ?? 0);
                        QuotationPackage::create([
                            'quotation_id' => $quotation->id,
                            'event_sub_category_id' => !empty($p['event_sub_category_id']) ? $p['event_sub_category_id'] : null,
                            'package_name' => $p['package_name'] ?? 'Package',
                            'description' => $p['description'] ?? null,
                            'cost_price' => (float)($p['cost_price'] ?? 0),
                            'sell_price' => $sell,
                            'qty' => $qty,
                            'total_price' => (float)($p['total_price'] ?? ($sell * $qty)),
                        ]);
                    }
                }
            } elseif ($quotation->subCategory || $quotation->package_sell_price > 0) {
                QuotationPackage::create([
                    'quotation_id' => $quotation->id,
                    'event_sub_category_id' => $quotation->event_sub_category_id,
                    'package_name' => $quotation->package_name ?? $quotation->subCategory?->name ?? 'Package',
                    'description' => $quotation->subCategory?->description ?? null,
                    'cost_price' => $quotation->package_cost_price ?? 0,
                    'sell_price' => $quotation->package_sell_price ?? 0,
                    'qty' => 1,
                    'total_price' => $quotation->package_sell_price ?? 0,
                ]);
            }

            $servicesTotal = 0;
            if ($request->has('services')) {
                foreach ($request->services as $svc) {
                    if (!empty($svc['description'])) {
                        $lineTotal = ($svc['unit_price'] ?? 0) * ($svc['qty'] ?? 1);
                        QuotationService::create([
                            'quotation_id' => $quotation->id,
                            'service_id' => $svc['service_id'] ?? null,
                            'description' => $svc['description'],
                            'qty' => $svc['qty'] ?? 1,
                            'unit_price' => $svc['unit_price'] ?? 0,
                            'total_price' => $lineTotal,
                        ]);
                        $servicesTotal += $lineTotal;
                    }
                }
            }

            $chargesTotal = 0;
            if ($request->has('other_charges')) {
                foreach ($request->other_charges as $charge) {
                    if (!empty($charge['description'])) {
                        QuotationOtherCharge::create([
                            'quotation_id' => $quotation->id,
                            'description' => $charge['description'],
                            'amount' => $charge['amount'] ?? 0,
                        ]);
                        $chargesTotal += (float) ($charge['amount'] ?? 0);
                    }
                }
            }

            $quotation->recalculateTotals();
        });

        \App\Services\AuditLogger::log(
            'created',
            'Quotation',
            "Quotation #{$quotation->quotation_no} - {$quotation->event_name}",
            "Created quotation #{$quotation->quotation_no} for customer " . ($quotation->customer?->name ?? 'N/A') . " (Total: Rs. " . number_format($quotation->total_amount, 2) . ")",
            $quotation->id,
            null,
            $quotation->toArray()
        );

        // Send customer SMS via Fit SMS (Mask: CEYLON WEDS)
        \App\Services\FitSmsService::sendQuotationCreatedSms($quotation);

        return redirect()->route('quotations.show', $quotation)->with('success', 'Quotation created!');
    }

    public function show(Quotation $quotation)
    {
        $quotation->recalculateTotals();
        $quotation->load([
            'customer',
            'subCategory.category',
            'subCategory.subCategoryServices.service',
            'packages.subCategory.category',
            'packages.subCategory.subCategoryServices.service',
            'services.service',
            'otherCharges'
        ]);
        return view('quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $categories = EventCategory::where('is_active', true)->get();
        $services = Service::where('is_active', true)->get();
        $quotation->load([
            'customer',
            'subCategory.category',
            'subCategory.subCategoryServices.service',
            'packages.subCategory.category',
            'packages.subCategory.subCategoryServices.service',
            'services.service',
            'otherCharges'
        ]);
        return view('quotations.edit', compact('quotation', 'customers', 'categories', 'services'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'event_sub_category_id' => 'nullable|exists:event_sub_categories,id',
            'package_sell_price' => 'nullable|numeric|min:0',
            'quotation_date' => 'required|date',
            'valid_until' => 'nullable|date',
            'event_name' => 'nullable|string|max:255',
            'event_date' => 'nullable|date',
            'venue' => 'nullable|string|max:255',
            'discount' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,sent,accepted,rejected',
            'remarks' => 'nullable|string',
            'packages' => 'nullable|array',
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
        $validated['package_cost_price'] = $totalPkgCost;
        $validated['package_sell_price'] = $totalPkgSell;

        $oldValues = $quotation->toArray();

        DB::transaction(function () use ($request, $validated, $packagesData, $quotation) {
            $quotation->update($validated);

            // Rebuild quotation_packages
            $quotation->packages()->delete();
            if (!empty($packagesData)) {
                foreach ($packagesData as $p) {
                    if (!empty($p['package_name']) || !empty($p['event_sub_category_id'])) {
                        $qty = (int)($p['qty'] ?? 1);
                        $sell = (float)($p['sell_price'] ?? 0);
                        QuotationPackage::create([
                            'quotation_id' => $quotation->id,
                            'event_sub_category_id' => !empty($p['event_sub_category_id']) ? $p['event_sub_category_id'] : null,
                            'package_name' => $p['package_name'] ?? 'Package',
                            'description' => $p['description'] ?? null,
                            'cost_price' => (float)($p['cost_price'] ?? 0),
                            'sell_price' => $sell,
                            'qty' => $qty,
                            'total_price' => (float)($p['total_price'] ?? ($sell * $qty)),
                        ]);
                    }
                }
            } elseif ($quotation->subCategory || $quotation->package_sell_price > 0) {
                QuotationPackage::create([
                    'quotation_id' => $quotation->id,
                    'event_sub_category_id' => $quotation->event_sub_category_id,
                    'package_name' => $quotation->package_name ?? $quotation->subCategory?->name ?? 'Package',
                    'description' => $quotation->subCategory?->description ?? null,
                    'cost_price' => $quotation->package_cost_price ?? 0,
                    'sell_price' => $quotation->package_sell_price ?? 0,
                    'qty' => 1,
                    'total_price' => $quotation->package_sell_price ?? 0,
                ]);
            }

            $quotation->services()->delete();
            $quotation->otherCharges()->delete();

            $servicesTotal = 0;
            if ($request->has('services')) {
                foreach ($request->services as $svc) {
                    if (!empty($svc['description'])) {
                        $lineTotal = ($svc['unit_price'] ?? 0) * ($svc['qty'] ?? 1);
                        QuotationService::create([
                            'quotation_id' => $quotation->id,
                            'service_id' => $svc['service_id'] ?? null,
                            'description' => $svc['description'],
                            'qty' => $svc['qty'] ?? 1,
                            'unit_price' => $svc['unit_price'] ?? 0,
                            'total_price' => $lineTotal,
                        ]);
                        $servicesTotal += $lineTotal;
                    }
                }
            }

            $chargesTotal = 0;
            if ($request->has('other_charges')) {
                foreach ($request->other_charges as $charge) {
                    if (!empty($charge['description'])) {
                        QuotationOtherCharge::create([
                            'quotation_id' => $quotation->id,
                            'description' => $charge['description'],
                            'amount' => $charge['amount'] ?? 0,
                        ]);
                        $chargesTotal += (float) ($charge['amount'] ?? 0);
                    }
                }
            }

            $quotation->recalculateTotals();
        });

        \App\Services\AuditLogger::log(
            'updated',
            'Quotation',
            "Quotation #{$quotation->quotation_no} - {$quotation->event_name}",
            "Updated quotation #{$quotation->quotation_no}",
            $quotation->id,
            $oldValues,
            $quotation->fresh()->toArray()
        );

        return redirect()->route('quotations.show', $quotation)->with('success', 'Quotation updated!');
    }

    public function destroy(Quotation $quotation)
    {
        $quoTitle = "Quotation #{$quotation->quotation_no} - {$quotation->event_name}";
        $oldValues = $quotation->toArray();

        $quotation->delete();

        \App\Services\AuditLogger::log('deleted', 'Quotation', $quoTitle, "Deleted quotation {$quoTitle}", $quotation->id, $oldValues, null);

        return redirect()->route('quotations.index')->with('success', 'Quotation deleted successfully.');
    }

    public function print(Quotation $quotation)
    {
        $quotation->recalculateTotals();
        $quotation->load([
            'customer',
            'subCategory.category',
            'subCategory.subCategoryServices.service',
            'packages.subCategory.category',
            'packages.subCategory.subCategoryServices.service',
            'services.service',
            'otherCharges'
        ]);
        return view('quotations.print', compact('quotation'));
    }

    public function convertToEvent(Quotation $quotation)
    {
        $quotation->update(['status' => 'accepted']);

        \App\Services\AuditLogger::log(
            'converted',
            'Quotation',
            "Quotation #{$quotation->quotation_no}",
            "Converted quotation #{$quotation->quotation_no} to Event",
            $quotation->id
        );

        return redirect()->route('events.create', ['quotation_id' => $quotation->id])
            ->with('info', 'Quotation loaded. Complete event registration.');
    }
}
