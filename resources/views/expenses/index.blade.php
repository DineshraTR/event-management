@extends('layouts.app')
@section('title', 'Expenses Management')
@section('page-title', 'Expenses')

@section('content')
<!-- Page Header (Tritcal Style) -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0"
            style="width: 44px; height: 44px; background: linear-gradient(135deg, #484646 0%, #3a3838 100%); font-size: 20px;">
            <i class="bi bi-wallet2"></i>
        </div>
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.3px;">Expenses Management</h4>
                <span class="badge bg-light text-dark border px-2 py-0.5" style="font-size: 11px; font-weight: 700;">
                    {{ $expenses->total() }} Recorded Expenses
                </span>
            </div>
            <p class="text-muted small mb-0">Track direct event expenditures, general operational overheads, and financial vouchers.</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('expenses.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Record New Expense
        </a>
    </div>
</div>

<!-- Stat Cards Row -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div>
                <p class="stat-label">Total Expenses</p>
                <p class="stat-value">Rs. {{ number_format($totalAmount, 2) }}</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #2563eb;">
                <i class="bi bi-calendar-event"></i>
            </div>
            <div>
                <p class="stat-label">Direct (Event Costs)</p>
                <p class="stat-value text-primary">Rs. {{ number_format($directAmount, 2) }}</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #d97706;">
                <i class="bi bi-building"></i>
            </div>
            <div>
                <p class="stat-label">General Overheads</p>
                <p class="stat-value text-warning">Rs. {{ number_format($normalAmount, 2) }}</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="bi bi-calendar-month"></i>
            </div>
            <div>
                <p class="stat-label">{{ now()->format('F Y') }} Total</p>
                <p class="stat-value text-success">Rs. {{ number_format($monthAmount, 2) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Card (Tritcal Style) -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #edf2f7 !important;">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 13.5px;">
            <i class="bi bi-funnel text-secondary"></i> Search & Filters
        </h6>
        @if(request()->anyFilled(['search', 'type', 'category', 'event_id', 'from_date', 'to_date']))
            <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-link text-decoration-none text-danger p-0" style="font-size: 12px;">
                <i class="bi bi-x-circle me-1"></i> Clear Filters
            </a>
        @endif
    </div>

    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('expenses.index') }}" id="expensesFilterForm">
            <div class="row g-3">
                <!-- 1. Quick Search -->
                <div class="col-12 col-md-6 col-lg-4">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-search me-1"></i> Quick Search
                    </label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" 
                        placeholder="Description, Expense No, Reference, Notes...">
                </div>

                <!-- 2. Expense Type -->
                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-tag me-1"></i> Expense Type
                    </label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="direct" @selected(request('type') === 'direct')>Direct (Event)</option>
                        <option value="normal" @selected(request('type') === 'normal')>General (Normal)</option>
                    </select>
                </div>

                <!-- 3. Category -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-folder2 me-1"></i> Category
                    </label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Related Event -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-calendar-event me-1"></i> Related Event
                    </label>
                    <select name="event_id" class="form-select">
                        <option value="">All Events</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" @selected(request('event_id') == $event->id)>
                                {{ $event->event_no }} - {{ Str::limit($event->event_name, 25) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 5. From Date -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-calendar-date me-1"></i> From Date
                    </label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>

                <!-- 6. To Date -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-calendar-date-fill me-1"></i> To Date
                    </label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>

                <!-- 7. Filter & Reset Buttons -->
                <div class="col-12 col-lg-6 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary shadow-sm flex-grow-1" style="max-width: 180px;">
                        <i class="bi bi-funnel-fill me-1"></i> Apply Filters
                    </button>
                    <a href="{{ route('expenses.index') }}" class="btn btn-secondary shadow-sm">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Expenses Table Card -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #edf2f7 !important;">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <h6 class="mb-0 fw-bold text-dark" style="font-size: 14px;">Expenses Log</h6>
            <span class="badge bg-light text-muted border" style="font-size: 11px;">
                Showing {{ $expenses->firstItem() ?? 0 }} - {{ $expenses->lastItem() ?? 0 }} of {{ $expenses->total() }}
            </span>
        </div>
        @if(request()->anyFilled(['search', 'type', 'category', 'event_id', 'from_date', 'to_date']))
            <div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1" style="font-size: 12px;">
                    Filtered Total: Rs. {{ number_format($filteredAmount, 2) }}
                </span>
            </div>
        @endif
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light" style="font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.5px;">
                <tr>
                    <th class="ps-4 py-3">Expense No</th>
                    <th class="py-3">Description & Category</th>
                    <th class="py-3">Type</th>
                    <th class="py-3">Related Event</th>
                    <th class="py-3">Date</th>
                    <th class="py-3">Reference</th>
                    <th class="py-3 text-end">Amount</th>
                    <th class="py-3 text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr>
                        <!-- Expense No -->
                        <td class="ps-4">
                            <div class="fw-bold text-dark font-monospace" style="font-size: 13px;">
                                {{ $expense->expense_no }}
                            </div>
                        </td>

                        <!-- Description & Category -->
                        <td>
                            <div class="fw-semibold text-dark" style="font-size: 13px;">
                                {{ $expense->description }}
                            </div>
                            @if($expense->category)
                                <span class="badge bg-light text-secondary border px-2 py-0.5 mt-0.5" style="font-size: 10.5px; font-weight: 600;">
                                    <i class="bi bi-tag me-1"></i>{{ $expense->category }}
                                </span>
                            @endif
                            @if($expense->notes)
                                <small class="text-muted d-block mt-0.5 text-truncate" style="max-width: 250px; font-size: 11px;" title="{{ $expense->notes }}">
                                    {{ $expense->notes }}
                                </small>
                            @endif
                        </td>

                        <!-- Type -->
                        <td>
                            @if($expense->type === 'direct')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1" style="font-size: 11px; font-weight: 600;">
                                    <i class="bi bi-calendar-event me-1"></i> Direct
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1" style="font-size: 11px; font-weight: 600;">
                                    <i class="bi bi-building me-1"></i> General
                                </span>
                            @endif
                        </td>

                        <!-- Related Event -->
                        <td>
                            @if($expense->event)
                                <a href="{{ route('events.show', $expense->event) }}" class="text-decoration-none fw-semibold text-dark d-block text-truncate" style="max-width: 180px; font-size: 12.5px;">
                                    {{ $expense->event->event_name }}
                                </a>
                                <small class="text-muted font-monospace" style="font-size: 10.5px;">
                                    {{ $expense->event->event_no }}
                                </small>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>

                        <!-- Expense Date -->
                        <td style="font-size: 12.5px;">
                            <div class="fw-medium text-dark">
                                {{ $expense->expense_date?->format('M d, Y') ?? '—' }}
                            </div>
                            @if($expense->expense_date)
                                <small class="text-muted" style="font-size: 11px;">
                                    {{ $expense->expense_date->diffForHumans() }}
                                </small>
                            @endif
                        </td>

                        <!-- Reference -->
                        <td>
                            @if($expense->reference)
                                <span class="badge bg-light text-secondary border font-monospace px-1.5 py-0.5" style="font-size: 11px;">
                                    {{ $expense->reference }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>

                        <!-- Amount -->
                        <td class="text-end">
                            <div class="fw-bold text-danger" style="font-size: 13.5px;">
                                Rs. {{ number_format((float)$expense->amount, 2) }}
                            </div>
                        </td>

                        <!-- Actions -->
                        @php
                            $isCheque = ($expense->payment_method === 'cheque' || $expense->cheque);
                            $chequeStatus = $expense->cheque?->status;
                            $isChequeLocked = $isCheque && $chequeStatus && in_array(strtolower($chequeStatus), ['realized', 'issued', 'released', 'cleared', 'deposited']);
                        @endphp
                        <td class="text-end pe-4 text-nowrap">
                            <div class="d-inline-flex align-items-center gap-1">
                                @if($isChequeLocked)
                                    <button type="button" class="btn btn-sm btn-light text-muted border px-2 py-1 opacity-75" 
                                            disabled 
                                            title="Cheque is {{ $chequeStatus }} - Edit disabled"
                                            style="font-size: 12px;">
                                        <i class="bi bi-lock-fill text-warning"></i>
                                    </button>
                                @else
                                    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-outline-secondary px-2 py-1" title="Edit Expense" style="font-size: 12px;">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endif

                                @if($isCheque)
                                    <button type="button" class="btn btn-sm btn-light text-muted border px-2 py-1 opacity-75" 
                                            disabled 
                                            title="Cheque expenses cannot be deleted"
                                            style="font-size: 12px;">
                                        <i class="bi bi-trash text-muted"></i>
                                    </button>
                                @else
                                    <form method="POST" action="{{ route('expenses.destroy', $expense) }}" class="d-inline m-0 p-0" onsubmit="handleDeleteConfirmation(event, this, {
                                        type: 'Expense Voucher',
                                        title: '{{ $expense->expense_no }} - {{ addslashes($expense->description) }}',
                                        hasPayments: false
                                    });">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" title="Delete Expense" style="font-size: 12px;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <i class="bi bi-wallet2 fs-1 text-secondary opacity-50 mb-2"></i>
                                <h6 class="fw-bold text-dark mb-1">No Expenses Found</h6>
                                <p class="small text-muted mb-3">No expense records match your current filter criteria.</p>
                                <a href="{{ route('expenses.create') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus-lg me-1"></i> Record New Expense
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    @if($expenses->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="small text-muted">
                Showing {{ $expenses->firstItem() }} to {{ $expenses->lastItem() }} of {{ $expenses->total() }} expenses
            </div>
            <div>
                {{ $expenses->links() }}
            </div>
        </div>
    @endif
</div>
@endsection