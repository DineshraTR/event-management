@extends('layouts.app')
@section('title', 'Expenses Report')
@section('page-title', 'Expenses Report')

@section('content')
<style>
    .stat-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px 20px;
        border: 1px solid #edf2f7;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
        gap: 16px;
        height: 100%;
    }
    .stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .stat-label {
        font-size: 11.5px;
        font-weight: 600;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 2px;
        letter-spacing: 0.3px;
    }
    .stat-value {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    /* Tab styles */
    .expense-nav-tabs {
        border-bottom: 2px solid #e2e8f0;
        gap: 8px;
    }
    .expense-nav-tabs .nav-link {
        border: none;
        border-bottom: 2.5px solid transparent;
        color: #64748b;
        font-weight: 600;
        font-size: 13.5px;
        padding: 12px 18px;
        border-radius: 0;
        background: transparent;
        transition: all 0.2s ease;
    }
    .expense-nav-tabs .nav-link:hover {
        color: #1e293b;
        border-bottom-color: #cbd5e1;
    }
    .expense-nav-tabs .nav-link.active {
        color: #1e293b;
        border-bottom-color: #484646;
        background: transparent;
    }

    .table-custom th {
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        background-color: #f8fafc;
        color: #475569;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 14px;
    }
    .table-custom td {
        font-size: 13px;
        vertical-align: middle;
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
    }
    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 13px;
    }
</style>

