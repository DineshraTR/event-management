@extends('layouts.app')
@section('title', 'Quotation ' . $quotation->quotation_no . ' Details')
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
                    <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.3px;">Quotation {{ $quotation->quotation_no }}</h4>
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
                    @if($quotation->event_name)
                        &bull; Event: <span class="text-secondary">{{ $quotation->event_name }}</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Header Action Buttons -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Quotations
            </a>
            <a href="{{ route('quotations.edit', $quotation) }}" class="btn btn-outline-primary shadow-sm">
                <i class="bi bi-pencil me-1"></i> Edit Quotation
            </a>
            <a href="{{ route('quotations.print', $quotation) }}" class="btn btn-outline-dark shadow-sm" target="_blank" rel="noopener noreferrer">
                <i class="bi bi-printer me-1"></i> Print / Export
            </a>
            <a href="{{ route('quotations.convert-to-event', $quotation) }}" class="btn btn-success shadow-sm" target="_blank">
                <i class="bi bi-calendar-check me-1"></i> Convert to Event
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Event Proposal Info, Services Table, Other Charges, Remarks -->
    <div class="col-12 col-lg-8">
        <!-- 1. Event & Scope Details Card -->
        <div class="section-card">
            <div class="section-card-header">
                <h6 class="section-card-title">
                    <i class="bi bi-calendar-event text-primary"></i> Event & Scope Information
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
                        <small class="text-muted d-block" style="font-size: 11.5px;">PROPOSED EVENT NAME</small>
                        <div class="fw-semibold text-dark fs-6 mt-1">{{ $quotation->event_name ?: 'General Event Proposal' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block" style="font-size: 11.5px;">PROPOSED EVENT DATE</small>
                        <div class="fw-semibold text-dark mt-1">
                            @if($quotation->event_date)
                                <i class="bi bi-calendar3 text-primary me-1"></i>
                                {{ $quotation->event_date->format('l, F j, Y') }}
                            @else
                                <span class="text-muted">Not specified</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block" style="font-size: 11.5px;">PACKAGE / CATEGORY</small>
                        <div class="fw-semibold text-dark mt-1">
                            @if($quotation->subCategory || $quotation->package_name)
                                <span>{{ $quotation->package_name ?? $quotation->subCategory->name }}</span>
                                <span class="badge bg-primary-subtle text-primary border ms-1">
                                    Base: Rs. {{ number_format($quotation->package_sell_price ?? $quotation->subCategory?->sell_price ?? 0, 2) }}
                                </span>
                                <small class="text-muted d-block">Category: {{ $quotation->subCategory?->category?->name ?? 'Package Preset' }}</small>
                            @else
                                <span class="text-muted">Custom Package & Services</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block" style="font-size: 11.5px;">PROPOSED VENUE / LOCATION</small>
                        <div class="fw-semibold text-dark mt-1">
                            @if($quotation->venue)
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $quotation->venue }}
                            @else
                                <span class="text-muted">Not specified</span>
                            @endif
                        </div>
                    </div>

                    @if($quotation->subCategory && $quotation->subCategory->subCategoryServices->isNotEmpty())
                        <div class="col-12 mt-3 pt-3 border-top">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <small class="text-muted fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                                    <i class="bi bi-check-circle-fill text-success me-1"></i> Deliverables Included in Package Preset:
                                </small>
                                <span class="badge bg-light text-secondary border">
                                    {{ $quotation->subCategory->subCategoryServices->count() }} Deliverables
                                </span>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($quotation->subCategory->subCategoryServices as $pkgSvc)
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5" style="font-weight: 500; font-size: 12px;">
                                        <i class="bi bi-check2 text-primary me-1"></i>
                                        {{ $pkgSvc->service?->name ?? 'Service #' . $pkgSvc->service_id }}
                                        <span class="text-muted ms-1">&bull; Qty: <strong>{{ $pkgSvc->qty }}</strong></span>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. Quoted Services & Items Table -->
        <div class="section-card">
            <div class="section-card-header">
                <h6 class="section-card-title">
                    <i class="bi bi-stars text-primary"></i> Additional Services & Deliverables
                </h6>
                <span class="badge bg-light text-dark border">
                    {{ $quotation->services->count() }} Additional Items
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4 py-3">Description</th>
                            <th class="py-3 text-center" style="width: 100px;">Qty</th>
                            <th class="py-3 text-end" style="width: 160px;">Unit Price</th>
                            <th class="py-3 text-end pe-4" style="width: 170px;">Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotation->services as $line)
                            <tr>
                                <td class="ps-4 fw-medium text-dark">
                                    {{ $line->description }}
                                    @if($line->service)
                                        <small class="text-muted d-block" style="font-size: 11px;">
                                            Preset: {{ $line->service->name }}
                                        </small>
                                    @endif
                                </td>
                                <td class="text-center">{{ $line->qty }}</td>
                                <td class="text-end">Rs. {{ number_format($line->unit_price, 2) }}</td>
                                <td class="text-end pe-4 fw-bold text-dark">Rs. {{ number_format($line->total_price, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4 small">
                                    <i class="bi bi-info-circle me-1"></i> No service lines recorded on this quotation.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($quotation->services->isNotEmpty())
                        @php
                            $servicesSum = $quotation->services->sum('total_price');
                        @endphp
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold text-secondary">Services Subtotal:</td>
                                <td class="text-end pe-4 fw-bold text-dark">
                                    Rs. {{ number_format($servicesSum, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <!-- 3. Other Charges Table (if applicable) -->
        @if($quotation->otherCharges && $quotation->otherCharges->isNotEmpty())
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
                            @foreach($quotation->otherCharges as $charge)
                                <tr>
                                    <td class="ps-4 fw-medium text-dark">{{ $charge->description }}</td>
                                    <td class="text-end pe-4 fw-bold text-dark">Rs. {{ number_format($charge->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td class="text-end fw-bold text-secondary">Charges Subtotal:</td>
                                <td class="text-end pe-4 fw-bold text-dark">
                                    Rs. {{ number_format($quotation->otherCharges->sum('amount'), 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif

        <!-- 4. Remarks / Terms & Conditions -->
        @if($quotation->remarks)
            <div class="section-card">
                <div class="section-card-header">
                    <h6 class="section-card-title">
                        <i class="bi bi-card-text text-secondary"></i> Quotation Remarks & Terms
                    </h6>
                </div>
                <div class="card-body p-4 text-secondary" style="font-size: 13.5px; line-height: 1.6;">
                    {{ $quotation->remarks }}
                </div>
            </div>
        @endif
    </div>

    <!-- Right Column: Customer Info, Financial Summary & Actions -->
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
                    <span class="text-muted">Issue Date:</span>
                    <strong class="text-dark">{{ $quotation->quotation_date?->format('M d, Y') ?? '—' }}</strong>
                </div>

                <div class="financial-line-item">
                    <span class="text-muted">Valid Until:</span>
                    @if($quotation->valid_until)
                        <strong class="{{ $quotation->valid_until->isPast() ? 'text-danger' : 'text-dark' }}">
                            {{ $quotation->valid_until->format('M d, Y') }}
                            @if($quotation->valid_until->isPast())
                                <small class="text-danger fw-normal">(Expired)</small>
                            @endif
                        </strong>
                    @else
                        <span class="text-muted">No expiry</span>
                    @endif
                </div>

                @php
                    $pkgSellPrice = (float)($quotation->package_sell_price ?? $quotation->subCategory?->sell_price ?? 0);
                    $svcTotal = (float)($quotation->additional_services_total ?? $quotation->services->sum('total_price'));
                    $chrTotal = (float)($quotation->other_charges_total ?? $quotation->otherCharges->sum('amount'));
                @endphp

                <div class="pt-2 border-top">
                    @if($pkgSellPrice > 0)
                        <div class="financial-line-item">
                            <span class="text-muted">Package Base Price:</span>
                            <strong class="text-primary">Rs. {{ number_format($pkgSellPrice, 2) }}</strong>
                        </div>
                    @endif

                    @if($svcTotal > 0 || $pkgSellPrice == 0)
                        <div class="financial-line-item">
                            <span class="text-muted">Additional Services:</span>
                            <strong class="text-dark">Rs. {{ number_format($svcTotal, 2) }}</strong>
                        </div>
                    @endif

                    @if($chrTotal > 0)
                        <div class="financial-line-item">
                            <span class="text-muted">Other Charges:</span>
                            <strong class="text-dark">Rs. {{ number_format($chrTotal, 2) }}</strong>
                        </div>
                    @endif
                </div>

                <div class="financial-line-item pt-2 border-top">
                    <span class="text-muted">Gross Subtotal:</span>
                    <strong class="text-dark">Rs. {{ number_format($quotation->sub_total, 2) }}</strong>
                </div>

                <div class="financial-line-item">
                    <span class="text-muted">Special Discount:</span>
                    <strong class="text-danger">- Rs. {{ number_format($quotation->discount, 2) }}</strong>
                </div>

                <div class="financial-total-banner d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small fw-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">Total Quoted</div>
                        <small class="text-muted">Net estimated value</small>
                    </div>
                    <div class="fs-4 fw-bold text-primary">
                        Rs. {{ number_format($quotation->total_amount, 2) }}
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
                        <i class="bi bi-pencil me-1.5"></i> Edit Quotation
                    </a>

                    <a href="{{ route('quotations.print', $quotation) }}" class="btn btn-outline-dark py-2 fw-semibold" target="_blank">
                        <i class="bi bi-printer me-1.5"></i> Print Quotation
                    </a>

                    <form method="POST" action="{{ route('quotations.destroy', $quotation) }}" class="mt-2" onsubmit="return confirm('Are you sure you want to delete this quotation?');">
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