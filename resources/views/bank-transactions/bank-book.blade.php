@extends('layouts.app')
@section('title', 'Bank Book Ledger')
@section('page-title', 'Bank Book Ledger')

@section('content')
@php
    $incomeTotal = $periodIncomeTotal ?? $transactions->where('type', 'income')->sum('amount');
    $expenseTotal = $periodExpenseTotal ?? $transactions->where('type', 'expense')->sum('amount');
    $closingBalance = $openingBalance + $incomeTotal - $expenseTotal;
    $currentRunningBalance = $openingBalance;
@endphp

<style>
    /* Premium KPI Stat Cards */
    .bank-stat-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 18px 20px;
        border: 1px solid #edf2f7;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
        gap: 16px;
        height: 100%;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .bank-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(0,0,0,0.04);
    }
    .bank-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .bank-stat-label {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 3px;
        letter-spacing: 0.5px;
    }
    .bank-stat-value {
        font-size: 19px;
        font-weight: 700;
        margin: 0;
        letter-spacing: -0.3px;
        line-height: 1.2;
    }

    /* Filter Card */
    .filter-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #edf2f7;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    }
    .quick-preset-btn {
        font-size: 12px;
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        transition: all 0.15s ease;
        text-decoration: none;
    }
    .quick-preset-btn:hover, .quick-preset-btn.active {
        background: #484646;
        color: #ffffff;
        border-color: #484646;
    }

    /* Ledger Table */
    .table-ledger {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    .table-ledger th {
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        background-color: #f8fafc;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
        padding: 13px 16px;
        white-space: nowrap;
    }
    .table-ledger td {
        font-size: 13px;
        vertical-align: middle;
        padding: 13px 16px;
        border-bottom: 1px solid #f1f5f9;
        background-color: #ffffff;
    }
    .table-ledger tbody tr:hover td {
        background-color: #f8fafc;
    }
    .table-ledger .opening-row td {
        background-color: #fafaf9 !important;
        font-weight: 600;
        border-bottom: 1.5px dashed #cbd5e1;
    }
    .table-ledger tfoot td {
        background-color: #f8fafc !important;
        font-weight: 700;
        border-top: 2px solid #cbd5e1;
        padding: 14px 16px;
    }

    .badge-inflow {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        font-weight: 700;
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 6px;
        display: inline-block;
    }
    .badge-outflow {
        background: #fff1f2;
        color: #e11d48;
        border: 1px solid #fecdd3;
        font-weight: 700;
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 6px;
        display: inline-block;
    }

    /* Print Styles */
    @media print {
        .no-print, .sidebar, .topbar, .btn, .filter-card, .page-header-actions {
            display: none !important;
        }
        body {
            background: #ffffff !important;
            color: #000000 !important;
            font-size: 12px;
        }
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .table-ledger th {
            background: #eeeeee !important;
            color: #000000 !important;
            border-bottom: 1px solid #000000 !important;
        }
        .table-ledger td {
            border-bottom: 1px solid #dddddd !important;
        }
        .print-header {
            display: block !important;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .print-signatures {
            display: flex !important;
            justify-content: space-between;
            margin-top: 50px;
            padding-top: 20px;
        }
    }
    .print-header, .print-signatures {
        display: none;
    }
</style>

<!-- Print Only Corporate Header -->
<div class="print-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-0">COMPANY BANK BOOK STATEMENT</h3>
            <p class="text-muted mb-0 small">Audit ledger for all institutional bank accounts, electronic transfers, and cheques</p>
        </div>
        <div class="text-end">
            <div class="fw-bold">Statement Period:</div>
            <div class="small">{{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</div>
            <div class="text-muted small">Generated on: {{ now()->format('d M Y, h:i A') }}</div>
        </div>
    </div>
</div>

<!-- Page Header (Tritcal Style) -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 no-print">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0"
            style="width: 44px; height: 44px; background: linear-gradient(135deg, #484646 0%, #3a3838 100%); font-size: 20px;">
            <i class="bi bi-bank"></i>
        </div>
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.3px;">Bank Book</h4>
                <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 11px; font-weight: 700;">
                    Bank Accounts Ledger
                </span>
                <span class="badge bg-light text-dark border px-2 py-0.5" style="font-size: 11px;">
                    {{ $transactions->count() }} Transactions
                </span>
            </div>
            <p class="text-muted small mb-0">Official ledger records of bank deposits, withdrawals, cheques, and online electronic transfers.</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap page-header-actions">
        <a href="{{ route('cash-book') }}" class="btn btn-outline-secondary shadow-sm" title="View Physical Till Cash Transactions">
            <i class="bi bi-journal-text me-1 text-primary"></i> Cash Book
        </a>
        <button type="button" class="btn btn-outline-secondary shadow-sm" onclick="window.print()" title="Print Bank Book Statement">
            <i class="bi bi-printer me-1"></i> Print Statement
        </button>
        <a href="{{ route('bank-transactions.create') }}" class="btn btn-primary shadow-sm" target="_blank">
            <i class="bi bi-plus-lg me-1"></i> Record Transaction
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 shadow-sm no-print" role="alert" style="border-radius: 10px;">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div>
            {{ session('success') }}
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- ============================================================== -->
<!-- 1. KPI STAT CARDS OVERVIEW                                    -->
<!-- ============================================================== -->
<div class="row g-3 mb-4">
    <!-- Opening Bank Balance -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="bank-stat-card">
            <div class="bank-stat-icon" style="background-color: #f1f5f9; color: #334155;">
                <i class="bi bi-bank"></i>
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <div class="bank-stat-label">Opening Bank Balance</div>
                <div class="bank-stat-value text-dark text-truncate">
                    Rs. {{ number_format($openingBalance, 2) }}
                </div>
                <small class="text-muted d-block text-truncate mt-1" style="font-size: 11px;">
                    Before {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}
                </small>
            </div>
        </div>
    </div>

    <!-- Total Bank Deposits (Inflows) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="bank-stat-card">
            <div class="bank-stat-icon" style="background-color: #ecfdf5; color: #059669;">
                <i class="bi bi-arrow-down-left-circle-fill"></i>
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <div class="bank-stat-label text-success">Total Bank Deposits</div>
                <div class="bank-stat-value text-success text-truncate">
                    Rs. {{ number_format($incomeTotal, 2) }}
                </div>
                <small class="text-muted d-block text-truncate mt-1" style="font-size: 11px;">
                    Deposits & Inflows in period
                </small>
            </div>
        </div>
    </div>

    <!-- Total Bank Withdrawals (Disbursements) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="bank-stat-card">
            <div class="bank-stat-icon" style="background-color: #fff1f2; color: #e11d48;">
                <i class="bi bi-arrow-up-right-circle-fill"></i>
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <div class="bank-stat-label text-danger">Total Bank Withdrawals</div>
                <div class="bank-stat-value text-danger text-truncate">
                    Rs. {{ number_format($expenseTotal, 2) }}
                </div>
                <small class="text-muted d-block text-truncate mt-1" style="font-size: 11px;">
                    Withdrawals & Cheques cleared
                </small>
            </div>
        </div>
    </div>

    <!-- Net Closing Bank Position -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="bank-stat-card" style="border-left: 4px solid {{ $closingBalance >= 0 ? '#10b981' : '#ef4444' }} !important;">
            <div class="bank-stat-icon" style="background-color: {{ $closingBalance >= 0 ? '#eff6ff' : '#fef2f2' }}; color: {{ $closingBalance >= 0 ? '#2563eb' : '#dc2626' }};">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <div class="bank-stat-label">Closing Bank Balance</div>
                <div class="bank-stat-value text-truncate {{ $closingBalance >= 0 ? 'text-dark' : 'text-danger' }}">
                    Rs. {{ number_format($closingBalance, 2) }}
                </div>
                <small class="d-block text-truncate mt-1 fw-semibold {{ $closingBalance >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 11px;">
                    <i class="bi bi-shield-check me-1"></i> {{ $closingBalance >= 0 ? 'Liquid Bank Funds Available' : 'Overdraft / Deficit Position' }}
                </small>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- 2. DATE RANGE & TRANSACTION FILTERS CARD                       -->
<!-- ============================================================== -->
<div class="card filter-card mb-4 no-print">
    <div class="card-body p-3.5">
        <form method="GET" action="{{ route('bank-book') }}" id="bankBookFilterForm">
            <!-- Quick Preset Date Buttons -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                    <span class="text-muted small fw-bold me-1 text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Quick Presets:</span>
                    <button type="button" class="quick-preset-btn" onclick="applyPreset('today')">Today</button>
                    <button type="button" class="quick-preset-btn" onclick="applyPreset('this_week')">This Week</button>
                    <button type="button" class="quick-preset-btn" onclick="applyPreset('this_month')">This Month</button>
                    <button type="button" class="quick-preset-btn" onclick="applyPreset('last_month')">Last Month</button>
                    <button type="button" class="quick-preset-btn" onclick="applyPreset('this_year')">This Year</button>
                </div>
                <div class="text-muted small" style="font-size: 11.5px;">
                    <i class="bi bi-calendar3 me-1"></i> Showing records from <strong class="text-dark">{{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}</strong> to <strong class="text-dark">{{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</strong>
                </div>
            </div>

            <div class="row g-2.5 align-items-end">
                <!-- Date From -->
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label small fw-bold text-dark mb-1" for="date_from">From Date</label>
                    <div class="input-group input-group-sm">
                        <input type="date" class="form-control form-control-sm border-start-0" id="date_from" name="date_from" value="{{ $dateFrom }}">
                    </div>
                </div>

                <!-- Date To -->
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label small fw-bold text-dark mb-1" for="date_to">To Date</label>
                    <div class="input-group input-group-sm">
                        <input type="date" class="form-control form-control-sm border-start-0" id="date_to" name="date_to" value="{{ $dateTo }}">
                    </div>
                </div>

                <!-- Bank Account Selector -->
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label small fw-bold text-dark mb-1" for="bank_select">Bank Account</label>
                    <select class="form-select form-select-sm" name="bank" id="bank_select">
                        <option value="">All Accounts</option>
                        @foreach($banks as $b)
                            <option value="{{ $b->bank_name }}" {{ ($bankFilter ?? '') == $b->bank_name ? 'selected' : '' }}>
                                {{ $b->bank_name }} ({{ $b->branch ?: 'Main' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Method Selector -->
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label small fw-bold text-dark mb-1" for="method_select">Method</label>
                    <select class="form-select form-select-sm" name="method" id="method_select">
                        <option value="all" {{ ($method ?? 'all') === 'all' ? 'selected' : '' }}>All Methods</option>
                        <option value="bank_transfer" {{ ($method ?? '') === 'bank_transfer' ? 'selected' : '' }}>🏦 Bank Transfer</option>
                        <option value="cheque" {{ ($method ?? '') === 'cheque' ? 'selected' : '' }}>📑 Cheque</option>
                    </select>
                </div>

                <!-- Flow Type Selector -->
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label small fw-bold text-dark mb-1" for="type_select">Flow Type</label>
                    <select class="form-select form-select-sm" name="type" id="type_select">
                        <option value="all" {{ ($type ?? 'all') === 'all' ? 'selected' : '' }}>All Entries</option>
                        <option value="income" {{ ($type ?? '') === 'income' ? 'selected' : '' }}>🟢 Deposits (In)</option>
                        <option value="expense" {{ ($type ?? '') === 'expense' ? 'selected' : '' }}>🔴 Withdrawals (Out)</option>
                    </select>
                </div>

                <!-- Keyword Search -->
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label small fw-bold text-dark mb-1" for="search_input">Search</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control form-control-sm border-start-0" id="search_input" name="search" 
                               value="{{ $search ?? '' }}" placeholder="Ref, Cheque, Payee...">
                    </div>
                </div>

                <!-- Filter Action Buttons (Aligned across) -->
                <div class="col-12 d-flex justify-content-end gap-1.5 mt-2 pt-2 border-top">
                    @if(request()->hasAny(['date_from', 'date_to', 'type', 'method', 'bank', 'search']))
                        <a href="{{ route('bank-book') }}" class="btn btn-outline-secondary btn-sm" title="Clear Filters" style="height: 31px;">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </a>
                    @endif
                    <button type="submit" class="btn btn-primary btn-sm fw-semibold px-3" style="height: 31px;">
                        <i class="bi bi-funnel-fill me-1"></i> Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- 3. BANK BOOK LEDGER TABLE (DOUBLE-ENTRY FORMAT)                -->
<!-- ============================================================== -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #edf2f7 !important;">
    <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 14px;">
                <i class="bi bi-journal-text text-primary"></i> Bank Ledger Statement
            </h6>
            <small class="text-muted" style="font-size: 11.5px;">
                Sequential bank transactions, electronic deposits, withdrawals, and calculated cumulative balance.
            </small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark border px-2.5 py-1 font-monospace" style="font-size: 11px;">
                Currency: LKR (Rs.)
            </span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-ledger align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 130px;">Date & TXN</th>
                    <th>Particulars & Bank Account</th>
                    <th style="width: 140px;">Method & Ref / Cheque</th>
                    <th class="text-end" style="width: 160px;">Deposits (Debit)</th>
                    <th class="text-end" style="width: 160px;">Withdrawals (Credit)</th>
                    <th class="text-end" style="width: 170px;">Running Balance</th>
                    <th class="text-center no-print" style="width: 70px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- 1. Opening Balance Brought Forward Row -->
                <tr class="opening-row">
                    <td>
                        <span class="badge bg-secondary text-white font-monospace" style="font-size: 11px;">B/F</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-bank text-secondary fs-6"></i>
                            <div>
                                <span class="fw-bold text-dark">Opening Bank Balance Brought Forward</span>
                                <small class="text-muted d-block" style="font-size: 11px;">Carried forward institutional balance prior to {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-light text-muted border font-monospace" style="font-size: 10.5px;">INITIAL BALANCE</span>
                    </td>
                    <td class="text-end text-muted">—</td>
                    <td class="text-end text-muted">—</td>
                    <td class="text-end fw-bold text-dark font-monospace" style="font-size: 13.5px;">
                        Rs. {{ number_format($openingBalance, 2) }}
                    </td>
                    <td class="text-center no-print text-muted">—</td>
                </tr>

                <!-- 2. Transaction Rows -->
                @forelse($transactions as $transaction)
                    @php
                        if ($transaction->type === 'income') {
                            $currentRunningBalance += (float)$transaction->amount;
                        } else {
                            $currentRunningBalance -= (float)$transaction->amount;
                        }
                    @endphp
                    <tr>
                        <!-- Date & Transaction No -->
                        <td>
                            <div class="fw-bold text-dark" style="font-size: 12.5px;">
                                {{ $transaction->transaction_date?->format('M d, Y') ?? '—' }}
                            </div>
                            <span class="badge bg-light text-secondary border font-monospace mt-0.5" style="font-size: 10px;">
                                {{ $transaction->transaction_no ?: 'TXN-' . str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>

                        <!-- Particulars & Bank -->
                        <td>
                            <div class="d-flex align-items-start gap-2">
                                <span class="badge {{ $transaction->type === 'income' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} p-1 rounded-circle flex-shrink-0 mt-0.5">
                                    <i class="bi {{ $transaction->type === 'income' ? 'bi-arrow-down-left' : 'bi-arrow-up-right' }}" style="font-size: 10px;"></i>
                                </span>
                                <div>
                                    <div class="fw-semibold text-dark" style="font-size: 13px; line-height: 1.3;">
                                        {{ $transaction->description }}
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                                        @if($transaction->bank_name)
                                            <span class="badge bg-light text-dark border" style="font-size: 10.5px;">
                                                <i class="bi bi-building me-1 text-primary"></i>{{ $transaction->bank_name }}
                                            </span>
                                        @endif
                                        @if($transaction->notes)
                                            <small class="text-muted" style="font-size: 11px;">
                                                <i class="bi bi-chat-left-text me-1"></i>{{ $transaction->notes }}
                                            </small>
                                        @endif
                                        @if($transaction->order_id || $transaction->customer_id || $transaction->cheque_id)
                                            @if($transaction->order)
                                                <a href="{{ route('orders.show', $transaction->order) }}" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none" style="font-size: 10px;">
                                                    <i class="bi bi-receipt me-1"></i>Order: {{ $transaction->order->order_no }}
                                                </a>
                                            @endif
                                            @if($transaction->customer)
                                                <span class="badge bg-light text-secondary border" style="font-size: 10px;">
                                                    <i class="bi bi-person me-1"></i>{{ $transaction->customer->name }}
                                                </span>
                                            @endif
                                            @if($transaction->cheque)
                                                <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 10px;">
                                                    <i class="bi bi-card-checklist me-1"></i>Cheque: {{ $transaction->cheque->cheque_number }}
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Method & Reference / Cheque -->
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <div>
                                    @if($transaction->transaction_method === 'cheque')
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning" style="font-size: 10.5px;">
                                            <i class="bi bi-journal-bookmark me-1"></i> Cheque
                                        </span>
                                    @else
                                        <span class="badge bg-info-subtle text-info-emphasis border border-info" style="font-size: 10.5px;">
                                            <i class="bi bi-arrow-left-right me-1"></i> Transfer
                                        </span>
                                    @endif
                                </div>
                                <div class="font-monospace small text-dark" style="font-size: 11px;">
                                    @if($transaction->cheque_no)
                                        <span class="text-secondary fw-semibold">CHQ:</span> {{ $transaction->cheque_no }}
                                    @elseif($transaction->reference_no)
                                        <span class="text-secondary fw-semibold">REF:</span> {{ $transaction->reference_no }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Bank Deposits (Debit / Receipts) -->
                        <td class="text-end font-monospace">
                            @if($transaction->type === 'income')
                                <span class="badge-inflow">
                                    + {{ number_format($transaction->amount, 2) }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <!-- Bank Withdrawals (Credit / Payments) -->
                        <td class="text-end font-monospace">
                            @if($transaction->type === 'expense')
                                <span class="badge-outflow">
                                    - {{ number_format($transaction->amount, 2) }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <!-- Cumulative Running Balance -->
                        <td class="text-end font-monospace fw-bold {{ $currentRunningBalance >= 0 ? 'text-dark' : 'text-danger' }}" style="font-size: 13.5px;">
                            Rs. {{ number_format($currentRunningBalance, 2) }}
                        </td>

                        <!-- Action Dropdown -->
                        <td class="text-center no-print">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border px-2 py-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 11px;">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size: 12.5px; border-radius: 8px;">
                                    <li>
                                        <a class="dropdown-item py-1.5" href="{{ route('bank-transactions.show', $transaction->id) }}">
                                            <i class="bi bi-eye text-primary me-2"></i> View Details
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-1.5" href="{{ route('bank-transactions.edit', $transaction->id) }}">
                                            <i class="bi bi-pencil text-secondary me-2"></i> Edit Record
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <div class="py-4">
                                <i class="bi bi-bank fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                <h6 class="fw-bold text-dark mb-1">No Bank Transactions Found</h6>
                                <p class="text-muted small mb-3">No bank transfers or cheque records were found matching the selected filter criteria.</p>
                                <a href="{{ route('bank-transactions.create') }}" class="btn btn-primary btn-sm shadow-sm" target="_blank">
                                    <i class="bi bi-plus-lg me-1"></i> Record Bank Entry
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <!-- 3. Totals & Carried Forward Footer -->
            <tfoot>
                <tr>
                    <td colspan="3" class="text-uppercase text-muted" style="letter-spacing: 0.5px; font-size: 12px;">
                        <strong>Statement Totals for Period</strong>
                    </td>
                    <td class="text-end font-monospace text-success fw-bold" style="font-size: 13.5px;">
                        + Rs. {{ number_format($incomeTotal, 2) }}
                    </td>
                    <td class="text-end font-monospace text-danger fw-bold" style="font-size: 13.5px;">
                        - Rs. {{ number_format($expenseTotal, 2) }}
                    </td>
                    <td class="text-end font-monospace fw-bold {{ $closingBalance >= 0 ? 'text-primary' : 'text-danger' }}" style="font-size: 14.5px;">
                        Rs. {{ number_format($closingBalance, 2) }}
                    </td>
                    <td class="no-print"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Card Footer Summary Information -->
    <div class="card-footer bg-light py-2.5 px-4 border-top d-flex align-items-center justify-content-between flex-wrap gap-2 text-muted small" style="font-size: 11.5px;">
        <div>
            <i class="bi bi-info-circle me-1"></i>
            Bank ledger position is calculated as <code>Opening Balance ({{ number_format($openingBalance, 2) }}) + Deposits ({{ number_format($incomeTotal, 2) }}) - Withdrawals ({{ number_format($expenseTotal, 2) }}) = Closing Balance ({{ number_format($closingBalance, 2) }})</code>.
        </div>
        <div>
            Reconciled against institutional bank accounts.
        </div>
    </div>
</div>

<!-- Print Only Signatures -->
<div class="print-signatures">
    <div>
        <div style="border-top: 1px solid #000; width: 220px; padding-top: 5px; text-align: center;">
            <strong>Finance Officer / Prepared By</strong>
            <div class="text-muted small">Signature & Date</div>
        </div>
    </div>
    <div>
        <div style="border-top: 1px solid #000; width: 220px; padding-top: 5px; text-align: center;">
            <strong>Internal Auditor / Reconciled By</strong>
            <div class="text-muted small">Signature & Date</div>
        </div>
    </div>
    <div>
        <div style="border-top: 1px solid #000; width: 220px; padding-top: 5px; text-align: center;">
            <strong>Managing Director / Approved By</strong>
            <div class="text-muted small">Signature & Date</div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Quick Preset Date Ranges
    function applyPreset(preset) {
        const today = new Date();
        const formatDate = (date) => {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        };

        let fromDate, toDate;

        if (preset === 'today') {
            fromDate = formatDate(today);
            toDate = formatDate(today);
        } else if (preset === 'this_week') {
            const firstDay = new Date(today.setDate(today.getDate() - today.getDay() + (today.getDay() === 0 ? -6 : 1))); // Monday
            const lastDay = new Date(firstDay);
            lastDay.setDate(lastDay.getDate() + 6); // Sunday
            fromDate = formatDate(firstDay);
            toDate = formatDate(lastDay);
        } else if (preset === 'this_month') {
            const first = new Date(today.getFullYear(), today.getMonth(), 1);
            const last = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            fromDate = formatDate(first);
            toDate = formatDate(last);
        } else if (preset === 'last_month') {
            const first = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            const last = new Date(today.getFullYear(), today.getMonth(), 0);
            fromDate = formatDate(first);
            toDate = formatDate(last);
        } else if (preset === 'this_year') {
            const first = new Date(today.getFullYear(), 0, 1);
            const last = new Date(today.getFullYear(), 11, 31);
            fromDate = formatDate(first);
            toDate = formatDate(last);
        }

        if (fromDate && toDate) {
            document.getElementById('date_from').value = fromDate;
            document.getElementById('date_to').value = toDate;
            document.getElementById('bankBookFilterForm').submit();
        }
    }
</script>
@endsection