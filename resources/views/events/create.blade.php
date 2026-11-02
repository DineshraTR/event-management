@extends('layouts.app')
@section('title', 'Register Event')
@section('page-title', 'Register Event')

@section('content')
<style>
    /* Custom Styling for Event Registration */
    .event-creator-card {
        border-radius: 16px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
        background: #ffffff;
        overflow: hidden;
    }

    .event-header-banner {
        background: #ffffff;
        border-bottom: 1px solid #edf2f7;
        padding: 16px 22px;
    }

    .event-icon-box {
        width: 38px;
        height: 38px;
        background: rgba(72, 70, 70, 0.08);
        border: 1px solid rgba(72, 70, 70, 0.12);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        color: #484646;
        flex-shrink: 0;
    }

    .form-section-title {
        font-size: 13.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.75px;
        color: #222d3c;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
    }

    .form-section-title i {
        font-size: 16px;
        color: #484646;
    }

    .custom-section-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 20px;
        margin-bottom: 24px;
        transition: all 0.2s ease;
    }

    .custom-section-box:hover {
        border-color: #cbd5e1;
    }

    .custom-input-label {
        font-weight: 600;
        font-size: 13px;
        color: #334155;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .custom-input-label i {
        color: #94a3b8;
        font-size: 14px;
    }

    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 9px 13px;
        font-size: 13.5px;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #484646;
        box-shadow: 0 0 0 3px rgba(72, 70, 70, 0.15);
    }

    /* Package included tags */
    .package-service-pill {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
        color: #334155;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    }

    .package-service-pill i {
        color: #16a34a;
        font-size: 14px;
    }

    /* Sub-tables */
    .modern-table-container {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #ffffff;
        overflow: hidden;
    }

    .modern-table {
        margin-bottom: 0;
    }

    .modern-table thead th {
        background: #f1f5f9;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 11px 14px;
        border-bottom: 1px solid #e2e8f0;
    }

    .modern-table td {
        padding: 10px 14px;
        vertical-align: middle;
        font-size: 13px;
    }

    /* Financial Summary Card */
    .financial-summary-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
    }

    .financial-line-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        font-size: 13.5px;
    }

    .financial-total-banner {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
        margin: 14px 0;
    }

    /* Sticky Footer Action Bar */
    .event-action-bar {
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    /* Light Gray Buttons (+ Add) */
    .btn-light-gray {
        background: #cbd5e1 !important;
        border: 1px solid #94a3b8 !important;
        color: #0f172a !important;
        font-weight: 600 !important;
        border-radius: 6px !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
    }

    .btn-light-gray:hover {
        background: #94a3b8 !important;
        border-color: #64748b !important;
        color: #0f172a !important;
    }
</style>

<div class="container-fluid px-0">
    <div class="event-creator-card mb-4">
        <!-- Card Header -->
        <div class="event-header-banner d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="event-icon-box">
                    <i class="bi bi-calendar2-plus"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark" style="font-size: 15.5px; letter-spacing: -0.2px;">Register New Event</h5>
                    <small class="text-muted" style="font-size: 12px;">Configure customer, package, add-on services, and payment details.</small>
                </div>
            </div>
            <div>
                <a href="{{ route('events.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1.5 px-3">
                    <i class="bi bi-arrow-left"></i> <span>Back to Events</span>
                </a>
            </div>
        </div>

        <div class="card-body p-4">
            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                        <strong class="text-dark">Please correct the following errors:</strong>
                    </div>
                    <ul class="mb-0 small ps-4">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(isset($quotation) && $quotation)
                <div class="alert alert-info border-0 shadow-sm d-flex flex-wrap align-items-center justify-content-between p-3 mb-4 rounded-3 gap-3" style="background: #eff6ff; border: 1px solid #bfdbfe !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-primary bg-white shadow-sm flex-shrink-0" style="width: 40px; height: 40px;">
                            <i class="bi bi-file-earmark-check-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <strong class="text-dark" style="font-size: 14px;">Pre-filled from Quotation: {{ $quotation->quotation_no }}</strong>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Accepted</span>
                            </div>
                            <span class="text-muted small">Customer, event name, date, venue, package, additional services, charges, and discount have been imported.</span>
                        </div>
                    </div>
                    <a href="{{ route('quotations.show', $quotation) }}" target="_blank" class="btn btn-sm btn-outline-primary fw-semibold px-3">
                        <i class="bi bi-box-arrow-up-right me-1"></i> View Quotation
                    </a>
                </div>
            @endif

            <form method="POST" action="{{ route('events.store') }}" id="eventRegisterForm">
                @csrf
                @if(isset($quotation) && $quotation)
                    <input type="hidden" name="quotation_id" value="{{ $quotation->id }}">
                @endif

                <!-- Section 1: Customer & Event Information -->
                <div class="custom-section-box">
                    <div class="form-section-title">
                        <i class="bi bi-person-vcard-fill"></i>
                        <span>Customer & Event Information</span>
                    </div>

                    <div class="row g-3">
                        <!-- Customer Selection + Quick Add -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="custom-input-label mb-0">
                                    <i class="bi bi-person-circle"></i>
                                    Select Customer <span class="text-danger">*</span>
                                </label>
                                <button type="button" class="btn btn-sm btn-light-gray py-0 px-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#quickCustomerModal" style="font-size: 11.5px; height: 26px;">
                                    <i class="bi bi-person-plus-fill me-1"></i> Quick Add Customer
                                </button>
                            </div>
                            <select name="customer_id" id="customerId" class="form-select" required>
                                <option value="">-- Choose Customer --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id', $quotation?->customer_id) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} ({{ $customer->phone ?: 'No phone' }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Choose existing client or click Quick Add to create a new one</small>
                        </div>

                        <!-- Event Name / Title -->
                        <div class="col-md-6">
                            <label class="custom-input-label">
                                <i class="bi bi-tag-fill"></i>
                                Event Name / Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="event_name" id="eventName" class="form-control" value="{{ old('event_name', $quotation?->event_name ?? ($quotation?->customer ? $quotation->customer->name . ' Event' : '')) }}" placeholder="e.g. Kasun & Nadeeka Wedding Reception" required>
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">A recognized title for schedules, contracts, and invoices</small>
                        </div>

                        <!-- Registration Date -->
                        <div class="col-md-4">
                            <label class="custom-input-label">
                                <i class="bi bi-calendar-event"></i>
                                Registration Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="today_date" class="form-control" value="{{ old('today_date', $today) }}" required>
                        </div>

                        <!-- Event Date -->
                        <div class="col-md-4">
                            <label class="custom-input-label">
                                <i class="bi bi-calendar-check"></i>
                                Event Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="event_date" id="eventDate" class="form-control" value="{{ old('event_date', $quotation?->event_date?->format('Y-m-d')) }}" required>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                <i class="bi bi-bell text-secondary me-1"></i>Auto reminders fire 7 days & 3 days prior.
                            </small>
                        </div>

                        <!-- Venue / Location -->
                        <div class="col-md-4">
                            <label class="custom-input-label">
                                <i class="bi bi-geo-alt-fill"></i>
                                Venue / Location
                            </label>
                            <input type="text" name="venue" class="form-control" value="{{ old('venue', $quotation?->venue) }}" placeholder="e.g. Kingsbury Grand Ballroom, Colombo">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Category & Package Cascade -->
                <div class="custom-section-box">
                    <div class="form-section-title">
                        <i class="bi bi-box-seam-fill"></i>
                        <span>Package Selection & Pricing</span>
                    </div>

                    <div class="row g-3">
                        <!-- Category -->
                        <div class="col-md-6">
                            <label class="custom-input-label">
                                <i class="bi bi-folder2-open"></i>
                                Select Category <span class="text-danger">*</span>
                            </label>
                            <select id="categoryId" class="form-select" onchange="onCategoryChange(this.value)">
                                <option value="">-- Choose Category --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $quotation?->subCategory?->event_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sub-Category / Package -->
                        <div class="col-md-6">
                            <label class="custom-input-label">
                                <i class="bi bi-boxes"></i>
                                Select Sub-Category / Package <span class="text-danger">*</span>
                            </label>
                            <select name="event_sub_category_id" id="subCategoryId" class="form-select" required onchange="onSubCategoryChange(this.value)">
                                @if(isset($initialSubCategories) && $initialSubCategories->isNotEmpty())
                                    <option value="">-- Select Package --</option>
                                    @foreach($initialSubCategories as $sub)
                                        <option value="{{ $sub->id }}" data-sell-price="{{ $sub->sell_price }}" {{ old('event_sub_category_id', $quotation?->event_sub_category_id) == $sub->id ? 'selected' : '' }}>
                                            {{ $sub->name }} (Sell: Rs. {{ number_format($sub->sell_price, 2) }})
                                        </option>
                                    @endforeach
                                @else
                                    <option value="">-- Select Category First --</option>
                                @endif
                            </select>
                        </div>

                        <!-- Package Base Sell Price (Editable) -->
                        <div class="col-md-6">
                            <label class="custom-input-label">
                                <i class="bi bi-cash-stack"></i>
                                Package Base Sell Price (Rs.) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="package_sell_price" id="packageSellPrice" class="form-control fw-bold text-primary" value="{{ old('package_sell_price', number_format($quotation?->package_sell_price ?? $quotation?->subCategory?->sell_price ?? 0, 2, '.', '')) }}" required oninput="calculateEventTotals()">
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Auto-populated from selected package; you can customize the base price here.</small>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="custom-input-label">
                                <i class="bi bi-flag-fill"></i>
                                Event Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" class="form-select" required>
                                <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending (Under Discussion)</option>
                                <option value="confirmed" {{ old('status', 'confirmed') === 'confirmed' ? 'selected' : '' }}>Confirmed (Booking Finalized)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Package Included Services Preview Box -->
                    @php
                        $pkgServices = $quotation?->subCategory?->subCategoryServices ?? collect();
                    @endphp
                    <div id="packageServicesBox" class="mt-3 p-3 bg-white rounded-3 border {{ $pkgServices->count() > 0 ? '' : 'd-none' }} shadow-sm">
                        <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                            <span class="fw-bold text-dark small">
                                <i class="bi bi-check-circle-fill text-success me-1"></i> Included in Selected Package:
                            </span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" id="pkgSvcCountBadge">
                                {{ $pkgServices->count() }} Services
                            </span>
                        </div>
                        <div id="packageServicesList" class="d-flex flex-wrap gap-2 pt-1">
                            @foreach($pkgServices as $ps)
                                <div class="package-service-pill">
                                    <i class="bi bi-check2-circle"></i>
                                    <span>{{ $ps->service?->name ?? 'Service' }} &bull; Qty: <strong>{{ $ps->qty }}</strong></span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Section 3: Additional Services -->
                @php
                    $prefilledServices = [];
                    if (old('additional_services')) {
                        $prefilledServices = old('additional_services');
                    } elseif (isset($quotation) && $quotation && $quotation->services->isNotEmpty()) {
                        $prefilledServices = $quotation->services->map(fn($s) => [
                            'service_id' => $s->service_id,
                            'description' => $s->description,
                            'qty' => $s->qty,
                            'unit_price' => $s->unit_price,
                            'total_price' => $s->total_price,
                        ])->toArray();
                    }
                @endphp
                <div class="custom-section-box">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div class="form-section-title mb-0">
                            <i class="bi bi-stars"></i>
                            <span>Additional Services (Add-ons & Upgrades)</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-light-gray fw-semibold px-3" onclick="addAdditionalServiceRow()">
                            <i class="bi bi-plus-lg me-1"></i> Add Service
                        </button>
                    </div>
                    <p class="text-muted small mb-3">Add custom services, upgrade items, or special requests required for this event.</p>

                    <div class="modern-table-container">
                        <table class="table modern-table align-middle" id="additionalServicesTable">
                            <thead>
                                <tr>
                                    <th>Service Preset / Custom Description</th>
                                    <th style="width: 110px;" class="text-center">Qty</th>
                                    <th style="width: 160px;" class="text-end">Unit Price (Rs.)</th>
                                    <th style="width: 170px;" class="text-end pe-4">Line Total (Rs.)</th>
                                    <th style="width: 50px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="additionalServicesTbody">
                                @foreach($prefilledServices as $idx => $svcRow)
                                    @php
                                        $rowQty = (int) ($svcRow['qty'] ?? 1);
                                        $rowPrice = (float) ($svcRow['unit_price'] ?? 0);
                                        $rowTotal = $rowQty * $rowPrice;
                                    @endphp
                                    <tr id="svc_row_{{ $idx }}">
                                        <td>
                                            <div class="row g-1">
                                                <div class="col-md-5">
                                                    <select class="form-select form-select-sm" onchange="onSelectServicePreset(this, {{ $idx }})">
                                                        <option value="">-- Custom / Select Service --</option>
                                                        @foreach($services as $svc)
                                                            <option value="{{ $svc->id }}" data-price="{{ $svc->cost_price }}" data-name="{{ $svc->name }}" {{ ($svcRow['service_id'] ?? '') == $svc->id ? 'selected' : '' }}>
                                                                {{ $svc->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-7">
                                                    <input type="hidden" name="additional_services[{{ $idx }}][service_id]" id="svc_id_{{ $idx }}" value="{{ $svcRow['service_id'] ?? '' }}">
                                                    <input type="text" name="additional_services[{{ $idx }}][description]" id="svc_desc_{{ $idx }}" class="form-control form-control-sm" placeholder="Service description..." value="{{ $svcRow['description'] ?? '' }}" required>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" min="1" step="1" name="additional_services[{{ $idx }}][qty]" id="svc_qty_{{ $idx }}" class="form-control form-control-sm text-center" value="{{ $rowQty }}" oninput="calculateLineTotal({{ $idx }})">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="additional_services[{{ $idx }}][unit_price]" id="svc_price_{{ $idx }}" class="form-control form-control-sm text-end" value="{{ number_format($rowPrice, 2, '.', '') }}" oninput="calculateLineTotal({{ $idx }})">
                                        </td>
                                        <td class="text-end pe-4 fw-bold text-dark" id="svc_line_total_{{ $idx }}">
                                            {{ number_format($rowTotal, 2) }}
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn-action btn-action-delete mx-auto" onclick="removeAdditionalServiceRow({{ $idx }})" title="Remove service">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold text-secondary">Extra Services Total:</td>
                                    <td class="text-end pe-4 fw-bold text-dark fs-6" id="extraServicesTotalDisplay">0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="noExtraServicesMsg" class="text-center py-4 bg-white border border-top-0 rounded-bottom text-muted small {{ count($prefilledServices) > 0 ? 'd-none' : '' }}">
                        <i class="bi bi-info-circle me-1"></i> No additional services added. Click <strong>"Add Service"</strong> to include extras.
                    </div>
                </div>

                <!-- Section 4: Other Charges (Transport, Surcharges) -->
                @php
                    $prefilledCharges = [];
                    if (old('other_charges')) {
                        $prefilledCharges = old('other_charges');
                    } elseif (isset($quotation) && $quotation && $quotation->otherCharges->isNotEmpty()) {
                        $prefilledCharges = $quotation->otherCharges->map(fn($c) => [
                            'description' => $c->description,
                            'amount' => $c->amount,
                        ])->toArray();
                    }
                @endphp
                <div class="custom-section-box">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div class="form-section-title mb-0">
                            <i class="bi bi-truck"></i>
                            <span>Other Charges (Transport, Hall Rental, Surcharges)</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-light-gray fw-semibold px-3" onclick="addOtherChargeRow()">
                            <i class="bi bi-plus-lg me-1"></i> Add Charge
                        </button>
                    </div>
                    <p class="text-muted small mb-3">Add transport charges, hall rental, cleaning fee, or custom surcharges.</p>

                    <div class="modern-table-container">
                        <table class="table modern-table align-middle" id="otherChargesTable">
                            <thead>
                                <tr>
                                    <th>Charge Description</th>
                                    <th style="width: 200px;" class="text-end pe-4">Amount (Rs.)</th>
                                    <th style="width: 50px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="otherChargesTbody">
                                @foreach($prefilledCharges as $cIdx => $chg)
                                    @php
                                        $chgAmount = (float) ($chg['amount'] ?? 0);
                                    @endphp
                                    <tr id="charge_row_{{ $cIdx }}">
                                        <td>
                                            <input type="text" name="other_charges[{{ $cIdx }}][description]" class="form-control form-control-sm" placeholder="e.g. Transport & Logistics, Hall Cleaning Surcharge" value="{{ $chg['description'] ?? '' }}" required>
                                        </td>
                                        <td class="pe-4">
                                            <input type="number" step="0.01" min="0" name="other_charges[{{ $cIdx }}][amount]" class="form-control form-control-sm text-end charge-amount-input" value="{{ number_format($chgAmount, 2, '.', '') }}" oninput="calculateEventTotals()">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn-action btn-action-delete mx-auto" onclick="removeOtherChargeRow({{ $cIdx }})" title="Remove charge">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td class="text-end fw-bold text-secondary">Other Charges Total:</td>
                                    <td class="text-end pe-4 fw-bold text-dark fs-6" id="otherChargesTotalDisplay">0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="noOtherChargesMsg" class="text-center py-4 bg-white border border-top-0 rounded-bottom text-muted small {{ count($prefilledCharges) > 0 ? 'd-none' : '' }}">
                        <i class="bi bi-info-circle me-1"></i> No other charges added. Click <strong>"Add Charge"</strong> for logistics or surcharges.
                    </div>
                </div>

                <!-- Section 5: Financial Summary, Discount & Advance Payment -->
                <div class="custom-section-box" style="border-color: #cbd5e1; background: #ffffff;">
                    <div class="form-section-title">
                        <i class="bi bi-calculator-fill"></i>
                        <span>Payment & Financial Summary</span>
                    </div>

                    <div class="row g-4 align-items-start">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="custom-input-label">
                                    <i class="bi bi-card-text"></i>
                                    Remarks / Special Arrangements Notes
                                </label>
                                <textarea name="remarks" rows="4" class="form-control" placeholder="Specify special arrangements, event schedule timings, dietary constraints or delivery instructions...">{{ old('remarks', $quotation?->remarks) }}</textarea>
                            </div>

                            <div class="p-3 bg-light rounded-3 border">
                                <div class="d-flex align-items-center gap-2 text-primary fw-bold small mb-1">
                                    <i class="bi bi-shield-check fs-5"></i> Automatic Order Creation Feature:
                                </div>
                                <p class="mb-0 text-muted small" style="line-height: 1.5;">
                                    When an <strong>Advance Payment</strong> is entered, an official Customer Order and a Cash Receipt transaction are <strong>automatically created & linked</strong> immediately upon saving!
                                </p>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="financial-summary-card">
                                <div class="financial-line-item">
                                    <span class="text-muted">Package Base Price:</span>
                                    <span class="fw-semibold text-dark" id="sumPackage">Rs. 0.00</span>
                                </div>
                                <div class="financial-line-item">
                                    <span class="text-muted">Extra Services Total:</span>
                                    <span class="fw-semibold text-dark" id="sumServices">Rs. 0.00</span>
                                </div>
                                <div class="financial-line-item">
                                    <span class="text-muted">Other Charges Total:</span>
                                    <span class="fw-semibold text-dark" id="sumCharges">Rs. 0.00</span>
                                </div>
                                <div class="financial-line-item pt-2 border-top">
                                    <span class="text-muted">Special Discount:</span>
                                    <div class="input-group input-group-sm" style="width: 160px;">
                                        <input type="number" step="0.01" min="0" name="discount" id="discountInput" 
                                               class="form-control text-end text-danger fw-bold" 
                                               value="{{ old('discount', number_format($quotation?->discount ?? 0, 2, '.', '')) }}" 
                                               oninput="calculateEventTotals()">
                                    </div>
                                </div>

                                <div class="financial-total-banner d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small fw-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">Final Total Amount</div>
                                        <small class="text-muted">Net payable value</small>
                                    </div>
                                    <div class="fs-4 fw-bold text-primary" id="finalTotalDisplay">
                                        Rs. 0.00
                                    </div>
                                </div>

                                <div class="financial-line-item mb-2">
                                    <div>
                                        <span class="fw-bold text-dark">Advance Payment:</span>
                                        <div class="text-muted" style="font-size: 11px;">Deposited at registration</div>
                                    </div>
                                    <div class="input-group input-group-sm" style="width: 160px;">
                                        <input type="number" step="0.01" min="0" name="advance_payment" id="advancePaymentInput" 
                                               class="form-control text-end text-success fw-bold" 
                                               value="{{ old('advance_payment', '0.00') }}" 
                                               oninput="calculateEventTotals()">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <span class="fw-semibold text-secondary small">Estimated Remaining Balance:</span>
                                    <span class="fw-bold text-dark" id="estimatedBalanceDisplay">Rs. 0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Footer Bar -->
                <div class="event-action-bar rounded-3">
                    <div class="d-flex align-items-center gap-2">
                        <!-- <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="bi bi-shield-lock text-success me-1"></i> Data validated upon registration
                        </span> -->
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary px-4 fw-semibold">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm" id="btnSubmitEvent">
                            <i class="bi bi-calendar2-check-fill me-1"></i> Register Event Now
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Quick Add Customer -->
<div class="modal fade" id="quickCustomerModal" tabindex="-1" aria-labelledby="quickCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-dark text-white p-3 px-4">
                <h5 class="modal-title fw-bold fs-6 text-white" id="quickCustomerModalLabel" >
                    <i class="bi bi-person-plus-fill text-warning me-2"></i>Quick Add New Customer
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="quickCustomerAlert" class="d-none alert alert-danger py-2 small"></div>
                <form id="quickCustomerForm">
                    <div class="mb-3">
                        <label class="custom-input-label">
                            <i class="bi bi-person"></i> Customer Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="quickName" class="form-control" placeholder="Test Customer Name" required>
                    </div>
                    <div class="mb-3">
                        <label class="custom-input-label">
                            <i class="bi bi-telephone"></i> Phone Number
                        </label>
                        <input type="text" id="quickPhone" class="form-control" placeholder="e.g. +94 77 123 4567">
                    </div>
                    <div class="mb-3">
                        <label class="custom-input-label">
                            <i class="bi bi-envelope"></i> Email Address
                        </label>
                        <input type="email" id="quickEmail" class="form-control" placeholder="e.g. ruwan@example.com">
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light p-3 px-4">
                <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-3 fw-semibold" id="btnSaveQuickCustomer" onclick="saveQuickCustomer()">
                    <i class="bi bi-check-lg me-1"></i> Save & Select
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const availableServices = @json($services);
    let extraServiceIndex = {{ count($prefilledServices ?? []) }};
    let otherChargeIndex = {{ count($prefilledCharges ?? []) }};

    // Quick Add Customer via AJAX
    function saveQuickCustomer() {
        const name = document.getElementById('quickName').value.trim();
        const phone = document.getElementById('quickPhone').value.trim();
        const email = document.getElementById('quickEmail').value.trim();
        const alertBox = document.getElementById('quickCustomerAlert');

        if (!name) {
            alertBox.classList.remove('d-none');
            alertBox.innerText = 'Customer name is required.';
            return;
        }

        const btn = document.getElementById('btnSaveQuickCustomer');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        fetch('{{ route("customers.quick-store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name, phone, email })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save & Select';

            if (data.success && data.customer) {
                const customerSelect = document.getElementById('customerId');
                const optText = `${data.customer.name} (${data.customer.phone || 'No phone'})`;
                
                if (typeof jQuery !== 'undefined') {
                    const $select = jQuery(customerSelect);
                    $select.append(new Option(optText, data.customer.id, true, true));
                    $select.val(data.customer.id).trigger('change');
                } else {
                    const opt = new Option(optText, data.customer.id, true, true);
                    customerSelect.add(opt);
                    customerSelect.value = data.customer.id;
                    customerSelect.dispatchEvent(new Event('change'));
                }

                // Auto set event name if empty
                const eventNameInput = document.getElementById('eventName');
                if (eventNameInput && !eventNameInput.value) {
                    eventNameInput.value = data.customer.name + ' Event';
                }

                const modalEl = document.getElementById('quickCustomerModal');
                if (window.bootstrap && bootstrap.Modal) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.hide();
                } else if (typeof jQuery !== 'undefined') {
                    jQuery(modalEl).modal('hide');
                }
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');
                document.body.style.removeProperty('overflow');

                document.getElementById('quickCustomerForm').reset();
                alertBox.classList.add('d-none');
            } else {
                alertBox.classList.remove('d-none');
                alertBox.innerText = data.message || 'Failed to save customer.';
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save & Select';
            alertBox.classList.remove('d-none');
            alertBox.innerText = 'An error occurred while saving.';
        });
    }

    // Category Change -> Cascade Sub-Categories (Packages)
    function onCategoryChange(categoryId) {
        const subSelect = document.getElementById('subCategoryId');
        subSelect.innerHTML = '<option value="">-- Loading Packages... --</option>';

        if (!categoryId) {
            subSelect.innerHTML = '<option value="">-- Select Category First --</option>';
            resetPackageDetails();
            return;
        }

        fetch(`/api/sub-categories/${categoryId}`)
            .then(res => res.json())
            .then(data => {
                subSelect.innerHTML = '<option value="">-- Select Package --</option>';
                data.forEach(sub => {
                    const opt = document.createElement('option');
                    opt.value = sub.id;
                    opt.setAttribute('data-sell-price', sub.sell_price);
                    opt.text = `${sub.name} (Sell: Rs. ${parseFloat(sub.sell_price).toLocaleString()})`;
                    subSelect.appendChild(opt);
                });
                resetPackageDetails();
            })
            .catch(() => {
                subSelect.innerHTML = '<option value="">Error loading packages</option>';
            });
    }

    // Sub-Category Change -> Load Package Services & Sell Price
    function onSubCategoryChange(subCategoryId) {
        if (!subCategoryId) {
            resetPackageDetails();
            return;
        }

        const subSelect = document.getElementById('subCategoryId');
        const selectedOpt = subSelect ? subSelect.options[subSelect.selectedIndex] : null;
        if (selectedOpt && selectedOpt.getAttribute('data-sell-price')) {
            const immediatePrice = parseFloat(selectedOpt.getAttribute('data-sell-price')) || 0;
            document.getElementById('packageSellPrice').value = immediatePrice.toFixed(2);
            calculateEventTotals();
        }

        fetch(`/api/sub-category/${subCategoryId}`)
            .then(res => {
                if (!res.ok) throw new Error('Network error');
                return res.json();
            })
            .then(data => {
                if (data.sell_price !== undefined && data.sell_price !== null) {
                    document.getElementById('packageSellPrice').value = parseFloat(data.sell_price).toFixed(2);
                }

                const pkgBox = document.getElementById('packageServicesBox');
                const pkgList = document.getElementById('packageServicesList');
                const badge = document.getElementById('pkgSvcCountBadge');

                pkgList.innerHTML = '';
                if (data.services && data.services.length > 0) {
                    pkgBox.classList.remove('d-none');
                    badge.innerText = `${data.services.length} Services`;

                    data.services.forEach(svc => {
                        const pill = document.createElement('div');
                        pill.className = 'package-service-pill';
                        pill.innerHTML = `<i class="bi bi-check2-circle"></i> <span>${svc.service_name} &bull; Qty: <strong>${svc.qty}</strong></span>`;
                        pkgList.appendChild(pill);
                    });
                } else {
                    pkgBox.classList.add('d-none');
                }

                calculateEventTotals();
            })
            .catch(err => {
                console.error('Error fetching package details:', err);
                calculateEventTotals();
            });
    }

    function resetPackageDetails() {
        document.getElementById('packageSellPrice').value = '0.00';
        document.getElementById('packageServicesBox').classList.add('d-none');
        document.getElementById('packageServicesList').innerHTML = '';
        calculateEventTotals();
    }

    // Add Additional Service Row
    function addAdditionalServiceRow() {
        const tbody = document.getElementById('additionalServicesTbody');
        const emptyMsg = document.getElementById('noExtraServicesMsg');
        if (emptyMsg) emptyMsg.classList.add('d-none');

        const idx = extraServiceIndex++;

        let serviceOptions = '<option value="">-- Custom / Select Service --</option>';
        availableServices.forEach(svc => {
            serviceOptions += `<option value="${svc.id}" data-price="${svc.cost_price}" data-name="${svc.name}">${svc.name}</option>`;
        });

        const tr = document.createElement('tr');
        tr.id = `svc_row_${idx}`;
        tr.innerHTML = `
            <td>
                <div class="row g-1">
                    <div class="col-md-5">
                        <select class="form-select form-select-sm" onchange="onSelectServicePreset(this, ${idx})">
                            ${serviceOptions}
                        </select>
                    </div>
                    <div class="col-md-7">
                        <input type="hidden" name="additional_services[${idx}][service_id]" id="svc_id_${idx}">
                        <input type="text" name="additional_services[${idx}][description]" id="svc_desc_${idx}" class="form-control form-control-sm" placeholder="Service description..." required>
                    </div>
                </div>
            </td>
            <td>
                <input type="number" min="1" step="1" name="additional_services[${idx}][qty]" id="svc_qty_${idx}" class="form-control form-control-sm text-center" value="1" oninput="calculateLineTotal(${idx})">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="additional_services[${idx}][unit_price]" id="svc_price_${idx}" class="form-control form-control-sm text-end" value="0.00" oninput="calculateLineTotal(${idx})">
            </td>
            <td class="text-end pe-4 fw-bold text-dark" id="svc_line_total_${idx}">
                0.00
            </td>
            <td class="text-center">
                <button type="button" class="btn-action btn-action-delete mx-auto" onclick="removeAdditionalServiceRow(${idx})" title="Remove service">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    }

    function onSelectServicePreset(selectEl, idx) {
        const selectedOpt = selectEl.options[selectEl.selectedIndex];
        const svcId = selectedOpt.value;
        const svcName = selectedOpt.getAttribute('data-name');
        const svcPrice = selectedOpt.getAttribute('data-price');

        if (svcId) {
            document.getElementById(`svc_id_${idx}`).value = svcId;
            document.getElementById(`svc_desc_${idx}`).value = svcName;
            document.getElementById(`svc_price_${idx}`).value = svcPrice || '0.00';
        }
        calculateLineTotal(idx);
    }

    function calculateLineTotal(idx) {
        const qty = parseInt(document.getElementById(`svc_qty_${idx}`)?.value) || 1;
        const price = parseFloat(document.getElementById(`svc_price_${idx}`)?.value) || 0;
        const total = qty * price;
        const cell = document.getElementById(`svc_line_total_${idx}`);
        if (cell) {
            cell.innerText = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        calculateEventTotals();
    }

    function removeAdditionalServiceRow(idx) {
        document.getElementById(`svc_row_${idx}`)?.remove();
        const tbody = document.getElementById('additionalServicesTbody');
        const emptyMsg = document.getElementById('noExtraServicesMsg');
        if (tbody.children.length === 0 && emptyMsg) {
            emptyMsg.classList.remove('d-none');
        }
        calculateEventTotals();
    }

    // Add Other Charge Row (Transport, etc.)
    function addOtherChargeRow() {
        const tbody = document.getElementById('otherChargesTbody');
        const emptyMsg = document.getElementById('noOtherChargesMsg');
        if (emptyMsg) emptyMsg.classList.add('d-none');

        const idx = otherChargeIndex++;

        const tr = document.createElement('tr');
        tr.id = `charge_row_${idx}`;
        tr.innerHTML = `
            <td>
                <input type="text" name="other_charges[${idx}][description]" class="form-control form-control-sm" placeholder="e.g. Transport & Logistics, Hall Cleaning Surcharge" required>
            </td>
            <td class="pe-4">
                <input type="number" step="0.01" min="0" name="other_charges[${idx}][amount]" class="form-control form-control-sm text-end charge-amount-input" value="0.00" oninput="calculateEventTotals()">
            </td>
            <td class="text-center">
                <button type="button" class="btn-action btn-action-delete mx-auto" onclick="removeOtherChargeRow(${idx})" title="Remove charge">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    }

    function removeOtherChargeRow(idx) {
        document.getElementById(`charge_row_${idx}`)?.remove();
        const tbody = document.getElementById('otherChargesTbody');
        const emptyMsg = document.getElementById('noOtherChargesMsg');
        if (tbody.children.length === 0 && emptyMsg) {
            emptyMsg.classList.remove('d-none');
        }
        calculateEventTotals();
    }

    // Live Calculation of All Totals
    function calculateEventTotals() {
        const packageSellPrice = parseFloat(document.getElementById('packageSellPrice').value) || 0;

        // Sum Extra Services
        let servicesTotal = 0;
        document.querySelectorAll('#additionalServicesTbody tr').forEach(row => {
            const qty = parseInt(row.querySelector('[id^="svc_qty_"]')?.value) || 0;
            const price = parseFloat(row.querySelector('[id^="svc_price_"]')?.value) || 0;
            servicesTotal += qty * price;
        });

        // Sum Other Charges
        let chargesTotal = 0;
        document.querySelectorAll('.charge-amount-input').forEach(input => {
            chargesTotal += parseFloat(input.value) || 0;
        });

        const discount = parseFloat(document.getElementById('discountInput').value) || 0;
        const advance = parseFloat(document.getElementById('advancePaymentInput').value) || 0;

        const subtotal = packageSellPrice + servicesTotal + chargesTotal;
        const finalTotal = Math.max(0, subtotal - discount);
        const estimatedBalance = Math.max(0, finalTotal - advance);

        // Update displays
        document.getElementById('extraServicesTotalDisplay').innerText = servicesTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('otherChargesTotalDisplay').innerText = chargesTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        document.getElementById('sumPackage').innerText = 'Rs. ' + packageSellPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('sumServices').innerText = 'Rs. ' + servicesTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('sumCharges').innerText = 'Rs. ' + chargesTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        document.getElementById('finalTotalDisplay').innerText = 'Rs. ' + finalTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('estimatedBalanceDisplay').innerText = 'Rs. ' + estimatedBalance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Trigger on load
    document.addEventListener('DOMContentLoaded', () => {
        calculateEventTotals();
    });
</script>
@endsection