@extends('layouts.app')
@section('title', 'Order Profit & Loss Statement')
@section('page-title', 'Order Profit & Loss')

@section('content')

<style id="pnlDocumentStyles">
    /* ========================================================================= */
    /* FORMAL PAPER FINANCIAL STATEMENT STYLING (TRITICAL P&L STYLE)             */
    /* ========================================================================= */
    .pnl-desk-bg {
        background: #e2e8f0;
        padding: 24px 12px;
        border-radius: 12px;
        margin-bottom: 30px;
    }

    .pnl-paper-sheet {
        background: #ffffff;
        max-width: 880px;
        margin: 0 auto;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, 0.12), 0 2px 6px rgba(15, 23, 42, 0.06);
        padding: 45px 50px;
        color: #0f172a;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
        position: relative;
    }

    /* Formal Top Bar on the sheet */
    .pnl-paper-topbar {
        height: 5px;
        background: linear-gradient(90deg, #0f172a 0%, #1e3a8a 60%, #3b82f6 100%);
        margin: -45px -50px 30px -50px;
        border-radius: 3px 3px 0 0;
    }

    /* Document Header */
    .pnl-doc-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 20px;
        border-bottom: 2px solid #0f172a;
        margin-bottom: 24px;
    }

    .pnl-company-title {
        font-size: 22px;
        font-weight: 900;
        letter-spacing: -0.5px;
        color: #0f172a;
        text-transform: uppercase;
    }

    .company-meta-text {
        font-size: 11.5px;
        color: #475569;
        line-height: 1.5;
        margin-top: 4px;
    }

    .pnl-doc-title-block {
        text-align: right;
    }

    .pnl-doc-main-title {
        font-size: 17px;
        font-weight: 800;
        color: #1e3a8a;
        letter-spacing: -0.3px;
        text-transform: uppercase;
        margin: 0 0 4px 0;
    }

    .pnl-doc-meta {
        font-size: 11.5px;
        color: #475569;
        line-height: 1.5;
    }

    /* Customer & Event Meta Box */
    .pnl-meta-box {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 16px 20px;
        margin-bottom: 28px;
        font-size: 13px;
    }

    @media (max-width: 640px) {
        .pnl-meta-box {
            grid-template-columns: 1fr;
        }

        .pnl-paper-sheet {
            padding: 24px 18px;
        }

        .pnl-paper-topbar {
            margin: -24px -18px 20px -18px;
        }
    }

    .pnl-meta-heading {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #475569;
        border-bottom: 1px solid #cbd5e1;
        padding-bottom: 5px;
        margin-bottom: 8px;
    }

    .pnl-meta-row {
        display: flex;
        margin-bottom: 4px;
        line-height: 1.4;
    }

    .pnl-meta-label {
        width: 125px;
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
        flex-shrink: 0;
    }

    .pnl-meta-val {
        color: #0f172a;
        font-weight: 700;
        font-size: 12.5px;
    }

    /* Formal Financial Section Headings */
    .pnl-sec-title {
        font-size: 13.5px;
        font-weight: 800;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #f1f5f9;
        border-left: 4px solid #0f172a;
        padding: 8px 12px;
        margin: 28px 0 12px 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Formal Tables */
    .pnl-table-wrap {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin-bottom: 14px;
        border-radius: 4px;
    }

    .pnl-table-wrap::-webkit-scrollbar {
        height: 6px;
    }

    .pnl-table-wrap::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .pnl-table-wrap::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .pnl-paper-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
        font-size: 13px;
    }

    .pnl-paper-table th {
        background: #f8fafc;
        border-top: 1px solid #94a3b8;
        border-bottom: 1.5px solid #0f172a;
        color: #0f172a;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 8px 10px;
        vertical-align: middle;
    }

    .pnl-paper-table td {
        padding: 8px 10px;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
        color: #1e293b;
    }

    .pnl-paper-table tbody tr:hover {
        background: #fafafa;
    }

    .pnl-paper-table .row-total td {
        font-weight: 800;
        border-top: 1.5px solid #0f172a;
        border-bottom: 1.5px solid #0f172a;
        background: #f8fafc;
        font-size: 13.5px;
    }

    /* Formal P&L Statement Box */
    .pnl-statement-ledger {
        border: 2px solid #0f172a;
        border-radius: 4px;
        margin-top: 28px;
        margin-bottom: 24px;
        overflow: hidden;
    }

    .pnl-statement-header {
        background: #0f172a;
        color: #ffffff;
        font-size: 12.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 8px 16px;
        text-align: center;
    }

    .pnl-statement-body {
        padding: 14px 20px;
        background: #ffffff;
    }

    .pnl-stmt-line {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        font-size: 13.5px;
        border-bottom: 1px dotted #cbd5e1;
    }

    .pnl-stmt-line.total-line {
        border-top: 1.5px solid #0f172a;
        border-bottom: 4px double #0f172a;
        padding: 10px 0 8px 0;
        margin-top: 6px;
        font-size: 16px;
        font-weight: 900;
    }

    /* Document Signatures Area */
    .pnl-signatures-area {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 30px;
        margin-top: 45px;
        padding-top: 20px;
        border-top: 1px solid #cbd5e1;
        font-size: 11.5px;
        text-align: center;
    }

    .pnl-sig-line {
        border-top: 1px dashed #64748b;
        margin-top: 40px;
        padding-top: 6px;
        font-weight: 700;
        color: #475569;
    }

    /* Print Rules */
    @media print {
        body > *:not(.app-container),
        .no-print,
        .sidebar,
        .header,
        .page-header,
        form,
        .modal,
        .card,
        .btn {
            display: none !important;
        }

        .main-content,
        .app-container {
            margin: 0 !important;
            padding: 0 !important;
            display: block !important;
            width: 100% !important;
            position: static !important;
            left: 0 !important;
            top: 0 !important;
        }

        body, html {
            background: #ffffff !important;
            color: #000000 !important;
            font-size: 11pt !important;
            margin: 0 !important;
            padding: 0 !important;
            min-width: 100% !important;
        }

        .pnl-desk-bg {
            background: #ffffff !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .pnl-paper-sheet {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
        }

        .pnl-paper-topbar {
            display: none !important;
        }

        .pnl-statement-ledger {
            border-color: #000000 !important;
            break-inside: avoid;
        }

        .pnl-paper-table th {
            background: #f1f5f9 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .pnl-table-wrap {
            overflow: visible !important;
            margin-bottom: 14px !important;
        }

        .pnl-paper-table {
            min-width: 100% !important;
            width: 100% !important;
        }
    }
