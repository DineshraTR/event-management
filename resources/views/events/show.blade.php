@extends('layouts.app')
@section('title', $event->event_name . ' • Event #' . $event->event_no)
@section('page-title', 'Event Details')

@section('content')
<style>
    .event-show-header {
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

<!-- Top Header (Tritcal Style) -->
<div class="event-show-header mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0"
                style="width: 46px; height: 46px; background: linear-gradient(135deg, #484646 0%, #3a3838 100%); font-size: 22px;">
                <i class="bi bi-calendar2-event"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.3px;">{{ $event->event_name }}</h4>
                    <span class="badge bg-light text-dark border font-monospace px-2 py-0.5" style="font-size: 11.5px; font-weight: 700;">
                        {{ $event->event_no }}
                    </span>
                    @if($event->status === 'confirmed')
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1" style="font-size: 11.5px; font-weight: 600;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981; display: inline-block; margin-right: 4px;"></span> Confirmed
                        </span>
                    @elseif($event->status === 'pending')
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1" style="font-size: 11.5px; font-weight: 600;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #f59e0b; display: inline-block; margin-right: 4px;"></span> Pending
                        </span>
                    @elseif($event->status === 'completed')
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1" style="font-size: 11.5px; font-weight: 600;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #3b82f6; display: inline-block; margin-right: 4px;"></span> Completed
                        </span>
                    @else
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1" style="font-size: 11.5px; font-weight: 600;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #ef4444; display: inline-block; margin-right: 4px;"></span> Cancelled
                        </span>
                    @endif
                </div>
                <p class="text-muted small mb-0">
                    Client: <strong class="text-dark">{{ $event->customer?->name ?? 'Walk-in Customer' }}</strong>
                    &bull; Registered on: <span>{{ $event->today_date?->format('M d, Y') ?? $event->created_at->format('M d, Y') }}</span>
                    @if($event->venue)
                        &bull; Venue: <span class="text-secondary"><i class="bi bi-geo-alt me-0.5"></i>{{ $event->venue }}</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Events
            </a>
            <a href="{{ route('events.edit', $event) }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-pencil me-1"></i> Edit Event
            </a>
            @if($event->order)
                <a href="{{ route('orders.print', $event->order) }}" class="btn btn-outline-dark shadow-sm" target="_blank">
                    <i class="bi bi-printer me-1"></i> Print Invoice
                </a>
                <a href="{{ route('orders.show', $event->order) }}" class="btn btn-dark shadow-sm" target="_blank">
                    <i class="bi bi-receipt me-1"></i> View Order ({{ $event->order->order_no }})
                </a>
            @else
                <a href="{{ route('events.create-order', $event) }}" class="btn btn-success shadow-sm" target="_blank">
                    <i class="bi bi-cart-check me-1"></i> Create Order
                </a>
            @endif
        </div>
    </div>
</div>



<div class="row g-4">
    <!-- Left Column: Details, Package Services, Extras, Charges & Orders -->
    <div class="col-12 col-lg-8">
        <!-- 1. Event & Schedule Information -->
        <div class="section-card">
            <div class="section-card-header">
                <h6 class="section-card-title">
                    <i class="bi bi-info-circle text-primary"></i> Event & Venue Information
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block" style="font-size: 11.5px;">EVENT NAME / OCCASION</small>
                        <div class="fw-semibold text-dark fs-6 mt-1">{{ $event->event_name }}</div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block" style="font-size: 11.5px;">EVENT DATE & DAY</small>
                        <div class="fw-semibold text-dark fs-6 mt-1">
                            <i class="bi bi-calendar3 me-1 text-primary"></i>
                            {{ $event->event_date?->format('l, F j, Y') ?? 'Not specified' }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block" style="font-size: 11.5px;">VENUE / RECEPTION LOCATION</small>
                        <div class="fw-semibold text-dark mt-1">
                            @if($event->venue)
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $event->venue }}
                            @else
                                <span class="text-muted">Not specified</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block" style="font-size: 11.5px;">REGISTRATION DATE</small>
                        <div class="text-dark mt-1">
                            <i class="bi bi-clock-history me-1 text-secondary"></i>
                            {{ $event->today_date?->format('M d, Y') ?? $event->created_at->format('M d, Y') }}
                        </div>
                    </div>
                    @if($event->remarks)
                        <div class="col-12 mt-2 pt-3 border-top">
                            <small class="text-muted d-block mb-1" style="font-size: 11.5px;">SPECIAL CLIENT REMARKS & ARRANGEMENTS NOTES</small>
                            <div class="p-3 bg-light rounded-3 text-secondary" style="font-size: 13px; line-height: 1.6;">
                                {{ $event->remarks }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. Booked Packages & Included Services (Multi-Package Supported) -->
        @php
            $displayPackages = $event->packages && $event->packages->isNotEmpty() ? $event->packages : collect();
        @endphp
        <div class="section-card">
            <div class="section-card-header">
                <h6 class="section-card-title">
                    <i class="bi bi-box-seam text-primary"></i> Booked Packages & Included Services
                </h6>
                <span class="badge bg-light text-dark border">
                    Total Packages: Rs. {{ number_format($event->package_sell_price, 2) }}
                </span>
            </div>
            <div class="card-body p-4">
                @if($displayPackages->isNotEmpty())
                    <div class="table-responsive mb-3">
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
                                                            ?: ($event->package_sell_price 
                                                                ?: ($event->subCategory?->sell_price ?? 0))))));
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
                                            @php
                                                $pkgDesc = $pkg->description ?? $pkg->subCategory?->description ?? '';
                                            @endphp
                                            @if(!empty($pkgDesc))
                                                <div class="text-muted small mt-1" style="white-space: pre-line; font-size: 11.5px; line-height: 1.45;">
                                                    <i class="bi bi-text-paragraph text-primary me-1"></i>{{ $pkgDesc }}
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
                            <h6 class="fw-bold text-dark mb-0">{{ $event->subCategory?->name ?? 'Standard Package' }}</h6>
                            <small class="text-muted">Category: {{ $event->subCategory?->category?->name ?? 'General Category' }}</small>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block" style="font-size: 11px;">PACKAGE BASE PRICE</small>
                            <span class="fw-bold text-primary fs-6">Rs. {{ number_format($event->package_sell_price, 2) }}</span>
                        </div>
                    </div>

                    @php
                        $baseDesc = $event->subCategory?->description;
                    @endphp

                    @if(!empty($baseDesc))
                        <div class="mt-2">
                            <small class="text-muted d-block mb-1 fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                                Package Description:
                            </small>
                            <div class="text-secondary small p-3 bg-light rounded-3" style="white-space: pre-line; line-height: 1.5;">{{ $baseDesc }}</div>
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
                    {{ $event->additionalServices->count() }} Extra Items
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
                        @forelse($event->additionalServices as $svc)
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
                                    <i class="bi bi-info-circle me-1"></i> No additional upgrade services added to this event.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($event->additionalServices->isNotEmpty())
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold text-secondary">Extra Services Total:</td>
                                <td class="text-end pe-4 fw-bold text-dark">
                                    Rs. {{ number_format($event->additional_services_total, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <!-- 4. Other Charges Table (Transport, Hall Rental, Surcharges) -->
        <div class="section-card">
            <div class="section-card-header">
                <h6 class="section-card-title">
                    <i class="bi bi-truck text-primary"></i> Other Charges (Transport, Hall Rental, Logistics)
                </h6>
                <span class="badge bg-light text-dark border">
                    {{ $event->otherCharges->count() }} Charges
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
                        @forelse($event->otherCharges as $charge)
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
                    @if($event->otherCharges->isNotEmpty())
                        <tfoot class="bg-light">
                            <tr>
                                <td class="text-end fw-bold text-secondary">Other Charges Total:</td>
                                <td class="text-end pe-4 fw-bold text-dark">
                                    Rs. {{ number_format($event->other_charges_total, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <!-- 5. Linked Customer Orders & Invoices -->
        <div class="section-card">
            <div class="section-card-header">
                <h6 class="section-card-title">
                    <i class="bi bi-receipt text-primary"></i> Associated Customer Orders & Settlements
                </h6>
                @if($event->orders->isNotEmpty())
                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                        {{ $event->orders->count() }} Linked Orders
                    </span>
                @endif
            </div>
            <div class="card-body p-4">
                @if($event->orders->isNotEmpty())
                    @foreach($event->orders as $ord)
                        <div class="border rounded-3 p-3 mb-3 bg-light">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2 pb-2 border-bottom">
                                <div>
                                    <span class="badge bg-light text-dark border font-monospace px-2 py-1 me-1" style="font-size: 12px; font-weight: 700;">
                                        <a href="{{ route('orders.show', $ord) }}" class="text-decoration-none text-dark" target="_blank">
                                            {{ $ord->order_no }}
                                        </a>
                                    </span>
                                    <span class="badge bg-{{ $ord->payment_status === 'paid' ? 'success' : ($ord->payment_status === 'partial' ? 'primary' : 'warning') }}-subtle text-{{ $ord->payment_status === 'paid' ? 'success' : ($ord->payment_status === 'partial' ? 'primary' : 'warning-emphasis') }} border">
                                        {{ ucfirst($ord->payment_status) }}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('orders.show', $ord) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="bi bi-eye me-1"></i> View Order
                                    </a>
                                    @if($ord->balance > 0)
                                        <a href="{{ route('payments.create-for-order', $ord) }}" class="btn btn-sm btn-success" target="_blank">
                                            <i class="bi bi-cash-stack me-1"></i> Record Payment
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="row g-2 text-dark small mb-2">
                                <div class="col-sm-4">
                                    <span class="text-muted">Order Date:</span> <strong>{{ $ord->order_date?->format('M d, Y') ?? 'N/A' }}</strong>
                                </div>
                                <div class="col-sm-4">
                                    <span class="text-muted">Order Total:</span> <strong>Rs. {{ number_format($ord->total_amount, 2) }}</strong>
                                </div>
                                <div class="col-sm-4">
                                    <span class="text-muted">Paid:</span> <strong class="text-success">Rs. {{ number_format($ord->paid_amount, 2) }}</strong>
                                    &bull; <span class="text-muted">Bal:</span> <strong class="{{ $ord->balance > 0 ? 'text-danger' : 'text-success' }}">Rs. {{ number_format($ord->balance, 2) }}</strong>
                                </div>
                            </div>

                            <!-- Payments sub-list -->
                            @if($ord->payments->isNotEmpty())
                                <div class="mt-2 pt-2 border-top">
                                    <small class="text-muted d-block mb-1 fw-bold">Payments Recorded on this Order:</small>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($ord->payments as $pmt)
                                            <span class="badge bg-white text-dark border px-2 py-1" style="font-size: 11px;">
                                                <i class="bi bi-check2 text-success me-1"></i>
                                                {{ $pmt->payment_date?->format('M d') }}: Rs. {{ number_format($pmt->amount, 2) }} ({{ ucfirst(str_replace('_', ' ', $pmt->payment_method)) }})
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-receipt text-secondary opacity-50 fs-1 d-block mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">No Official Order Created Yet</h6>
                        <p class="text-muted small mb-3">Convert this event into an official customer sales order to issue invoices and record client payment settlements.</p>
                        <a href="{{ route('events.create-order', $event) }}" class="btn btn-success px-4 fw-semibold shadow-sm" target="_blank">
                            <i class="bi bi-cart-check me-1"></i> Create Customer Order Now
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Customer Info, Financial Breakdown & Summary -->
    <div class="col-12 col-lg-4">
        <!-- 1. Customer Profile Card -->
        <div class="section-card">
            <div class="section-card-header">
                <h6 class="section-card-title">
                    <i class="bi bi-person text-primary"></i> Customer Contact
                </h6>
            </div>
            <div class="card-body p-4">
                @if($event->customer)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm flex-shrink-0"
                            style="width: 44px; height: 44px; background: linear-gradient(135deg, #484646 0%, #3a3838 100%); font-size: 17px;">
                            {{ strtoupper(substr($event->customer->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-6">{{ $event->customer->name }}</div>
                            <small class="text-muted">Registered Client</small>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2.5 pt-2 border-top">
                        <div class="d-flex align-items-center gap-2 text-dark small">
                            <i class="bi bi-telephone text-primary" style="width: 18px;"></i>
                            @if($event->customer->phone)
                                <a href="tel:{{ $event->customer->phone }}" class="text-decoration-none text-dark fw-medium">
                                    {{ $event->customer->phone }}
                                </a>
                            @else
                                <span class="text-muted">No phone provided</span>
                            @endif
                        </div>

                        <div class="d-flex align-items-center gap-2 text-dark small">
                            <i class="bi bi-envelope text-primary" style="width: 18px;"></i>
                            @if($event->customer->email)
                                <a href="mailto:{{ $event->customer->email }}" class="text-decoration-none text-dark">
                                    {{ $event->customer->email }}
                                </a>
                            @else
                                <span class="text-muted">No email provided</span>
                            @endif
                        </div>

                        @if($event->customer->address)
                            <div class="d-flex align-items-start gap-2 text-dark small">
                                <i class="bi bi-geo-alt text-primary mt-0.5" style="width: 18px;"></i>
                                <span class="text-secondary">{{ $event->customer->address }}</span>
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
                    <strong class="text-dark">Rs. {{ number_format($event->package_sell_price, 2) }}</strong>
                </div>

                <div class="financial-line-item">
                    <span class="text-muted">Extra Services Total:</span>
                    <strong class="text-dark">Rs. {{ number_format($event->additional_services_total, 2) }}</strong>
                </div>

                <div class="financial-line-item">
                    <span class="text-muted">Other Charges Total:</span>
                    <strong class="text-dark">Rs. {{ number_format($event->other_charges_total, 2) }}</strong>
                </div>

                <div class="financial-line-item pt-2 border-top">
                    <span class="text-muted">Gross Subtotal:</span>
                    <strong class="text-dark">Rs. {{ number_format($event->sub_total, 2) }}</strong>
                </div>

                <div class="financial-line-item">
                    <span class="text-muted">Special Discount:</span>
                    <strong class="text-danger">- Rs. {{ number_format($event->discount, 2) }}</strong>
                </div>

                <div class="financial-total-banner d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small fw-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">Final Total</div>
                        <small class="text-muted">Net payable amount</small>
                    </div>
                    <div class="fs-4 fw-bold text-primary">
                        Rs. {{ number_format($event->total_amount, 2) }}
                    </div>
                </div>

                <div class="financial-line-item">
                    <div>
                        <span class="text-dark fw-semibold">Advance Payment:</span>
                        <div class="text-muted" style="font-size: 11px;">Deposited at registration</div>
                    </div>
                    <strong class="text-success fs-6">Rs. {{ number_format($event->advance_payment, 2) }}</strong>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-2.5 mt-2 border-top">
                    <span class="fw-bold text-dark">Remaining Balance:</span>
                    @if($event->balance > 0)
                        <strong class="text-danger fs-5">
                            Rs. {{ number_format($event->balance, 2) }}
                        </strong>
                    @else
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1" style="font-size: 12px; font-weight: 700;">
                            <i class="bi bi-check2 me-1"></i> Paid in Full
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. Quick Actions Card -->
        <div class="section-card">
            <div class="section-card-header">
                <h6 class="section-card-title">
                    <i class="bi bi-lightning-charge text-warning"></i> Quick Operations
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="d-grid gap-2">
                    <a href="{{ route('events.edit', $event) }}" class="btn btn-outline-primary py-2 fw-semibold">
                        <i class="bi bi-pencil me-1.5"></i> Edit Event Details
                    </a>

                    @if($event->order)
                        <a href="{{ route('orders.show', $event->order) }}" class="btn btn-outline-dark py-2 fw-semibold" target="_blank">
                            <i class="bi bi-receipt me-1.5"></i> Open Customer Order
                        </a>
                        @if($event->order->balance > 0)
                            <a href="{{ route('payments.create-for-order', $event->order) }}" class="btn btn-success py-2 fw-semibold" target="_blank">
                                <i class="bi bi-credit-card me-1.5"></i> Record Order Payment
                            </a>
                        @endif
                    @else
                        <a href="{{ route('events.create-order', $event) }}" class="btn btn-success py-2 fw-semibold" target="_blank">
                            <i class="bi bi-cart-check me-1.5"></i> Create Order from Event
                        </a>
                    @endif

                    @if(!$event->hasPayments())
                        <form method="POST" action="{{ route('events.destroy', $event) }}" class="mt-2" onsubmit="handleDeleteConfirmation(event, this, {
                            type: 'Event',
                            title: '{{ $event->event_no }} - {{ addslashes($event->event_name) }}',
                            hasPayments: false,
                            extraNote: '{{ $event->order ? 'Associated Order #' . $event->order->order_no . ' and all its items will also be permanently deleted.' : '' }}'
                        });">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100 py-2 fw-semibold">
                                <i class="bi bi-trash3 me-1.5"></i> Delete Event
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection