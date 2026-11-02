<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = ExpenseCategory::withCount('expenses');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status !== null && $status !== '' && $status !== 'all') {
            if ($status === 'active' || $status === '1') {
                $query->where('status', 'active');
            } elseif ($status === 'inactive' || $status === '0') {
                $query->where('status', 'inactive');
            }
        }

        $categories = $query->latest()->paginate(25)->withQueryString();
        return view('expense-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('expense-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name',
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|in:active,inactive',
        ]);

        if ($request->has('is_active')) {
            $validated['status'] = $request->boolean('is_active') ? 'active' : 'inactive';
        } elseif ($request->filled('status')) {
            $validated['status'] = $request->input('status');
        } else {
            $validated['status'] = 'inactive';
        }

        ExpenseCategory::create($validated);

        return redirect()->route('expense-categories.create')
            ->with('success', 'Expense category created successfully.');
    }

    public function show(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->load(['expenses' => function ($q) {
            $q->latest('expense_date')->take(20);
        }]);
        $expenseCategory->loadCount('expenses');

        return view('expense-categories.show', compact('expenseCategory'));
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        return view('expense-categories.edit', compact('expenseCategory'));
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $expenseCategory->id,
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|in:active,inactive',
        ]);

        if ($request->has('is_active')) {
            $validated['status'] = $request->boolean('is_active') ? 'active' : 'inactive';
        } elseif ($request->filled('status')) {
            $validated['status'] = $request->input('status');
        } else {
            $validated['status'] = 'inactive';
        }

        $expenseCategory->update($validated);

        return redirect()->route('expense-categories.index')
            ->with('success', 'Expense category updated successfully.');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->expenses()->count() > 0) {
            return redirect()->route('expense-categories.index')
                ->with('error', 'Cannot delete this expense category because it is linked to existing expense records.');
        }

        $expenseCategory->delete();

        return redirect()->route('expense-categories.index')
            ->with('success', 'Expense category deleted successfully.');
    }
}
