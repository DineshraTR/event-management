<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->order_no }} - {{ $order->customer?->name ?? 'Customer' }}</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body {
            background-color: #f1f5f9;
            color: #1e293b;
            margin: 0;
            padding: 20px 0 50px;
            font-size: 12px;
            -webkit-font-smoothing: antialiased;
        }

        .invoice-sheet {
            background: #ffffff;
            max-width: 860px;
            margin: 0 auto;
            padding: 36px 42px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .invoice-sheet::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #3a3838 0%, #484646 50%, #64748b 100%);
            border-radius: 12px 12px 0 0;
        }

        /* Typography */
        .company-brand-title {
            font-size: 19px;
            font-weight: 800;
            letter-spacing: -0.3px;
            color: #0f172a;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .company-meta-text {
            font-size: 11px;
            color: #64748b;
            line-height: 1.45;
        }

        .invoice-title-badge {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 0.5px;
            color: #0f172a;
            text-transform: uppercase;
            line-height: 1.1;
        }

        .font-num {
            font-variant-numeric: tabular-nums;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, monospace;
        }

        /* Information Cards */
        .info-card-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 14px;
            height: 100%;
        }

        .info-card-heading {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #475569;
            margin-bottom: 7px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Table Styling */
        .table-invoice {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
            width: 100%;
        }

        .table-invoice thead th {
            background: #f1f5f9 !important;
            color: #334155 !important;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 12px;
            border-bottom: 1.5px solid #cbd5e1;
            white-space: nowrap;
        }

        .table-invoice tbody td {
            padding: 7px 12px;
            font-size: 11.5px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-invoice tbody tr:nth-child(even) {
            background-color: #fafbfc;
        }

        /* Badges & Pills */
        .badge-status-pill {
            font-size: 10.5px;
            font-weight: 700;
            padding: 3.5px 9px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .pkg-tag-pill {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            padding: 2.5px 7px;
            font-size: 10.5px;
            color: #334155;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* Summary Calculations */
        .summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 14px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
            font-size: 12px;
        }

        .grand-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            border: 1.5px solid #0f172a;
            border-radius: 6px;
            padding: 7px 12px;
            margin: 6px 0;
            font-size: 13.5px;
            font-weight: 800;
            color: #0f172a;
        }

        .balance-highlight-box {
            border-radius: 6px;
            padding: 8px 12px;
            margin-top: 6px;
        }

        /* Signatures */
        .sign-line {
            border-top: 1px dashed #94a3b8;
            margin-top: 32px;
            padding-top: 5px;
            font-size: 10.5px;
            font-weight: 600;
            color: #475569;
            text-align: center;
        }

        .page-break-avoid {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* ========================================================= */
        /* PRINT STYLES: OPTIMIZED FOR EXACT SINGLE A4 PORTRAIT FIT  */
        /* ========================================================= */
        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm 10mm;
            }

            html, body {
                background: #ffffff !important;
                color: #000000 !important;
                margin: 0 !important;
                padding: 0 !important;
                font-size: 10.5px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }

            .invoice-sheet {
                border: none !important;
                box-shadow: none !important;
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 4px 8px !important;
            }

            .invoice-sheet::before {
                height: 4px !important;
            }

            .info-card-box, .summary-card {
                background: #ffffff !important;
                border: 1px solid #d1d5db !important;
                padding: 8px 10px !important;
            }

            .table-invoice {
                border: 1px solid #cbd5e1 !important;
            }

            .table-invoice thead th {
                background: #f1f5f9 !important;
                color: #000000 !important;
                border-bottom: 1.5px solid #000000 !important;
                padding: 6px 8px !important;
                font-size: 10px !important;
            }

            .table-invoice tbody td {
                padding: 5px 8px !important;
                font-size: 10.5px !important;
                border-bottom: 1px solid #e2e8f0 !important;
            }

            .table-sm th, .table-sm td {
                padding: 4px 6px !important;
                font-size: 10px !important;
            }

            .grand-total-row {
                background: #f8fafc !important;
                border: 1.5px solid #000000 !important;
                padding: 6px 10px !important;
            }

            .balance-highlight-box {
                padding: 6px 10px !important;
            }

            .sign-line {
                margin-top: 24px !important;
            }
        }
    </style>
</head>
<body>

    <!-- Screen Action Toolbar (no-print) -->
    <div class="container mb-3 no-print" style="max-width: 860px;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 bg-white p-3 rounded-3 shadow-sm border">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Order
                </a>
                @if($order->event)
                    <a href="{{ route('events.show', $order->event) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-calendar-event me-1"></i> View Linked Event
                    </a>
                @endif
                <span class="badge bg-light text-dark border ms-1 py-1.5 px-2.5">
                    <i class="bi bi-receipt me-1"></i> Invoice: {{ $order->order_no }}
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($order->balance > 0)
                    <a href="{{ route('payments.create-for-order', $order) }}" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-plus-circle me-1"></i> Add Payment
                    </a>
                @endif
                <button type="button" class="btn btn-sm btn-primary px-3 fw-bold shadow-sm" onclick="window.print()">
                    <i class="bi bi-printer-fill me-1"></i> Print / Save as PDF
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mt-2 mb-0 py-2 px-3 small" role="alert">
                <i class="bi bi-check-circle-fill me-1.5"></i> {{ session('success') }}
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- Official Printable Invoice Sheet -->
    <main class="invoice-sheet">
        <!-- 1. Header: Company Brand & Invoice Meta -->
        <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3 gap-3 flex-wrap">
            <div class="d-flex align-items-center gap-3">
                <!-- <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0 shadow-sm"
                    style="width: 46px; height: 46px; background: #3a3838; font-size: 22px;">
                    <i class="bi bi-stars"></i>
                </div> -->
                <div>
                    <div class="company-brand-title">CEYLON WEDDING COMPANY</div>
                    <div class="company-meta-text">
                        <span>Photography & Videography Solutions</span><br>
                        <span>Hotline: 0777524937 | Email: ceylonwedding.co@gmail.com</span><br>
                        <span>www.ceylonweddingcompany.com</span>
                    </div>
                </div>
            </div>

            <div class="text-md-end text-start">
                <div class="invoice-title-badge">INVOICE</div>
                <div class="fw-bold font-num text-dark mt-0.5" style="font-size: 13.5px;">
                    #{{ $order->order_no }}
                </div>
                <div class="company-meta-text mt-0.5">
                    Date: <strong class="text-dark">{{ $order->order_date?->format('d M Y') ?? date('d M Y') }}</strong>
                </div>
                <div class="mt-1">
                    @if($order->payment_status === 'paid')
                        <span class="badge-status-pill" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">
                            Paid in Full
                        </span>
                    @elseif($order->payment_status === 'partial')
                        <span class="badge-status-pill" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">
                            Partial Payment
                        </span>
                    @else
                        <span class="badge-status-pill" style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a;">
                            Payment Due
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. Client & Event Details Cards (Original 2-Column Grid) -->
        <div class="row g-2.5 mb-3">
            <!-- Client Info -->
            <div class="col-md-6">
                <div class="info-card-box">
                    <div class="info-card-heading">
                        Billed To (Client Details)
                    </div>
                    <div class="fw-bold text-dark fs-6">{{ $order->customer?->name ?? 'Walk-in Client' }}</div>
                    @if($order->customer?->company_name)
                        <div class="text-secondary small fw-semibold">{{ $order->customer->company_name }}</div>
                    @endif
                    <div class="small text-muted mt-1" style="line-height: 1.45;">
                        @if($order->customer?->phone)
                            <div><strong class="text-dark font-num">{{ $order->customer->phone }}</strong></div>
                        @endif
                        @if($order->customer?->email)
                            <div>{{ $order->customer->email }}</div>
                        @endif
                        @if($order->customer?->nic)
                            <div>NIC: <span class="font-num">{{ $order->customer->nic }}</span></div>
                        @endif
                        @if($order->customer?->address)
                            <div>{{ $order->customer->address }}{{ $order->customer->city ? ', ' . $order->customer->city : '' }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Event & Booking Info -->
            @php
                $displayPackages = $order->packages && $order->packages->isNotEmpty()
                    ? $order->packages
                    : ($order->event?->packages && $order->event->packages->isNotEmpty()
                        ? $order->event->packages
                        : collect());
            @endphp
            <div class="col-md-6">
                <div class="info-card-box">
                    <div class="info-card-heading">
                        Event & Booking Details
                    </div>
                    @if($order->event)
                        <div class="fw-bold text-dark fs-6">{{ $order->event->event_name }}</div>
                        <div class="small text-muted mt-1" style="line-height: 1.45;">
                            <div>
                                Event No: 
                                <strong class="text-dark font-num">{{ $order->event->event_no }}</strong>
                                &bull;
                                <strong class="text-dark">{{ $order->event->event_date?->format('l, d M Y') ?? 'N/A' }}</strong>
                            </div>
                            @if($order->event->venue)
                                <div>
                                    Venue: 
                                    <span class="text-dark">{{ $order->event->venue }}</span>
                                </div>
                            @endif
                            @if($displayPackages->isNotEmpty())
                                <div>
                                    Package{{ $displayPackages->count() > 1 ? 's' : '' }}: 
                                    <span class="text-dark fw-semibold">
                                        {{ $displayPackages->pluck('package_name')->join(', ') }}
                                    </span>
                                </div>
                            @elseif($order->event->subCategory)
                                <div>
                                    Package: 
                                    <span class="text-dark fw-semibold">{{ $order->event->subCategory->name }}</span>
                                    @if($order->event->subCategory->category)
                                        <small class="text-muted">({{ $order->event->subCategory->category->name }})</small>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-muted small">Direct Customer Order (No linked event profile)</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 4. Line Items Table (Deliverables & Charges) -->
        <div class="table-responsive mb-3">
            <table class="table table-invoice mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;" class="text-center">#</th>
                        <th>Item / Service Description</th>
                        <th style="width: 80px;" class="text-center">Qty</th>
                        <th style="width: 140px;" class="text-end">Unit Price (Rs.)</th>
                        <th style="width: 150px;" class="text-end pe-3">Line Total (Rs.)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $itemCount = 1; @endphp
                    @php
                        $additionalServices = $order->services->filter(
                            fn($line) => !$line->is_package_item && !str_contains($line->description, '(Base Package)')
                        );
                    @endphp

                    <!-- 1. Packages Line Items (product with description directly underneath) -->
                    @if($displayPackages->isNotEmpty())
                        @foreach($displayPackages as $pkg)
                            @php
                                $pkgUnit = (float) (
                                    $pkg->sell_price > 0 
                                        ? $pkg->sell_price 
                                        : ($pkg->unit_price > 0 
                                            ? $pkg->unit_price 
                                            : ($pkg->qty > 0 && $pkg->total_price > 0 
                                                ? $pkg->total_price / $pkg->qty 
                                                : ($pkg->subCategory?->sell_price 
                                                    ?: ($order->package_sell_price 
                                                        ?: ($order->event?->package_sell_price 
                                                            ?: ($order->event?->subCategory?->sell_price ?? 0)))))));
                                $pkgTot = (float)($pkg->total_price > 0 ? $pkg->total_price : ($pkgUnit * ($pkg->qty ?: 1)));
                                $pkgDesc = $pkg->description ?? $pkg->subCategory?->description ?? '';
                            @endphp
                            <tr style="background-color: #fafbfc;">
                                <td class="text-center text-muted font-num">{{ $itemCount++ }}</td>
                                <td class="fw-bold text-dark">
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                        <span>{{ $pkg->package_name }}</span>
                                        @if($pkg->category_name ?? $pkg->subCategory?->category?->name ?? $order->event?->subCategory?->category?->name)
                                            <small class="text-muted fw-normal">({{ $pkg->category_name ?? $pkg->subCategory?->category?->name ?? $order->event?->subCategory?->category?->name }})</small>
                                        @endif
                                    </div>
                                    @if(!empty($pkgDesc))
                                        <div class="text-muted small fw-normal" style="font-size: 11.5px; line-height: 1.45;">
                                            {!! nl2br(e($pkgDesc)) !!}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center font-num">{{ $pkg->qty ?: 1 }}</td>
                                <td class="text-end font-num">{{ number_format($pkgUnit, 2) }}</td>
                                <td class="text-end pe-3 fw-bold text-primary font-num">{{ number_format($pkgTot, 2) }}</td>
                            </tr>
                        @endforeach
                    @elseif((float)$order->package_sell_price > 0 || (float)$order->event?->package_sell_price > 0 || $order->event?->subCategory)
                        @php
                            $basePkgPrice = (float)($order->package_sell_price ?: ($order->event?->package_sell_price ?: ($order->event?->subCategory?->sell_price ?? 0)));
                            $basePkgName = $order->package_name ?? $order->event?->package_name ?? $order->event?->subCategory?->name ?? 'Event Package';
                            $basePkgCat = $order->event?->subCategory?->category?->name;
                            $basePkgDesc = $order->event?->subCategory?->description ?? '';
                        @endphp
                        @if($basePkgPrice > 0 || !empty($basePkgDesc))
                            <tr style="background-color: #fafbfc;">
                                <td class="text-center text-muted font-num">{{ $itemCount++ }}</td>
                                <td class="fw-bold text-dark">
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                        <span>{{ $basePkgName }}</span>
                                        @if($basePkgCat)
                                            <small class="text-muted fw-normal">({{ $basePkgCat }})</small>
                                        @endif
                                    </div>
                                    @if(!empty($basePkgDesc))
                                        <div class="text-muted small fw-normal" style="font-size: 11px; line-height: 1.45;">
                                            {!! nl2br(e($basePkgDesc)) !!}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center font-num">1</td>
                                <td class="text-end font-num">{{ number_format($basePkgPrice, 2) }}</td>
                                <td class="text-end pe-3 fw-bold text-primary font-num">{{ number_format($basePkgPrice, 2) }}</td>
                            </tr>
                        @endif
                    @endif

                    <!-- 2. Extra Services Line Items -->
                    @forelse($additionalServices as $line)
                        <tr>
                            <td class="text-center text-muted font-num">{{ $itemCount++ }}</td>
                            <td class="fw-semibold text-dark">
                                {{ $line->description }}
                                @if($line->service && $line->description !== $line->service->name)
                                    <small class="text-muted d-block" style="font-size: 10.5px;">Preset: {{ $line->service->name }}</small>
                                @endif
                            </td>
                            <td class="text-center font-num">{{ $line->qty }}</td>
                            <td class="text-end font-num">{{ number_format($line->unit_price, 2) }}</td>
                            <td class="text-end pe-3 fw-bold text-dark font-num">{{ number_format($line->total_price, 2) }}</td>
                        </tr>
                    @empty
                        @if($displayPackages->isEmpty() && (float)$order->package_sell_price <= 0 && (float)$order->event?->package_sell_price <= 0)
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No line items recorded on this order.</td>
                            </tr>
                        @endif
                    @endforelse

                    <!-- Other Charges (Transport, Logistics) -->
                    @if($order->otherCharges && $order->otherCharges->isNotEmpty())
                        @foreach($order->otherCharges as $charge)
                            <tr>
                                <td class="text-center text-muted font-num">{{ $itemCount++ }}</td>
                                <td class="fw-semibold text-dark">
                                    {{ $charge->description }}
                                    <small class="badge bg-light text-secondary border ms-1" style="font-size: 9.5px;">Surcharge / Extra</small>
                                </td>
                                <td class="text-center font-num">1</td>
                                <td class="text-end font-num">{{ number_format($charge->amount, 2) }}</td>
                                <td class="text-end pe-3 fw-bold text-dark font-num">{{ number_format($charge->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <!-- 5. Totals & Payment Breakdown (Original 2-Column Balanced Layout) -->
        <div class="row g-3 mb-3 align-items-start page-break-avoid">
            <!-- Left: Payment Receipts Ledger & Notes -->
            <div class="col-md-7">
                <div class="info-card-heading mb-1.5">
                    Payment & Settlement Receipts
                </div>
                @if($order->payments && $order->payments->isNotEmpty())
                    <table class="table table-sm table-bordered align-middle mb-2" style="font-size: 11px;">
                        <thead class="table-light">
                            <tr>
                                <th style="padding: 5px 8px;">Receipt #</th>
                                <th style="padding: 5px 8px;">Date</th>
                                <th style="padding: 5px 8px;">Method</th>
                                <th style="padding: 5px 8px;">Type</th>
                                <th style="padding: 5px 8px;" class="text-end">Paid (Rs.)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->payments as $pay)
                                @php $isHighlighted = isset($highlightPaymentId) && $highlightPaymentId == $pay->id; @endphp
                                <tr style="{{ $isHighlighted ? 'background-color: #fef08a !important;' : '' }}">
                                    <td style="padding: 5px 8px;" class="font-num fw-bold text-dark">{{ $pay->payment_no }}</td>
                                    <td style="padding: 5px 8px;">{{ $pay->payment_date?->format('d M Y') }}</td>
                                    <td style="padding: 5px 8px;" class="text-capitalize">
                                        {{ $pay->payment_method }}
                                        @if($pay->cheque_no)
                                            <small class="text-muted font-num">({{ $pay->cheque_no }})</small>
                                        @endif
                                    </td>
                                    <td style="padding: 5px 8px;" class="text-capitalize">{{ $pay->payment_type }}</td>
                                    <td style="padding: 5px 8px;" class="text-end fw-bold font-num text-success">
                                        {{ number_format($pay->amount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-2.5 bg-light rounded border text-muted small mb-2" style="font-size: 11px;">
                        <i class="bi bi-info-circle me-1"></i> No payments have been recorded towards this order yet.
                    </div>
                @endif

                @if($order->notes)
                    <div class="small p-2 bg-light rounded border text-muted" style="font-size: 10.5px;">
                        <strong class="text-dark d-block mb-0.5">Notes & Instructions:</strong>
                        <span style="white-space: pre-line;">{{ $order->notes }}</span>
                    </div>
                @endif
            </div>

            <!-- Right: Calculation Box -->
            <div class="col-md-5">
                <div class="summary-card">
                    <div class="summary-row">
                        <span class="text-muted">Sub Total:</span>
                        <strong class="text-dark font-num">Rs. {{ number_format((float)$order->sub_total, 2) }}</strong>
                    </div>

                    @if((float)$order->discount > 0)
                        <div class="summary-row text-danger">
                            <span>Special Discount:</span>
                            <span class="font-num">- Rs. {{ number_format((float)$order->discount, 2) }}</span>
                        </div>
                    @endif

                    <div class="grand-total-row">
                        <span>Net Invoice Total:</span>
                        <span class="text-dark font-num">Rs. {{ number_format((float)$order->total_amount, 2) }}</span>
                    </div>

                    <div class="summary-row pt-1 text-success">
                        <span>Total Paid / Advance:</span>
                        <strong class="font-num">Rs. {{ number_format((float)$order->paid_amount, 2) }}</strong>
                    </div>

                    <div class="balance-highlight-box {{ $order->balance <= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border {{ $order->balance <= 0 ? 'border-success-subtle' : 'border-danger-subtle' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold small text-uppercase">{{ $order->balance <= 0 ? 'Settled In Full' : 'Balance Remaining Due' }}:</span>
                            <span class="fw-bold font-num" style="font-size: 14px;">Rs. {{ number_format((float)$order->balance, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $primaryBank = \App\Models\Bank::getPrimaryBank();
        @endphp
        <!-- 6. Bank Transfer Account Instructions (Footer) -->
        <div class="p-2.5 bg-light border rounded-3 mb-3 page-break-avoid" style="font-size: 11px; line-height: 1.45;">
            <div class="fw-bold text-dark mb-1">
                <i class="bi bi-bank me-1 text-primary"></i> Payment & Bank Settlement Details:
            </div>
            <div class="text-secondary">
                @if(!empty($primaryBank))
                    Bank: <strong class="text-dark">{{ $primaryBank->bank_name }}</strong> &bull;
                    Account Name: <strong class="text-dark">{{ $primaryBank->account_name }}</strong> &bull;
                    Account No: <strong class="text-dark font-num">{{ $primaryBank->account_number }}</strong>
                    @if(!empty($primaryBank->branch)) &bull; Branch: {{ $primaryBank->branch }} @endif
                    @if(!empty($primaryBank->swift_code)) &bull; SWIFT: {{ $primaryBank->swift_code }} @endif
                    <br>
                @endif
                For direct bank transfers, please mention the Invoice Number <strong class="text-dark font-num">#{{ $order->order_no }}</strong> as payment reference and send the bank receipt via WhatsApp or Email.
            </div>
        </div>

        <!-- 7. Compact Signatures -->
        <div class="row pt-1 page-break-avoid">
            <div class="col-4">
                <div class="sign-line">Prepared & Issued By</div>
            </div>
            <div class="col-4">
                <div class="sign-line">Checked & Verified By</div>
            </div>
            <div class="col-4">
                <div class="sign-line">Customer Acceptance & Signature</div>
            </div>
        </div>

        <!-- 8. Polite Footer Notice -->
        <div class="text-center text-muted mt-3 pt-1.5 border-top page-break-avoid" style="font-size: 10px;">
            Thank you for choosing <strong>CEYLON WEDDING COMPANY</strong> for your special occasion. This is a computer-generated invoice.<br>
            Software by <a class="text-muted" href="https://www.tritcal.com" target="_blank" style="text-decoration: none;">Tritcal International LLC | www.tritcal.com </a> 
        </div>
    </main>

    <script>
        // Automatic print prompt if auto_print=1 in URL parameter
        document.addEventListener('DOMContentLoaded', function () {
            const params = new URLSearchParams(window.location.search);
            if (params.get('auto_print') === '1') {
                setTimeout(function () {
                    window.print();
                }, 500);
            }
        });
    </script>
</body>
</html>