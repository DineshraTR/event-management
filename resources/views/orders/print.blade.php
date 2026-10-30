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
    <!-- Google Fonts -->
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
            padding: 24px 0 60px;
            -webkit-font-smoothing: antialiased;
        }

        .invoice-sheet {
            background: #ffffff;
            max-width: 860px;
            margin: 0 auto;
            padding: 44px 48px;
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
            height: 6px;
            background: linear-gradient(90deg, #3a3838 0%, #484646 50%, #64748b 100%);
            border-radius: 12px 12px 0 0;
        }

        .company-brand-title {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.3px;
            color: #0f172a;
            text-transform: uppercase;
        }

        .company-meta-text {
            font-size: 11.5px;
            color: #64748b;
            line-height: 1.5;
        }

        .invoice-title-badge {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 0.5px;
            color: #0f172a;
            text-transform: uppercase;
        }

        .info-card-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            height: 100%;
        }

        .info-card-heading {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #64748b;
            margin-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }

        .table-invoice th {
            background: #f1f5f9 !important;
            color: #334155 !important;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 14px;
            border-bottom: 1px solid #cbd5e1;
        }

        .table-invoice td {
            padding: 10px 14px;
            font-size: 12.5px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .badge-status-pill {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        .pkg-tag-pill {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 3px 8px;
            font-size: 11px;
            color: #334155;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 13px;
        }

        .grand-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
            padding: 10px 14px;
            margin-top: 6px;
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
        }

        .balance-highlight-box {
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 10px;
        }

        .sign-line {
            border-top: 1px dashed #94a3b8;
            margin-top: 45px;
            padding-top: 6px;
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            text-align: center;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                color: #000000 !important;
            }
            .invoice-sheet {
                border: none !important;
                box-shadow: none !important;
                max-width: 100% !important;
                padding: 10px 20px !important;
            }
            .no-print {
                display: none !important;
            }
            .info-card-box {
                border: 1px solid #cccccc !important;
                background: #ffffff !important;
            }
            .table-invoice th {
                background: #eeeeee !important;
                color: #000000 !important;
                border-bottom: 1px solid #000000 !important;
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
                        <i class="bi bi-calendar-event me-1"></i> Back to Event
                    </a>
                @endif
                <span class="badge bg-light text-dark border ms-2">
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
                    <i class="bi bi-printer-fill me-1"></i> Print Invoice / Receipt
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mt-3 mb-0" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- Official Printable Invoice Sheet -->
    <main class="invoice-sheet">
        <!-- 1. Header: Company & Invoice Info -->
        <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4 gap-3 flex-wrap">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                    style="width: 48px; height: 48px; background: #3a3838; font-size: 24px;">
                    <i class="bi bi-stars"></i>
                </div>
                <div>
                    <div class="company-brand-title">Event Management System</div>
                    <div class="company-meta-text">
                        <span>Event Planning, Design & Luxury Catering Solutions</span><br>
                        <span>Hotline: +94 11 234 5678 / +94 77 123 4567</span> &bull; 
                        <span>Email: info@eventmanagement.lk</span>
                    </div>
                </div>
            </div>

            <div class="text-md-end text-start">
                <div class="invoice-title-badge">INVOICE</div>
                <div class="fw-bold font-monospace text-dark mt-0.5" style="font-size: 14px;">
                    #{{ $order->order_no }}
                </div>
                <div class="small text-muted mt-1">
                    Date: <strong class="text-dark">{{ $order->order_date?->format('d M Y') ?? date('d M Y') }}</strong>
                </div>
                <div class="mt-1.5">
                    @if($order->payment_status === 'paid')
                        <span class="badge-status-pill" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;">
                            <i class="bi bi-check-circle-fill me-1"></i> Fully Paid
                        </span>
                    @elseif($order->payment_status === 'partial')
                        <span class="badge-status-pill" style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;">
                            <i class="bi bi-clock-history me-1"></i> Part Payment Received
                        </span>
                    @else
                        <span class="badge-status-pill" style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a;">
                            <i class="bi bi-exclamation-circle-fill me-1"></i> Payment Due
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. Client & Event Details Cards -->
        <div class="row g-3 mb-4">
            <!-- Client Info -->
            <div class="col-md-6">
                <div class="info-card-box">
                    <div class="info-card-heading">
                        <i class="bi bi-person-fill me-1"></i> Billed To (Client Details)
                    </div>
                    <div class="fw-bold text-dark fs-6">{{ $order->customer?->name ?? 'Walk-in Client' }}</div>
                    @if($order->customer?->company_name)
                        <div class="text-secondary small fw-semibold">{{ $order->customer->company_name }}</div>
                    @endif
                    <div class="small text-muted mt-1.5" style="line-height: 1.5;">
                        @if($order->customer?->phone)
                            <div><i class="bi bi-telephone me-1.5 text-secondary"></i>{{ $order->customer->phone }}</div>
                        @endif
                        @if($order->customer?->email)
                            <div><i class="bi bi-envelope me-1.5 text-secondary"></i>{{ $order->customer->email }}</div>
                        @endif
                        @if($order->customer?->nic)
                            <div><i class="bi bi-person-badge me-1.5 text-secondary"></i>NIC: {{ $order->customer->nic }}</div>
                        @endif
                        @if($order->customer?->address)
                            <div><i class="bi bi-geo-alt me-1.5 text-secondary"></i>{{ $order->customer->address }}, {{ $order->customer->city }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Event & Booking Info -->
            <div class="col-md-6">
                <div class="info-card-box">
                    <div class="info-card-heading">
                        <i class="bi bi-calendar2-event-fill me-1"></i> Event & Booking Details
                    </div>
                    @if($order->event)
                        <div class="fw-bold text-dark fs-6">{{ $order->event->event_name }}</div>
                        <div class="small text-muted mt-1.5" style="line-height: 1.5;">
                            <div>
                                <i class="bi bi-hash me-1.5 text-secondary"></i>Event No: 
                                <strong class="text-dark font-monospace">{{ $order->event->event_no }}</strong>
                            </div>
                            <div>
                                <i class="bi bi-calendar3 me-1.5 text-secondary"></i>Event Date: 
                                <strong class="text-dark">{{ $order->event->event_date?->format('l, F j, Y') ?? 'N/A' }}</strong>
                            </div>
                            @if($order->event->venue)
                                <div>
                                    <i class="bi bi-geo-alt-fill me-1.5 text-danger"></i>Venue: 
                                    <span class="text-dark">{{ $order->event->venue }}</span>
                                </div>
                            @endif
                            @if($order->event->subCategory)
                                <div>
                                    <i class="bi bi-box-seam me-1.5 text-primary"></i>Package: 
                                    <span class="text-dark fw-semibold">{{ $order->event->subCategory->name }}</span>
                                    <small class="text-muted">({{ $order->event->subCategory->category?->name ?? 'General' }})</small>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-muted small">Direct Customer Order (No linked event)</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. Package Deliverables Summary Box (if event package has included services) -->
        @if($order->event?->subCategory?->subCategoryServices && $order->event->subCategory->subCategoryServices->isNotEmpty())
            <div class="p-3 bg-light rounded-3 border mb-4">
                <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                    <span class="fw-bold small text-dark">
                        <i class="bi bi-check2-circle text-success me-1"></i> Package Included Deliverables ({{ $order->event->subCategory->name }}):
                    </span>
                    <span class="badge bg-secondary-subtle text-secondary small" style="font-size: 10px;">
                        Standard Package Items
                    </span>
                </div>
                <div class="d-flex flex-wrap gap-2 pt-1">
                    @foreach($order->event->subCategory->subCategoryServices as $ps)
                        <div class="pkg-tag-pill">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 10px;"></i>
                            <span>{{ $ps->service?->name ?? 'Service' }} &bull; Qty: <strong>{{ $ps->qty }}</strong></span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 4. Line Items Table (Deliverables & Charges) -->
        <div class="table-responsive mb-4">
            <table class="table table-invoice mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;" class="text-center">#</th>
                        <th>Item / Service Description</th>
                        <th style="width: 90px;" class="text-center">Qty</th>
                        <th style="width: 140px;" class="text-end">Unit Price (Rs.)</th>
                        <th style="width: 150px;" class="text-end pe-3">Line Total (Rs.)</th>
                    </tr>
                </thead>
                <tbody>
                    @php($itemCount = 1)
                    @forelse($order->services as $line)
                        <tr>
                            <td class="text-center text-muted font-monospace">{{ $itemCount++ }}</td>
                            <td class="fw-semibold text-dark">
                                {{ $line->description }}
                                @if($line->service && $line->description !== $line->service->name)
                                    <small class="text-muted d-block" style="font-size: 11px;">Preset: {{ $line->service->name }}</small>
                                @endif
                            </td>
                            <td class="text-center font-monospace">{{ $line->qty }}</td>
                            <td class="text-end font-monospace">{{ number_format($line->unit_price, 2) }}</td>
                            <td class="text-end pe-3 fw-bold text-dark font-monospace">{{ number_format($line->total_price, 2) }}</td>
                        </tr>
                    @empty
                        <!-- If order services is empty, show base package if available -->
                        @if($order->event?->package_sell_price > 0)
                            <tr>
                                <td class="text-center text-muted font-monospace">1</td>
                                <td class="fw-semibold text-dark">{{ $order->event->subCategory?->name ?? 'Event Package' }} (Base Event Package)</td>
                                <td class="text-center font-monospace">1</td>
                                <td class="text-end font-monospace">{{ number_format($order->event->package_sell_price, 2) }}</td>
                                <td class="text-end pe-3 fw-bold text-dark font-monospace">{{ number_format($order->event->package_sell_price, 2) }}</td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No line items recorded on this order.</td>
                            </tr>
                        @endif
                    @endforelse

                    <!-- Other Charges (Transport, Logistics) -->
                    @if($order->otherCharges && $order->otherCharges->isNotEmpty())
                        @foreach($order->otherCharges as $charge)
                            <tr>
                                <td class="text-center text-muted font-monospace">{{ $itemCount++ }}</td>
                                <td class="fw-semibold text-dark">
                                    {{ $charge->description }}
                                    <small class="badge bg-light text-secondary border ms-1" style="font-size: 10px;">Surcharge / Extra</small>
                                </td>
                                <td class="text-center font-monospace">1</td>
                                <td class="text-end font-monospace">{{ number_format($charge->amount, 2) }}</td>
                                <td class="text-end pe-3 fw-bold text-dark font-monospace">{{ number_format($charge->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <!-- 5. Totals & Payment Breakdown -->
        <div class="row g-4 mb-4 align-items-start">
            <!-- Left: Payment Receipts Ledger -->
            <div class="col-md-7">
                <div class="info-card-heading mb-2">
                    <i class="bi bi-wallet2 me-1"></i> Payment & Settlement Receipts
                </div>
                @if($order->payments && $order->payments->isNotEmpty())
                    <table class="table table-sm table-bordered align-middle mb-2" style="font-size: 11.5px;">
                        <thead class="table-light">
                            <tr>
                                <th>Receipt #</th>
                                <th>Date</th>
                                <th>Method</th>
                                <th>Type</th>
                                <th class="text-end">Paid (Rs.)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->payments as $pay)
                                @php($isHighlighted = isset($highlightPaymentId) && $highlightPaymentId == $pay->id)
                                <tr style="{{ $isHighlighted ? 'background-color: #fef08a !important;' : '' }}">
                                    <td class="font-monospace fw-bold text-dark">{{ $pay->payment_no }}</td>
                                    <td>{{ $pay->payment_date?->format('d M Y') }}</td>
                                    <td class="text-capitalize">
                                        {{ $pay->payment_method }}
                                        @if($pay->cheque_no)
                                            <small class="text-muted font-monospace">({{ $pay->cheque_no }})</small>
                                        @endif
                                    </td>
                                    <td class="text-capitalize">{{ $pay->payment_type }}</td>
                                    <td class="text-end fw-bold font-monospace text-success">
                                        {{ number_format($pay->amount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-3 bg-light rounded border text-muted small mb-2">
                        <i class="bi bi-info-circle me-1"></i> No payments have been recorded towards this order yet.
                    </div>
                @endif

                @if($order->notes)
                    <div class="small mt-2 p-2 bg-light rounded border">
                        <strong class="text-dark d-block mb-1">Notes & Instructions:</strong>
                        <span class="text-muted" style="white-space: pre-line;">{{ $order->notes }}</span>
                    </div>
                @endif
            </div>

            <!-- Right: Calculation Box -->
            <div class="col-md-5">
                <div class="p-3 bg-white border rounded-3 shadow-sm">
                    <div class="summary-row">
                        <span class="text-muted">Sub Total:</span>
                        <strong class="text-dark font-monospace">Rs. {{ number_format((float)$order->sub_total, 2) }}</strong>
                    </div>

                    @if((float)$order->discount > 0)
                        <div class="summary-row text-danger">
                            <span>Special Discount:</span>
                            <span class="font-monospace">- Rs. {{ number_format((float)$order->discount, 2) }}</span>
                        </div>
                    @endif

                    <div class="grand-total-row">
                        <span>Net Invoice Total:</span>
                        <span class="text-primary font-monospace">Rs. {{ number_format((float)$order->total_amount, 2) }}</span>
                    </div>

                    <div class="summary-row pt-2 text-success">
                        <span>Total Paid / Advance:</span>
                        <strong class="font-monospace">Rs. {{ number_format((float)$order->paid_amount, 2) }}</strong>
                    </div>

                    <div class="balance-highlight-box {{ $order->balance <= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border {{ $order->balance <= 0 ? 'border-success-subtle' : 'border-danger-subtle' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold small text-uppercase">{{ $order->balance <= 0 ? 'Settled In Full' : 'Balance Remaining Due' }}:</span>
                            <span class="fs-5 fw-bold font-monospace">Rs. {{ number_format((float)$order->balance, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Bank Transfer Account Instructions (Footer) -->
        <div class="p-3 bg-light border rounded-3 mb-4" style="font-size: 11.5px; line-height: 1.5;">
            <div class="fw-bold text-dark mb-1">
                <i class="bi bi-bank me-1 text-primary"></i> Payment & Bank Settlement Details:
            </div>
            <div class="text-muted">
                Please make all cheques or bank deposits payable to <strong>Event Management System</strong>. 
                For direct bank transfers, please mention the Invoice Number <strong class="text-dark font-monospace">#{{ $order->order_no }}</strong> as payment reference and send the bank receipt via WhatsApp or Email.
            </div>
        </div>

        <!-- 7. Signatures -->
        <div class="row pt-3">
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