<!-- Page Header (Tritcal Style) -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0"
            style="width: 44px; height: 44px; background: linear-gradient(135deg, #484646 0%, #3a3838 100%); font-size: 20px;">
            <i class="bi bi-wallet2"></i>
        </div>
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.3px;">Expenses Report</h4>
                <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 11px; font-weight: 600;">
                    Audited Ledger
                </span>
            </div>
            <p class="text-muted small mb-0">Detailed breakdown of direct event-related expenditures and general operational overheads.</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <button type="button" class="btn btn-outline-secondary shadow-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print Report
        </button>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#quickExpenseModal">
            <i class="bi bi-plus-lg me-1"></i> Quick Add Expense
        </button>
        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary shadow-sm" title="Full Screen Expense Terminal">
            <i class="bi bi-box-arrow-up-right"></i> Terminal
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 shadow-sm" role="alert" style="border-radius: 10px;">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div>
            {{ session('success') }}
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4 shadow-sm" role="alert" style="border-radius: 10px;">
        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        <div>
            {{ session('error') }}
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Summary KPI Cards -->
<div class="row g-3 mb-4">
    <!-- Total Expenses -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(72, 70, 70, 0.1); color: #484646;">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div>
                <p class="stat-label">Total Disbursed</p>
                <p class="stat-value">Rs. {{ number_format($totalAmount, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Direct Event Expenses -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                <i class="bi bi-calendar-check"></i>
            </div>
            <div>
                <p class="stat-label">Direct Event Costs</p>
                <p class="stat-value text-primary">Rs. {{ number_format($directTotal, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Normal General Overheads -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(217, 119, 6, 0.1); color: #d97706;">
                <i class="bi bi-building"></i>
            </div>
            <div>
                <p class="stat-label">General Overheads</p>
                <p class="stat-value text-warning">Rs. {{ number_format($normalTotal, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Month Total -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(22, 163, 74, 0.1); color: #16a34a;">
                <i class="bi bi-calendar-month"></i>
            </div>
            <div>
                <p class="stat-label">{{ now()->format('F Y') }} Total</p>
                <p class="stat-value text-success">Rs. {{ number_format($monthTotal, 2) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filters Card -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #edf2f7 !important;">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 13.5px;">
            <i class="bi bi-funnel text-secondary"></i> Filter Expenses
        </h6>
        @if(request()->anyFilled(['search', 'category', 'customer_id', 'payment_method', 'date_from', 'date_to']))
            <a href="{{ route('reports.expenses') }}" class="btn btn-sm btn-link text-decoration-none text-danger p-0" style="font-size: 12px;">
                <i class="bi bi-x-circle me-1"></i> Clear Filters
            </a>
        @endif
    </div>

    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('reports.expenses') }}" id="expensesFilterForm">
            <input type="hidden" name="tab" id="filterActiveTab" value="{{ $activeTab }}">
            <div class="row g-3">
                <!-- Search -->
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-search me-1"></i> Quick Search
                    </label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" 
                        placeholder="Expense No, Payee, Description, Reference...">
                </div>

                <!-- Category -->
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-tags me-1"></i> Category
                    </label>
                    <select name="category" class="form-select searchable-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" @selected(request('category') == $cat)>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Customer (for direct) -->
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-person me-1"></i> Customer (Direct)
                    </label>
                    <select name="customer_id" class="form-select searchable-select">
                        <option value="">All Customers</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" @selected(request('customer_id') == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Payment Method -->
                <div class="col-12 col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-wallet2 me-1"></i> Method
                    </label>
                    <select name="payment_method" class="form-select">
                        <option value="">All Methods</option>
                        <option value="cash" @selected(request('payment_method') == 'cash')>Cash</option>
                        <option value="bank_transfer" @selected(request('payment_method') == 'bank_transfer')>Bank Transfer</option>
                        <option value="cheque" @selected(request('payment_method') == 'cheque')>Cheque</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div class="col-12 col-md-6 col-lg-4">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-calendar-range me-1"></i> Date From
                    </label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-calendar-range me-1"></i> Date To
                    </label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>

                <!-- Submit Button -->
                <div class="col-12 col-lg-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm fw-semibold">
                        <i class="bi bi-funnel me-1"></i> Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ============================================== -->
<!-- 2 TABS: DIRECT EXPENSES & NORMAL EXPENSES      -->
<!-- ============================================== -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #edf2f7 !important;">
    <div class="card-header bg-white p-0 border-bottom">
        <ul class="nav expense-nav-tabs px-3 pt-2" id="expenseTabs" role="tablist">
            <!-- Tab 1: Direct Expenses -->
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab === 'direct' ? 'active' : '' }}" 
                        id="tab-direct-btn" data-bs-toggle="tab" data-bs-target="#tab-direct" 
                        type="button" role="tab" aria-controls="tab-direct" aria-selected="{{ $activeTab === 'direct' ? 'true' : 'false' }}"
                        onclick="setReportTab('direct')">
                    <i class="bi bi-calendar-check me-1.5 text-primary"></i> Direct Event Expenses
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-2 rounded-pill px-2">
                        {{ $directCount }}
                    </span>
                </button>
            </li>

            <!-- Tab 2: Normal Expenses -->
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab === 'normal' ? 'active' : '' }}" 
                        id="tab-normal-btn" data-bs-toggle="tab" data-bs-target="#tab-normal" 
                        type="button" role="tab" aria-controls="tab-normal" aria-selected="{{ $activeTab === 'normal' ? 'true' : 'false' }}"
                        onclick="setReportTab('normal')">
                    <i class="bi bi-building me-1.5 text-warning"></i> Normal Operational Overheads
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle ms-2 rounded-pill px-2">
                        {{ $normalCount }}
                    </span>
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="expenseTabsContent">
        <!-- ============================================== -->
        <!-- TAB 1: DIRECT EXPENSES CONTENT                 -->
        <!-- ============================================== -->
        <div class="tab-pane fade {{ $activeTab === 'direct' ? 'show active' : '' }}" id="tab-direct" role="tabpanel" aria-labelledby="tab-direct-btn">
            <div class="table-responsive">
                <table class="table table-custom table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Voucher / Ref</th>
                            <th>Customer & Order / Event</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Method</th>
                            <th class="text-end">Amount (Rs.)</th>
                            <th class="text-center" style="width: 80px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($directExpenses as $exp)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $exp->expense_date?->format('d M Y') ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $exp->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark font-monospace border px-2 py-1">{{ $exp->expense_no }}</span>
                                    @if($exp->reference)
                                        <div class="text-muted small mt-0.5" style="font-size: 11px;">Ref: {{ $exp->reference }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($exp->order && $exp->order->customer)
                                        <div class="fw-bold text-dark">{{ $exp->order->customer->name }}</div>
                                        <div class="text-muted small" style="font-size: 11px;">
                                            <span class="text-primary font-monospace">{{ $exp->order->order_no }}</span> &bull; 
                                            {{ $exp->order->event?->event_name ?? 'Event' }}
                                        </div>
                                    @elseif($exp->event)
                                        <div class="fw-bold text-dark">{{ $exp->event->customer?->name ?? 'Customer' }}</div>
                                        <div class="text-muted small" style="font-size: 11px;">{{ $exp->event->event_name }}</div>
                                    @else
                                        <span class="text-muted small">No order linked</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-0.5" style="font-size: 11.5px;">
                                        {{ $exp->category ?: 'Direct Expense' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-dark fw-medium text-truncate" style="max-width: 250px;">{{ $exp->description }}</div>
                                    @if($exp->notes)
                                        <small class="text-muted text-truncate d-block" style="max-width: 250px; font-size: 11px;">{{ $exp->notes }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($exp->payment_method === 'bank_transfer')
                                        <span class="badge bg-info-subtle text-info border border-info-subtle">
                                            <i class="bi bi-bank me-1"></i> Bank
                                        </span>
                                        @if($exp->bank_name)
                                            <small class="text-muted d-block text-truncate" style="font-size: 10.5px; max-width: 140px;">{{ $exp->bank_name }}</small>
                                        @endif
                                    @elseif($exp->payment_method === 'cheque')
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                            <i class="bi bi-journal-check me-1"></i> Cheque
                                        </span>
                                        @if($exp->cheque_no)
                                            <small class="text-muted d-block font-monospace" style="font-size: 10.5px;">#{{ $exp->cheque_no }}</small>
                                        @endif
                                        @if($exp->cheque)
                                            <span class="{{ $exp->cheque->badge_class }} d-inline-block mt-0.5" style="font-size: 9.5px; padding: 1.5px 5px;">
                                                {{ $exp->cheque->status }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-cash me-1"></i> Cash
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="fw-bold text-danger fs-6">Rs. {{ number_format($exp->amount, 2) }}</div>
                                </td>
                                @php
                                    $isChequeDirect = ($exp->payment_method === 'cheque' || $exp->cheque);
                                    $chequeStatusDirect = $exp->cheque?->status;
                                    $isChequeLockedDirect = $isChequeDirect && $chequeStatusDirect && in_array(strtolower($chequeStatusDirect), ['realized', 'issued', 'released', 'cleared', 'deposited']);
                                @endphp
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        @if($isChequeLockedDirect)
                                            <button type="button" class="btn btn-sm btn-light text-muted border btn-action opacity-75" 
                                                    disabled 
                                                    title="Cheque is {{ $chequeStatusDirect }} - Edit disabled">
                                                <i class="bi bi-lock-fill text-warning"></i>
                                            </button>
                                        @else
                                            <a href="{{ route('expenses.edit', $exp->id) }}" class="btn btn-sm btn-outline-secondary btn-action" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif

                                        @if($isChequeDirect)
                                            <button type="button" class="btn btn-sm btn-light text-muted border btn-action opacity-75" 
                                                    disabled 
                                                    title="Cheque expenses cannot be deleted">
                                                <i class="bi bi-trash3 text-muted"></i>
                                            </button>
                                        @else
                                            <form method="POST" action="{{ route('expenses.destroy', $exp->id) }}" onsubmit="handleDeleteConfirmation(event, this, {
                                                type: 'Expense Voucher',
                                                title: '{{ $exp->expense_no }} - {{ addslashes($exp->description) }}',
                                                hasPayments: false
                                            });" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-action" title="Delete">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bi bi-folder2-open text-muted" style="font-size: 32px;"></i>
                                    <p class="text-muted small mt-2 mb-0">No direct event expenses found matching the selected filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($directExpenses->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $directExpenses->links() }}
                </div>
            @endif
        </div>

        <!-- ============================================== -->
        <!-- TAB 2: NORMAL EXPENSES CONTENT                 -->
        <!-- ============================================== -->
        <div class="tab-pane fade {{ $activeTab === 'normal' ? 'show active' : '' }}" id="tab-normal" role="tabpanel" aria-labelledby="tab-normal-btn">
            <div class="table-responsive">
                <table class="table table-custom table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Voucher / Ref</th>
                            <th>Category</th>
                            <th>Description / Payee</th>
                            <th>Method</th>
                            <th class="text-end">Amount (Rs.)</th>
                            <th class="text-center" style="width: 80px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($normalExpenses as $exp)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $exp->expense_date?->format('d M Y') ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $exp->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark font-monospace border px-2 py-1">{{ $exp->expense_no }}</span>
                                    @if($exp->reference)
                                        <div class="text-muted small mt-0.5" style="font-size: 11px;">Ref: {{ $exp->reference }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-0.5" style="font-size: 11.5px; font-weight: 600;">
                                        {{ $exp->category ?: 'General' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-dark fw-medium text-truncate" style="max-width: 320px;">{{ $exp->description }}</div>
                                    @if($exp->notes)
                                        <small class="text-muted text-truncate d-block" style="max-width: 320px; font-size: 11px;">{{ $exp->notes }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($exp->payment_method === 'bank_transfer')
                                        <span class="badge bg-info-subtle text-info border border-info-subtle">
                                            <i class="bi bi-bank me-1"></i> Bank
                                        </span>
                                        @if($exp->bank_name)
                                            <small class="text-muted d-block text-truncate" style="font-size: 10.5px; max-width: 140px;">{{ $exp->bank_name }}</small>
                                        @endif
                                    @elseif($exp->payment_method === 'cheque')
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                            <i class="bi bi-journal-check me-1"></i> Cheque
                                        </span>
                                        @if($exp->cheque_no)
                                            <small class="text-muted d-block font-monospace" style="font-size: 10.5px;">#{{ $exp->cheque_no }}</small>
                                        @endif
                                        @if($exp->cheque)
                                            <span class="{{ $exp->cheque->badge_class }} d-inline-block mt-0.5" style="font-size: 9.5px; padding: 1.5px 5px;">
                                                {{ $exp->cheque->status }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-cash me-1"></i> Cash
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="fw-bold text-danger fs-6">Rs. {{ number_format($exp->amount, 2) }}</div>
                                </td>
                                @php
                                    $isChequeDirect = ($exp->payment_method === 'cheque' || $exp->cheque);
                                    $chequeStatusDirect = $exp->cheque?->status;
                                    $isChequeLockedDirect = $isChequeDirect && $chequeStatusDirect && in_array(strtolower($chequeStatusDirect), ['realized', 'issued', 'released', 'cleared', 'deposited']);
                                @endphp
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        @if($isChequeLockedDirect)
                                            <button type="button" class="btn btn-sm btn-light text-muted border btn-action opacity-75" 
                                                    disabled 
                                                    title="Cheque is {{ $chequeStatusDirect }} - Edit disabled">
                                                <i class="bi bi-lock-fill text-warning"></i>
                                            </button>
                                        @else
                                            <a href="{{ route('expenses.edit', $exp->id) }}" class="btn btn-sm btn-outline-secondary btn-action" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif

                                        @if($isChequeDirect)
                                            <button type="button" class="btn btn-sm btn-light text-muted border btn-action opacity-75" 
                                                    disabled 
                                                    title="Cheque expenses cannot be deleted">
                                                <i class="bi bi-trash3 text-muted"></i>
                                            </button>
                                        @else
                                            <form method="POST" action="{{ route('expenses.destroy', $exp->id) }}" onsubmit="handleDeleteConfirmation(event, this, {
                                                type: 'Expense Voucher',
                                                title: '{{ $exp->expense_no }} - {{ addslashes($exp->description) }}',
                                                hasPayments: false
                                            });" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-action" title="Delete">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-folder2-open text-muted" style="font-size: 32px;"></i>
                                    <p class="text-muted small mt-2 mb-0">No normal operational expenses found matching the selected filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($normalExpenses->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $normalExpenses->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- POPUP MODAL: QUICK ADD EXPENSE                                 -->
<!-- ============================================================== -->
<div class="modal fade" id="quickExpenseModal" tabindex="-1" aria-labelledby="quickExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <h6 class="modal-title fw-bold text-white mb-0 d-flex align-items-center gap-2" id="quickExpenseModalLabel">
                    <i class="bi bi-wallet2 text-warning"></i> Quick Record Expense Voucher
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('expenses.store') }}" id="modalExpenseForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Expense Type -->
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Classification <span class="text-danger">*</span>
                            </label>
                            <select name="type" id="modalExpType" class="form-select fw-semibold" onchange="toggleModalExpType(this.value)" required>
                                <option value="normal">🏢 Normal Operational Overhead</option>
                                <option value="direct">🎉 Direct Event Expense</option>
                            </select>
                        </div>

                        <!-- Target Order (for Direct) -->
                        <div class="col-12 col-md-6 d-none" id="modalOrderCol">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Target Order / Customer <span class="text-danger">*</span>
                            </label>
                            <select name="order_id" id="modalOrderId" class="form-select">
                                <option value="">-- Choose Order --</option>
                                @foreach($orders as $o)
                                    <option value="{{ $o->id }}">
                                        {{ $o->order_no }} - {{ $o->customer?->name ?? 'Walk-in' }} ({{ $o->event?->event_name ?? 'Event' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Category (for Normal) -->
                        <div class="col-12 col-md-6" id="modalCategoryCol">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Category <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="category" id="modalCategoryInput" class="form-control" list="modalCategoryList" placeholder="e.g. Office Rent, Electricity, Fuel...">
                            <datalist id="modalCategoryList">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}">
                                @endforeach
                            </datalist>
                        </div>

                        <!-- Description -->
                        <div class="col-12 col-md-8">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Description / Payee <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="description" class="form-control" placeholder="What was this payment for?" required>
                        </div>

                        <!-- Expense Date -->
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <!-- Amount -->
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Amount (Rs.) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold bg-light text-danger">Rs.</span>
                                <input type="number" step="0.01" min="0.01" name="amount" class="form-control font-monospace fw-bold text-danger fs-6" placeholder="0.00" required>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Payment Method <span class="text-danger">*</span>
                            </label>
                            <select name="payment_method" id="modalPaymentMethod" class="form-select" onchange="toggleModalMethod(this.value)" required>
                                <option value="cash">💵 Cash Payment</option>
                                <option value="bank_transfer">🏦 Bank Transfer</option>
                                <option value="cheque">📑 Cheque Payment</option>
                            </select>
                        </div>

                        <!-- Bank Account (if Bank Transfer) -->
                        <div class="col-12 d-none" id="modalBankRow">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label small fw-bold text-dark mb-0">
                                    Debited Bank Account <span class="text-danger">*</span>
                                </label>
                                <button type="button" class="btn btn-sm btn-light border py-0 px-2" style="font-size: 11px;" data-bs-toggle="modal" data-bs-target="#quickBankModal">
                                    <i class="bi bi-plus-lg me-1 text-primary"></i> Add Bank
                                </button>
                            </div>
                            <select name="bank_name" id="modalBankSelect" class="form-select">
                                <option value="">-- Choose Debited Bank Account --</option>
                                @foreach($banks as $b)
                                    <option value="{{ $b->bank_name }} - {{ $b->account_number }} ({{ $b->branch }})">
                                        {{ $b->bank_name }} &bull; {{ $b->account_number }} ({{ $b->branch ?: 'Main' }}) - Bal: Rs. {{ number_format($b->current_balance, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Cheque Number (if Cheque) -->
                        <div class="col-12 d-none" id="modalChequeRow">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Cheque Serial Number <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="cheque_no" id="modalChequeInput" class="form-control font-monospace" placeholder="e.g. CHQ-901824">
                        </div>

                        <!-- Reference & Notes -->
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">Reference / Bill #</label>
                            <input type="text" name="reference" class="form-control" placeholder="Slip or Invoice #">
                        </div>
                        <div class="col-12 col-md-8">
                            <label class="form-label small fw-bold text-dark mb-1">Internal Notes</label>
                            <input type="text" name="notes" class="form-control" placeholder="Optional notes...">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i> Save & Post Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- POPUP MODAL: QUICK ADD BANK ACCOUNT                            -->
<!-- ============================================================== -->
<div class="modal fade" id="quickBankModal" tabindex="-1" aria-labelledby="quickBankModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <h6 class="modal-title fw-bold text-white mb-0 d-flex align-items-center gap-2" id="quickBankModalLabel">
                    <i class="bi bi-bank text-warning"></i> Quick Register Bank Account
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="quickBankModalAlert" class="alert alert-danger d-none py-2 px-3 small" style="border-radius: 8px;"></div>
                <form id="quickBankForm">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Bank Name <span class="text-danger">*</span></label>
                        <input type="text" id="modalBankName" class="form-control" placeholder="e.g. Bank of Ceylon, Commercial Bank..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Account Holder Name <span class="text-danger">*</span></label>
                        <input type="text" id="modalAccountName" class="form-control" placeholder="e.g. PUBUDU ISHANTHA" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Account Number <span class="text-danger">*</span></label>
                        <input type="text" id="modalAccountNumber" class="form-control font-monospace" placeholder="e.g. 86952315" required>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark mb-1">Branch Name</label>
                            <input type="text" id="modalBranch" class="form-control" placeholder="e.g. Colombo Main">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark mb-1">Opening Balance (Rs.)</label>
                            <input type="number" step="0.01" min="0" id="modalOpeningBalance" class="form-control font-monospace" placeholder="0.00" value="0">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light py-2 px-4">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm fw-semibold" id="btnSaveQuickBank" onclick="saveQuickBankAjax('modalBankSelect')">
                    <i class="bi bi-check-lg me-1"></i> Save & Select Bank
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function setReportTab(tabName) {
        document.getElementById('filterActiveTab').value = tabName;
    }

    function toggleModalExpType(type) {
        const orderCol = document.getElementById('modalOrderCol');
        const catCol = document.getElementById('modalCategoryCol');
        const orderSelect = document.getElementById('modalOrderId');

        if (type === 'direct') {
            orderCol.classList.remove('d-none');
            catCol.classList.add('d-none');
            orderSelect.setAttribute('required', 'required');
        } else {
            orderCol.classList.add('d-none');
            catCol.classList.remove('d-none');
            orderSelect.removeAttribute('required');
        }
    }

    function toggleModalMethod(method) {
        const bankRow = document.getElementById('modalBankRow');
        const chequeRow = document.getElementById('modalChequeRow');
        const bankSelect = document.getElementById('modalBankSelect');
        const chequeInput = document.getElementById('modalChequeInput');

        if (method === 'bank_transfer') {
            bankRow.classList.remove('d-none');
            chequeRow.classList.add('d-none');
            bankSelect.setAttribute('required', 'required');
            chequeInput.removeAttribute('required');
        } else if (method === 'cheque') {
            bankRow.classList.add('d-none');
            chequeRow.classList.remove('d-none');
            bankSelect.removeAttribute('required');
            chequeInput.setAttribute('required', 'required');
        } else {
            bankRow.classList.add('d-none');
            chequeRow.classList.add('d-none');
            bankSelect.removeAttribute('required');
            chequeInput.removeAttribute('required');
        }
    }

    function saveQuickBankAjax(targetSelectId = 'modalBankSelect') {
        const bankName = document.getElementById('modalBankName').value.trim();
        const accountName = document.getElementById('modalAccountName').value.trim();
        const accountNumber = document.getElementById('modalAccountNumber').value.trim();
        const branch = document.getElementById('modalBranch').value.trim();
        const openingBalance = document.getElementById('modalOpeningBalance').value.trim() || 0;
        const alertBox = document.getElementById('quickBankModalAlert');
        const btnSave = document.getElementById('btnSaveQuickBank');

        if (!bankName || !accountName || !accountNumber) {
            alertBox.classList.remove('d-none');
            alertBox.innerText = 'Bank Name, Account Holder Name, and Account Number are required.';
            return;
        }

        alertBox.classList.add('d-none');
        btnSave.disabled = true;
        btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        fetch("{{ route('banks.quick-store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                bank_name: bankName,
                account_name: accountName,
                account_number: accountNumber,
                branch: branch,
                opening_balance: openingBalance
            })
        })
        .then(res => res.json())
        .then(data => {
            btnSave.disabled = false;
            btnSave.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save & Select Bank';

            if (data.success && data.bank) {
                const selectEl = document.getElementById(targetSelectId);
                if (selectEl) {
                    const optVal = data.display_value;
                    const optText = data.display_text;
                    if (typeof jQuery !== 'undefined') {
                        const $select = jQuery(selectEl);
                        $select.append(new Option(optText, optVal, true, true));
                        $select.val(optVal).trigger('change');
                    } else {
                        selectEl.add(new Option(optText, optVal, true, true));
                        selectEl.value = optVal;
                        selectEl.dispatchEvent(new Event('change'));
                    }
                }

                const modalEl = document.getElementById('quickBankModal');
                if (window.bootstrap && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                } else if (typeof jQuery !== 'undefined') {
                    jQuery(modalEl).modal('hide');
                }
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.getElementById('quickBankForm').reset();
            } else {
                alertBox.classList.remove('d-none');
                alertBox.innerText = data.message || 'Failed to save bank.';
            }
        })
        .catch(err => {
            btnSave.disabled = false;
            btnSave.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save & Select Bank';
            alertBox.classList.remove('d-none');
            alertBox.innerText = 'An error occurred while saving bank account.';
        });
    }
</script>
@endsection
