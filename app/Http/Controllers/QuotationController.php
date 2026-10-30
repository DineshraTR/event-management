<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
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

        $query = Quotation::with(['customer', 'subCategory.category']);

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
        ]);

        DB::transaction(function () use ($request, $validated, &$quotation) {
            $subCategory = !empty($validated['event_sub_category_id']) 
                ? EventSubCategory::find($validated['event_sub_category_id']) 
                : null;
            $packageSellPrice = $subCategory ? (float) $request->input('package_sell_price', $subCategory->sell_price) : 0;
            $packageName = $subCategory ? $subCategory->name : null;
            $packageCostPrice = $subCategory ? (float) $subCategory->cost_price : 0;

            $validated['package_name'] = $packageName;
            $validated['package_cost_price'] = $packageCostPrice;
            $validated['package_sell_price'] = $packageSellPrice;

            $quotation = Quotation::create($validated);

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

            $subTotal = $packageSellPrice + $servicesTotal + $chargesTotal;
            $discount = (float) ($validated['discount'] ?? 0);
            $totalAmount = max(0, $subTotal - $discount);

            $quotation->update([
                'additional_services_total' => $servicesTotal,
                'other_charges_total' => $chargesTotal,
                'sub_total' => $subTotal,
                'total_amount' => $totalAmount,
            ]);
        });

        return redirect()->route('quotations.show', $quotation)->with('success', 'Quotation created!');
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['customer', 'subCategory.category', 'subCategory.subCategoryServices.service', 'services.service', 'otherCharges']);
        return view('quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $categories = EventCategory::where('is_active', true)->get();
        $services = Service::where('is_active', true)->get();
        $quotation->load(['customer', 'subCategory.category', 'subCategory.subCategoryServices.service', 'services.service', 'otherCharges']);
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
        ]);

        DB::transaction(function () use ($request, $validated, $quotation) {
            $subCategory = !empty($validated['event_sub_category_id']) 
                ? EventSubCategory::find($validated['event_sub_category_id']) 
                : null;
            $packageSellPrice = $subCategory ? (float) $request->input('package_sell_price', $subCategory->sell_price) : 0;
            $packageName = $subCategory ? $subCategory->name : null;
            $packageCostPrice = $subCategory ? (float) $subCategory->cost_price : 0;

            $validated['package_name'] = $packageName;
            $validated['package_cost_price'] = $packageCostPrice;
            $validated['package_sell_price'] = $packageSellPrice;

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

            $subTotal = $packageSellPrice + $servicesTotal + $chargesTotal;
            $discount = (float) ($validated['discount'] ?? 0);
            $totalAmount = max(0, $subTotal - $discount);

            $validated['additional_services_total'] = $servicesTotal;
            $validated['other_charges_total'] = $chargesTotal;
            $validated['sub_total'] = $subTotal;
            $validated['total_amount'] = $totalAmount;

            $quotation->update($validated);
        });

        return redirect()->route('quotations.show', $quotation)->with('success', 'Quotation updated!');
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->delete();
        return redirect()->route('quotations.index')->with('success', 'Quotation deleted.');
    }

    public function print(Quotation $quotation)
    {
        $quotation->load(['customer', 'subCategory.category', 'subCategory.subCategoryServices.service', 'services.service', 'otherCharges']);
        return view('quotations.print', compact('quotation'));
    }

    public function convertToEvent(Quotation $quotation)
    {
        $quotation->update(['status' => 'accepted']);
        return redirect()->route('events.create', ['quotation_id' => $quotation->id])
            ->with('info', 'Quotation loaded. Complete event registration.');
    }
}
