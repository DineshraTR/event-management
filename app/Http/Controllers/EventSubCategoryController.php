<?php

namespace App\Http\Controllers;

use App\Models\EventSubCategory;
use App\Models\EventCategory;
use App\Models\Service;
use App\Models\EventSubCategoryService;
use Illuminate\Http\Request;

class EventSubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = EventSubCategory::with('category');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status !== null && $status !== '' && $status !== 'all') {
            if ($status === 'active' || $status === '1') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive' || $status === '0') {
                $query->where('is_active', false);
            }
        }

        $subCategories = $query->latest()->paginate(25)->withQueryString();
        return view('event-sub-categories.index', compact('subCategories'));
    }

    public function create()
    {
        $categories = EventCategory::where('is_active', true)->get();
        $services = Service::where('is_active', true)->get();
        return view('event-sub-categories.create', compact('categories', 'services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_category_id' => 'required|exists:event_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sell_price' => 'required|numeric|min:0',
            'services' => 'nullable|array',
            'services.*.service_id' => 'exists:services,id',
            'services.*.qty' => 'integer|min:1',
            'services.*.cost_price' => 'numeric|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['cost_price'] = 0;

        $subCategory = EventSubCategory::create($validated);

        // Attach services
        $totalCost = 0;
        if (!empty($request->services)) {
            foreach ($request->services as $svc) {
                if (!empty($svc['service_id'])) {
                    EventSubCategoryService::create([
                        'event_sub_category_id' => $subCategory->id,
                        'service_id' => $svc['service_id'],
                        'qty' => $svc['qty'] ?? 1,
                        'cost_price' => $svc['cost_price'],
                    ]);
                    $totalCost += $svc['cost_price'] * ($svc['qty'] ?? 1);
                }
            }
        }

        $subCategory->update(['cost_price' => $totalCost]);

        return redirect()->route('event-sub-categories.index')
            ->with('success', 'Sub-category created successfully.');
    }

    public function show(EventSubCategory $eventSubCategory)
    {
        $eventSubCategory->load(['category', 'subCategoryServices.service']);
        return view('event-sub-categories.show', compact('eventSubCategory'));
    }

    public function edit(EventSubCategory $eventSubCategory)
    {
        $categories = EventCategory::where('is_active', true)->get();
        $services = Service::where('is_active', true)->get();
        $eventSubCategory->load('subCategoryServices.service');
        return view('event-sub-categories.edit', compact('eventSubCategory', 'categories', 'services'));
    }

    public function update(Request $request, EventSubCategory $eventSubCategory)
    {
        $validated = $request->validate([
            'event_category_id' => 'required|exists:event_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sell_price' => 'required|numeric|min:0',
            'services' => 'nullable|array',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Delete old services and re-add
        $eventSubCategory->subCategoryServices()->delete();

        $totalCost = 0;
        if (!empty($request->services)) {
            foreach ($request->services as $svc) {
                if (!empty($svc['service_id'])) {
                    EventSubCategoryService::create([
                        'event_sub_category_id' => $eventSubCategory->id,
                        'service_id' => $svc['service_id'],
                        'qty' => $svc['qty'] ?? 1,
                        'cost_price' => $svc['cost_price'],
                    ]);
                    $totalCost += $svc['cost_price'] * ($svc['qty'] ?? 1);
                }
            }
        }

        $validated['cost_price'] = $totalCost;
        $eventSubCategory->update($validated);

        return redirect()->route('event-sub-categories.index')
            ->with('success', 'Sub-category updated successfully.');
    }

    public function destroy(EventSubCategory $eventSubCategory)
    {
        $eventSubCategory->delete();
        return redirect()->route('event-sub-categories.index')
            ->with('success', 'Sub-category deleted.');
    }

    // API: Get sub-category details (for event form)
    public function apiGetSubCategory($id)
    {
        $sub = EventSubCategory::with(['subCategoryServices.service'])->findOrFail($id);
        return response()->json([
            'id' => $sub->id,
            'name' => $sub->name,
            'cost_price' => $sub->cost_price,
            'sell_price' => $sub->sell_price,
            'services' => $sub->subCategoryServices->map(fn($s) => [
                'service_id' => $s->service_id,
                'service_name' => $s->service?->name ?? ('Service #' . $s->service_id),
                'qty' => $s->qty,
                'unit_price' => $s->cost_price,
                'total_price' => $s->cost_price * $s->qty,
            ]),
        ]);
    }

    // API: Get sub-categories by category
    public function apiGetByCategory($categoryId)
    {
        $subs = EventSubCategory::where('event_category_id', $categoryId)
            ->where('is_active', true)
            ->get(['id', 'name', 'sell_price', 'cost_price']);
        return response()->json($subs);
    }

    public function getServices(EventSubCategory $subCategory)
    {
        $subCategory->load('subCategoryServices.service');
        return response()->json($subCategory->subCategoryServices);
    }
}
