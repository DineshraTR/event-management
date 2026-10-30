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

                <!-- Section 2: Package Selection & Pricing (From Event Register Style) -->
                @php
                    $currentCategoryId = old('category_id', $quotation->subCategory?->event_category_id);
                    $currentSubCategoryId = old('event_sub_category_id', $quotation->event_sub_category_id);
                    $currentCategory = $categories->firstWhere('id', $currentCategoryId);
                    $subCategories = $currentCategory ? \App\Models\EventSubCategory::where('event_category_id', $currentCategory->id)->where('is_active', true)->get() : collect();
                    $initialPackageSellPrice = old('package_sell_price', $quotation->package_sell_price ?? $quotation->subCategory?->sell_price ?? '0.00');
                    $initialIncludedServices = $quotation->subCategory?->subCategoryServices ?? collect();
                @endphp
                <div class="custom-section-box">
                    <div class="form-section-title">
                        <i class="bi bi-box-seam-fill"></i>
                        <span>Package Selection & Pricing</span>
                    </div>

                    <div class="row g-3">
                        <!-- Category -->
                        <div class="col-md-4">
                            <label class="custom-input-label">
                                <i class="bi bi-folder2-open"></i>
                                Select Category
                            </label>
                            <select id="categoryId" class="form-select" onchange="onCategoryChange(this.value)">
                                <option value="">-- Choose Category --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected($currentCategoryId == $cat->id)>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sub-Category / Package -->
                        <div class="col-md-4">
                            <label class="custom-input-label">
                                <i class="bi bi-boxes"></i>
                                Select Sub-Category / Package
                            </label>
                            <select name="event_sub_category_id" id="subCategoryId" class="form-select" onchange="onSubCategoryChange(this.value)">
                                <option value="">-- Select Package --</option>
                                @foreach($subCategories as $sub)
                                    <option value="{{ $sub->id }}" data-sell-price="{{ $sub->sell_price }}" @selected($currentSubCategoryId == $sub->id)>
                                        {{ $sub->name }} (Sell: Rs. {{ number_format($sub->sell_price, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Auto-loads included deliverables and package base price.</small>
                        </div>

                        <!-- Package Base Sell Price (Editable) -->
                        <div class="col-md-4">
                            <label class="custom-input-label">
                                <i class="bi bi-cash-stack"></i>
                                Package Base Sell Price (Rs.)
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="package_sell_price" id="packageSellPrice" class="form-control fw-bold text-primary" value="{{ number_format((float)$initialPackageSellPrice, 2, '.', '') }}" oninput="calculateQuotationTotals()">
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Base price for this package; included directly in quotation total.</small>
                        </div>
                    </div>

                    <!-- Package Included Services Preview Box -->
                    <div id="packageServicesBox" class="mt-3 p-3 bg-white rounded-3 border {{ $initialIncludedServices->count() > 0 ? '' : 'd-none' }} shadow-sm">
                        <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                            <span class="fw-bold text-dark small">
                                <i class="bi bi-check-circle-fill text-success me-1"></i> Included in Selected Package:
                            </span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" id="pkgSvcCountBadge">
                                {{ $initialIncludedServices->count() }} Services
                            </span>
                        </div>
                        <div id="packageServicesList" class="d-flex flex-wrap gap-2 pt-1">
                            @foreach($initialIncludedServices as $isvc)
                                <div class="package-service-pill">
                                    <i class="bi bi-check2-circle"></i> <span>{{ $isvc->service?->name ?? 'Service #' . $isvc->service_id }} &bull; Qty: <strong>{{ $isvc->qty }}</strong></span>
                                </div>
                            @endforeach
                        </div>
                    </div>
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
                                    <span class="fw-semibold text-primary" id="calcPackageSum">Rs. {{ number_format((float)$initialPackageSellPrice, 2) }}</span>
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
    let qServiceIndex = {{ count($existingServices) }};
    let qChargeIndex = {{ count($existingCharges) }};
    let selectedPackageData = null;

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

    // Sub-Category Change -> Load Package Details & Included Deliverables
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
            calculateQuotationTotals();
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

                calculateQuotationTotals();
            })
            .catch(() => {
                calculateQuotationTotals();
            });
    }

    function resetPackageDetails() {
        document.getElementById('packageSellPrice').value = '0.00';
        const pkgBox = document.getElementById('packageServicesBox');
        if (pkgBox) pkgBox.classList.add('d-none');
        const pkgList = document.getElementById('packageServicesList');
        if (pkgList) pkgList.innerHTML = '';
        calculateQuotationTotals();
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
        const pkgBasePrice = parseFloat(document.getElementById('packageSellPrice')?.value) || 0;

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
        calculateQuotationTotals();
    });
</script>
@endsection