</style>

<!-- ========================================================================= -->
<!-- SEARCH & FILTER BAR (Screen Only - Matching Tritcal Style)               -->
<!-- ========================================================================= -->
<div class="card no-print mb-4 border-0 shadow-sm" style="border-radius: 10px;">
    <div class="card-header bg-white py-3"
        style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; border-bottom: 1px solid #f1f5f9;">
        <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
            <i class="bi bi-search text-primary"></i> Select Order to Generate P&amp;L Statement
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('reports.monthly-pl') }}" class="btn btn-sm btn-outline-primary fw-semibold">
                <i class="bi bi-calendar3 me-1"></i> Monthly P&amp;L
            </a>
            @if($singleOrder)
                <a href="{{ route('reports.order-pl') }}" class="btn btn-sm btn-outline-secondary fw-semibold">
                    <i class="bi bi-list-task me-1"></i> View All Orders
                </a>
                <button type="button" onclick="printPnlStatement()" class="btn btn-sm btn-primary fw-semibold shadow-sm">
                    <i class="bi bi-printer me-1"></i> Print Statement
                </button>
            @else
                <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-secondary fw-semibold">
                    <i class="bi bi-printer me-1"></i> Print Overview Table
                </button>
            @endif
        </div>
    </div>
    <div class="card-body p-3">
        <form method="GET" action="{{ route('reports.order-pl') }}" id="pnlSearchForm">
            <div class="row g-3">
                <!-- Search by Customer -->
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label small fw-semibold text-muted mb-1">
                        <i class="bi bi-person me-1"></i> Customer Search
                    </label>
                    <select name="customer_id" id="filter_customer_id" class="form-select form-select-sm"
                        onchange="onCustomerFilterChange(this.value)">
                        <option value="">-- All Customers --</option>
                        @foreach($allCustomers as $cust)
                            <option value="{{ $cust->id }}" {{ ($customerId == $cust->id || ($singleOrder && $singleOrder->customer_id == $cust->id)) ? 'selected' : '' }}>
                                {{ $cust->name }} ({{ $cust->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Select Specific Order Directly -->
                <div class="col-12 col-md-6 col-lg-4">
                    <label class="form-label small fw-semibold text-muted mb-1">
                        <i class="bi bi-receipt me-1"></i> Choose Order
                        <span class="text-primary fw-normal" style="font-size: 11px;">(Generates Statement)</span>
                    </label>
                    <select name="order_id" id="filter_order_id" class="form-select form-select-sm"
                        onchange="if(this.value){ this.form.submit(); }">
                        <option value="">-- Choose Order to View P&amp;L --</option>
                        @foreach($allOrdersQuickList as $qo)
                            @if($customerId > 0 && (int)$qo['customer_id'] !== (int)$customerId)
                                @continue
                            @endif
                            <option value="{{ $qo['id'] }}" data-customer-id="{{ $qo['customer_id'] }}"
                                {{ ($singleOrder && $singleOrder->id == $qo['id']) ? 'selected' : '' }}>
                                #{{ $qo['order_no'] }} - {{ $qo['event_name'] }} ({{ $qo['customer_name'] }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Search by Order Code -->
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label small fw-semibold text-muted mb-1">
                        <i class="bi bi-hash me-1"></i> Order #
                    </label>
                    <input type="text" name="order_no" id="filter_order_no" class="form-control form-control-sm"
                        placeholder="e.g. ORD-000001" value="{{ $orderNo }}">
                </div>

                <!-- Search by Event / Customer -->
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label small fw-semibold text-muted mb-1">
                        <i class="bi bi-tag me-1"></i> Event / Keyword
                    </label>
                    <input type="text" name="event_name" id="filter_event_name" class="form-control form-control-sm"
                        placeholder="e.g. Wedding, Birthday..." value="{{ $eventName }}">
                </div>

                <!-- Submit / Reset Buttons -->
                <div class="col-12 col-md-12 col-lg-1 d-flex align-items-end gap-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold" title="Apply Filter" style="height: 31px;">
                        <i class="bi bi-funnel"></i>
                    </button>
                    @if($singleOrder || $customerId || $orderNo || $eventName || request()->hasAny(['date_from', 'date_to']))
                        <a href="{{ route('reports.order-pl') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters"
                            style="height: 31px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Date Range Filters & Quick Presets (Screen only) -->
            <div class="row g-2 align-items-center mt-2 pt-2 border-top">
                <div class="col-12 col-md-auto d-flex align-items-center gap-2">
                    <span class="small fw-semibold text-muted"><i class="bi bi-calendar-range me-1"></i> Date Filter:</span>
                    <input type="date" name="date_from" id="filter_date_from" class="form-control form-control-sm" style="width: 140px;" value="{{ $dateFrom }}">
                    <span class="text-muted small">to</span>
                    <input type="date" name="date_to" id="filter_date_to" class="form-control form-control-sm" style="width: 140px;" value="{{ $dateTo }}">
                    <button type="submit" class="btn btn-xs btn-outline-primary py-1 px-2 small">Update</button>
                </div>
                <div class="col-12 col-md d-flex gap-1 flex-wrap align-items-center">
                    <span class="text-muted small ms-md-2 me-1" style="font-size: 11.5px;">Quick:</span>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 11px;" onclick="setDateRange('today')">Today</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 11px;" onclick="setDateRange('this_week')">This Week</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 11px;" onclick="setDateRange('this_month')">This Month</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 11px;" onclick="setDateRange('last_month')">Last Month</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 11px;" onclick="setDateRange('this_year')">This Year</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 11px;" onclick="setDateRange('all_time')">All Time</button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($singleOrder && $singleOrderData)
    <!-- ========================================================================= -->
    <!-- PAPER DOCUMENT VIEW: FORMAL FINANCIAL STATEMENT SHEET (Tritcal Style)     -->
    <!-- ========================================================================= -->
    <div class="pnl-desk-bg">
        <!-- Action Toolbar for Statement Document -->
        <div class="no-print"
            style="max-width: 880px; margin: 0 auto 14px auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div style="font-size: 13px; color: #475569; font-weight: 600;">
                <i class="bi bi-file-earmark-text text-primary me-1"></i> Formal Order P&amp;L Statement Document
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('reports.order-pl') }}" class="btn btn-sm btn-secondary fw-semibold">
                    <i class="bi bi-arrow-left me-1"></i> Back to Overview
                </a>
                <button type="button" onclick="printPnlStatement()"
                    class="btn btn-sm btn-primary fw-bold px-3 shadow-sm"
                    style="box-shadow: 0 2px 6px rgba(37,99,235,0.25);">
                    <i class="bi bi-printer me-1"></i> Print Document / Save PDF
                </button>
            </div>
        </div>

        <div class="pnl-paper-sheet">
            <!-- Top Colored Accent Bar -->
            <div class="pnl-paper-topbar"></div>

            <!-- Formal Document Header -->
            <div class="pnl-doc-header">
                <div>
                    <div class="pnl-company-title">EVENTPRO MANAGEMENT SYSTEM</div>
                    <div class="company-meta-text">
                        Reg: EV-00482910 &bull; Premier Event &amp; Production Management<br>
                        No. 142/2, Main Street, Colombo, Sri Lanka<br>
                        Tel: +94 11 25 80 900 / +94 77 12 34 567 &bull; info@eventpro.lk
                    </div>
                </div>
                <div class="pnl-doc-title-block">
                    <h1 class="pnl-doc-main-title">ORDER PROFIT &amp; LOSS STATEMENT</h1>
                    <div class="pnl-doc-meta">
                        <div>Statement Ref: <strong>PNL-{{ $singleOrder->order_no }}</strong></div>
                        <div>Date of Issue: <strong>{{ date('d M Y') }}</strong></div>
                        <div>Currency: <strong>LKR (Rs.)</strong></div>
                    </div>
                </div>
            </div>

            <!-- Customer & Order Specifications Meta Box -->
            <div class="pnl-meta-box">
                <!-- Left: Customer Details -->
                <div>
                    <div class="pnl-meta-heading"><i class="bi bi-person-badge me-1"></i> Customer Information</div>
                    <div class="pnl-meta-row">
                        <span class="pnl-meta-label">Customer Name:</span>
                        <span class="pnl-meta-val">{{ $singleOrder->customer?->name ?? 'Walk-in Customer' }}</span>
                    </div>
                    <div class="pnl-meta-row">
                        <span class="pnl-meta-label">Phone:</span>
                        <span class="pnl-meta-val">{{ $singleOrder->customer?->phone ?? 'N/A' }}</span>
                    </div>
                    @if($singleOrder->customer?->email)
                        <div class="pnl-meta-row">
                            <span class="pnl-meta-label">Email:</span>
                            <span class="pnl-meta-val">{{ $singleOrder->customer->email }}</span>
                        </div>
                    @endif
                    <div class="pnl-meta-row">
                        <span class="pnl-meta-label">Location / City:</span>
                        <span class="pnl-meta-val">{{ $singleOrder->customer?->city ?? $singleOrder->customer?->address ?? 'Not Specified' }}</span>
                    </div>
                </div>

                <!-- Right: Order Specifications -->
                <div>
                    <div class="pnl-meta-heading"><i class="bi bi-calendar-event me-1"></i> Order &amp; Event Specifications</div>
                    <div class="pnl-meta-row">
                        <span class="pnl-meta-label">Order Code:</span>
                        <span class="pnl-meta-val">#{{ $singleOrder->order_no }}</span>
                    </div>
                    <div class="pnl-meta-row">
                        <span class="pnl-meta-label">Linked Event:</span>
                        <span class="pnl-meta-val">{{ $singleOrder->event?->event_name ?? 'Direct Order' }}</span>
                    </div>
                    <div class="pnl-meta-row">
                        <span class="pnl-meta-label">Event Date:</span>
                        <span class="pnl-meta-val">
                            {{ $singleOrder->event?->event_date?->format('d M Y') ?? $singleOrder->order_date?->format('d M Y') ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="pnl-meta-row">
                        <span class="pnl-meta-label">Venue:</span>
                        <span class="pnl-meta-val">{{ $singleOrder->event?->venue ?? 'N/A' }}</span>
                    </div>
                    <div class="pnl-meta-row">
                        <span class="pnl-meta-label">Payment Status:</span>
                        <span class="pnl-meta-val"
                            style="color: {{ $singleOrder->payment_status === 'paid' ? '#059669' : ($singleOrder->payment_status === 'partial' ? '#0284c7' : '#d97706') }}; text-transform: uppercase;">
                            {{ $singleOrder->payment_status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- ============================================================= -->
            <!-- 1. ORDER TOTAL VALUE & REVENUE                                -->
            <!-- ============================================================= -->
            <div class="pnl-sec-title">
                <span>1. Order Total Value &amp; Revenue</span>
                <span style="font-size: 11px; font-weight: 600; color: #475569; text-transform: none;">Contract Valuation &amp; Items</span>
            </div>
            <div class="pnl-table-wrap">
                <table class="pnl-paper-table" style="min-width: 580px;">
                    <thead>
                        <tr>
                            <th style="width: 50%;">Valuation Line Item / Description</th>
                            <th style="width: 15%; text-align: center;">Classification</th>
                            <th style="width: 15%; text-align: center;">Qty</th>
                            <th style="width: 20%; text-align: right;">Amount (Rs.)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($singleOrder->services && $singleOrder->services->count() > 0)
                            @foreach($singleOrder->services as $srv)
                                <tr>
                                    <td>
                                        <strong>{{ $srv->description }}</strong>
                                        @if($srv->service)
                                            <div style="font-size: 11.5px; color: #64748b;">Service: {{ $srv->service->name }}</div>
                                        @endif
                                    </td>
                                    <td style="text-align: center; font-size: 11.5px; color: #475569;">
                                        {{ $loop->first && $singleOrder->event ? 'Base Package' : 'Service Line' }}
                                    </td>
                                    <td style="text-align: center; font-size: 12px; color: #475569;">
                                        {{ $srv->qty ?? $srv->quantity ?? 1 }}
                                    </td>
                                    <td style="text-align: right; font-weight: 700;">
                                        {{ number_format($srv->total_price, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        @elseif($singleOrder->event)
                            <tr>
                                <td>
                                    <strong>Base Event Package ({{ $singleOrder->event->event_name }})</strong>
                                    <div style="font-size: 11.5px; color: #64748b;">Package: {{ $singleOrder->event->subCategory?->name ?? 'Custom' }}</div>
                                </td>
                                <td style="text-align: center; font-size: 11.5px; color: #475569;">Package Price</td>
                                <td style="text-align: center; font-size: 12px; color: #475569;">1</td>
                                <td style="text-align: right; font-weight: 700;">
                                    {{ number_format($singleOrder->event->package_sell_price, 2) }}
                                </td>
                            </tr>
                        @endif

                        @if($singleOrder->otherCharges && $singleOrder->otherCharges->count() > 0)
                            @foreach($singleOrder->otherCharges as $chg)
                                <tr>
                                    <td>
                                        <strong>{{ $chg->charge_name ?? 'Additional Charge' }}</strong>
                                    </td>
                                    <td style="text-align: center; font-size: 11.5px; color: #475569;">Other Charge</td>
                                    <td style="text-align: center; font-size: 12px; color: #475569;">1</td>
                                    <td style="text-align: right; font-weight: 700;">
                                        {{ number_format($chg->amount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        @if($singleOrder->discount > 0)
                            <tr>
                                <td>
                                    <strong style="color: #b91c1c;">Special Contract Discount / Waiver</strong>
                                </td>
                                <td style="text-align: center; font-size: 11.5px; color: #b91c1c;">Deduction</td>
                                <td style="text-align: center; font-size: 12px; color: #b91c1c;">-</td>
                                <td style="text-align: right; font-weight: 700; color: #dc2626;">
                                    - {{ number_format($singleOrder->discount, 2) }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr class="row-total">
                            <td colspan="3">TOTAL ORDER REVENUE (Net Billable Value)</td>
                            <td style="text-align: right; font-size: 14.5px; color: #0f172a;">
                                {{ number_format($singleOrderData['revenue'], 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- ============================================================= -->
            <!-- 2. PAYMENT HISTORY & DUE CALCULATION                          -->
            <!-- ============================================================= -->
            <div class="pnl-sec-title">
                <span>2. Payment History (Customer Collections)</span>
                <span style="font-size: 11px; font-weight: 600; color: #475569; text-transform: none;">
                    {{ $singleOrder->payments ? $singleOrder->payments->count() : 0 }} Receipt(s) Recorded
                </span>
            </div>
            @if(!$singleOrder->payments || $singleOrder->payments->count() == 0)
                <div style="padding: 16px; text-align: center; color: #94a3b8; font-style: italic; border: 1px dashed #cbd5e1; border-radius: 4px; margin-bottom: 14px;">
                    No customer payments recorded for this order yet.
                </div>
            @else
                <div class="pnl-table-wrap">
                    <table class="pnl-paper-table" style="min-width: 620px;">
                        <thead>
                            <tr>
                                <th style="width: 35%;">Receipt / Voucher #</th>
                                <th style="width: 18%;">Payment Date</th>
                                <th style="width: 25%;">Method / Channel</th>
                                <th style="width: 22%; text-align: right;">Amount (Rs.)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($singleOrder->payments as $pay)
                                <tr>
                                    <td>
                                        <strong style="font-family: monospace;">#{{ $pay->payment_no }}</strong>
                                        @if($pay->notes)
                                            <div style="font-size: 11px; color: #64748b;">{{ $pay->notes }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $pay->payment_date ? date('d M Y', strtotime($pay->payment_date)) : 'N/A' }}</td>
                                    <td style="font-size: 12px;">
                                        @if($pay->payment_method === 'bank_transfer' || $pay->payment_method === 'bank')
                                            Bank Transfer {{ $pay->bank_name ? "({$pay->bank_name})" : '' }}
                                        @elseif($pay->payment_method === 'cheque')
                                            Cheque {{ $pay->cheque_no ? "(#{$pay->cheque_no})" : '' }}
                                        @else
                                            Cash Collection
                                        @endif
                                    </td>
                                    <td style="text-align: right; font-weight: 700; color: #059669;">
                                        {{ number_format($pay->amount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="row-total">
                                <td colspan="3">TOTAL PAYMENTS RECEIVED</td>
                                <td style="text-align: right; font-size: 14.5px; color: #059669;">
                                    {{ number_format($singleOrderData['paid'], 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif

            <!-- Balance Due (Outstanding) Summary Banner -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; margin: 8px 0 18px 0; font-size: 13px;">
                <span style="font-weight: 800; color: #334155; font-size: 15px;">
                    Balance Due (Outstanding Customer Balance):
                </span>
                <span style="font-weight: 800; font-size: 16px; color: {{ $singleOrderData['due'] > 0 ? '#dc2626' : '#166534' }};">
                    {{ number_format($singleOrderData['due'], 2) }}
                    @if($singleOrderData['due'] <= 0)
                        <span style="font-size: 11.5px; font-weight: 700; color: #059669; margin-left: 6px;">(Settled Full)</span>
                    @endif
                </span>
            </div>

            <!-- ============================================================= -->
            <!-- 3. DIRECT EXPENSES (ORDER & EVENT DIRECT COSTS)               -->
            <!-- ============================================================= -->
            <div class="pnl-sec-title">
                <span>3. Direct Expenses (Order &amp; Event Direct Costs)</span>
                <span style="font-size: 11px; font-weight: 600; color: #475569; text-transform: none;">
                    {{ count($singleOrderData['direct_expenses']) + ($singleOrderData['package_cost'] > 0 ? 1 : 0) }} Cost Item(s)
                </span>
            </div>
            @if(count($singleOrderData['direct_expenses']) == 0 && $singleOrderData['package_cost'] <= 0)
                <div style="padding: 16px; text-align: center; color: #94a3b8; font-style: italic; border: 1px dashed #cbd5e1; border-radius: 4px; margin-bottom: 14px;">
                    No direct expenses or package costs recorded for this order yet.
                </div>
            @else
                <div class="pnl-table-wrap">
                    <table class="pnl-paper-table" style="min-width: 620px;">
                        <thead>
                            <tr>
                                <th style="width: 32%;">Description</th>
                                <th style="width: 16%;">Voucher #</th>
                                <th style="width: 16%;">Date</th>
                                <th style="width: 18%;">Category / Mode</th>
                                <th style="width: 18%; text-align: right;">Amount (Rs.)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($singleOrderData['package_cost'] > 0)
                                <tr>
                                    <td>
                                        <strong>Base Event Package Direct Cost</strong>
                                        <div style="font-size: 11px; color: #64748b;">Estimated direct production &amp; execution cost</div>
                                    </td>
                                    <td style="font-family: monospace; font-size: 12px; font-weight: 600; color: #64748b;">
                                        PACKAGE-COST
                                    </td>
                                    <td>{{ $singleOrder->order_date ? date('d M Y', strtotime($singleOrder->order_date)) : 'N/A' }}</td>
                                    <td style="font-size: 12px;">Package Execution</td>
                                    <td style="text-align: right; font-weight: 700; color: #dc2626;">
                                        {{ number_format($singleOrderData['package_cost'], 2) }}
                                    </td>
                                </tr>
                            @endif

                            @foreach($singleOrderData['direct_expenses'] as $exp)
                                <tr>
                                    <td>
                                        <strong>{{ $exp->description ?: $exp->category ?: 'Direct Expense' }}</strong>
                                        @if($exp->notes)
                                            <div style="font-size: 11px; color: #64748b;">{{ $exp->notes }}</div>
                                        @endif
                                    </td>
                                    <td style="font-family: monospace; font-size: 12px; font-weight: 600;">
                                        #{{ $exp->expense_no }}
                                    </td>
                                    <td>{{ $exp->expense_date ? date('d M Y', strtotime($exp->expense_date)) : 'N/A' }}</td>
                                    <td style="font-size: 12px;">
                                        {{ $exp->category ?: 'Direct Cost' }}
                                        <div style="font-size: 11px; color: #64748b;">via {{ $exp->payment_method }}</div>
                                    </td>
                                    <td style="text-align: right; font-weight: 700; color: #dc2626;">
                                        {{ number_format($exp->amount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="row-total">
                                <td colspan="4">TOTAL DIRECT COST (EXPENSES)</td>
                                <td style="text-align: right; font-size: 14.5px; color: #dc2626;">
                                    {{ number_format($singleOrderData['total_direct_cost'], 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif

            <!-- ============================================================= -->
            <!-- 4. NET ORDER PROFIT OR LOSS STATEMENT LEDGER                  -->
            <!-- ============================================================= -->
            <div class="pnl-statement-ledger">
                <div class="pnl-statement-header">
                    STATEMENT OF PROFIT OR LOSS (P&amp;L)
                </div>
                <div class="pnl-statement-body">
                    <div class="pnl-stmt-line">
                        <span>Total Order Revenue</span>
                        <strong style="color: #0f172a;">+ {{ number_format($singleOrderData['revenue'], 2) }}</strong>
                    </div>
                    <div class="pnl-stmt-line">
                        <span style="color: #b91c1c;">Total Direct Expenses (Order Direct Cost)</span>
                        <strong style="color: #b91c1c;">- {{ number_format($singleOrderData['total_direct_cost'], 2) }}</strong>
                    </div>
                    <div class="pnl-stmt-line total-line">
                        <span>
                            NET ORDER {{ $singleOrderData['is_profit'] ? 'PROFIT' : 'LOSS' }}:
                        </span>
                        <span style="color: {{ $singleOrderData['is_profit'] ? '#15803d' : '#b91c1c' }};">
                            {{ ($singleOrderData['net_profit_loss'] < 0 ? '-' : '') . number_format(abs($singleOrderData['net_profit_loss']), 2) }}
                        </span>
                    </div>

                    <!-- Margin and Summary notes -->
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-top: 14px; font-size: 12.5px; color: #475569;">
                        <div>
                            <strong>Classification:</strong>
                            <span style="font-weight: 800; color: {{ $singleOrderData['is_profit'] ? '#15803d' : '#b91c1c' }};">
                                {{ $singleOrderData['is_profit'] ? 'PROFITABLE ORDER' : 'LOSS INCURRED' }}
                            </span>
                            &nbsp;|&nbsp;
                            <strong>Profit Margin:</strong> <strong>{{ $singleOrderData['profit_margin'] }}%</strong>
                        </div>
                        <div>
                            <strong>Cash Collections Position:</strong>
                            Received ({{ number_format($singleOrderData['paid'], 2) }}) - Cost
                            ({{ number_format($singleOrderData['total_direct_cost'], 2) }}) =
                            <strong style="color: {{ $singleOrderData['cash_profit_loss'] >= 0 ? '#059669' : '#dc2626' }};">
                                {{ ($singleOrderData['cash_profit_loss'] < 0 ? '-' : '') . number_format(abs($singleOrderData['cash_profit_loss']), 2) }}
                            </strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formal Document Signatures Area -->
            <div class="pnl-signatures-area">
                <div>
                    <div style="font-size: 11px; color: #64748b;">Prepared By</div>
                    <div class="pnl-sig-line">
                        {{ auth()->user()->name ?? 'Accountant' }}
                    </div>
                </div>
                <div>
                    <div style="font-size: 11px; color: #64748b;">Checked &amp; Verified</div>
                    <div class="pnl-sig-line">
                        Accounts &amp; Finance
                    </div>
                </div>
                <div>
                    <div style="font-size: 11px; color: #64748b;">Approved &amp; Authorized</div>
                    <div class="pnl-sig-line">
                        Managing Director / Partner
                    </div>
                </div>
            </div>

            <!-- Bottom Disclaimer Notice -->
            <div style="text-align: center; margin-top: 25px; font-size: 10.5px; color: #94a3b8; border-top: 1px dotted #e2e8f0; padding-top: 10px;">
                This document is a formal order profitability and revenue statement generated by EventPro Management System.
            </div>
        </div>
    </div>

@else

    <!-- ========================================================================= -->
    <!-- OVERVIEW STATS CARDS                                                      -->
    <!-- ========================================================================= -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                        style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); font-size: 20px;">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div>
                        <div class="small fw-semibold text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Total Revenue</div>
                        <div class="fs-5 fw-bold text-success">Rs. {{ number_format($totalRevenue, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                        style="width: 48px; height: 48px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); font-size: 20px;">
                        <i class="bi bi-credit-card-2-front"></i>
                    </div>
                    <div>
                        <div class="small fw-semibold text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Direct Costs</div>
                        <div class="fs-5 fw-bold text-danger">Rs. {{ number_format($totalCost, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                        style="width: 48px; height: 48px; background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); font-size: 20px;">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div>
                        <div class="small fw-semibold text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Net Profit</div>
                        <div class="fs-5 fw-bold text-primary">Rs. {{ number_format($totalProfit, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white"
                        style="width: 48px; height: 48px; background: linear-gradient(135deg, #64748b 0%, #475569 100%); font-size: 20px;">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div>
                        <div class="small fw-semibold text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Total Orders</div>
                        <div class="fs-5 fw-bold text-dark">{{ $orders->count() }} Registered</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- OVERVIEW DIRECTORY TABLE: ALL ORDERS P&L SUMMARY (Tritcal Style)         -->
    <!-- ========================================================================= -->
    <div class="card border-0 shadow-sm" style="border-radius: 10px; overflow: hidden;">
        <div class="card-header bg-white py-3"
            style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; border-bottom: 1px solid #f1f5f9;">
            <div class="card-title mb-0 fw-bold fs-6 text-dark">
                <i class="bi bi-table text-primary me-1"></i> All Orders Profit &amp; Loss Overview
                <span class="badge bg-light text-secondary border ms-1">({{ count($orders) }})</span>
            </div>
            <div style="font-size: 12.5px; color: #64748b;">
                Select any order to view its formal paper P&amp;L statement
            </div>
        </div>
        <div class="card-body p-0">
            @if(count($orders) === 0)
                <div style="padding: 40px; text-align: center; color: #94a3b8;">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                    No orders found matching the specified filters. Try resetting search criteria.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                        <thead class="table-light">
                            <tr>
                                <th>Order No</th>
                                <th>Event &amp; Package</th>
                                <th>Customer</th>
                                <th style="text-align: right;">Subtotal</th>
                                <th style="text-align: right;">Discount</th>
                                <th style="text-align: right;">Total Revenue</th>
                                <th style="text-align: right;">Total Paid</th>
                                <th style="text-align: right;">Balance DUE</th>
                                <th style="text-align: right;">Direct Cost</th>
                                <th style="text-align: right;">Net Profit / Loss</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $row)
                                <tr>
                                    <td>
                                        <a href="{{ route('reports.order-pl', ['order_id' => $row['order']->id]) }}"
                                            style="font-weight: 800; color: #2563eb; text-decoration: none;">
                                            #{{ $row['order']->order_no }}
                                        </a>
                                        <div style="font-size: 11px; color: #64748b;">
                                            {{ $row['order']->order_date ? date('d M Y', strtotime($row['order']->order_date)) : '' }}
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $row['order']->event?->event_name ?? 'Direct Order' }}</strong>
                                        <div style="font-size: 11px; color: #64748b;">
                                            {{ $row['order']->event?->subCategory?->name ?? 'Standard Package' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div><strong>{{ $row['order']->customer?->name ?? 'Walk-in' }}</strong></div>
                                        <div style="font-size: 11px; color: #64748b;">
                                            {{ $row['order']->customer?->phone ?? '—' }}
                                        </div>
                                    </td>
                                    <td style="text-align: right; font-weight: 600;">
                                        {{ number_format($row['sub_total'], 2) }}
                                    </td>
                                    <td style="text-align: right; color: #b91c1c;">
                                        {{ $row['discount'] > 0 ? '-' . number_format($row['discount'], 2) : '0.00' }}
                                    </td>
                                    <td style="text-align: right; font-weight: 800; color: #0f172a;">
                                        {{ number_format($row['revenue'], 2) }}
                                    </td>
                                    <td style="text-align: right; color: #059669; font-weight: 600;">
                                        {{ number_format($row['paid'], 2) }}
                                    </td>
                                    <td style="text-align: right; color: {{ $row['due'] > 0 ? '#dc2626' : '#64748b' }}; font-weight: 700;">
                                        {{ number_format($row['due'], 2) }}
                                    </td>
                                    <td style="text-align: right; color: #dc2626;">
                                        {{ number_format($row['cost'], 2) }}
                                    </td>
                                    <td style="text-align: right;">
                                        <span class="badge"
                                            style="background: {{ $row['is_profit'] ? '#dcfce7' : '#fee2e2' }}; color: {{ $row['is_profit'] ? '#166534' : '#991b1b' }}; font-size: 12px; font-weight: 800; padding: 4px 8px;">
                                            {{ ($row['profit'] < 0 ? '-' : '') . number_format(abs($row['profit']), 2) }}
                                        </span>
                                        <div style="font-size: 10.5px; color: #64748b; margin-top: 2px;">
                                            {{ $row['margin'] }}% margin
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="{{ route('reports.order-pl', ['order_id' => $row['order']->id]) }}" class="btn btn-sm btn-primary"
                                            style="padding: 4px 10px; font-size: 12px; font-weight: 700;">
                                            <i class="bi bi-file-earmark-text me-1"></i> View Statement
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light" style="font-weight: 800; font-size: 13.5px; border-top: 2px solid #cbd5e1;">
                            <tr>
                                <td colspan="3">TOTALS ({{ count($orders) }} Orders)</td>
                                <td style="text-align: right;">{{ number_format($totalSubTotal, 2) }}</td>
                                <td style="text-align: right; color: #b91c1c;">-{{ number_format($totalDiscount, 2) }}</td>
                                <td style="text-align: right; color: #0f172a;">{{ number_format($totalRevenue, 2) }}</td>
                                <td style="text-align: right; color: #059669;">{{ number_format($totalPaid, 2) }}</td>
                                <td style="text-align: right; color: #dc2626;">{{ number_format($totalDue, 2) }}</td>
                                <td style="text-align: right; color: #dc2626;">{{ number_format($totalCost, 2) }}</td>
                                <td style="text-align: right; color: {{ $totalProfit >= 0 ? '#15803d' : '#b91c1c' }}; font-size: 14.5px;">
                                    {{ ($totalProfit < 0 ? '-' : '') . number_format(abs($totalProfit), 2) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endif

<script>
    // Dedicated print function: Prints strictly ONLY the formal paper statement sheet
    function printPnlStatement() {
        var sheet = document.querySelector('.pnl-paper-sheet');
        if (!sheet) {
            window.print();
            return;
        }

        var styleEl = document.getElementById('pnlDocumentStyles');
        var styles = styleEl ? styleEl.innerHTML : '';

        var printFrame = document.getElementById('pnlPrintFrame');
        if (printFrame) {
            printFrame.remove();
        }
        printFrame = document.createElement('iframe');
        printFrame.id = 'pnlPrintFrame';
        printFrame.style.position = 'fixed';
        printFrame.style.right = '0';
        printFrame.style.bottom = '0';
        printFrame.style.width = '0';
        printFrame.style.height = '0';
        printFrame.style.border = '0';
        document.body.appendChild(printFrame);

        var doc = printFrame.contentWindow.document;
        doc.open();
        doc.write('<!DOCTYPE html>' +
            '<html>' +
            '<head>' +
            '    <meta charset="UTF-8">' +
            '    <title>P&L Statement - {{ !empty($singleOrder->order_no) ? $singleOrder->order_no : "" }}</title>' +
            '    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">' +
            '    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">' +
            '    <style>' +
            '        * { box-sizing: border-box; }' +
            '        @page { size: A4 portrait; margin: 10mm 10mm; }' +
            '        body { margin: 0; padding: 0; background: #ffffff !important; color: #0f172a; font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }' +
            '        ' + styles +
            '        .pnl-desk-bg { background: transparent !important; padding: 0 !important; margin: 0 !important; }' +
            '        .pnl-paper-sheet { box-shadow: none !important; border: none !important; padding: 0 !important; max-width: 100% !important; width: 100% !important; margin: 0 !important; }' +
            '        .pnl-paper-topbar { display: none !important; }' +
            '        .no-print { display: none !important; }' +
            '    </style>' +
            '</head>' +
            '<body>' +
            '    <div class="pnl-paper-sheet">' +
            sheet.innerHTML +
            '    </div>' +
            '</body>' +
            '</html>');
        doc.close();

        setTimeout(function () {
            printFrame.contentWindow.focus();
            printFrame.contentWindow.print();
        }, 450);
    }

    // Quick range setter function
    function setDateRange(range) {
        var today = new Date();
        var from = '', to = '';

        function fmt(d) {
            var month = '' + (d.getMonth() + 1);
            var day = '' + d.getDate();
            var year = d.getFullYear();
            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;
            return [year, month, day].join('-');
        }

        if (range === 'today') {
            from = to = fmt(today);
        } else if (range === 'this_week') {
            var dayOfWeek = today.getDay();
            var diff = today.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1);
            var monday = new Date(today.setDate(diff));
            from = fmt(monday);
            to = fmt(new Date());
        } else if (range === 'this_month') {
            from = fmt(new Date(today.getFullYear(), today.getMonth(), 1));
            to = fmt(new Date(today.getFullYear(), today.getMonth() + 1, 0));
        } else if (range === 'last_month') {
            from = fmt(new Date(today.getFullYear(), today.getMonth() - 1, 1));
            to = fmt(new Date(today.getFullYear(), today.getMonth(), 0));
        } else if (range === 'this_year') {
            from = fmt(new Date(today.getFullYear(), 0, 1));
            to = fmt(new Date(today.getFullYear(), 11, 31));
        } else if (range === 'all_time') {
            from = '';
            to = '';
        }

        document.getElementById('filter_date_from').value = from;
        document.getElementById('filter_date_to').value = to;
        document.getElementById('pnlSearchForm').submit();
    }

    // Dynamic filtering for Choose Order dropdown when customer is selected
    var ALL_ORDERS_DATA = {!! json_encode($allOrdersQuickList) !!};
    var INITIAL_SELECTED_ORDER = {!! json_encode($singleOrder ? $singleOrder->id : null) !!};

    function onCustomerFilterChange(customerId) {
        var ordSelect = document.getElementById('filter_order_id');
        if (!ordSelect) return;

        var currentVal = ordSelect.value || INITIAL_SELECTED_ORDER;
        var filteredList = ALL_ORDERS_DATA;

        if (customerId) {
            filteredList = ALL_ORDERS_DATA.filter(function (o) {
                return String(o.customer_id) === String(customerId);
            });
        }

        ordSelect.innerHTML = '<option value="">-- Choose Order to View P&amp;L --</option>';
        var hasMatchingVal = false;

        filteredList.forEach(function (o) {
            var opt = document.createElement('option');
            opt.value = o.id;
            opt.setAttribute('data-customer-id', o.customer_id);
            opt.textContent = '#' + o.order_no + ' - ' + o.event_name + ' (' + o.customer_name + ')';
            if (String(o.id) === String(currentVal)) {
                opt.selected = true;
                hasMatchingVal = true;
            }
            ordSelect.appendChild(opt);
        });

        if (!hasMatchingVal && currentVal) {
            ordSelect.value = '';
        }
    }
</script>

@endsection