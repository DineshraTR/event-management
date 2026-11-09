@extends('layouts.app')
@section('title', 'Edit Quotation - ' . $quotation->quotation_no)
@section('page-title', 'Edit Quotation')

@section('content')
<style>
    /* Custom Styling for Quotation Edit */
    .quotation-creator-card {
        border-radius: 16px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
        background: #ffffff;
        overflow: hidden;
    }

    .quotation-header-banner {
        background: #ffffff;
        border-bottom: 1px solid #edf2f7;
        padding: 16px 22px;
    }

    .quotation-icon-box {
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
    .quotation-action-bar {
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    /* Darker Light Gray Buttons (+ Add) */
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

    /* Included Package Service Deliverable Pill */
    .package-service-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 12px;
        color: #334155;
        font-weight: 500;
    }
    .package-service-pill i {
        color: #0d6efd;
    }
</style>

<div class="container-fluid px-0">
    <div class="quotation-creator-card mb-4">
        <!-- Card Header -->
        <div class="quotation-header-banner d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="quotation-icon-box">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark" style="font-size: 15.5px; letter-spacing: -0.2px;">
                        Edit Quotation: {{ $quotation->quotation_no }}
                    </h5>
                    <small class="text-muted" style="font-size: 12px;">
                        Customer: <strong class="text-dark">{{ $quotation->customer?->name ?? 'N/A' }}</strong> &bull; Date: {{ $quotation->quotation_date?->format('Y-m-d') }}
                    </small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('quotations.show', $quotation) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1.5 px-3">
                    <i class="bi bi-eye"></i> <span>View Proposal</span>
                </a>
                <a href="{{ route('quotations.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1.5 px-3">
                    <i class="bi bi-arrow-left"></i> <span>Back to Quotations</span>
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

            <form method="POST" action="{{ route('quotations.update', $quotation) }}" id="quotationEditForm">
                @csrf
                @method('PUT')

                <!-- Section 1: Customer & Event Information -->
                <div class="custom-section-box">
                    <div class="form-section-title">
                        <i class="bi bi-person-vcard-fill"></i>
                        <span>Customer & Occasion Details</span>
                    </div>

                    <div class="row g-3">
                        <!-- Customer Selection -->
                        <div class="col-md-6">
                            <label class="custom-input-label">
                                <i class="bi bi-person-circle"></i>
                                Customer <span class="text-danger">*</span>
                            </label>
                            <select name="customer_id" id="customerId" class="form-select" required>
                                <option value="">-- Choose Customer --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id', $quotation->customer_id) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} ({{ $customer->phone ?: 'No phone' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Event / Occasion Name -->
                        <div class="col-md-6">
                            <label class="custom-input-label">
                                <i class="bi bi-tag-fill"></i>
                                Event / Occasion Name
                            </label>
                            <input type="text" name="event_name" id="eventName" class="form-control" value="{{ old('event_name', $quotation->event_name) }}" placeholder="e.g. Ruwan & Anuki Wedding Reception">
                        </div>

                        <!-- Quotation Date -->
                        <div class="col-md-4">
                            <label class="custom-input-label">
                                <i class="bi bi-calendar-event"></i>
                                Quotation Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="quotation_date" class="form-control" value="{{ old('quotation_date', $quotation->quotation_date?->format('Y-m-d')) }}" required>
                        </div>

                        <!-- Valid Until -->
                        <div class="col-md-4">
                            <label class="custom-input-label">
                                <i class="bi bi-calendar-check"></i>
                                Valid Until
                            </label>
                            <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until', $quotation->valid_until?->format('Y-m-d')) }}">
                        </div>

                        <!-- Status -->
                        <div class="col-md-4">
                            <label class="custom-input-label">
                                <i class="bi bi-flag-fill"></i>
                                Proposal Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" class="form-select" required>
                                <option value="draft" @selected(old('status', $quotation->status) === 'draft')>Draft (Under Preparation)</option>
                                <option value="sent" @selected(old('status', $quotation->status) === 'sent')>Sent (Presented to Client)</option>
                                <option value="accepted" @selected(old('status', $quotation->status) === 'accepted')>Accepted (Client Approved)</option>
                                <option value="rejected" @selected(old('status', $quotation->status) === 'rejected')>Rejected (Declined)</option>
                            </select>
                        </div>

                        <!-- Target Event Date -->
                        <div class="col-md-6">
                            <label class="custom-input-label">
                                <i class="bi bi-calendar-heart"></i>
                                Target Event Date
                            </label>
                            <input type="date" name="event_date" class="form-control" value="{{ old('event_date', $quotation->event_date?->format('Y-m-d')) }}">
                        </div>

                        <!-- Venue / Location -->
                        <div class="col-md-6">
                            <label class="custom-input-label">
                                <i class="bi bi-geo-alt-fill"></i>
                                Venue / Proposed Location
                            </label>
                            <input type="text" name="venue" class="form-control" value="{{ old('venue', $quotation->venue) }}" placeholder="e.g. Cinnamon Grand Colombo">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Package Selection & Pricing (Multi-Package Supported) -->
                @php
                    $prefilledPackages = old('packages', $quotation->packages->map(function($p) {
                        return [
                            'id' => $p->id,
                            'event_sub_category_id' => $p->event_sub_category_id,
                            'package_name' => $p->package_name,
                            'category_name' => $p->category_name,
                            'description' => $p->description ?? $p->subCategory?->description ?? '',
                            'unit_price' => (float)$p->unit_price,
                            'qty' => (int)$p->qty,
                            'total_price' => (float)$p->total_price,
                        ];
                    })->values()->toArray());

                    if (empty($prefilledPackages) && $quotation->subCategory) {
                        $prefilledPackages[] = [
                            'id' => null,
                            'event_sub_category_id' => $quotation->event_sub_category_id,
                            'package_name' => $quotation->subCategory->name,
                            'category_name' => $quotation->subCategory->eventCategory?->name,
                            'description' => $quotation->subCategory->description ?? '',
                            'unit_price' => (float)($quotation->package_sell_price ?? $quotation->subCategory->sell_price),
                            'qty' => 1,
                            'total_price' => (float)($quotation->package_sell_price ?? $quotation->subCategory->sell_price),
                        ];
                    }
                    $initialPackageSellPrice = collect($prefilledPackages)->sum('total_price') ?: (float)($quotation->package_sell_price ?? 0);
                @endphp
                <div class="custom-section-box">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div class="form-section-title mb-0">
                            <i class="bi bi-box-seam-fill"></i>
                            <span>Quotation Packages Selection & Pricing</span>
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
                        <div id="pkgPickerServicesPreview" class="d-none mt-2 pt-2 border-top">
                            <div id="pkgPickerServicesRow" class="mb-1 d-none">
                                <small class="text-muted fw-semibold me-2"><i class="bi bi-box-seam text-primary me-1"></i>Included Services:</small>
                                <span id="pkgPickerServicesPills" class="d-inline-flex flex-wrap gap-1"></span>
                            </div>
                            <div id="pkgPickerDescRow" class="d-none">
                                <small class="text-muted fw-semibold me-2"><i class="bi bi-card-text text-primary me-1"></i>Package Description:</small>
                                <div id="pkgPickerDescText" class="text-secondary small mt-1" style="white-space: pre-line;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Packages List Table -->
                    <div class="modern-table-container">
                        <table class="table modern-table align-middle mb-0" id="quotationPackagesTable">
                            <thead>
                                <tr>
                                    <th>Package Details & Description</th>
                                    <th style="width: 160px;" class="text-end">Unit Price (Rs.)</th>
                                    <th style="width: 100px;" class="text-center">Qty</th>
                                    <th style="width: 170px;" class="text-end pe-4">Line Total (Rs.)</th>
                                    <th style="width: 50px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="quotationPackagesTbody">
                                <!-- Dynamic Packages Rows -->
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold text-secondary">Total Packages Amount:</td>
                                    <td class="text-end pe-4 fw-bold text-dark fs-6" id="packagesTotalDisplay">0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="noPackagesMsg" class="text-center py-4 bg-white border border-top-0 rounded-bottom text-muted small">
                        <i class="bi bi-info-circle me-1"></i> No packages added yet. Select a category &amp; package above and click <strong>"Add Package"</strong>.
                    </div>

                    <!-- Hidden input to maintain package_sell_price legacy compatibility -->
                    <input type="hidden" name="package_sell_price" id="packageSellPrice" value="{{ old('package_sell_price', $quotation->package_sell_price ?? '0.00') }}">
                </div>

                <!-- Section 3: Additional Services & Deliverables -->
                @php
                    $existingServices = old('services', $quotation->services->map(fn($s) => [
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
                            <span>Additional Services & Deliverables</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-light-gray fw-semibold px-3" onclick="addQuotationServiceRow()">
                            <i class="bi bi-plus-lg me-1"></i> Add Service
                        </button>
                    </div>
                    <p class="text-muted small mb-3">Add custom service requests, photography, decorations or food deliverables beyond the package preset.</p>

                    <div class="modern-table-container">
                        <table class="table modern-table align-middle" id="quotationServicesTable">
                            <thead>
                                <tr>
                                    <th>Service Preset / Item Description</th>
                                    <th style="width: 110px;" class="text-center">Qty</th>
                                    <th style="width: 160px;" class="text-end">Unit Price (Rs.)</th>
                                    <th style="width: 170px;" class="text-end pe-4">Line Total (Rs.)</th>
                                    <th style="width: 50px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="quotationServicesTbody">
                                @foreach($existingServices as $idx => $sRow)
                                    @php
                                        $rQty = (int) ($sRow['qty'] ?? 1);
                                        $rPrice = (float) ($sRow['unit_price'] ?? 0);
                                        $rTotal = $rQty * $rPrice;
                                    @endphp
                                    <tr id="qsvc_row_{{ $idx }}">
                                        <td>
                                            <div class="row g-1">
                                                <div class="col-md-5">
                                                    <select class="form-select form-select-sm" onchange="onSelectQuotationServicePreset(this, {{ $idx }})">
                                                        <option value="">-- Custom / Select Service --</option>
                                                        @foreach($services as $svc)
                                                            <option value="{{ $svc->id }}" data-price="{{ $svc->cost_price }}" data-name="{{ $svc->name }}" @selected(($sRow['service_id'] ?? '') == $svc->id)>
                                                                {{ $svc->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-7">
                                                    <input type="hidden" name="services[{{ $idx }}][service_id]" id="qsvc_id_{{ $idx }}" value="{{ $sRow['service_id'] ?? '' }}">
                                                    <input type="text" name="services[{{ $idx }}][description]" id="qsvc_desc_{{ $idx }}" class="form-control form-control-sm" value="{{ $sRow['description'] ?? '' }}" placeholder="Service description..." required>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" min="1" step="1" name="services[{{ $idx }}][qty]" id="qsvc_qty_{{ $idx }}" class="form-control form-control-sm text-center" value="{{ $rQty }}" oninput="calcQuotationServiceLine({{ $idx }})">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="services[{{ $idx }}][unit_price]" id="qsvc_price_{{ $idx }}" class="form-control form-control-sm text-end" value="{{ number_format($rPrice, 2, '.', '') }}" oninput="calcQuotationServiceLine({{ $idx }})">
                                        </td>
                                        <td class="text-end pe-4 fw-bold text-dark" id="qsvc_line_total_{{ $idx }}">
                                            {{ number_format($rTotal, 2) }}
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn-action btn-action-delete mx-auto" onclick="removeQuotationServiceRow({{ $idx }})" title="Remove service">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold text-secondary">Services Subtotal:</td>
                                    <td class="text-end pe-4 fw-bold text-dark fs-6" id="servicesTotalDisplay">0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="noServicesMsg" class="text-center py-4 bg-white border border-top-0 rounded-bottom text-muted small {{ count($existingServices) > 0 ? 'd-none' : '' }}">
                        <i class="bi bi-info-circle me-1"></i> No services added yet. Click <strong>"Add Service"</strong> to include line items.
                    </div>
                </div>

                <!-- Section 4: Other Charges (Transport, Surcharges) -->
                @php
                    $existingCharges = old('other_charges', $quotation->otherCharges->map(fn($c) => [
                        'description' => $c->description,
                        'amount' => $c->amount,
                    ])->toArray());
                @endphp
                <div class="custom-section-box">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div class="form-section-title mb-0">
                            <i class="bi bi-truck"></i>
                            <span>Other Charges (Transport, Hall Rental, Logistics)</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-light-gray fw-semibold px-3" onclick="addQuotationChargeRow()">
                            <i class="bi bi-plus-lg me-1"></i> Add Charge
                        </button>
                    </div>
                    <p class="text-muted small mb-3">Add transport fees, hall booking, cleaning surcharge, or logistical expenses.</p>

                    <div class="modern-table-container">
                        <table class="table modern-table align-middle" id="quotationChargesTable">
                            <thead>
                                <tr>
                                    <th>Charge Description</th>
                                    <th style="width: 200px;" class="text-end pe-4">Amount (Rs.)</th>
                                    <th style="width: 50px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="quotationChargesTbody">
                                @foreach($existingCharges as $cIdx => $cRow)
                                    @php
                                        $cAmount = (float) ($cRow['amount'] ?? 0);
                                    @endphp
                                    <tr id="qcharge_row_{{ $cIdx }}">
                                        <td>
                                            <input type="text" name="other_charges[{{ $cIdx }}][description]" class="form-control form-control-sm" value="{{ $cRow['description'] ?? '' }}" placeholder="Charge description..." required>
                                        </td>
                                        <td class="pe-4">
                                            <input type="number" step="0.01" min="0" name="other_charges[{{ $cIdx }}][amount]" class="form-control form-control-sm text-end quotation-charge-amount-input" value="{{ number_format($cAmount, 2, '.', '') }}" oninput="calculateQuotationTotals()">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn-action btn-action-delete mx-auto" onclick="removeQuotationChargeRow({{ $cIdx }})" title="Remove charge">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td class="text-end fw-bold text-secondary">Charges Subtotal:</td>
                                    <td class="text-end pe-4 fw-bold text-dark fs-6" id="chargesTotalDisplay">0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="noChargesMsg" class="text-center py-4 bg-white border border-top-0 rounded-bottom text-muted small {{ count($existingCharges) > 0 ? 'd-none' : '' }}">
                        <i class="bi bi-info-circle me-1"></i> No other charges added. Click <strong>"Add Charge"</strong> for logistics or surcharges.
                    </div>
                </div>

                <!-- Section 5: Quotation Totals & Terms -->
                <div class="custom-section-box" style="border-color: #cbd5e1; background: #ffffff;">
                    <div class="form-section-title">
                        <i class="bi bi-calculator-fill"></i>
                        <span>Quotation Totals & Notes</span>
                    </div>

                    <div class="row g-4 align-items-start">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="custom-input-label">
                                    <i class="bi bi-card-text"></i>
                                    Remarks / Terms & Proposal Notes
                                </label>
                                <textarea name="remarks" rows="4" class="form-control" placeholder="Specify quotation terms, validity conditions, advance requirements, or delivery notes...">{{ old('remarks', $quotation->remarks) }}</textarea>
                            </div>

                            <div class="p-3 bg-light rounded-3 border">
                                <div class="d-flex align-items-center gap-2 text-primary fw-bold small mb-1">
                                    <i class="bi bi-printer-fill fs-5"></i> Client Deliverable:
                                </div>
                                <p class="mb-0 text-muted small" style="line-height: 1.5;">
                                    You can print or download this estimate as a professional PDF quotation directly from the quotation details view.
                                </p>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="financial-summary-card">
                                <div class="financial-line-item">
                                    <span class="text-muted">Package Base Price:</span>
                                    <span class="fw-semibold text-primary" id="calcPackageSum">Rs. {{ number_format((float)($initialPackageSellPrice ?? $quotation->package_sell_price ?? 0), 2) }}</span>
                                </div>
                                <div class="financial-line-item">
                                    <span class="text-muted">Additional Services Subtotal:</span>
                                    <span class="fw-semibold text-dark" id="sumServicesDisplay">Rs. 0.00</span>
                                </div>
                                <div class="financial-line-item">
                                    <span class="text-muted">Other Charges Subtotal:</span>
                                    <span class="fw-semibold text-dark" id="sumChargesDisplay">Rs. 0.00</span>
                                </div>
                                <div class="financial-line-item pt-2 border-top">
                                    <span class="text-muted">Special Discount:</span>
                                    <div class="input-group input-group-sm" style="width: 160px;">
                                        <input type="number" step="0.01" min="0" name="discount" id="quotationDiscountInput" 
                                               class="form-control text-end text-danger fw-bold" 
                                               value="{{ old('discount', $quotation->discount ?? '0.00') }}" 
                                               oninput="calculateQuotationTotals()">
                                    </div>
                                </div>

                                <div class="financial-total-banner d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small fw-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">Estimated Total Value</div>
                                        <small class="text-muted">Net quoted price</small>
                                    </div>
                                    <div class="fs-4 fw-bold text-primary" id="finalQuotationTotalDisplay">
                                        Rs. 0.00
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sticky Action Footer Bar -->
                <div class="quotation-action-bar rounded-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="bi bi-clock-history text-secondary me-1"></i> Last updated: {{ $quotation->updated_at?->diffForHumans() }}
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('quotations.show', $quotation) }}" class="btn btn-outline-secondary px-4 fw-semibold">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm" id="btnSubmitQuotation">
                            <i class="bi bi-file-earmark-check me-1"></i> Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const availableServices = @json($services);
    const prefilledPackages = @json($prefilledPackages);
    let qServiceIndex = {{ count($existingServices) }};
    let qChargeIndex = {{ count($existingCharges) }};
    let qPkgIndex = 0;
    let currentPickerPkgData = null;

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    // Category Change -> Cascade Sub-Categories (Packages)
    function onPkgCategoryChange(categoryId) {
        const subSelect = document.getElementById('pkgSubCategoryId');
        subSelect.innerHTML = '<option value="">-- Loading Packages... --</option>';
        document.getElementById('pkgPriceInput').value = '0.00';
        const previewBox = document.getElementById('pkgPickerServicesPreview');
        if (previewBox) previewBox.classList.add('d-none');
        const pillsSpan = document.getElementById('pkgPickerServicesPills');
        if (pillsSpan) pillsSpan.innerHTML = '';
        const descText = document.getElementById('pkgPickerDescText');
        if (descText) descText.innerText = '';
        const svcRow = document.getElementById('pkgPickerServicesRow');
        if (svcRow) svcRow.classList.add('d-none');
        const descRow = document.getElementById('pkgPickerDescRow');
        if (descRow) descRow.classList.add('d-none');
        currentPickerPkgData = null;

        if (!categoryId) {
            subSelect.innerHTML = '<option value="">-- Select Category First --</option>';
            subSelect.value = '';
            subSelect.selectedIndex = 0;
            if (typeof jQuery !== 'undefined' && jQuery(subSelect).hasClass('select2-hidden-accessible')) {
                jQuery(subSelect).trigger('change.select2');
            }
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

    // Sub-Category Change -> Load Package Details & Included Deliverables
    function onPkgSubCategoryChange(subCategoryId) {
        const previewBox = document.getElementById('pkgPickerServicesPreview');
        const pillsSpan = document.getElementById('pkgPickerServicesPills');
        const descText = document.getElementById('pkgPickerDescText');
        const svcRow = document.getElementById('pkgPickerServicesRow');
        const descRow = document.getElementById('pkgPickerDescRow');

        if (!subCategoryId) {
            document.getElementById('pkgPriceInput').value = '0.00';
            if (previewBox) previewBox.classList.add('d-none');
            if (pillsSpan) pillsSpan.innerHTML = '';
            if (descText) descText.innerText = '';
            if (svcRow) svcRow.classList.add('d-none');
            if (descRow) descRow.classList.add('d-none');
            currentPickerPkgData = null;
            return;
        }

        const subSelect = document.getElementById('pkgSubCategoryId');
        const selectedOpt = subSelect ? subSelect.options[subSelect.selectedIndex] : null;
        if (selectedOpt && selectedOpt.getAttribute('data-sell-price')) {
            const immediatePrice = parseFloat(selectedOpt.getAttribute('data-sell-price')) || 0;
            document.getElementById('pkgPriceInput').value = immediatePrice.toFixed(2);
        }

        fetch(`/api/sub-category/${subCategoryId}`)
            .then(res => {
                if (!res.ok) throw new Error('Network error');
                return res.json();
            })
            .then(data => {
                currentPickerPkgData = data;
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
            .catch(err => {
                console.error('Error fetching package details:', err);
                currentPickerPkgData = null;
            });
    }

    // Add Package to Quotation Table
    function addSelectedPackage() {
        const subSelect = document.getElementById('pkgSubCategoryId');
        const catSelect = document.getElementById('pkgCategoryId');
        const subId = subSelect.value;
        if (!subId) {
            alert('Please select a package first.');
            return;
        }

        const packageName = subSelect.options[subSelect.selectedIndex].text.replace(/\s*\(Sell:.*?\)\s*/i, '').trim();
        const categoryName = catSelect.options[catSelect.selectedIndex]?.text?.trim() || '';
        const unitPrice = parseFloat(document.getElementById('pkgPriceInput').value) || 0;
        const qty = parseInt(document.getElementById('pkgQtyInput').value) || 1;
        const description = (currentPickerPkgData && currentPickerPkgData.description) ? currentPickerPkgData.description : '';

        addQuotationPackageRow({
            event_sub_category_id: subId,
            package_name: packageName,
            category_name: categoryName,
            description: description,
            unit_price: unitPrice,
            qty: qty
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

        document.getElementById('pkgPriceInput').value = '0.00';
        document.getElementById('pkgQtyInput').value = '1';
        currentPickerPkgData = null;

        const previewBox = document.getElementById('pkgPickerServicesPreview');
        if (previewBox) previewBox.classList.add('d-none');
        const pillsSpan = document.getElementById('pkgPickerServicesPills');
        if (pillsSpan) pillsSpan.innerHTML = '';
        const descEl = document.getElementById('pkgPickerDescText');
        if (descEl) descEl.innerText = '';
        const svcRow = document.getElementById('pkgPickerServicesRow');
        if (svcRow) svcRow.classList.add('d-none');
        const descRow = document.getElementById('pkgPickerDescRow');
        if (descRow) descRow.classList.add('d-none');
    }

    function addQuotationPackageRow(pkg) {
        const tbody = document.getElementById('quotationPackagesTbody');
        const emptyMsg = document.getElementById('noPackagesMsg');
        if (emptyMsg) emptyMsg.classList.add('d-none');

        const idx = qPkgIndex++;
        const lineTotal = (parseFloat(pkg.unit_price) || 0) * (parseInt(pkg.qty) || 1);
        const pkgDesc = pkg.description || '';

        let descHtml = '';
        if (pkgDesc && pkgDesc.trim() !== '') {
            descHtml = `<div class="text-muted small mt-1" style="white-space: pre-line; font-size: 11.5px; line-height: 1.45;"><i class="bi bi-text-paragraph text-primary me-1"></i>${escapeHtml(pkgDesc)}</div>`;
        }

        const tr = document.createElement('tr');
        tr.id = `qpkg_row_${idx}`;
        tr.className = 'qpkg-row';
        tr.innerHTML = `
            <td>
                <div class="fw-bold text-dark">${escapeHtml(pkg.package_name)}</div>
                ${pkg.category_name ? `<div class="small text-muted"><i class="bi bi-tag me-1"></i>${escapeHtml(pkg.category_name)}</div>` : ''}
                ${descHtml}
                <input type="hidden" name="packages[${idx}][id]" value="${pkg.id || ''}">
                <input type="hidden" name="packages[${idx}][event_sub_category_id]" value="${pkg.event_sub_category_id || ''}">
                <input type="hidden" name="packages[${idx}][package_name]" value="${escapeHtml(pkg.package_name)}">
                <input type="hidden" name="packages[${idx}][category_name]" value="${escapeHtml(pkg.category_name || '')}">
                <input type="hidden" name="packages[${idx}][description]" value="${escapeHtml(pkgDesc)}">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="packages[${idx}][unit_price]" id="qpkg_unit_price_${idx}"
                    class="form-control form-control-sm text-end fw-semibold qpkg-unit-price-input"
                    value="${parseFloat(pkg.unit_price).toFixed(2)}"
                    oninput="calcQuotationPackageLine(${idx})">
            </td>
            <td>
                <input type="number" min="1" step="1" name="packages[${idx}][qty]" id="qpkg_qty_${idx}"
                    class="form-control form-control-sm text-center fw-semibold qpkg-qty-input"
                    value="${parseInt(pkg.qty) || 1}"
                    oninput="calcQuotationPackageLine(${idx})">
            </td>
            <td class="text-end pe-4 fw-bold text-dark fs-6">
                <span id="qpkg_total_display_${idx}">${lineTotal.toFixed(2)}</span>
                <input type="hidden" name="packages[${idx}][total_price]" id="qpkg_total_val_${idx}" class="qpkg-line-total-val" value="${lineTotal.toFixed(2)}">
            </td>
            <td class="text-center">
                <button type="button" class="btn-action btn-action-delete mx-auto" onclick="removeQuotationPackageRow(${idx})" title="Remove package">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        calculateQuotationTotals();
    }

    function calcQuotationPackageLine(idx) {
        const unitPrice = parseFloat(document.getElementById(`qpkg_unit_price_${idx}`)?.value) || 0;
        const qty = parseInt(document.getElementById(`qpkg_qty_${idx}`)?.value) || 1;
        const lineTotal = unitPrice * qty;

        const displayEl = document.getElementById(`qpkg_total_display_${idx}`);
        const valEl = document.getElementById(`qpkg_total_val_${idx}`);
        if (displayEl) displayEl.innerText = lineTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (valEl) valEl.value = lineTotal.toFixed(2);

        calculateQuotationTotals();
    }

    function removeQuotationPackageRow(idx) {
        document.getElementById(`qpkg_row_${idx}`)?.remove();
        const tbody = document.getElementById('quotationPackagesTbody');
        const emptyMsg = document.getElementById('noPackagesMsg');
        if (tbody.querySelectorAll('tr').length === 0 && emptyMsg) {
            emptyMsg.classList.remove('d-none');
        }
        reindexQuotationPackageRows();
        calculateQuotationTotals();
    }

    function reindexQuotationPackageRows() {
        const rows = document.querySelectorAll('#quotationPackagesTbody tr.qpkg-row');
        rows.forEach((row, i) => {
            row.querySelectorAll('input').forEach(input => {
                const name = input.getAttribute('name');
                if (name && name.startsWith('packages[')) {
                    input.setAttribute('name', name.replace(/packages\[\d+\]/, `packages[${i}]`));
                }
            });
        });
    }

    function addQuotationServiceRow() {
        addQuotationServiceRowCustom('', 1, 0, null);
    }

    function addQuotationServiceRowCustom(description = '', qty = 1, unitPrice = 0, serviceId = null) {
        const tbody = document.getElementById('quotationServicesTbody');
        const emptyMsg = document.getElementById('noServicesMsg');
        if (emptyMsg) emptyMsg.classList.add('d-none');

        const idx = qServiceIndex++;

        let serviceOptions = '<option value="">-- Custom / Select Service --</option>';
        availableServices.forEach(svc => {
            serviceOptions += `<option value="${svc.id}" data-price="${svc.cost_price}" data-name="${svc.name}" ${serviceId == svc.id ? 'selected' : ''}>${svc.name}</option>`;
        });

        const tr = document.createElement('tr');
        tr.id = `qsvc_row_${idx}`;
        tr.innerHTML = `
            <td>
                <div class="row g-1">
                    <div class="col-md-5">
                        <select class="form-select form-select-sm" onchange="onSelectQuotationServicePreset(this, ${idx})">
                            ${serviceOptions}
                        </select>
                    </div>
                    <div class="col-md-7">
                        <input type="hidden" name="services[${idx}][service_id]" id="qsvc_id_${idx}" value="${serviceId || ''}">
                        <input type="text" name="services[${idx}][description]" id="qsvc_desc_${idx}" class="form-control form-control-sm" value="${description}" placeholder="Service description..." required>
                    </div>
                </div>
            </td>
            <td>
                <input type="number" min="1" step="1" name="services[${idx}][qty]" id="qsvc_qty_${idx}" class="form-control form-control-sm text-center" value="${qty}" oninput="calcQuotationServiceLine(${idx})">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="services[${idx}][unit_price]" id="qsvc_price_${idx}" class="form-control form-control-sm text-end" value="${parseFloat(unitPrice).toFixed(2)}" oninput="calcQuotationServiceLine(${idx})">
            </td>
            <td class="text-end pe-4 fw-bold text-dark" id="qsvc_line_total_${idx}">
                ${(qty * unitPrice).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
            </td>
            <td class="text-center">
                <button type="button" class="btn-action btn-action-delete mx-auto" onclick="removeQuotationServiceRow(${idx})" title="Remove service">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        calcQuotationServiceLine(idx);
    }

    function onSelectQuotationServicePreset(selectEl, idx) {
        const selectedOpt = selectEl.options[selectEl.selectedIndex];
        const svcId = selectedOpt.value;
        const svcName = selectedOpt.getAttribute('data-name');
        const svcPrice = selectedOpt.getAttribute('data-price');

        if (svcId) {
            document.getElementById(`qsvc_id_${idx}`).value = svcId;
            document.getElementById(`qsvc_desc_${idx}`).value = svcName;
            document.getElementById(`qsvc_price_${idx}`).value = svcPrice || '0.00';
        }
        calcQuotationServiceLine(idx);
    }

    function calcQuotationServiceLine(idx) {
        const qty = parseInt(document.getElementById(`qsvc_qty_${idx}`)?.value) || 1;
        const price = parseFloat(document.getElementById(`qsvc_price_${idx}`)?.value) || 0;
        const lineTotal = qty * price;
        const cell = document.getElementById(`qsvc_line_total_${idx}`);
        if (cell) {
            cell.innerText = lineTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        calculateQuotationTotals();
    }

    function removeQuotationServiceRow(idx) {
        document.getElementById(`qsvc_row_${idx}`)?.remove();
        const tbody = document.getElementById('quotationServicesTbody');
        const emptyMsg = document.getElementById('noServicesMsg');
        if (tbody.querySelectorAll('tr').length === 0 && emptyMsg) {
            emptyMsg.classList.remove('d-none');
        }
        calculateQuotationTotals();
    }

    function addQuotationChargeRow() {
        addQuotationChargeRowCustom('', 0);
    }

    function addQuotationChargeRowCustom(description = '', amount = 0) {
        const tbody = document.getElementById('quotationChargesTbody');
        const emptyMsg = document.getElementById('noChargesMsg');
        if (emptyMsg) emptyMsg.classList.add('d-none');

        const idx = qChargeIndex++;

        const tr = document.createElement('tr');
        tr.id = `qcharge_row_${idx}`;
        tr.innerHTML = `
            <td>
                <input type="text" name="other_charges[${idx}][description]" class="form-control form-control-sm" value="${description}" placeholder="Charge description..." required>
            </td>
            <td class="pe-4">
                <input type="number" step="0.01" min="0" name="other_charges[${idx}][amount]" class="form-control form-control-sm text-end quotation-charge-amount-input" value="${parseFloat(amount).toFixed(2)}" oninput="calculateQuotationTotals()">
            </td>
            <td class="text-center">
                <button type="button" class="btn-action btn-action-delete mx-auto" onclick="removeQuotationChargeRow(${idx})" title="Remove charge">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        calculateQuotationTotals();
    }

    function removeQuotationChargeRow(idx) {
        document.getElementById(`qcharge_row_${idx}`)?.remove();
        const tbody = document.getElementById('quotationChargesTbody');
        const emptyMsg = document.getElementById('noChargesMsg');
        if (tbody.querySelectorAll('tr').length === 0 && emptyMsg) {
            emptyMsg.classList.remove('d-none');
        }
        calculateQuotationTotals();
    }

    function calculateQuotationTotals() {
        let pkgBasePrice = 0;
        document.querySelectorAll('.qpkg-line-total-val').forEach(input => {
            pkgBasePrice += parseFloat(input.value) || 0;
        });

        const pkgDisplayEl = document.getElementById('packagesTotalDisplay');
        if (pkgDisplayEl) {
            pkgDisplayEl.innerText = pkgBasePrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        const pkgHiddenInput = document.getElementById('packageSellPrice');
        if (pkgHiddenInput) {
            pkgHiddenInput.value = pkgBasePrice.toFixed(2);
        }

        let servicesTotal = 0;
        document.querySelectorAll('#quotationServicesTbody tr').forEach(row => {
            const qty = parseInt(row.querySelector('[id^="qsvc_qty_"]')?.value) || 0;
            const price = parseFloat(row.querySelector('[id^="qsvc_price_"]')?.value) || 0;
            servicesTotal += qty * price;
        });

        let chargesTotal = 0;
        document.querySelectorAll('.quotation-charge-amount-input').forEach(input => {
            chargesTotal += parseFloat(input.value) || 0;
        });

        const discount = parseFloat(document.getElementById('quotationDiscountInput')?.value) || 0;
        const subtotal = pkgBasePrice + servicesTotal + chargesTotal;
        const finalTotal = Math.max(0, subtotal - discount);

        document.getElementById('servicesTotalDisplay').innerText = servicesTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('chargesTotalDisplay').innerText = chargesTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        if (document.getElementById('calcPackageSum')) {
            document.getElementById('calcPackageSum').innerText = 'Rs. ' + pkgBasePrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        document.getElementById('sumServicesDisplay').innerText = 'Rs. ' + servicesTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('sumChargesDisplay').innerText = 'Rs. ' + chargesTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('finalQuotationTotalDisplay').innerText = 'Rs. ' + finalTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (prefilledPackages && prefilledPackages.length > 0) {
            prefilledPackages.forEach(pkg => addQuotationPackageRow(pkg));
        }
        calculateQuotationTotals();
    });
</script>
@endsection