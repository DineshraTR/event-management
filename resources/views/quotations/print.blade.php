<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation - {{ $quotation->quotation_no }} - {{ $quotation->customer?->name ?? 'Customer' }}</title>

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

        .quotation-sheet {
            background: #ffffff;
            max-width: 860px;
            margin: 0 auto;
            padding: 44px 48px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .quotation-sheet::before {
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

        .quotation-title-badge {
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

        .table-quotation th {
            background: #f1f5f9 !important;
            color: #334155 !important;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 14px;
            border-bottom: 1px solid #cbd5e1;
        }

        .table-quotation td {
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
            background: #ffffff;
            border: 1px solid #cbd5e1;
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
            .quotation-sheet {
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
            .table-quotation th {
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
                <a href="{{ route('quotations.show', $quotation) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Proposal
                </a>
                <a href="{{ route('quotations.edit', $quotation) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i> Edit Quotation
                </a>
                <span class="badge bg-light text-dark border ms-2">
                    <i class="bi bi-file-earmark-text me-1"></i> Quote: {{ $quotation->quotation_no }}
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('quotations.convert-to-event', $quotation) }}" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-calendar-check me-1"></i> Convert to Event
                </a>
                <button type="button" class="btn btn-sm btn-primary px-3 fw-bold shadow-sm" onclick="window.print()">
                    <i class="bi bi-printer-fill me-1"></i> Print / Save as PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Official Printable Quotation Sheet -->
    <main class="quotation-sheet">
        <!-- 1. Header: Company & Quotation Info -->
        <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4 gap-3 flex-wrap">
            <div class="d-flex align-items-center gap-3">
                <!-- <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                    style="width: 48px; height: 48px; background: #3a3838; font-size: 24px;">
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
                <div class="quotation-title-badge">QUOTATION</div>
                <div class="fw-bold font-monospace text-dark mt-0.5" style="font-size: 14px;">
                    #{{ $quotation->quotation_no }}
                </div>
                <div class="small text-muted mt-1">
                    Date: <strong class="text-dark">{{ $quotation->quotation_date?->format('d M Y') ?? date('d M Y') }}</strong>
                </div>
                @if($quotation->valid_until)
                    <div class="small text-muted">
                        Valid Until: <strong class="{{ $quotation->valid_until->isPast() ? 'text-danger' : 'text-dark' }}">{{ $quotation->valid_until->format('d M Y') }}</strong>
                    </div>
                @endif
                <div class="mt-1.5">
                    @if($quotation->status === 'accepted')
                        <span class="badge-status-pill" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;">
                            <i class="bi bi-check-circle-fill me-1"></i> Accepted Proposal
                        </span>
                    @elseif($quotation->status === 'sent')
                        <span class="badge-status-pill" style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;">
                            <i class="bi bi-send-check-fill me-1"></i> Presented to Client
                        </span>
                    @elseif($quotation->status === 'rejected')
                        <span class="badge-status-pill" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;">
                            <i class="bi bi-x-circle-fill me-1"></i> Declined
                        </span>
                    @else
                        <span class="badge-status-pill" style="background: #f8fafc; color: #475569; border: 1px solid #cbd5e1;">
                            <i class="bi bi-file-earmark-text me-1"></i> Draft Proposal
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. Client & Occasion Details Cards -->
        <div class="row g-3 mb-4">
            <!-- Client Info -->
            <div class="col-md-6">
                <div class="info-card-box">
                    <div class="info-card-heading">
                        <i class="bi bi-person-fill me-1"></i> Prepared For (Client Details)
                    </div>
                    <div class="fw-bold text-dark fs-6">{{ $quotation->customer?->name ?? 'Valued Client' }}</div>
                    @if($quotation->customer?->company_name)
                        <div class="text-secondary small fw-semibold">{{ $quotation->customer->company_name }}</div>
                    @endif
                    <div class="small text-muted mt-1.5" style="line-height: 1.5;">
                        @if($quotation->customer?->phone)
                            <div><i class="bi bi-telephone me-1.5 text-secondary"></i>{{ $quotation->customer->phone }}</div>
                        @endif
                        @if($quotation->customer?->email)
                            <div><i class="bi bi-envelope me-1.5 text-secondary"></i>{{ $quotation->customer->email }}</div>
                        @endif
                        @if($quotation->customer?->address)
                            <div><i class="bi bi-geo-alt me-1.5 text-secondary"></i>{{ $quotation->customer->address }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Occasion Details -->
            <div class="col-md-6">
                <div class="info-card-box">
                    <div class="info-card-heading">
                        <i class="bi bi-calendar2-event-fill me-1"></i> Event & Occasion Scope
                    </div>
                    <div class="fw-bold text-dark fs-6">{{ $quotation->event_name ?: 'Event Proposal' }}</div>
                    <div class="small text-muted mt-1.5" style="line-height: 1.5;">
                        @if($quotation->event_date)
                            <div>
                                <i class="bi bi-calendar3 me-1.5 text-secondary"></i>Target Date: 
                                <strong class="text-dark">{{ $quotation->event_date->format('l, F j, Y') }}</strong>
                            </div>
                        @endif
                        @if($quotation->venue)
                            <div>
                                <i class="bi bi-geo-alt-fill me-1.5 text-danger"></i>Proposed Venue: 
                                <span class="text-dark">{{ $quotation->venue }}</span>
                            </div>
                        @endif
                        @php
                            $displayPackages = $quotation->packages && $quotation->packages->isNotEmpty()
                                ? $quotation->packages
                                : collect();
                        @endphp
                        @if($displayPackages->isNotEmpty())
                            <div>
                                <i class="bi bi-box-seam me-1.5 text-primary"></i>Package{{ $displayPackages->count() > 1 ? 's' : '' }}: 
                                <span class="text-dark fw-semibold">{{ $displayPackages->pluck('package_name')->join(', ') }}</span>
                            </div>
                        @elseif($quotation->subCategory || $quotation->package_name)
                            <div>
                                <i class="bi bi-box-seam me-1.5 text-primary"></i>Package Preset: 
                                <span class="text-dark fw-semibold">{{ $quotation->package_name ?? $quotation->subCategory?->name }}</span>
                                @if($quotation->subCategory?->category)
                                    <small class="text-muted">({{ $quotation->subCategory->category->name }})</small>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Package Deliverables Summary Box (Multi-Package Supported) -->
        @php
            $includedServices = $quotation->subCategory?->subCategoryServices ?? collect();
            $pkgBasePrice = $displayPackages->isNotEmpty()
                ? (float) $displayPackages->sum(function($p) use ($quotation) {
                    $unit = (float)($p->sell_price > 0 ? $p->sell_price : ($p->unit_price > 0 ? $p->unit_price : ($p->subCategory?->sell_price ?: ($quotation->package_sell_price ?: ($quotation->subCategory?->sell_price ?? 0)))));
                    return (float)($p->total_price > 0 ? $p->total_price : ($unit * ($p->qty ?: 1)));
                })
                : (float)($quotation->package_sell_price ?: ($quotation->subCategory?->sell_price ?? 0));
            $svcTotal = (float)($quotation->services->isNotEmpty() ? $quotation->services->sum('total_price') : $quotation->additional_services_total);
            $chrTotal = (float)($quotation->otherCharges->isNotEmpty() ? $quotation->otherCharges->sum('amount') : $quotation->other_charges_total);
            $calcSubTotal = $pkgBasePrice + $svcTotal + $chrTotal;
            $finalSubTotal = (float)($quotation->sub_total > 0 ? $quotation->sub_total : $calcSubTotal);
            $discountVal = (float)($quotation->discount ?? 0);
            $finalTotalAmount = (float)($quotation->total_amount > 0 ? $quotation->total_amount : max(0, $finalSubTotal - $discountVal));
        @endphp

        <!-- 4. Line Items Table (Packages & Charges) -->
        <div class="table-responsive mb-4">
            <table class="table table-quotation mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;" class="text-center">#</th>
                        <th>Item / Package Description</th>
                        <th style="width: 90px;" class="text-center">Qty</th>
                        <th style="width: 150px;" class="text-end">Unit Price (Rs.)</th>
                        <th style="width: 160px;" class="text-end pe-3">Line Total (Rs.)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $itemCount = 1; @endphp

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
                                                    ?: ($quotation->package_sell_price 
                                                        ?: ($quotation->subCategory?->sell_price ?? 0))))));
                                $pkgTot = (float)($pkg->total_price > 0 ? $pkg->total_price : ($pkgUnit * ($pkg->qty ?: 1)));
                                $pkgDesc = $pkg->description ?? $pkg->subCategory?->description ?? '';
                            @endphp
                            <tr style="background-color: #fafbfc;">
                                <td class="text-center text-muted font-monospace">{{ $itemCount++ }}</td>
                                <td class="fw-bold text-dark">
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                        <span>{{ $pkg->package_name }}</span>
                                        @if($pkg->category_name ?? $pkg->subCategory?->category?->name)
                                            <small class="text-muted fw-normal">({{ $pkg->category_name ?? $pkg->subCategory?->category?->name }})</small>
                                        @endif
                                    </div>
                                    @if(!empty($pkgDesc))
                                        <div class="text-muted small fw-normal" style="font-size: 11.5px; line-height: 1.45;">
                                            {!! nl2br(e($pkgDesc)) !!}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center font-monospace">{{ $pkg->qty ?: 1 }}</td>
                                <td class="text-end font-monospace">{{ number_format($pkgUnit, 2) }}</td>
                                <td class="text-end pe-3 fw-bold text-primary font-monospace">{{ number_format($pkgTot, 2) }}</td>
                            </tr>
                        @endforeach
                    @elseif($pkgBasePrice > 0)
                        @php
                            $singleDesc = $quotation->subCategory?->description ?? '';
                        @endphp
                        <tr style="background-color: #fafbfc;">
                            <td class="text-center text-muted font-monospace">{{ $itemCount++ }}</td>
                            <td class="fw-bold text-dark">
                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                    <span>{{ $quotation->package_name ?? $quotation->subCategory?->name ?? 'Event Package' }}</span>
                                    @if($quotation->subCategory?->category)
                                        <small class="text-muted fw-normal">({{ $quotation->subCategory->category->name }})</small>
                                    @endif
                                </div>
                                @if(!empty($singleDesc))
                                    <div class="text-muted small fw-normal" style="font-size: 11.5px; line-height: 1.45;">
                                        {!! nl2br(e($singleDesc)) !!}
                                    </div>
                                @endif
                            </td>
                            <td class="text-center font-monospace">1</td>
                            <td class="text-end font-monospace">{{ number_format($pkgBasePrice, 2) }}</td>
                            <td class="text-end pe-3 fw-bold text-primary font-monospace">{{ number_format($pkgBasePrice, 2) }}</td>
                        </tr>
                    @endif

                    <!-- 2. Additional Services / Custom Line Items -->
                    @forelse($quotation->services as $line)
                        <tr>
                            <td class="text-center text-muted font-monospace">{{ $itemCount++ }}</td>
                            <td class="fw-semibold text-dark">
                                {{ $line->description }}
                                @if($line->service && $line->description !== $line->service->name)
                                    <small class="text-muted d-block" style="font-size: 11px;">Service: {{ $line->service->name }}</small>
                                @endif
                            </td>
                            <td class="text-center font-monospace">{{ $line->qty }}</td>
                            <td class="text-end font-monospace">{{ number_format($line->unit_price, 2) }}</td>
                            <td class="text-end pe-3 fw-bold text-dark font-monospace">{{ number_format($line->total_price, 2) }}</td>
                        </tr>
                    @empty
                        @if($pkgBasePrice <= 0)
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No line items recorded on this quotation.</td>
                            </tr>
                        @endif
                    @endforelse

                    <!-- 3. Other Charges (Transport, Hall, Logistics) -->
                    @if($quotation->otherCharges && $quotation->otherCharges->isNotEmpty())
                        @foreach($quotation->otherCharges as $charge)
                            <tr>
                                <td class="text-center text-muted font-monospace">{{ $itemCount++ }}</td>
                                <td class="fw-semibold text-dark">
                                    {{ $charge->description }}
                                    <small class="badge bg-light text-secondary border ms-1" style="font-size: 10px;">Logistics / Surcharge</small>
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

        <!-- 5. Quotation Notes & Calculation Box -->
        <div class="row g-4 mb-4 align-items-start">
            <!-- Left: Proposal Notes & Conditions -->
            <div class="col-md-7">
                <div class="info-card-heading mb-2">
                    <i class="bi bi-card-text me-1"></i> Terms & Proposal Conditions
                </div>
                <div class="p-3 bg-light rounded-3 border text-secondary small" style="line-height: 1.6;">
                    @if($quotation->remarks)
                        <div class="text-dark fw-medium mb-2" style="white-space: pre-line;">
                            {{ $quotation->remarks }}
                        </div>
                    @else
                        <div class="text-muted mb-2">
                            Standard event terms apply. This quotation is an initial estimate and subject to date availability and final scope sign-off.
                        </div>
                    @endif
                    <ul class="ps-3 mb-0 text-muted" style="font-size: 11px;">
                        <li>Prices quoted in Sri Lankan Rupees (LKR) and valid until stated expiry date.</li>
                        <li>An advance deposit is required to confirm booking and secure the event date.</li>
                        <li>Deliverables beyond the agreed scope are subject to additional quotation.</li>
                    </ul>
                </div>
            </div>

            <!-- Right: Commercial Summary Breakdown -->
            <div class="col-md-5">
                <div class="p-3 bg-white border rounded-3 shadow-sm">
                    @if($pkgBasePrice > 0)
                        <div class="summary-row">
                            <span class="text-muted">Package Base Price:</span>
                            <span class="font-monospace fw-semibold text-dark">Rs. {{ number_format($pkgBasePrice, 2) }}</span>
                        </div>
                    @endif

                    @if($svcTotal > 0)
                        <div class="summary-row">
                            <span class="text-muted">Additional Services:</span>
                            <span class="font-monospace fw-semibold text-dark">Rs. {{ number_format($svcTotal, 2) }}</span>
                        </div>
                    @endif

                    @if($chrTotal > 0)
                        <div class="summary-row">
                            <span class="text-muted">Other Charges:</span>
                            <span class="font-monospace fw-semibold text-dark">Rs. {{ number_format($chrTotal, 2) }}</span>
                        </div>
                    @endif

                    <div class="summary-row pt-1 border-top">
                        <span class="text-muted">Sub Total:</span>
                        <strong class="text-dark font-monospace">Rs. {{ number_format($finalSubTotal, 2) }}</strong>
                    </div>

                    @if($discountVal > 0)
                        <div class="summary-row text-danger">
                            <span>Special Discount:</span>
                            <span class="font-monospace">- Rs. {{ number_format($discountVal, 2) }}</span>
                        </div>
                    @endif

                    <div class="grand-total-row">
                        <span>Total Estimated Value:</span>
                        <span class="text-primary font-monospace">Rs. {{ number_format($finalTotalAmount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Acceptance & Signatures -->
        <div class="row pt-4">
            <div class="col-4">
                <div class="sign-line">Prepared & Proposed By</div>
            </div>
            <div class="col-4">
                <div class="sign-line">Authorized Signatory</div>
            </div>
            <div class="col-4">
                <div class="sign-line">Client Acceptance & Signature</div>
            </div>
        </div>

        <!-- 7. Polite Footer Notice -->
        <div class="text-center text-muted mt-3 pt-1.5 border-top page-break-avoid" style="font-size: 10px;">
            Thank you for choosing <strong>CEYLON WEDDING COMPANY</strong> for your special occasion. This is a computer-generated quotation.<br>
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
