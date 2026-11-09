@extends('layouts.app')
@section('title', 'Edit Event - ' . $event->event_no)
@section('page-title', 'Edit Event')

@section('content')
<style>
    /* Custom Styling for Event Edit */
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
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark" style="font-size: 15.5px; letter-spacing: -0.2px;">
                        Edit Event: {{ $event->event_name }}
                    </h5>
                    <small class="text-muted" style="font-size: 12px;">
                        Event No: <strong class="text-dark">{{ $event->event_no }}</strong> &bull; Registration Date: {{ $event->today_date?->format('Y-m-d') }}
                    </small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('events.show', $event) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1.5 px-3">
                    <i class="bi bi-eye"></i> <span>View Event</span>
                </a>
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

            <form method="POST" action="{{ route('events.update', $event) }}" id="eventEditForm">
                @csrf
                @method('PUT')

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
                                    <option value="{{ $customer->id }}" {{ old('customer_id', $event->customer_id) == $customer->id ? 'selected' : '' }}>
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
                            <input type="text" name="event_name" id="eventName" class="form-control" value="{{ old('event_name', $event->event_name) }}" placeholder="e.g. Kasun & Nadeeka Wedding Reception" required>
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">A recognized title for schedules, contracts, and invoices</small>
                        </div>

                        <!-- Registration Date -->
                        <div class="col-md-3">
                            <label class="custom-input-label">
                                <i class="bi bi-calendar-event"></i>
                                Registration Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="today_date" class="form-control" value="{{ old('today_date', $event->today_date?->format('Y-m-d')) }}" required>
                        </div>

                        <!-- Event Date -->
                        <div class="col-md-3">
                            <label class="custom-input-label">
                                <i class="bi bi-calendar-check"></i>
                                Event Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="event_date" id="eventDate" class="form-control" value="{{ old('event_date', $event->event_date?->format('Y-m-d')) }}" required>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                <i class="bi bi-bell text-secondary me-1"></i>Auto reminders fire 7 days & 3 days prior.
                            </small>
                        </div>

                        <!-- Venue / Location -->
                        <div class="col-md-3">
                            <label class="custom-input-label">
                                <i class="bi bi-geo-alt-fill"></i>
                                Venue / Location
                            </label>
                            <input type="text" name="venue" class="form-control" value="{{ old('venue', $event->venue) }}" placeholder="e.g. Kingsbury Grand Ballroom, Colombo">
                        </div>

                        <!-- Event Status (Moved to Customer & Event Information) -->
                        <div class="col-md-3">
                            <label class="custom-input-label">
                                <i class="bi bi-flag-fill"></i>
                                Event Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" class="form-select" required>
                                <option value="pending" {{ old('status', $event->status) === 'pending' ? 'selected' : '' }}>Pending (Under Discussion)</option>
                                <option value="confirmed" {{ old('status', $event->status) === 'confirmed' ? 'selected' : '' }}>Confirmed (Booking Finalized)</option>
                                <option value="completed" {{ old('status', $event->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $event->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Event Packages (Multi-Package Support) -->
                @php
                    $prefilledPackages = [];
                    if (old('packages')) {
                        $prefilledPackages = old('packages');
                    } elseif ($event->packages && $event->packages->isNotEmpty()) {
                        $prefilledPackages = $event->packages->map(fn($p) => [
                            'id' => $p->id,
                            'event_sub_category_id' => $p->event_sub_category_id,
                            'category_name' => $p->subCategory?->category?->name ?? 'General',
                            'package_name' => $p->package_name,
                            'description' => $p->description ?? $p->subCategory?->description ?? '',
                            'cost_price' => (float)$p->cost_price,
                            'sell_price' => (float)$p->sell_price,
                            'qty' => $p->qty ?? 1,
                            'total_price' => (float)$p->total_price,
                        ])->toArray();
                    } elseif ($event->subCategory || $event->package_sell_price > 0) {
                        $prefilledPackages = [[
                            'id' => null,
                            'event_sub_category_id' => $event->event_sub_category_id,
                            'category_name' => $event->subCategory?->category?->name ?? 'General',
                            'package_name' => $event->package_name ?? $event->subCategory?->name ?? 'Event Package',
                            'description' => $event->subCategory?->description ?? '',
                            'cost_price' => (float)($event->package_cost_price ?? $event->subCategory?->cost_price ?? 0),
                            'sell_price' => (float)($event->package_sell_price ?? $event->subCategory?->sell_price ?? 0),
                            'qty' => 1,
                            'total_price' => (float)($event->package_sell_price ?? $event->subCategory?->sell_price ?? 0),
                        ]];
                    }
                @endphp
                <div class="custom-section-box">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div class="form-section-title mb-0">
                            <i class="bi bi-box-seam-fill"></i>
                            <span>Event Packages Selection & Pricing</span>
                        </div>
                        <span class="badge bg-secondary-subtle text-secondary border px-2.5 py-1">
                            <i class="bi bi-layers-fill me-1"></i> Multi-Package Supported
                        </span>
                    </div>

                    <!-- Package Selector Bar -->
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="custom-input-label mb-1">
                                    <i class="bi bi-folder2-open"></i> Category
                                </label>
                                <select id="pkgCategoryId" class="form-select form-select-sm" onchange="onPkgCategoryChange(this.value)">
                                    <option value="">-- Choose Category --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="custom-input-label mb-1">
                                    <i class="bi bi-boxes"></i> Package / Sub-Category
                                </label>
                                <select id="pkgSubCategoryId" class="form-select form-select-sm" onchange="onPkgSubCategoryChange(this.value)">
                                    <option value="">-- Select Category First --</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="custom-input-label mb-1">
                                    <i class="bi bi-cash-stack"></i> Price (Rs.)
                                </label>
                                <input type="number" step="0.01" min="0" id="pkgPriceInput" class="form-control form-control-sm text-end fw-semibold" value="0.00">
                            </div>
                            <div class="col-md-1">
                                <label class="custom-input-label mb-1">
                                    Qty
                                </label>
                                <input type="number" min="1" step="1" id="pkgQtyInput" class="form-control form-control-sm text-center fw-semibold" value="1">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-dark w-100 fw-bold d-inline-flex align-items-center justify-content-center gap-1" style="height: 33px;" onclick="addSelectedPackage()">
                                    <i class="bi bi-plus-circle"></i> Add Package
                                </button>
                            </div>
                        </div>
                        <!-- Live preview of description & bundled services for currently selected package in picker -->
                        <div id="pickerPkgServicesPreview" class="d-none mt-2 pt-2 border-top">
                            <div id="pickerPkgServicesRow" class="mb-1 d-none">
                                <small class="text-muted fw-semibold me-2"><i class="bi bi-box-seam text-primary me-1"></i>Included Services:</small>
                                <span id="pickerPkgServicesPills" class="d-inline-flex flex-wrap gap-1"></span>
                            </div>
                            <div id="pickerPkgDescRow" class="d-none">
                                <small class="text-muted fw-semibold me-2"><i class="bi bi-card-text text-primary me-1"></i>Package Description:</small>
                                <div id="pickerPkgDescText" class="text-secondary small mt-1" style="white-space: pre-line;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden fields for compatibility -->
                    <input type="hidden" name="package_sell_price" id="packageSellPrice" value="{{ old('package_sell_price', $event->package_sell_price) }}">
                    <input type="hidden" name="event_sub_category_id" id="eventSubCategoryId" value="{{ old('event_sub_category_id', $event->event_sub_category_id) }}">

                    <!-- Packages Table -->
                    <div class="modern-table-container">
                        <table class="table modern-table align-middle" id="packagesTable">
                            <thead>
                                <tr>
                                    <th style="width: 40px;" class="text-center">#</th>
                                    <th>Package Details & Description</th>
                                    <th style="width: 150px;" class="text-end">Unit Price (Rs.)</th>
                                    <th style="width: 90px;" class="text-center">Qty</th>
                                    <th style="width: 160px;" class="text-end pe-4">Line Total (Rs.)</th>
                                    <th style="width: 50px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="packagesTbody">
                                <!-- Dynamic Rows -->
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold text-secondary">Total Packages Price:</td>
                                    <td class="text-end pe-4 fw-bold text-primary fs-6" id="packagesTotalDisplay">0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="noPackagesMsg" class="text-center py-4 bg-white border border-top-0 rounded-bottom text-muted small d-none">
                        <i class="bi bi-info-circle me-1"></i> No packages added yet. Select a category and package above and click <strong>"Add Package"</strong>.
                    </div>
                </div>

                <!-- Section 3: Additional Services -->
                @php
                    $existingServices = old('additional_services', $event->additionalServices->map(fn($s) => [
                        'service_id' => $s->service_id,
                        'description' => $s->description,
                        'qty' => $s->qty,
                        'unit_price' => $s->unit_price,
                        'total_price' => $s->total_price,
                    ])->toArray());
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
                                @foreach($existingServices as $idx => $svcRow)
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
                    <div id="noExtraServicesMsg" class="text-center py-4 bg-white border border-top-0 rounded-bottom text-muted small {{ count($existingServices) > 0 ? 'd-none' : '' }}">
                        <i class="bi bi-info-circle me-1"></i> No additional services added. Click <strong>"+ Add Service"</strong> to include extras.
                    </div>
                </div>

                <!-- Section 4: Other Charges (Transport, Surcharges) -->
                @php
                    $existingCharges = old('other_charges', $event->otherCharges->map(fn($c) => [
                        'description' => $c->description,
                        'amount' => $c->amount,
                    ])->toArray());
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
                                @foreach($existingCharges as $cIdx => $chargeRow)
                                    @php
                                        $cAmount = (float) ($chargeRow['amount'] ?? 0);
                                    @endphp
                                    <tr id="charge_row_{{ $cIdx }}">
                                        <td>
                                            <input type="text" name="other_charges[{{ $cIdx }}][description]" class="form-control form-control-sm" placeholder="e.g. Transport & Logistics, Hall Cleaning Surcharge" value="{{ $chargeRow['description'] ?? '' }}" required>
                                        </td>
                                        <td class="pe-4">
                                            <input type="number" step="0.01" min="0" name="other_charges[{{ $cIdx }}][amount]" class="form-control form-control-sm text-end charge-amount-input" value="{{ number_format($cAmount, 2, '.', '') }}" oninput="calculateEventTotals()">
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
                    <div id="noOtherChargesMsg" class="text-center py-4 bg-white border border-top-0 rounded-bottom text-muted small {{ count($existingCharges) > 0 ? 'd-none' : '' }}">
                        <i class="bi bi-info-circle me-1"></i> No other charges added. Click <strong>"+ Add Charge"</strong> for logistics or surcharges.
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
                                <textarea name="remarks" rows="4" class="form-control" placeholder="Specify special arrangements, event schedule timings, dietary constraints or delivery instructions...">{{ old('remarks', $event->remarks) }}</textarea>
                            </div>

                            <div class="p-3 bg-light rounded-3 border">
                                <div class="d-flex align-items-center gap-2 text-primary fw-bold small mb-1">
                                    <i class="bi bi-info-circle-fill fs-5"></i> Financial Auto-Recalculation:
                                </div>
                                <p class="mb-0 text-muted small" style="line-height: 1.5;">
                                    Updating package price, extra services, other charges, or discount will automatically recalculate the event's net total and outstanding balance.
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
                                               value="{{ old('discount', $event->discount ?? '0.00') }}" 
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

                                <div class="financial-line-item mb-2" style="display: none;">
                                    <div>
                                        <span class="fw-bold text-dark">Advance Payment:</span>
                                        <div class="text-muted" style="font-size: 11px;">Deposited at registration</div>
                                    </div>
                                    <div class="input-group input-group-sm" style="width: 160px;">
                                        <input type="number" step="0.01" min="0" name="advance_payment" id="advancePaymentInput" 
                                               class="form-control text-end text-success fw-bold" 
                                               value="{{ old('advance_payment', $event->advance_payment ?? '0.00') }}" 
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
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="bi bi-clock-history text-secondary me-1"></i> Last updated: {{ $event->updated_at?->diffForHumans() }}
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('events.show', $event) }}" class="btn btn-outline-secondary px-4 fw-semibold">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm" id="btnSubmitEvent">
                            <i class="bi bi-check2-circle me-1"></i> Save Changes
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
                <h5 class="modal-title fw-bold fs-6 text-white" id="quickCustomerModalLabel">
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
                        <input type="text" id="quickName" class="form-control" placeholder="Customer Name" required>
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
                        <input type="email" id="quickEmail" class="form-control" placeholder="e.g. customer@example.com">
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
    const prefilledPackages = @json($prefilledPackages ?? []);
    let packageIndex = 0;
    let extraServiceIndex = {{ count($existingServices) }};
    let otherChargeIndex = {{ count($existingCharges) }};
    let currentSelectedPackageDetails = null;

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    // Category Change in Package Picker
    function onPkgCategoryChange(categoryId) {
        const subSelect = document.getElementById('pkgSubCategoryId');
        subSelect.innerHTML = '<option value="">-- Loading Packages... --</option>';
        const previewBox = document.getElementById('pickerPkgServicesPreview');
        if (previewBox) previewBox.classList.add('d-none');
        const pillsSpan = document.getElementById('pickerPkgServicesPills');
        if (pillsSpan) pillsSpan.innerHTML = '';
        const descText = document.getElementById('pickerPkgDescText');
        if (descText) descText.innerText = '';
        const svcRow = document.getElementById('pickerPkgServicesRow');
        if (svcRow) svcRow.classList.add('d-none');
        const descRow = document.getElementById('pickerPkgDescRow');
        if (descRow) descRow.classList.add('d-none');
        currentSelectedPackageDetails = null;

        if (!categoryId) {
            subSelect.innerHTML = '<option value="">-- Select Category First --</option>';
            subSelect.value = '';
            subSelect.selectedIndex = 0;
            if (typeof jQuery !== 'undefined' && jQuery(subSelect).hasClass('select2-hidden-accessible')) {
                jQuery(subSelect).trigger('change.select2');
            }
            document.getElementById('pkgPriceInput').value = '0.00';
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
                    opt.setAttribute('data-name', sub.name);
                    opt.text = `${sub.name} (Sell: Rs. ${parseFloat(sub.sell_price).toLocaleString()})`;
                    subSelect.appendChild(opt);
                });
                subSelect.value = '';
                subSelect.selectedIndex = 0;
                if (typeof jQuery !== 'undefined' && jQuery(subSelect).hasClass('select2-hidden-accessible')) {
                    jQuery(subSelect).trigger('change.select2');
                }
            })
            .catch(() => {
                subSelect.innerHTML = '<option value="">Error loading packages</option>';
                if (typeof jQuery !== 'undefined' && jQuery(subSelect).hasClass('select2-hidden-accessible')) {
                    jQuery(subSelect).trigger('change.select2');
                }
            });
    }

    // Sub-Category Change in Package Picker
    function onPkgSubCategoryChange(subCategoryId) {
        const previewBox = document.getElementById('pickerPkgServicesPreview');
        const pillsSpan = document.getElementById('pickerPkgServicesPills');
        const descText = document.getElementById('pickerPkgDescText');
        const svcRow = document.getElementById('pickerPkgServicesRow');
        const descRow = document.getElementById('pickerPkgDescRow');

        if (!subCategoryId) {
            document.getElementById('pkgPriceInput').value = '0.00';
            if (previewBox) previewBox.classList.add('d-none');
            if (pillsSpan) pillsSpan.innerHTML = '';
            if (descText) descText.innerText = '';
            if (svcRow) svcRow.classList.add('d-none');
            if (descRow) descRow.classList.add('d-none');
            currentSelectedPackageDetails = null;
            return;
        }

        const subSelect = document.getElementById('pkgSubCategoryId');
        const selectedOpt = subSelect ? subSelect.options[subSelect.selectedIndex] : null;
        if (selectedOpt && selectedOpt.getAttribute('data-sell-price')) {
            document.getElementById('pkgPriceInput').value = parseFloat(selectedOpt.getAttribute('data-sell-price')).toFixed(2);
        }

        fetch(`/api/sub-category/${subCategoryId}`)
            .then(res => res.json())
            .then(data => {
                currentSelectedPackageDetails = data;
                if (data.sell_price !== undefined && data.sell_price !== null) {
                    document.getElementById('pkgPriceInput').value = parseFloat(data.sell_price).toFixed(2);
                }

                let hasServices = false;
                let hasDesc = false;

                if (pillsSpan && Array.isArray(data.services) && data.services.length > 0) {
                    hasServices = true;
                    pillsSpan.innerHTML = data.services.map(svc => 
                        `<span class="package-service-pill"><i class="bi bi-check2-circle text-success"></i> ${escapeHtml(svc.service_name || '')} &bull; Qty: <strong>${svc.qty || 1}</strong></span>`
                    ).join(' ');
                    if (svcRow) svcRow.classList.remove('d-none');
                } else {
                    if (pillsSpan) pillsSpan.innerHTML = '';
                    if (svcRow) svcRow.classList.add('d-none');
                }

                if (descText && data.description && data.description.trim() !== '') {
                    hasDesc = true;
                    descText.innerText = data.description;
                    if (descRow) descRow.classList.remove('d-none');
                } else {
                    if (descText) descText.innerText = '';
                    if (descRow) descRow.classList.add('d-none');
                }

                if (previewBox) {
                    if (hasServices || hasDesc) {
                        previewBox.classList.remove('d-none');
                    } else {
                        previewBox.classList.add('d-none');
                    }
                }
            })
            .catch(() => {
                currentSelectedPackageDetails = null;
            });
    }

    // Add Package from Picker into Table
    function addSelectedPackage() {
        const catSelect = document.getElementById('pkgCategoryId');
        const subSelect = document.getElementById('pkgSubCategoryId');
        const priceInput = document.getElementById('pkgPriceInput');
        const qtyInput = document.getElementById('pkgQtyInput');

        const subCategoryId = subSelect.value;
        if (!subCategoryId) {
            alert('Please select a package first.');
            return;
        }

        const selectedOpt = subSelect.options[subSelect.selectedIndex];
        const packageName = selectedOpt.getAttribute('data-name') || selectedOpt.text.split('(')[0].trim();
        const categoryName = catSelect.options[catSelect.selectedIndex]?.text || '';
        const sellPrice = parseFloat(priceInput.value) || 0;
        const qty = parseInt(qtyInput.value) || 1;
        const costPrice = currentSelectedPackageDetails?.cost_price ? parseFloat(currentSelectedPackageDetails.cost_price) : 0;
        const description = currentSelectedPackageDetails?.description || '';

        addPackageRow({
            event_sub_category_id: subCategoryId,
            category_name: categoryName,
            package_name: packageName,
            description: description,
            cost_price: costPrice,
            sell_price: sellPrice,
            qty: qty,
            total_price: sellPrice * qty
        });

        // Reset package picker form completely
        catSelect.value = '';
        catSelect.selectedIndex = 0;
        if (typeof jQuery !== 'undefined' && jQuery(catSelect).hasClass('select2-hidden-accessible')) {
            jQuery(catSelect).val('').trigger('change.select2');
        }

        subSelect.innerHTML = '<option value="">-- Select Category First --</option>';
        subSelect.value = '';
        subSelect.selectedIndex = 0;
        if (typeof jQuery !== 'undefined' && jQuery(subSelect).hasClass('select2-hidden-accessible')) {
            jQuery(subSelect).val('').trigger('change.select2');
        }

        priceInput.value = '0.00';
        qtyInput.value = '1';
        currentSelectedPackageDetails = null;

        const previewBox = document.getElementById('pickerPkgServicesPreview');
        if (previewBox) previewBox.classList.add('d-none');
        const pillsSpan = document.getElementById('pickerPkgServicesPills');
        if (pillsSpan) pillsSpan.innerHTML = '';
        const descText = document.getElementById('pickerPkgDescText');
        if (descText) descText.innerText = '';
        const svcRow = document.getElementById('pickerPkgServicesRow');
        if (svcRow) svcRow.classList.add('d-none');
        const descRow = document.getElementById('pickerPkgDescRow');
        if (descRow) descRow.classList.add('d-none');
    }

    // Render Package Row in Table
    function addPackageRow(pkg) {
        const tbody = document.getElementById('packagesTbody');
        const emptyMsg = document.getElementById('noPackagesMsg');
        if (emptyMsg) emptyMsg.classList.add('d-none');

        const idx = packageIndex++;
        const sellPrice = parseFloat(pkg.sell_price || 0);
        const qty = parseInt(pkg.qty || 1);
        const totalPrice = parseFloat(pkg.total_price || (sellPrice * qty));
        const pkgDesc = pkg.description || '';

        let descHtml = '';
        if (pkgDesc && pkgDesc.trim() !== '') {
            descHtml = `<div class="text-muted small mt-1" style="white-space: pre-line; font-size: 11.5px; line-height: 1.45;"><i class="bi bi-text-paragraph text-primary me-1"></i>${escapeHtml(pkgDesc)}</div>`;
        }

        const tr = document.createElement('tr');
        tr.id = `pkg_row_${idx}`;
        tr.innerHTML = `
            <td class="text-center text-muted fw-bold font-num">${tbody.children.length + 1}</td>
            <td>
                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                    <strong class="text-dark fs-6">${escapeHtml(pkg.package_name)}</strong>
                    ${pkg.category_name ? `<span class="badge bg-secondary-subtle text-secondary border small">${escapeHtml(pkg.category_name)}</span>` : ''}
                </div>
                ${descHtml}
                <input type="hidden" name="packages[${idx}][id]" value="${pkg.id || ''}">
                <input type="hidden" name="packages[${idx}][event_sub_category_id]" class="pkg-subcat-id-field" value="${pkg.event_sub_category_id || ''}">
                <input type="hidden" name="packages[${idx}][package_name]" value="${escapeHtml(pkg.package_name)}">
                <input type="hidden" name="packages[${idx}][description]" value="${escapeHtml(pkgDesc)}">
                <input type="hidden" name="packages[${idx}][cost_price]" value="${pkg.cost_price || 0}">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="packages[${idx}][sell_price]" id="pkg_price_${idx}" class="form-control form-control-sm text-end fw-semibold" value="${sellPrice.toFixed(2)}" oninput="calcPackageLine(${idx})">
            </td>
            <td>
                <input type="number" min="1" step="1" name="packages[${idx}][qty]" id="pkg_qty_${idx}" class="form-control form-control-sm text-center fw-semibold" value="${qty}" oninput="calcPackageLine(${idx})">
            </td>
            <td class="text-end pe-4 fw-bold text-dark font-num fs-6" id="pkg_line_total_${idx}">
                ${totalPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                <input type="hidden" name="packages[${idx}][total_price]" id="pkg_total_input_${idx}" class="pkg-line-total-val" value="${totalPrice.toFixed(2)}">
            </td>
            <td class="text-center">
                <button type="button" class="btn-action btn-action-delete mx-auto" onclick="removePackageRow(${idx})" title="Remove package">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        reindexPackageRows();
        calculateEventTotals();
    }

    function calcPackageLine(idx) {
        const qty = parseInt(document.getElementById(`pkg_qty_${idx}`)?.value) || 1;
        const price = parseFloat(document.getElementById(`pkg_price_${idx}`)?.value) || 0;
        const lineTotal = qty * price;
        const cell = document.getElementById(`pkg_line_total_${idx}`);
        const hiddenTotal = document.getElementById(`pkg_total_input_${idx}`);
        if (cell) {
            cell.innerText = lineTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        if (hiddenTotal) {
            hiddenTotal.value = lineTotal.toFixed(2);
        }
        calculateEventTotals();
    }

    function removePackageRow(idx) {
        document.getElementById(`pkg_row_${idx}`)?.remove();
        reindexPackageRows();
        calculateEventTotals();
    }

    function reindexPackageRows() {
        const tbody = document.getElementById('packagesTbody');
        const rows = tbody.querySelectorAll('tr');
        const emptyMsg = document.getElementById('noPackagesMsg');
        if (rows.length === 0) {
            if (emptyMsg) emptyMsg.classList.remove('d-none');
        } else {
            if (emptyMsg) emptyMsg.classList.add('d-none');
            rows.forEach((row, index) => {
                const numCell = row.querySelector('td:first-child');
                if (numCell) numCell.innerText = index + 1;
            });
        }
    }

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
        if (tbody.querySelectorAll('tr').length === 0 && emptyMsg) {
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
        if (tbody.querySelectorAll('tr').length === 0 && emptyMsg) {
            emptyMsg.classList.remove('d-none');
        }
        calculateEventTotals();
    }

    // Live Calculation of All Totals
    function calculateEventTotals() {
        // Sum Packages from Table
        let packagesTotal = 0;
        let firstSubCatId = '';
        document.querySelectorAll('#packagesTbody tr').forEach(row => {
            const lineVal = parseFloat(row.querySelector('.pkg-line-total-val')?.value) || 0;
            packagesTotal += lineVal;
            if (!firstSubCatId) {
                const subCatField = row.querySelector('.pkg-subcat-id-field');
                if (subCatField && subCatField.value) {
                    firstSubCatId = subCatField.value;
                }
            }
        });

        document.getElementById('packageSellPrice').value = packagesTotal.toFixed(2);
        document.getElementById('eventSubCategoryId').value = firstSubCatId;
        const pkgTotalDisplay = document.getElementById('packagesTotalDisplay');
        if (pkgTotalDisplay) {
            pkgTotalDisplay.innerText = packagesTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

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

        const subtotal = packagesTotal + servicesTotal + chargesTotal;
        const finalTotal = Math.max(0, subtotal - discount);
        const estimatedBalance = Math.max(0, finalTotal - advance);

        // Update displays
        document.getElementById('extraServicesTotalDisplay').innerText = servicesTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('otherChargesTotalDisplay').innerText = chargesTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        document.getElementById('sumPackage').innerText = 'Rs. ' + packagesTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('sumServices').innerText = 'Rs. ' + servicesTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('sumCharges').innerText = 'Rs. ' + chargesTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        document.getElementById('finalTotalDisplay').innerText = 'Rs. ' + finalTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('estimatedBalanceDisplay').innerText = 'Rs. ' + estimatedBalance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Trigger on load
    document.addEventListener('DOMContentLoaded', () => {
        if (prefilledPackages && prefilledPackages.length > 0) {
            prefilledPackages.forEach(pkg => addPackageRow(pkg));
        } else {
            reindexPackageRows();
        }
        calculateEventTotals();
    });
</script>
@endsection