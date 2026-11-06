<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - {{ $payment->payment_no }} - {{ $payment->customer?->name ?? 'Customer' }}</title>

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
            padding: 24px 0 60px;
            font-size: 12px;
            -webkit-font-smoothing: antialiased;
        }

        .receipt-sheet {
            background: #ffffff;
            max-width: 780px;
            margin: 0 auto;
            padding: 36px 42px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .receipt-sheet::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #16a34a 0%, #059669 50%, #0d9488 100%);
            border-radius: 12px 12px 0 0;
        }

        .company-brand-title {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.3px;
            color: #0f172a;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .company-meta-text {
            font-size: 11px;
            color: #64748b;
            line-height: 1.5;
        }

        .receipt-title-badge {
            font-size: 20px;
            font-weight: 900;
            letter-spacing: 0.8px;
            color: #166534;
            text-transform: uppercase;
            line-height: 1.1;
        }

        .font-num {
            font-variant-numeric: tabular-nums;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, monospace;
        }

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
            margin-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .payment-highlight-card {
            background: #f0fdf4;
            border: 1.5px solid #86efac;
            border-radius: 10px;
            padding: 18px 24px;
            margin-bottom: 20px;
        }

        .amount-big-display {
            font-size: 26px;
            font-weight: 800;
            color: #15803d;
            letter-spacing: -0.5px;
        }

        .table-custom-summary {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
        }

        .table-custom-summary th {
            background: #f8fafc;
            color: #475569;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 8px 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-custom-summary td {
            padding: 8px 12px;
            font-size: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .sign-line {
            border-top: 1px dashed #94a3b8;
            margin-top: 40px;
            padding-top: 6px;
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            text-align: center;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                color: #000000 !important;
                font-size: 11px !important;
            }

            .receipt-sheet {
                border: none !important;
                box-shadow: none !important;
                max-width: 100% !important;
                padding: 10px 15px !important;
                margin: 0 !important;
            }

            .receipt-sheet::before {
                display: none !important;
            }

            .no-print {
                display: none !important;
            }

            .info-card-box, .payment-highlight-card {
                border: 1px solid #cccccc !important;
                background: #ffffff !important;
            }

            @page {
                size: A4 portrait;
                margin: 8mm 12mm;
            }
        }
    </style>
