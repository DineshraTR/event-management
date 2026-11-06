@extends('layouts.app')
@section('title', 'Create Quotation')
@section('page-title', 'Create Quotation')

@section('content')
<style>
    /* Custom Styling for Quotation Creation */
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
        font-size: 13px;
    }
</style>

<div class="container-fluid px-0">
    <div class="quotation-creator-card mb-4">
        <!-- Card Header -->
        <div class="quotation-header-banner d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="quotation-icon-box">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark" style="font-size: 15.5px; letter-spacing: -0.2px;">Create Quotation / Estimate</h5>
                    <small class="text-muted" style="font-size: 12px;">Prepare cost estimates, packages, deliverables, and customized proposals for clients.</small>
                </div>
            </div>
            <div>
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

            <form method="POST" action="{{ route('quotations.store') }}" id="quotationCreateForm">
                @csrf

                <!-- Section 1: Customer & Event Information -->
                <div class="custom-section-box">
                    <div class="form-section-title">
                        <i class="bi bi-person-vcard-fill"></i>
                        <span>Customer & Occasion Details</span>
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
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} ({{ $customer->phone ?: 'No phone' }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Choose existing client or click Quick Add to create a new one</small>
                        </div>

                        <!-- Event / Occasion Name -->
                        <div class="col-md-6">
                            <label class="custom-input-label">
                                <i class="bi bi-tag-fill"></i>
                                Event / Occasion Name
                            </label>
                            <input type="text" name="event_name" id="eventName" class="form-control" value="{{ old('event_name') }}" placeholder="e.g. Ruwan & Anuki Wedding Reception">
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Event title or purpose for this quotation</small>
                        </div>

                        <!-- Quotation Date -->
                        <div class="col-md-4">
                            <label class="custom-input-label">
                                <i class="bi bi-calendar-event"></i>
                                Quotation Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="quotation_date" class="form-control" value="{{ old('quotation_date', $today) }}" required>
                        </div>

                        <!-- Valid Until -->
                        <div class="col-md-4">
                            <label class="custom-input-label">
                                <i class="bi bi-calendar-check"></i>
                                Valid Until
                            </label>
                            <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until', \Carbon\Carbon::parse($today)->addDays(14)->format('Y-m-d')) }}">
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">Default estimate validity: 14 days</small>
                        </div>

                        <!-- Target Event Date -->
                        <div class="col-md-4">
                            <label class="custom-input-label">
                                <i class="bi bi-calendar-heart"></i>
                                Target Event Date
                            </label>
                            <input type="date" name="event_date" class="form-control" value="{{ old('event_date') }}">
                        </div>

                        <!-- Venue / Location -->
                        <div class="col-12">
                            <label class="custom-input-label">
                                <i class="bi bi-geo-alt-fill"></i>
                                Venue / Proposed Location
                            </label>
                            <input type="text" name="venue" class="form-control" value="{{ old('venue') }}" placeholder="e.g. Cinnamon Grand Colombo, Cedar Room">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Package Selection & Pricing (Multi-Package Supported) -->
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
                        <!-- Live preview of services for currently selected package in picker -->
                        <div id="pkgPickerServicesPreview" class="d-none mt-2 pt-2 border-top small text-muted">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <strong class="text-dark"><i class="bi bi-check2-circle text-success"></i> Includes:</strong>
                                <span id="pkgPickerServicesList" class="d-inline-flex flex-wrap gap-1"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Packages List Table -->
                    <div class="modern-table-container">
                        <table class="table modern-table align-middle mb-0" id="quotationPackagesTable">
                            <thead>
                                <tr>
                                    <th>Package / Sub-Category</th>
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
                    <input type="hidden" name="package_sell_price" id="packageSellPrice" value="{{ old('package_sell_price', '0.00') }}">
                </div>

                <!-- Section 3: Additional Services & Deliverables -->
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
                    <p class="text-muted small mb-3">Add package items, custom service requests, photography, decorations or food deliverables.</p>

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
                                <!-- Dynamic Rows -->
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
                    <div id="noServicesMsg" class="text-center py-4 bg-white border border-top-0 rounded-bottom text-muted small">
                        <i class="bi bi-info-circle me-1"></i> No services added yet. Click <strong>"Add Service"</strong> to include line items.
                    </div>
                </div>

                <!-- Section 4: Other Charges (Transport, Surcharges) -->
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
                                <!-- Dynamic Rows -->
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
                    <div id="noChargesMsg" class="text-center py-4 bg-white border border-top-0 rounded-bottom text-muted small">
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
                                <textarea name="remarks" rows="4" class="form-control" placeholder="Specify quotation terms, validity conditions, advance requirements, or delivery notes...">{{ old('remarks') }}</textarea>
                            </div>

                            <div class="p-3 bg-light rounded-3 border">
                                <div class="d-flex align-items-center gap-2 text-primary fw-bold small mb-1">
                                    <i class="bi bi-check2-circle fs-5 text-success"></i> Fast Conversion to Event:
                                </div>
                                <p class="mb-0 text-muted small" style="line-height: 1.5;">
                                    When the customer approves this estimate, click <strong>"Convert to Event"</strong> from the quotation view to instantly transfer all client details and line items into an active booking!
                                </p>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="financial-summary-card">
                                <div class="financial-line-item">
                                    <span class="text-muted">Package Base Price:</span>
                                    <span class="fw-semibold text-primary" id="calcPackageSum">Rs. 0.00</span>
                                </div>
                                <div class="financial-line-item">
                                    <span class="text-muted">Additional Services:</span>
                                    <span class="fw-semibold text-dark" id="sumServicesDisplay">Rs. 0.00</span>
                                </div>
                                <div class="financial-line-item">
                                    <span class="text-muted">Other Charges:</span>
                                    <span class="fw-semibold text-dark" id="sumChargesDisplay">Rs. 0.00</span>
                                </div>
                                <div class="financial-line-item pt-2 border-top">
                                    <span class="text-muted">Special Discount:</span>
                                    <div class="input-group input-group-sm" style="width: 160px;">
                                        <input type="number" step="0.01" min="0" name="discount" id="quotationDiscountInput" 
                                               class="form-control text-end text-danger fw-bold" 
                                               value="{{ old('discount', '0.00') }}" 
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
                        <!-- <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="bi bi-shield-check text-success me-1"></i> Estimates generated live
                        </span> -->
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary px-4 fw-semibold">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm" id="btnSubmitQuotation">
                            <i class="bi bi-file-earmark-check me-1"></i> Save Quotation
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
    let qServiceIndex = 0;
    let qChargeIndex = 0;
    let selectedPackageData = null;

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

    let qPkgIndex = 0;
    let currentPickerPkgData = null;

    // Category Change -> Cascade Sub-Categories (Packages)
    function onPkgCategoryChange(categoryId) {
        const subSelect = document.getElementById('pkgSubCategoryId');
        subSelect.innerHTML = '<option value="">-- Loading Packages... --</option>';
        document.getElementById('pkgPriceInput').value = '0.00';
        document.getElementById('pkgPickerServicesPreview').classList.add('d-none');
        document.getElementById('pkgPickerServicesList').innerHTML = '';
        currentPickerPkgData = null;

        if (!categoryId) {
            subSelect.innerHTML = '<option value="">-- Select Category First --</option>';
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
            })
            .catch(() => {
                subSelect.innerHTML = '<option value="">Error loading packages</option>';
            });
    }

    // Sub-Category Change -> Load Package Details & Included Deliverables
    function onPkgSubCategoryChange(subCategoryId) {
        if (!subCategoryId) {
            document.getElementById('pkgPriceInput').value = '0.00';
            document.getElementById('pkgPickerServicesPreview').classList.add('d-none');
            document.getElementById('pkgPickerServicesList').innerHTML = '';
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

                const previewBox = document.getElementById('pkgPickerServicesPreview');
                const listEl = document.getElementById('pkgPickerServicesList');
                listEl.innerHTML = '';

                if (data.services && data.services.length > 0) {
                    previewBox.classList.remove('d-none');
                    data.services.forEach(s => {
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-white border text-dark fw-normal';
                        badge.textContent = `${s.service_name} (x${s.qty})`;
                        listEl.appendChild(badge);
                    });
                } else {
                    previewBox.classList.add('d-none');
                }
            })
            .catch(err => {
                console.error('Error fetching package details:', err);
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
        const categoryName = catSelect.options[catSelect.selectedIndex].text.trim();
        const unitPrice = parseFloat(document.getElementById('pkgPriceInput').value) || 0;
        const qty = parseInt(document.getElementById('pkgQtyInput').value) || 1;
        const deliverables = (currentPickerPkgData && currentPickerPkgData.services) ? currentPickerPkgData.services : [];

        addQuotationPackageRow({
            event_sub_category_id: subId,
            package_name: packageName,
            category_name: categoryName,
            unit_price: unitPrice,
            qty: qty,
            deliverables: deliverables
        });

        // Reset picker selection
        subSelect.value = '';
        document.getElementById('pkgPriceInput').value = '0.00';
        document.getElementById('pkgQtyInput').value = '1';
        document.getElementById('pkgPickerServicesPreview').classList.add('d-none');
        document.getElementById('pkgPickerServicesList').innerHTML = '';
        currentPickerPkgData = null;
    }

    function addQuotationPackageRow(pkg) {
        const tbody = document.getElementById('quotationPackagesTbody');
        const emptyMsg = document.getElementById('noPackagesMsg');
        if (emptyMsg) emptyMsg.classList.add('d-none');

        const idx = qPkgIndex++;
        const lineTotal = (parseFloat(pkg.unit_price) || 0) * (parseInt(pkg.qty) || 1);

        let deliverableBadgesHtml = '';
        if (pkg.deliverables && pkg.deliverables.length > 0) {
            deliverableBadgesHtml = '<div class="d-flex flex-wrap gap-1 mt-1">';
            pkg.deliverables.forEach(d => {
                const sName = d.service_name || d.name || 'Item';
                const sQty = d.qty || 1;
                deliverableBadgesHtml += `<span class="badge bg-light text-secondary border fw-normal" style="font-size: 10.5px;"><i class="bi bi-check2"></i> ${sName} (x${sQty})</span>`;
            });
            deliverableBadgesHtml += '</div>';
        }

        const tr = document.createElement('tr');
        tr.id = `qpkg_row_${idx}`;
        tr.className = 'qpkg-row';
        tr.innerHTML = `
            <td>
                <div class="fw-bold text-dark">${pkg.package_name}</div>
                ${pkg.category_name ? `<div class="small text-muted"><i class="bi bi-tag me-1"></i>${pkg.category_name}</div>` : ''}
                ${deliverableBadgesHtml}
                <input type="hidden" name="packages[${idx}][event_sub_category_id]" value="${pkg.event_sub_category_id || ''}">
                <input type="hidden" name="packages[${idx}][package_name]" value="${pkg.package_name}">
                <input type="hidden" name="packages[${idx}][category_name]" value="${pkg.category_name || ''}">
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

    // Add Quotation Service Row
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

    // Add Other Charge Row
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
                <input type="text" name="other_charges[${idx}][description]" class="form-control form-control-sm" value="${description}" placeholder="Charge description (e.g. Transport, Hall Fee)..." required>
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

    // Calculate Quotation Totals
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
        calculateQuotationTotals();
    });
</script>
@endsection