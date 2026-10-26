@extends('layouts.app')
@section('title', 'Date-wise & Monthly Profit & Loss Statement')
@section('page-title', 'Profit & Loss Report')

@section('content')

<style id="pnlDocumentStyles">
    /* ========================================================================= */
    /* FORMAL WHITE PAPER SHEET LAYOUT (TRITCAL DATE-WISE P&L STYLE)             */
    /* ========================================================================= */
    .pnl-desk-bg {
        background: #e2e8f0;
        padding: 30px 15px;
        border-radius: 12px;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 30px;
    }

    .pnl-paper-sheet {
        background: #ffffff;
        width: 100%;
        max-width: 900px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12), 0 1px 4px rgba(0, 0, 0, 0.08);
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        padding: 42px 48px;
        box-sizing: border-box;
        color: #0f172a;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
        position: relative;
    }

    /* Paper Top Action Bar */
    .pnl-paper-topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding-bottom: 20px;
        margin-bottom: 25px;
        border-bottom: 1.5px dashed #cbd5e1;
    }

    /* Header & Branding */
    .pnl-header-brand {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 20px;
        border-bottom: 2px solid #0f172a;
        margin-bottom: 24px;
    }

    .company-fallback-title {
        font-size: 22px;
        font-weight: 900;
        color: #0f172a;
        letter-spacing: -0.5px;
        text-transform: uppercase;
    }

    .company-meta-text {
        font-size: 11.5px;
        color: #475569;
        line-height: 1.5;
        margin-top: 4px;
    }

    .doc-title-block {
        text-align: right;
    }

    .pnl-doc-title {
        font-size: 20px;
        font-weight: 900;
        color: #0f172a;
        letter-spacing: -0.3px;
        text-transform: uppercase;
        margin: 0 0 4px 0;
    }

    .pnl-doc-meta {
        font-size: 11.5px;
        color: #475569;
        line-height: 1.5;
    }

    /* Meta Scope Box */
    .pnl-meta-box {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 16px 20px;
        margin-bottom: 26px;
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
        width: 135px;
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

    /* Section Headings */
    .pnl-sec-title {
        font-size: 13px;
        font-weight: 800;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #f1f5f9;
        border-left: 4px solid #0f172a;
        padding: 8px 12px;
        margin: 26px 0 12px 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Formal Tables with Touch Scroll */
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
        font-size: 12.5px;
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
        font-size: 13px;
    }

    /* P&L Statement Box */
    .pnl-statement-ledger {
        border: 2px solid #0f172a;
        border-radius: 4px;
        margin-top: 26px;
        margin-bottom: 22px;
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
        font-size: 13px;
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
        margin-top: 42px;
        padding-top: 18px;
        border-top: 1px solid #cbd5e1;
        font-size: 11.5px;
        text-align: center;
    }

    .pnl-sig-line {
        border-top: 1px dashed #64748b;
        margin-top: 38px;
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
            <i class="bi bi-calendar-range text-primary"></i> Date Range Filter (Profit &amp; Loss Period)
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('reports.order-pl') }}" class="btn btn-sm btn-outline-primary fw-semibold">
                <i class="bi bi-receipt me-1"></i> Order-wise P&amp;L
            </a>
            <a href="{{ route('reports.monthly-pl') }}" class="btn btn-sm btn-outline-secondary fw-semibold">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Current Month
            </a>
            <button type="button" onclick="printDatePnlStatement()" class="btn btn-sm btn-primary fw-bold px-3 shadow-sm">
                <i class="bi bi-printer me-1"></i> Print / PDF Statement
            </button>
        </div>
    </div>
    <div class="card-body p-3">
        <form method="GET" action="{{ route('reports.monthly-pl') }}" id="datePnlFilterForm">
            <div class="row g-3 align-items-end">
                <!-- From Date -->
                <div class="col-12 col-md-4 col-lg-3">
                    <label class="form-label small fw-semibold text-muted mb-1">
                        <i class="bi bi-calendar me-1"></i> From Date
                    </label>
                    <input type="date" name="start_date" id="pnl_start_date" class="form-control form-control-sm"
                        value="{{ $startDate }}" required>
                </div>

                <!-- To Date -->
                <div class="col-12 col-md-4 col-lg-3">
                    <label class="form-label small fw-semibold text-muted mb-1">
                        <i class="bi bi-calendar me-1"></i> To Date
                    </label>
                    <input type="date" name="end_date" id="pnl_end_date" class="form-control form-control-sm"
                        value="{{ $endDate }}" required>
                </div>

                <!-- Financial Year Filter -->
                <div class="col-12 col-md-4 col-lg-2">
                    <label class="form-label small fw-semibold text-muted mb-1">
                        <i class="bi bi-calendar-event me-1"></i> Year Filter
                    </label>
                    <input type="number" name="year" id="pnl_year" class="form-control form-control-sm"
                        value="{{ $year }}" min="2000" max="2100">
                </div>

                <!-- Buttons -->
                <div class="col-12 col-md-12 col-lg-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm fw-bold px-3" style="height: 31px;">
                        <i class="bi bi-graph-up-arrow me-1"></i> Generate Statement
                    </button>
                    <button type="button" onclick="printDatePnlStatement()" class="btn btn-outline-secondary btn-sm fw-semibold" style="height: 31px;">
                        <i class="bi bi-printer me-1"></i> Print Preview
                    </button>
                </div>
            </div>

            <!-- Quick Presets -->
            <div class="d-flex gap-1 flex-wrap align-items-center mt-3 pt-2 border-top text-muted small">
                <span class="fw-semibold text-muted me-1" style="font-size: 11.5px;">
                    <i class="bi bi-clock-history me-1"></i> Quick Range:
                </span>
                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 11px;" onclick="setDateRange('today')">Today</button>
                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 11px;" onclick="setDateRange('this_week')">This Week</button>
                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 11px;" onclick="setDateRange('this_month')">This Month</button>
                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 11px;" onclick="setDateRange('last_month')">Last Month</button>
                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 11px;" onclick="setDateRange('this_year')">This Year</button>
                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 11px;" onclick="setDateRange('all_time')">All Time</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- FORMAL WHITE PAPER SHEET (Accounting Document Presentation - Tritcal Style)-->
