@extends('layouts.app')
@section('title', $expenseCategory->name . ' - Expense Category Details')
@section('page-title', 'Expense Category Details')

@section('content')
<!-- Page Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0"
            style="width: 44px; height: 44px; background: linear-gradient(135deg, #484646 0%, #3a3838 100%); font-size: 20px;">
            <i class="bi bi-tags-fill"></i>
        </div>
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.3px;">{{ $expenseCategory->name }}</h4>
                <span class="badge bg-light text-dark border px-2 py-0.5" style="font-size: 11px; font-weight: 700;">
                    #EXPCAT-{{ str_pad($expenseCategory->id, 4, '0', STR_PAD_LEFT) }}
                </span>
                @if($expenseCategory->is_active)
                    <span class="badge bg-success-subtle text-success border border-success-subtle d-inline-flex align-items-center gap-1.5 px-2 py-1" style="font-size: 11px; font-weight: 600;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #22c55e;"></span> Active
                    </span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle d-inline-flex align-items-center gap-1.5 px-2 py-1" style="font-size: 11px; font-weight: 600;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #94a3b8;"></span> Inactive
                    </span>
                @endif
            </div>
            <p class="text-muted small mb-0">Expense classification and audit tracking.</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('expense-categories.index') }}" class="btn btn-secondary shadow-sm">
            <i class="bi bi-arrow-left"></i> Back to Categories
        </a>
        <a href="{{ route('expense-categories.edit', $expenseCategory) }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-pencil"></i> Edit Category
        </a>
    </div>
</div>

<!-- Main Details Card -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid #edf2f7 !important;">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 13.5px;">
            <i class="bi bi-info-circle text-secondary"></i> Category Information
        </h6>
        <span class="text-muted small">ID: #{{ $expenseCategory->id }}</span>
    </div>
    <div class="card-body p-4 bg-white">
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <span class="text-muted small d-block mb-1">Category Name</span>
                <strong class="text-dark fs-6">{{ $expenseCategory->name }}</strong>
            </div>
            <div class="col-md-6 col-lg-4">
                <span class="text-muted small d-block mb-1">Status</span>
                <span class="badge-status badge-{{ $expenseCategory->is_active ? 'accepted' : 'draft' }}">
                    <i class="bi bi-dot"></i> {{ $expenseCategory->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div class="col-md-6 col-lg-4">
                <span class="text-muted small d-block mb-1">Total Linked Expenses</span>
                <strong class="text-dark fs-6">{{ $expenseCategory->expenses_count ?? 0 }} Records</strong>
            </div>
            <div class="col-12">
                <span class="text-muted small d-block mb-1">Description</span>
                <div class="p-3 bg-light rounded-3 border text-secondary" style="font-size: 13.5px;">
                    {{ $expenseCategory->description ?: 'No detailed description provided for this expense category.' }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Linked Expenses Card -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid #edf2f7 !important;">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 13.5px;">
            <i class="bi bi-receipt text-secondary"></i> Recent Expenses in this Category
        </h6>
        <a href="{{ route('expenses.index', ['category' => $expenseCategory->name]) }}" class="btn btn-sm btn-outline-secondary">
            View All Expenses
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted" style="border-bottom: 1px solid #edf2f7;">
                <tr>
                    <th class="ps-4">Expense No</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Payment Method</th>
                    <th class="text-end pe-4">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenseCategory->expenses as $exp)
                    <tr>
                        <td class="ps-4 fw-bold font-monospace text-dark">{{ $exp->expense_no }}</td>
                        <td class="text-muted small">{{ $exp->expense_date?->format('M d, Y') }}</td>
                        <td>{{ $exp->description }}</td>
                        <td><span class="badge bg-light text-dark border">{{ ucfirst(str_replace('_', ' ', $exp->payment_method)) }}</span></td>
                        <td class="text-end pe-4 fw-bold text-dark">Rs. {{ number_format($exp->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-2 d-block mb-1 opacity-50"></i>
                            No expense records recorded under this category yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

