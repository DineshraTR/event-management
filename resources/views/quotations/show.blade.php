@extends('layouts.app')
@section('title', ($quotation->event_name ?: 'Quotation') . ' • Quotation #' . $quotation->quotation_no)
@section('page-title', 'Quotation Details')

@section('content')
<style>
    .quotation-show-header {
        background: #ffffff;
        border-radius: 14px;
        padding: 20px 24px;
        border: 1px solid #edf2f7;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.03);
    }

    .section-card {
        background: #ffffff;
        border: 1px solid #edf2f7;
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .section-card-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .section-card-title {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .package-service-pill {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 7px 12px;
        font-size: 12.5px;
        font-weight: 600;
        color: #334155;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .financial-line-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        font-size: 13.5px;
    }

    .financial-total-banner {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
        margin: 14px 0;
    }
</style>

@php
    $displayPackages = $quotation->packages && $quotation->packages->isNotEmpty() ? $quotation->packages : collect();
    $pkgBasePrice = $displayPackages->isNotEmpty()
        ? (float) $displayPackages->sum(function($p) use ($quotation) {
            $unit = (float)($p->sell_price > 0 ? $p->sell_price : ($p->unit_price > 0 ? $p->unit_price : ($p->subCategory?->sell_price ?: ($quotation->package_sell_price ?: ($quotation->subCategory?->sell_price ?? 0)))));
            return (float)($p->total_price > 0 ? $p->total_price : ($unit * ($p->qty ?: 1)));
        })
        : (float)($quotation->package_sell_price ?: ($quotation->subCategory?->sell_price ?? 0));
    $svcTotal = (float)($quotation->services->isNotEmpty() ? $quotation->services->sum('total_price') : $quotation->additional_services_total);
    $chrTotal = (float)($quotation->otherCharges->isNotEmpty() ? $quotation->otherCharges->sum('amount') : $quotation->other_charges_total);
    $calculatedSubTotal = $pkgBasePrice + $svcTotal + $chrTotal;
    $finalSubTotal = (float)($quotation->sub_total > 0 ? $quotation->sub_total : $calculatedSubTotal);
    $discountVal = (float)($quotation->discount ?? 0);
    $finalTotalAmount = (float)($quotation->total_amount > 0 ? $quotation->total_amount : max(0, $finalSubTotal - $discountVal));
@endphp

<!-- Top Header (Tritcal Style) -->
<div class="quotation-show-header mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0"
                style="width: 46px; height: 46px; background: linear-gradient(135deg, #484646 0%, #3a3838 100%); font-size: 22px;">
                <i class="bi bi-file-earmark-text"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.3px;">{{ $quotation->event_name ?: ('Quotation #' . $quotation->quotation_no) }}</h4>
                    <span class="badge bg-light text-dark border font-monospace px-2 py-0.5" style="font-size: 11.5px; font-weight: 700;">
                        {{ $quotation->quotation_no }}
                    </span>
                    @if($quotation->status === 'accepted')
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1" style="font-size: 11.5px; font-weight: 600;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981; display: inline-block; margin-right: 4px;"></span> Accepted
                        </span>
                    @elseif($quotation->status === 'sent')
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1" style="font-size: 11.5px; font-weight: 600;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #3b82f6; display: inline-block; margin-right: 4px;"></span> Sent to Client
                        </span>
                    @elseif($quotation->status === 'rejected')
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1" style="font-size: 11.5px; font-weight: 600;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #ef4444; display: inline-block; margin-right: 4px;"></span> Rejected
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1" style="font-size: 11.5px; font-weight: 600;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #64748b; display: inline-block; margin-right: 4px;"></span> Draft Estimate
                        </span>
                    @endif
                </div>
                <p class="text-muted small mb-0">
                    Client: <strong class="text-dark">{{ $quotation->customer?->name ?? 'Walk-in Customer' }}</strong>
                    &bull; Issue Date: <span>{{ $quotation->quotation_date?->format('M d, Y') ?? 'N/A' }}</span>
                    @if($quotation->valid_until)
                        &bull; Valid Until: <span class="{{ $quotation->valid_until->isPast() ? 'text-danger fw-semibold' : 'text-dark' }}">{{ $quotation->valid_until->format('M d, Y') }}</span>
                    @endif
                    @if($quotation->venue)
                        &bull; Venue: <span class="text-secondary"><i class="bi bi-geo-alt me-0.5"></i>{{ $quotation->venue }}</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Quotations
            </a>
            <a href="{{ route('quotations.edit', $quotation) }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-pencil me-1"></i> Edit Quotation
            </a>
            <a href="{{ route('quotations.print', $quotation) }}" class="btn btn-outline-dark shadow-sm" target="_blank" rel="noopener noreferrer">
                <i class="bi bi-printer me-1"></i> Print Proposal
            </a>
            <a href="{{ route('quotations.convert-to-event', $quotation) }}" class="btn btn-success shadow-sm" target="_blank">
                <i class="bi bi-calendar-check me-1"></i> Convert to Event
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Details, Package Services, Extras, Charges & Remarks -->
    <div class="col-12 col-lg-8">
        <!-- 1. Event & Schedule Information -->
        <div class="section-card">
            <div class="section-card-header">
                <h6 class="section-card-title">
                    <i class="bi bi-info-circle text-primary"></i> Proposal & Event Scope Information
                </h6>
                @if($quotation->subCategory)
                    <span class="badge bg-light text-dark border">
                        <i class="bi bi-box-seam me-1 text-primary"></i> {{ $quotation->subCategory->name }}
                    </span>
                @else
                    <span class="badge bg-light text-secondary border">Custom Scope</span>
                @endif
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block" style="font-size: 11.5px;">PROPOSED EVENT / OCCASION</small>
                        <div class="fw-semibold text-dark fs-6 mt-1">{{ $quotation->event_name ?: 'General Event Proposal' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block" style="font-size: 11.5px;">PROPOSED EVENT DATE</small>
                        <div class="fw-semibold text-dark fs-6 mt-1">
                            @if($quotation->event_date)
                                <i class="bi bi-calendar3 me-1 text-primary"></i>
                                {{ $quotation->event_date->format('l, F j, Y') }}
                            @else
                                <span class="text-muted">Not specified</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block" style="font-size: 11.5px;">VENUE / RECEPTION LOCATION</small>
                        <div class="fw-semibold text-dark mt-1">
                            @if($quotation->venue)
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $quotation->venue }}
                            @else
                                <span class="text-muted">Not specified</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block" style="font-size: 11.5px;">QUOTATION ISSUE & VALIDITY</small>
                        <div class="text-dark mt-1">
                            <i class="bi bi-clock-history me-1 text-secondary"></i>
                            {{ $quotation->quotation_date?->format('M d, Y') ?? 'N/A' }}
                            @if($quotation->valid_until)
                                &bull; <span class="{{ $quotation->valid_until->isPast() ? 'text-danger fw-semibold' : 'text-muted' }}">Expires: {{ $quotation->valid_until->format('M d, Y') }}</span>
                            @endif
                        </div>
                    </div>
                    @if($quotation->remarks)
                        <div class="col-12 mt-2 pt-3 border-top">
                            <small class="text-muted d-block mb-1" style="font-size: 11.5px;">SPECIAL CLIENT REMARKS & SCOPE NOTES</small>
                            <div class="p-3 bg-light rounded-3 text-secondary" style="font-size: 13px; line-height: 1.6; white-space: pre-line;">
                                {{ $quotation->remarks }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. Booked Packages & Included Services (Multi-Package Supported) -->
        <div class="section-card">
            <div class="section-card-header">
                <h6 class="section-card-title">
                    <i class="bi bi-box-seam text-primary"></i> Booked Packages & Included Services
                </h6>
                <span class="badge bg-light text-dark border">
                    Total Packages: Rs. {{ number_format($pkgBasePrice, 2) }}
                </span>
            </div>
            <div class="card-body p-4">
                @if($displayPackages->isNotEmpty())
                    <div class="table-responsive mb-0">
                        <table class="table modern-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Package Name</th>
                                    <th>Category</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($displayPackages as $pkg)
                                    @php
                                        $pkgUnitPrice = (float) (
                                            $pkg->sell_price > 0 
                                                ? $pkg->sell_price 
                                                : ($pkg->unit_price > 0 
                                                    ? $pkg->unit_price 
                                                    : ($pkg->qty > 0 && $pkg->total_price > 0 
                                                        ? $pkg->total_price / $pkg->qty 
                                                        : ($pkg->subCategory?->sell_price 
                                                            ?: ($quotation->package_sell_price 
                                                                ?: ($quotation->subCategory?->sell_price ?? 0))))));
                                        $pkgTotalPrice = (float) ($pkg->total_price > 0 ? $pkg->total_price : ($pkgUnitPrice * ($pkg->qty ?: 1)));
                                        $delivItems = $pkg->deliverables;
                                        if (empty($delivItems) && $pkg->subCategory && $pkg->subCategory->subCategoryServices) {
                                            $delivItems = $pkg->subCategory->subCategoryServices->map(fn($s) => [
                                                'service_name' => $s->service?->name ?? 'Item',
                                                'qty' => $s->qty
                                            ])->toArray();
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $pkg->package_name }}</div>
                                            @if(!empty($delivItems) && is_array($delivItems) && count($delivItems) > 0)
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    @foreach($delivItems as $deliv)
                                                        <span class="badge bg-light text-secondary border fw-normal" style="font-size: 10.5px;">
                                                            <i class="bi bi-check2"></i> {{ $deliv['service_name'] ?? $deliv['name'] ?? 'Item' }} (x{{ $deliv['qty'] ?? 1 }})
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-secondary small">{{ $pkg->category_name ?? $pkg->subCategory?->category?->name ?? 'Package' }}</td>
                                        <td class="text-center font-num fw-semibold">{{ $pkg->qty ?: 1 }}</td>
                                        <td class="text-end font-num">Rs. {{ number_format($pkgUnitPrice, 2) }}</td>
                                        <td class="text-end font-num fw-bold text-dark">Rs. {{ number_format($pkgTotalPrice, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom flex-wrap gap-2">
                        <div>
                            <h6 class="fw-bold text-dark mb-0">{{ $quotation->subCategory?->name ?? $quotation->package_name ?? 'Standard Package' }}</h6>
                            <small class="text-muted">Category: {{ $quotation->subCategory?->category?->name ?? 'General Category' }}</small>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block" style="font-size: 11px;">PACKAGE BASE PRICE</small>
                            <span class="fw-bold text-primary fs-6">Rs. {{ number_format($pkgBasePrice, 2) }}</span>
                        </div>
                    </div>

                    @php
                        $includedServices = $quotation->subCategory?->subCategoryServices ?? collect();
                    @endphp

                    <small class="text-muted d-block mb-2 fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                        Included Package Services ({{ $includedServices->count() }} Items):
                    </small>

                    @if($includedServices->isNotEmpty())
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($includedServices as $pkgSvc)
                                <div class="package-service-pill">
                                    <i class="bi bi-check2-circle text-success fs-6"></i>
                                    <span>{{ $pkgSvc->service?->name ?? 'Service' }} &bull; Qty: <strong>{{ $pkgSvc->qty }}</strong></span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-muted small py-2">
                            <i class="bi bi-info-circle me-1"></i> No standard bundled services defined for this package.
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- 3. Additional Services Table -->
        <div class="section-card">
            <div class="section-card-header">
                <h6 class="section-card-title">
                    <i class="bi bi-stars text-primary"></i> Additional Services (Add-ons & Upgrades)
                </h6>
                <span class="badge bg-light text-dark border">
                    {{ $quotation->services->count() }} Extra Items
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4 py-3">Service Description</th>
                            <th class="py-3 text-center" style="width: 100px;">Qty</th>
                            <th class="py-3 text-end" style="width: 160px;">Unit Price</th>
                            <th class="py-3 text-end pe-4" style="width: 170px;">Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotation->services as $svc)
                            <tr>
                                <td class="ps-4 fw-medium text-dark">
                                    {{ $svc->description }}
                                    @if($svc->service)
                                        <small class="text-muted d-block" style="font-size: 11px;">
                                            Preset: {{ $svc->service->name }}
                                        </small>
                                    @endif
                                </td>
                                <td class="text-center">{{ $svc->qty }}</td>
                                <td class="text-end">Rs. {{ number_format($svc->unit_price, 2) }}</td>
                                <td class="text-end pe-4 fw-bold text-dark">Rs. {{ number_format($svc->total_price, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4 small">
                                    <i class="bi bi-info-circle me-1"></i> No additional upgrade services added to this quotation.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($quotation->services->isNotEmpty())
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold text-secondary">Extra Services Total:</td>
                                <td class="text-end pe-4 fw-bold text-dark">
                                    Rs. {{ number_format($svcTotal, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <!-- 4. Other Charges Table (Transport, Hall Rental, Logistics) -->
        <div class="section-card">
            <div class="section-card-header">
                <h6 class="section-card-title">
                    <i class="bi bi-truck text-primary"></i> Other Charges (Transport, Logistics, Surcharges)
                </h6>
                <span class="badge bg-light text-dark border">
                    {{ $quotation->otherCharges->count() }} Charges
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4 py-3">Charge Description</th>
                            <th class="py-3 text-end pe-4" style="width: 200px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotation->otherCharges as $charge)
                            <tr>
                                <td class="ps-4 fw-medium text-dark">{{ $charge->description }}</td>
                                <td class="text-end pe-4 fw-bold text-dark">Rs. {{ number_format($charge->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-4 small">
                                    <i class="bi bi-info-circle me-1"></i> No other charges or logistics fees recorded.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($quotation->otherCharges->isNotEmpty())
                        <tfoot class="bg-light">
                            <tr>
                                <td class="text-end fw-bold text-secondary">Other Charges Total:</td>
                                <td class="text-end pe-4 fw-bold text-dark">
                                    Rs. {{ number_format($chrTotal, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Customer Info, Financial Breakdown & Summary -->
    <div class="col-12 col-lg-4">
        <!-- 1. Customer Contact Card -->
        <div class="section-card">
            <div class="section-card-header">
                <h6 class="section-card-title">
                    <i class="bi bi-person text-primary"></i> Customer Contact
                </h6>
            </div>
            <div class="card-body p-4">
                @if($quotation->customer)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm flex-shrink-0"
                            style="width: 44px; height: 44px; background: linear-gradient(135deg, #484646 0%, #3a3838 100%); font-size: 17px;">
                            {{ strtoupper(substr($quotation->customer->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-6">{{ $quotation->customer->name }}</div>
                            <small class="text-muted">Client Contact</small>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2.5 pt-2 border-top">
                        <div class="d-flex align-items-center gap-2 text-dark small">
                            <i class="bi bi-telephone text-primary" style="width: 18px;"></i>
                            @if($quotation->customer->phone)
                                <a href="tel:{{ $quotation->customer->phone }}" class="text-decoration-none text-dark fw-medium">
                                    {{ $quotation->customer->phone }}
                                </a>
                            @else
                                <span class="text-muted">No phone provided</span>
                            @endif
                        </div>

                        <div class="d-flex align-items-center gap-2 text-dark small">
                            <i class="bi bi-envelope text-primary" style="width: 18px;"></i>
                            @if($quotation->customer->email)
                                <a href="mailto:{{ $quotation->customer->email }}" class="text-decoration-none text-dark">
                                    {{ $quotation->customer->email }}
                                </a>
                            @else
                                <span class="text-muted">No email provided</span>
                            @endif
                        </div>

                        @if($quotation->customer->address)
                            <div class="d-flex align-items-start gap-2 text-dark small">
                                <i class="bi bi-geo-alt text-primary mt-0.5" style="width: 18px;"></i>
                                <span class="text-secondary">{{ $quotation->customer->address }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="bi bi-person-x fs-2 d-block mb-1 text-secondary opacity-50"></i>
                        <span>Walk-in Customer</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- 2. Financial Summary Card -->
        <div class="section-card">
            <div class="section-card-header">
                <h6 class="section-card-title">
                    <i class="bi bi-calculator text-primary"></i> Financial Summary
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="financial-line-item">
                    <span class="text-muted">Package Base Price:</span>
                    <strong class="text-dark">Rs. {{ number_format($pkgBasePrice, 2) }}</strong>
                </div>

                <div class="financial-line-item">
                    <span class="text-muted">Extra Services Total:</span>
                    <strong class="text-dark">Rs. {{ number_format($svcTotal, 2) }}</strong>
                </div>

                <div class="financial-line-item">
                    <span class="text-muted">Other Charges Total:</span>
                    <strong class="text-dark">Rs. {{ number_format($chrTotal, 2) }}</strong>
                </div>

                <div class="financial-line-item pt-2 border-top">
                    <span class="text-muted">Gross Subtotal:</span>
                    <strong class="text-dark">Rs. {{ number_format($finalSubTotal, 2) }}</strong>
                </div>

                <div class="financial-line-item">
                    <span class="text-muted">Special Discount:</span>
                    <strong class="text-danger">- Rs. {{ number_format($discountVal, 2) }}</strong>
                </div>

                <div class="financial-total-banner d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small fw-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">Total Quoted</div>
                        <small class="text-muted">Net estimated value</small>
                    </div>
                    <div class="fs-4 fw-bold text-primary">
                        Rs. {{ number_format($finalTotalAmount, 2) }}
                    </div>
                </div>

                <div class="mt-3 pt-2 border-top text-center">
                    <span class="badge bg-{{ $quotation->status === 'accepted' ? 'success' : ($quotation->status === 'sent' ? 'primary' : ($quotation->status === 'rejected' ? 'danger' : 'secondary')) }}-subtle text-{{ $quotation->status === 'accepted' ? 'success' : ($quotation->status === 'sent' ? 'primary' : ($quotation->status === 'rejected' ? 'danger' : 'secondary')) }} border px-3 py-1.5" style="font-size: 12px; font-weight: 700;">
                        <i class="bi bi-dot"></i> Status: {{ ucfirst($quotation->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 3. Quick Operations Card -->
        <div class="section-card">
            <div class="section-card-header">
                <h6 class="section-card-title">
                    <i class="bi bi-lightning-charge text-warning"></i> Quick Operations
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="d-grid gap-2">
                    <a href="{{ route('quotations.convert-to-event', $quotation) }}" class="btn btn-success py-2 fw-semibold" target="_blank">
                        <i class="bi bi-calendar-check me-1.5"></i> Convert to Event Now
                    </a>

                    <a href="{{ route('quotations.edit', $quotation) }}" class="btn btn-outline-primary py-2 fw-semibold">
                        <i class="bi bi-pencil me-1.5"></i> Edit Quotation Details
                    </a>

                    <a href="{{ route('quotations.print', $quotation) }}" class="btn btn-outline-dark py-2 fw-semibold" target="_blank">
                        <i class="bi bi-printer me-1.5"></i> Print Proposal
                    </a>

                    <form method="POST" action="{{ route('quotations.destroy', $quotation) }}" class="mt-2" onsubmit="handleDeleteConfirmation(event, this, {
                        type: 'Quotation',
                        title: '{{ $quotation->quotation_no }} - {{ addslashes($quotation->event_name) }}',
                        hasPayments: false
                    });">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100 py-2 fw-semibold">
                            <i class="bi bi-trash3 me-1.5"></i> Delete Quotation
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