<!-- ========================================================================= -->
<div class="pnl-desk-bg">
    <div class="pnl-paper-sheet">

        <!-- Top Action Bar inside Paper Sheet -->
        <div class="pnl-paper-topbar no-print">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span class="badge" style="background: #0f172a; color: #fff; font-size: 11px; padding: 4px 8px;">
                    PERIOD P&amp;L STATEMENT
                </span>
                <span style="font-size: 12.5px; color: #475569; font-weight: 600;">
                    {{ $periodLabel }}
                </span>
            </div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <button type="button" onclick="printDatePnlStatement()" class="btn btn-sm btn-primary fw-bold px-3">
                    <i class="bi bi-printer me-1"></i> Print Statement
                </button>
            </div>
        </div>

        <!-- 1. Header & Branding -->
        <div class="pnl-header-brand">
            <div>
                <div class="company-fallback-title">EVENTPRO MANAGEMENT SYSTEM</div>
                <div class="company-meta-text">
                    Reg: EV-00482910 &bull; Event Planning &amp; Production Management<br>
                    No. 142/2, Main Street, Colombo, Sri Lanka<br>
                    Tel: +94 11 25 80 900 / +94 77 12 34 567 &bull; info@eventpro.lk
                </div>
            </div>
            <div class="doc-title-block">
                <div class="pnl-doc-title">PERIOD P&amp;L STATEMENT</div>
                <div class="pnl-doc-meta">
                    <strong>Statement Ref:</strong> PNL-D-{{ date('Ymd', strtotime($startDate)) }}-{{ date('Ymd', strtotime($endDate)) }}<br>
                    <strong>Period:</strong> {{ $periodLabel }}<br>
                    <strong>Generated:</strong> {{ date('d M Y, h:i A') }}<br>
                    <strong>Currency:</strong> Sri Lankan Rupee (LKR)
                </div>
            </div>
        </div>

        <!-- 2. Statement Metadata Box -->
        <div class="pnl-meta-box">
            <div>
                <div class="pnl-meta-heading"><i class="bi bi-calendar-check me-1"></i> Period &amp; Criteria</div>
                <div class="pnl-meta-row">
                    <span class="pnl-meta-label">From Date:</span>
                    <span class="pnl-meta-val">{{ date('d M Y', strtotime($startDate)) }}</span>
                </div>
                <div class="pnl-meta-row">
                    <span class="pnl-meta-label">To Date:</span>
                    <span class="pnl-meta-val">{{ date('d M Y', strtotime($endDate)) }}</span>
                </div>
                <div class="pnl-meta-row">
                    <span class="pnl-meta-label">Revenue Scope:</span>
                    <span class="pnl-meta-val">New Orders &amp; Client Payment Receipts</span>
                </div>
                <div class="pnl-meta-row">
                    <span class="pnl-meta-label">Expense Scope:</span>
                    <span class="pnl-meta-val">Direct Event Costs + Operating Overheads</span>
                </div>
            </div>
            <div>
                <div class="pnl-meta-heading"><i class="bi bi-graph-up me-1"></i> Profitability Performance</div>
                <div class="pnl-meta-row">
                    <span class="pnl-meta-label">Registered Orders:</span>
                    <span class="pnl-meta-val">{{ $totalOrdersCount }} Order(s)</span>
                </div>
                <div class="pnl-meta-row">
                    <span class="pnl-meta-label">Total Revenue:</span>
                    <span class="pnl-meta-val" style="color: #0284c7;">Rs. {{ number_format($totalRevenue, 2) }}</span>
                </div>
                <div class="pnl-meta-row">
                    <span class="pnl-meta-label">Gross Profit:</span>
                    <span class="pnl-meta-val" style="color: #059669;">Rs. {{ number_format($grossProfit, 2) }} ({{ $grossProfitMargin }}%)</span>
                </div>
                <div class="pnl-meta-row">
                    <span class="pnl-meta-label">{{ $isNetProfit ? 'Net Period Profit' : 'Net Period Loss' }}:</span>
                    <span class="pnl-meta-val" style="color: {{ $isNetProfit ? '#15803d' : '#dc2626' }}; font-weight: 800; font-size: 13.5px;">
                        Rs. {{ number_format($netProfitOrLoss, 2) }} ({{ $netProfitMargin }}%)
                    </span>
                </div>
            </div>
        </div>

        <!-- ============================================================= -->
        <!-- 1. ORDERS VALUATION & PAYMENT RECEIPTS (REVENUE TOTALS)       -->
        <!-- ============================================================= -->
        <div class="pnl-sec-title">
            <span>1. Revenue (Orders Contract Value &amp; Collections)</span>
            <span style="font-size: 11px; font-weight: 600; color: #475569; text-transform: none;">
                {{ $totalOrdersCount }} Order(s) &bull; {{ $totalPaymentsCount }} Receipt(s)
            </span>
        </div>
        <div class="pnl-table-wrap">
            <table class="pnl-paper-table" style="min-width: 600px;">
                <thead>
                    <tr>
                        <th style="width: 55%;">Revenue Classification / Description</th>
                        <th style="width: 20%; text-align: center;">Transactions</th>
                        <th style="width: 25%; text-align: right;">Amount (Rs.)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>Order Total Value (New Bookings / Orders)</strong>
                            <div style="font-size: 11.5px; color: #64748b;">Contract valuation of all orders registered in this period</div>
                        </td>
                        <td style="text-align: center; font-size: 12px; color: #475569;">
                            {{ $totalOrdersCount }} Registered Order(s)
                        </td>
                        <td style="text-align: right; font-weight: 700; color: #0f172a; font-size: 13.5px;">
                            {{ number_format($totalOrderValuation, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Customer Payments Collected</strong>
                            <div style="font-size: 11.5px; color: #64748b;">Total cash, bank and cleared cheque receipts received in this period</div>
                        </td>
                        <td style="text-align: center; font-size: 12px; color: #475569;">
                            {{ $totalPaymentsCount }} Payment Receipt(s)
                        </td>
                        <td style="text-align: right; font-weight: 700; color: #0284c7; font-size: 13.5px;">
                            {{ number_format($totalPaymentsReceived, 2) }}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="row-total">
                        <td colspan="2">TOTAL REVENUE (Recognized Period Revenue)</td>
                        <td style="text-align: right; font-size: 14.5px; color: #0f172a;">
                            {{ number_format($totalRevenue, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- ============================================================= -->
        <!-- 2. DIRECT PROJECT & EVENT COSTS (COST OF SALES TOTAL)         -->
        <!-- ============================================================= -->
        <div class="pnl-sec-title">
            <span>2. Direct Event &amp; Project Costs (Cost of Sales)</span>
            <span style="font-size: 11px; font-weight: 600; color: #475569; text-transform: none;">
                {{ count($directExpenses) + ($packageCostsTotal > 0 ? 1 : 0) }} Direct Cost Item(s)
            </span>
        </div>
        <div class="pnl-table-wrap">
            <table class="pnl-paper-table" style="min-width: 600px;">
                <thead>
                    <tr>
                        <th style="width: 55%;">Cost Item / Classification</th>
                        <th style="width: 20%; text-align: center;">Transactions</th>
                        <th style="width: 25%; text-align: right;">Amount (Rs.)</th>
                    </tr>
                </thead>
                <tbody>
                    @if($packageCostsTotal > 0)
                        <tr>
                            <td>
                                <strong>Base Package Execution Costs</strong>
                                <div style="font-size: 11.5px; color: #64748b;">Estimated direct execution and package costs for booked events</div>
                            </td>
                            <td style="text-align: center; font-size: 12px; color: #475569;">
                                {{ $totalOrdersCount }} Order(s)
                            </td>
                            <td style="text-align: right; font-weight: 700; color: #dc2626; font-size: 13.5px;">
                                {{ number_format($packageCostsTotal, 2) }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td>
                            <strong>Direct Project / Event Expense Vouchers</strong>
                            <div style="font-size: 11.5px; color: #64748b;">Direct supplier, rentals, and operational vouchers recorded</div>
                        </td>
                        <td style="text-align: center; font-size: 12px; color: #475569;">
                            {{ count($directExpenses) }} Voucher(s)
                        </td>
                        <td style="text-align: right; font-weight: 700; color: #dc2626; font-size: 13.5px;">
                            {{ number_format($totalDirectVouchers, 2) }}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="row-total">
                        <td colspan="2">TOTAL DIRECT COST (EXPENSES)</td>
                        <td style="text-align: right; font-size: 14.5px; color: #dc2626;">
                            {{ number_format($totalDirectCost, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- ============================================================= -->
        <!-- 3. GROSS PROFIT ROW (TOTAL REVENUE - DIRECT EXPENSES)         -->
        <!-- ============================================================= -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 16px; background: #ecfdf5; border: 1.5px solid #a7f3d0; border-radius: 4px; margin: 14px 0 22px 0; font-size: 13.5px;">
            <span style="font-weight: 800; color: #065f46;">
                <i class="bi bi-layers text-success me-1"></i> GROSS PROFIT (Total Revenue - Direct Costs):
            </span>
            <span style="font-weight: 900; font-size: 16px; color: #047857;">
                Rs. {{ number_format($grossProfit, 2) }}
                <span style="font-size: 11.5px; font-weight: 700; color: #059669; margin-left: 4px;">(Margin: {{ $grossProfitMargin }}%)</span>
            </span>
        </div>

        <!-- ============================================================= -->
        <!-- 4. OPERATING / GENERAL EXPENSES (OVERHEADS)                   -->
        <!-- ============================================================= -->
        <div class="pnl-sec-title">
            <span>3. Operating &amp; General Expenses (Overheads)</span>
            <span style="font-size: 11px; font-weight: 600; color: #475569; text-transform: none;">
                {{ count($operatingExpenses) }} Expense Voucher(s)
            </span>
        </div>
        @if(count($operatingExpenses) === 0)
            <div style="padding: 16px; text-align: center; color: #94a3b8; font-style: italic; border: 1px dashed #cbd5e1; border-radius: 4px; margin-bottom: 14px;">
                No operating or normal general expenses were recorded in this period.
            </div>
        @else
            <div class="pnl-table-wrap">
                <table class="pnl-paper-table" style="min-width: 650px;">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Voucher #</th>
                            <th style="width: 28%;">Description / Remarks</th>
                            <th style="width: 15%;">Date</th>
                            <th style="width: 16%;">Category / Mode</th>
                            <th style="width: 16%; text-align: right;">Amount (Rs.)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($operatingExpenses as $nExp)
                            <tr>
                                <td style="font-family: monospace; font-size: 12px; font-weight: 700;">
                                    #{{ $nExp->expense_no }}
                                </td>
                                <td>{{ $nExp->description ?: $nExp->notes ?: ($nExp->category ?: 'Operating Cost') }}</td>
                                <td>{{ $nExp->expense_date ? date('d M Y', strtotime($nExp->expense_date)) : 'N/A' }}</td>
                                <td style="font-size: 12px;">
                                    {{ $nExp->category ?: 'Operating' }}
                                    <div style="font-size: 10.5px; color: #64748b;">via {{ $nExp->payment_method }}</div>
                                </td>
                                <td style="text-align: right; font-weight: 700; color: #dc2626;">
                                    {{ number_format($nExp->amount, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="row-total">
                            <td colspan="4">TOTAL OPERATING / GENERAL EXPENSES</td>
                            <td style="text-align: right; font-size: 14px; color: #dc2626;">
                                {{ number_format($totalOperatingCost, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif

        <!-- ============================================================= -->
        <!-- 5. FORMAL STATEMENT OF PROFIT OR LOSS (P&L) LEDGER            -->
        <!-- ============================================================= -->
        <div class="pnl-statement-ledger">
            <div class="pnl-statement-header">
                STATEMENT OF PROFIT OR LOSS (P&amp;L) &bull; {{ $periodLabel }}
            </div>
            <div class="pnl-statement-body">
                <div class="pnl-stmt-line" style="background: #f8fafc; font-weight: 800; padding: 7px 6px;">
                    <span>TOTAL PERIOD REVENUE</span>
                    <strong style="color: #0f172a; font-size: 14px;">Rs. {{ number_format($totalRevenue, 2) }}</strong>
                </div>
                <div class="pnl-stmt-line">
                    <span style="color: #b91c1c;">Direct Event &amp; Production Costs (Cost of Sales)</span>
                    <strong style="color: #b91c1c;">- Rs. {{ number_format($totalDirectCost, 2) }}</strong>
                </div>
                <div class="pnl-stmt-line" style="background: #f0fdf4; font-weight: 800; padding: 7px 6px;">
                    <span style="color: #065f46;">GROSS PROFIT (Revenue - Direct Costs) [Margin: {{ $grossProfitMargin }}%]</span>
                    <strong style="color: #047857; font-size: 14px;">Rs. {{ number_format($grossProfit, 2) }}</strong>
                </div>
                <div class="pnl-stmt-line">
                    <span style="color: #b91c1c;">Operating &amp; General Expenses (Overheads)</span>
                    <strong style="color: #b91c1c;">- Rs. {{ number_format($totalOperatingCost, 2) }}</strong>
                </div>

                <!-- Net Profit / Loss Line -->
                <div class="pnl-stmt-line total-line" style="background: {{ $isNetProfit ? '#f0fdf4' : '#fef2f2' }}; padding-left: 8px; padding-right: 8px;">
                    <span style="color: {{ $isNetProfit ? '#15803d' : '#b91c1c' }}; display: flex; align-items: center; gap: 8px;">
                        <i class="bi {{ $isNetProfit ? 'bi-graph-up-arrow' : 'bi-graph-down-arrow' }}"></i>
                        {{ $isNetProfit ? 'NET PROFIT FOR THE PERIOD' : 'NET LOSS FOR THE PERIOD' }} (Gross Profit - Operating Expenses):
                    </span>
                    <span style="color: {{ $isNetProfit ? '#15803d' : '#b91c1c' }}; font-size: 18px; font-weight: 900;">
                        Rs. {{ number_format($netProfitOrLoss, 2) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 6. Document Signatures Area -->
        <div class="pnl-signatures-area">
            <div>
                <div class="pnl-sig-line">Prepared By</div>
                <div style="font-size: 10.5px; color: #64748b; margin-top: 3px;">
                    {{ auth()->user()->name ?? 'Accounts Executive' }}
                </div>
            </div>
            <div>
                <div class="pnl-sig-line">Checked &amp; Verified By</div>
                <div style="font-size: 10.5px; color: #64748b; margin-top: 3px;">
                    Finance Manager / Auditor
                </div>
            </div>
            <div>
                <div class="pnl-sig-line">Approved By</div>
                <div style="font-size: 10.5px; color: #64748b; margin-top: 3px;">
                    Managing Director / Partner
                </div>
            </div>
        </div>

        <!-- 7. Bottom Disclaimer Notice -->
        <div style="text-align: center; margin-top: 25px; font-size: 10.5px; color: #94a3b8; border-top: 1px dotted #e2e8f0; padding-top: 10px;">
            This document is a formal management Profit &amp; Loss statement generated by EventPro Management System.
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 12-MONTH PERFORMANCE COMPARATIVE OVERVIEW TABLE                           -->
<!-- ========================================================================= -->
<div class="card no-print border-0 shadow-sm mt-4" style="border-radius: 10px; overflow: hidden;">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
        <div>
            <h6 class="mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                <i class="bi bi-bar-chart-fill text-primary"></i> {{ $year }} Monthly Profit &amp; Loss Comparative Summary
            </h6>
            <small class="text-muted">Month-by-month financial review across the entire fiscal year</small>
        </div>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 fw-bold">
            Year {{ $year }}
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
            <thead class="table-light text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                <tr>
                    <th class="ps-4">Month</th>
                    <th class="text-end">Revenue</th>
                    <th class="text-end">Direct Costs</th>
                    <th class="text-end">Operating Costs</th>
                    <th class="text-end">Total Expenses</th>
                    <th class="text-end">Net Profit / Loss</th>
                    <th class="text-end pe-4">Profit Margin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($months as $m)
                    <tr>
                        <td class="ps-4 fw-bold text-dark">{{ $m['month'] }}</td>
                        <td class="text-end text-success fw-semibold">Rs. {{ number_format($m['revenue'], 2) }}</td>
                        <td class="text-end text-danger">Rs. {{ number_format($m['direct_cost'], 2) }}</td>
                        <td class="text-end text-danger">Rs. {{ number_format($m['operating_cost'], 2) }}</td>
                        <td class="text-end text-danger fw-semibold">Rs. {{ number_format($m['expenses'], 2) }}</td>
                        <td class="text-end fw-bold {{ $m['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                            Rs. {{ number_format($m['profit'], 2) }}
                        </td>
                        <td class="text-end pe-4">
                            <span class="badge {{ $m['profit'] >= 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} px-2 py-1">
                                {{ $m['margin'] }}%
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light fw-bold" style="border-top: 2px solid #cbd5e1; font-size: 13.5px;">
                <tr>
                    <td class="ps-4">TOTAL ({{ $year }})</td>
                    <td class="text-end text-success">Rs. {{ number_format($yearTotalRevenue, 2) }}</td>
                    <td class="text-end text-danger">-</td>
                    <td class="text-end text-danger">-</td>
                    <td class="text-end text-danger">Rs. {{ number_format($yearTotalExpenses, 2) }}</td>
                    <td class="text-end {{ $yearTotalProfit >= 0 ? 'text-success' : 'text-danger' }}">
                        Rs. {{ number_format($yearTotalProfit, 2) }}
                    </td>
                    <td class="text-end pe-4">
                        {{ $yearTotalRevenue > 0 ? round(($yearTotalProfit / $yearTotalRevenue) * 100, 1) : 0 }}%
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
    // Dedicated print function: Prints strictly ONLY the formal paper statement sheet
    function printDatePnlStatement() {
        var sheet = document.querySelector('.pnl-paper-sheet');
        if (!sheet) {
            window.print();
            return;
        }

        var styleEl = document.getElementById('pnlDocumentStyles');
        var styles = styleEl ? styleEl.innerHTML : '';

        var printFrame = document.getElementById('pnlDatePrintFrame');
        if (printFrame) {
            printFrame.remove();
        }
        printFrame = document.createElement('iframe');
        printFrame.id = 'pnlDatePrintFrame';
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
            '    <title>P&L Statement - {{ $periodLabel }}</title>' +
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

        document.getElementById('pnl_start_date').value = from;
        document.getElementById('pnl_end_date').value = to;
        document.getElementById('datePnlFilterForm').submit();
    }
</script>

@endsection