</head>
<body>

    <!-- Screen Action Toolbar (no-print) -->
    <div class="container mb-3 no-print" style="max-width: 780px;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 bg-white p-2.5 rounded-3 shadow-sm border">
            <div class="d-flex align-items-center gap-2">
                @if($payment->order)
                    <a href="{{ route('orders.show', $payment->order) }}" class="btn btn-sm btn-outline-secondary" style="font-size: 12px;">
                        <i class="bi bi-arrow-left me-1"></i> Back to Order (#{{ $payment->order->order_no }})
                    </a>
                @endif
                <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size: 12px;">
                    <i class="bi bi-wallet2 me-1"></i> Payments Ledger
                </a>
                <span class="badge bg-light text-dark border ms-1 py-1.5 px-2.5">
                    <i class="bi bi-receipt me-1"></i> Receipt: {{ $payment->payment_no }}
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($payment->order)
                    <a href="{{ route('orders.print', $payment->order) }}" class="btn btn-sm btn-outline-primary" style="font-size: 12px;" target="_blank">
                        <i class="bi bi-file-earmark-text me-1"></i> View Full Invoice
                    </a>
                @endif
                <button type="button" class="btn btn-sm btn-primary px-3 fw-bold shadow-sm" style="font-size: 12px;" onclick="window.print()">
                    <i class="bi bi-printer-fill me-1"></i> Print Receipt
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

    <!-- Official Printable Payment Receipt Sheet -->
    <main class="receipt-sheet">
        <!-- 1. Header: Company Brand & Receipt Info -->
        <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3 gap-3 flex-wrap">
            <div class="d-flex align-items-center gap-2.5">
                <!-- <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0 shadow-sm"
                    style="width: 44px; height: 44px; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); font-size: 20px;">
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
                <div class="d-flex align-items-center justify-content-md-end gap-2 mb-0.5">
                    <span class="receipt-title-badge">PAYMENT RECEIPT</span>
                    <span class="badge bg-success text-white font-num px-2.5 py-1" style="font-size: 12px; font-weight: 700;">#{{ $payment->payment_no }}</span>
                </div>
                <div class="company-meta-text">
                    Receipt Date: <strong class="text-dark">{{ $payment->payment_date?->format('d M Y') ?? date('d M Y') }}</strong>
                </div>
                <div class="mt-1">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5" style="font-size: 10px; font-weight: 700; text-transform: uppercase;">
                        <i class="bi bi-check-circle-fill me-1"></i> Payment Verified & Cleared
                    </span>
                </div>
            </div>
        </div>

        <!-- 2. Client & Order Scope (2-Column Balanced Cards) -->
        <div class="row g-2.5 mb-3">
            <!-- Client Info -->
            <div class="col-6">
                <div class="info-card-box">
                    <div class="info-card-heading">
                        Received From (Client)
                    </div>
                    <div class="fw-bold text-dark fs-6">{{ $payment->customer?->name ?? 'Valued Customer' }}</div>
                    @if($payment->customer?->company_name)
                        <div class="text-secondary small fw-semibold">{{ $payment->customer->company_name }}</div>
                    @endif
                    <div class="small text-muted mt-1" style="line-height: 1.45;">
                        @if($payment->customer?->phone)
                            <div><strong class="text-dark font-num">{{ $payment->customer->phone }}</strong></div>
                        @endif
                        @if($payment->customer?->email)
                            <div>{{ $payment->customer->email }}</div>
                        @endif
                        @if($payment->customer?->nic)
                            <div>NIC: <span class="font-num">{{ $payment->customer->nic }}</span></div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Booking & Occasion Scope -->
            <div class="col-6">
                <div class="info-card-box">
                    <div class="info-card-heading">
                        Applied Towards (Booking Reference)
                    </div>
                    @if($payment->order)
                        <div class="fw-bold text-dark" style="font-size: 13px;">
                            Order Ref: <span class="text-primary font-num">#{{ $payment->order->order_no }}</span>
                        </div>
                        <div class="small text-muted mt-1" style="line-height: 1.45;">
                            @if($payment->order->event)
                                <div>Event: <strong class="text-dark">{{ $payment->order->event->event_name }}</strong></div>
                                <div>Date: <span class="text-dark">{{ $payment->order->event->event_date?->format('l, d M Y') ?? 'N/A' }}</span></div>
                                @if($payment->order->event->venue)
                                    <div>Venue: {{ $payment->order->event->venue }}</div>
                                @endif
                            @else
                                <div>Direct Order Invoice</div>
                            @endif
                        </div>
                    @else
                        <div class="text-muted small">No order reference linked.</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. Prominent Payment Amount & Method Card -->
        <div class="payment-highlight-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <span class="text-uppercase fw-bold text-success" style="font-size: 11px; letter-spacing: 0.5px;">
                        <i class="bi bi-check2-circle me-1"></i> Net Amount Received in This Transaction:
                    </span>
                    <div class="amount-big-display font-num mt-0.5">
                        Rs. {{ number_format((float)$payment->amount, 2) }}
                    </div>
                </div>

                <div class="text-md-end">
                    <div class="small text-muted mb-0.5">Payment Method & Type:</div>
                    <div class="d-flex align-items-center gap-1.5 justify-content-md-end">
                        <span class="badge bg-white text-dark border px-2.5 py-1 fw-bold text-capitalize" style="font-size: 12px;">
                            @if($payment->payment_method === 'cash')
                                Cash Payment
                            @elseif($payment->payment_method === 'bank_transfer')
                                Bank Transfer / Deposit
                            @elseif($payment->payment_method === 'cheque')
                                Cheque Payment
                            @else
                                {{ $payment->payment_method }}
                            @endif
                        </span>
                        <span class="badge bg-success text-white px-2.5 py-1 text-capitalize" style="font-size: 11.5px;">
                            {{ $payment->payment_type }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Additional Transaction Specifics -->
            <div class="mt-2.5 pt-2 border-top border-success-subtle text-secondary small" style="font-size: 11px;">
                <div class="row g-2">
                    @if($payment->payment_method === 'bank_transfer' && $payment->bank_name)
                        <div class="col-12 col-md-6">
                            <i class="bi bi-bank me-1 text-success"></i> Deposited Bank: <strong class="text-dark">{{ $payment->bank_name }}</strong>
                        </div>
                    @endif
                    @if($payment->payment_method === 'cheque' && $payment->cheque_no)
                        <div class="col-12 col-md-6">
                            <i class="bi bi-journal-text me-1 text-success"></i> Cheque Serial Number: <strong class="text-dark font-num">{{ $payment->cheque_no }}</strong>
                        </div>
                    @endif
                    @if($payment->reference)
                        <div class="col-12 col-md-6">
                            <i class="bi bi-hash me-1 text-success"></i> Reference Slip / Voucher: <strong class="text-dark font-num">{{ $payment->reference }}</strong>
                        </div>
                    @endif
                    @if($payment->notes)
                        <div class="col-12">
                            <i class="bi bi-chat-left-text me-1 text-success"></i> Remarks / Notes: <span class="text-dark">{{ $payment->notes }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 4. Account Balance Position at this Payment -->
        @if($payment->order)
            <div class="mb-4">
                <div class="info-card-heading mb-1.5">
                    Order Account Balance Snapshot
                </div>
                <table class="table-custom-summary">
                    <thead>
                        <tr>
                            <th>Total Order Value</th>
                            <th class="text-center">This Receipt Payment</th>
                            <th class="text-center">Total Cumulative Paid</th>
                            <th class="text-end">Balance Remaining</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-num fw-semibold text-dark">
                                Rs. {{ number_format((float)$payment->order->total_amount, 2) }}
                            </td>
                            <td class="text-center font-num fw-bold text-success">
                                + Rs. {{ number_format((float)$payment->amount, 2) }}
                            </td>
                            <td class="text-center font-num text-success fw-semibold">
                                Rs. {{ number_format((float)$payment->order->paid_amount, 2) }}
                            </td>
                            <td class="text-end font-num fw-bold {{ $payment->order->balance <= 0 ? 'text-success' : 'text-danger' }}">
                                @if($payment->order->balance <= 0)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2">Settled in Full</span>
                                @else
                                    Rs. {{ number_format((float)$payment->order->balance, 2) }}
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        <!-- 5. Signatures Block -->
        <div class="row pt-2">
            <div class="col-6">
                <div class="sign-line">Received & Issued By (Cashier / Coordinator)</div>
            </div>
            <div class="col-6">
                <div class="sign-line">Customer Acceptance & Signature</div>
            </div>
        </div>

        <!-- 6. Footer Notice -->
        <div class="text-center text-muted mt-3 pt-2 border-top" style="font-size: 9.5px;">
            Thank you for your payment! This is an official computer-generated receipt issued by <strong>CEYLON WEDDING COMPANY</strong>.<br>
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
                }, 400);
            }
        });
    </script>
</body>
</html>

