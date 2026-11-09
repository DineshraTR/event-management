@extends('layouts.app')
@section('title', 'Create Order')
@section('page-title', 'Create Order')

@section('content')
<style>
    /* Custom Styling for Order Creation */
    .order-creator-card {
        border-radius: 16px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
        background: #ffffff;
        overflow: hidden;
    }

    .order-header-banner {
        background: #ffffff;
        border-bottom: 1px solid #edf2f7;
        padding: 16px 22px;
    }

    .order-icon-box {
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
    .order-action-bar {
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
</style>

<div class="container-fluid px-0">
    <div class="order-creator-card mb-4">
        <!-- Card Header -->
        <div class="order-header-banner d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="order-icon-box">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark" style="font-size: 15.5px; letter-spacing: -0.2px;">Create Customer Order</h5>
                    <small class="text-muted" style="font-size: 12px;">Select an event, review bundled package services, add extra items and finalize invoice items.</small>
                </div>
            </div>
            <div>
                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1.5 px-3">
                    <i class="bi bi-arrow-left"></i> <span>Back to Orders</span>
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

            <form method="POST" action="{{ route('orders.store') }}" id="orderCreateForm">
                @csrf

                <!-- Section 1: Customer & Event Selection -->
                <div class="custom-section-box">
                    <div class="form-section-title">
                        <i class="bi bi-calendar2-event-fill"></i>
                        <span>Select Event & Customer</span>
                    </div>

                    <div class="row g-3">
                        <!-- Customer Selection -->
                        <div class="col-md-5">
                            <label class="custom-input-label">
                                <i class="bi bi-person-circle"></i>
                                Customer <span class="text-danger">*</span>
                            </label>
                            <select name="customer_id" id="orderCustomerId" class="form-select" required onchange="onOrderCustomerChange(this.value)">
                                <option value="">-- Select Customer --</option>
                                @foreach($customers as $cust)
                                    <option value="{{ $cust->id }}" {{ (old('customer_id', $selectedEvent?->customer_id) == $cust->id) ? 'selected' : '' }}>
                                        {{ $cust->name }} ({{ $cust->phone ?: 'No phone' }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Linked customer profile for official billing and receipt generation.</small>
                        </div>
                        
                        <!-- Event Selection -->
                        <div class="col-md-5">
                            <label class="custom-input-label">
                                <i class="bi bi-calendar-check"></i>
                                Select Registered Event <span class="text-danger">*</span>
                            </label>
                            <select name="event_id" id="orderEventId" class="form-select" required onchange="onOrderEventChange(this.value)">
                                <option value="">-- Choose Registered Event --</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ (old('event_id', $selectedEvent?->id) == $event->id) ? 'selected' : '' }} data-customer-id="{{ $event->customer_id }}">
                                        {{ $event->event_no }} &bull; {{ $event->event_name }} ({{ $event->customer?->name }}) - {{ $event->event_date?->format('M d, Y') }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Selecting an event auto-populates its customer, package deliverables, and charges.</small>
                        </div>

                        <!-- Order Date -->
                        <div class="col-md-2">
                            <label class="custom-input-label">
                                <i class="bi bi-calendar-date"></i>
                                Order Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="order_date" class="form-control" value="{{ old('order_date', $today) }}" required>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Package Selection & Pricing (Multi-Package Table) -->
                @php
                    $initOrderPackages = [];
                    if (old('packages')) {
                        $initOrderPackages = old('packages');
                    } elseif (isset($selectedEvent) && $selectedEvent) {
                        if ($selectedEvent->packages && $selectedEvent->packages->isNotEmpty()) {
                            $initOrderPackages = $selectedEvent->packages->map(fn($p) => [
                                'event_package_id' => $p->id,
                                'event_sub_category_id' => $p->event_sub_category_id,
                                'category_name' => $p->subCategory?->category?->name ?? 'General',
                                'package_name' => $p->package_name,
                                'description' => $p->description ?? $p->subCategory?->description ?? '',
                                'cost_price' => (float)$p->cost_price,
                                'sell_price' => (float)$p->sell_price,
                                'qty' => $p->qty ?? 1,
                                'total_price' => (float)$p->total_price,
                            ])->toArray();
                        } elseif ($selectedEvent->subCategory || $selectedEvent->package_sell_price > 0) {
                            $initOrderPackages = [[
                                'event_package_id' => null,
                                'event_sub_category_id' => $selectedEvent->event_sub_category_id,
                                'category_name' => $selectedEvent->subCategory?->category?->name ?? 'General',
                                'package_name' => $selectedEvent->package_name ?? $selectedEvent->subCategory?->name ?? 'Event Package',
                                'description' => $selectedEvent->subCategory?->description ?? '',
                                'cost_price' => (float)($selectedEvent->package_cost_price ?? 0),
                                'sell_price' => (float)($selectedEvent->package_sell_price ?? 0),
                                'qty' => 1,
                                'total_price' => (float)($selectedEvent->package_sell_price ?? 0),
                            ]];
                        }
                    }
                @endphp
                <div class="custom-section-box">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div class="form-section-title mb-0">
                            <i class="bi bi-box-seam-fill"></i>
                            <span>Package Selection & Pricing</span>
                        </div>
                        <span class="badge bg-secondary-subtle text-secondary border px-2.5 py-1">
                            <i class="bi bi-layers-fill me-1"></i> Multi-Package Supported
                        </span>
                    </div>

                    <!-- Package Selector Bar (Add Extra Package to Order) -->
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="custom-input-label mb-1">
                                    <i class="bi bi-folder2-open"></i> Category
                                </label>
                                <select id="orderPkgCategoryId" class="form-select form-select-sm" onchange="onOrderPkgCategoryChange(this.value)">
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
                                <select id="orderPkgSubCategoryId" class="form-select form-select-sm" onchange="onOrderPkgSubCategoryChange(this.value)">
                                    <option value="">-- Select Category First --</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="custom-input-label mb-1">
                                    <i class="bi bi-cash-stack"></i> Price (Rs.)
                                </label>
                                <input type="number" step="0.01" min="0" id="orderPkgPriceInput" class="form-control form-control-sm text-end fw-semibold" value="0.00">
                            </div>
                            <div class="col-md-1">
                                <label class="custom-input-label mb-1">
                                    Qty
                                </label>
                                <input type="number" min="1" step="1" id="orderPkgQtyInput" class="form-control form-control-sm text-center fw-semibold" value="1">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-dark w-100 fw-bold d-inline-flex align-items-center justify-content-center gap-1" style="height: 33px;" onclick="addSelectedOrderPackage()">
                                    <i class="bi bi-plus-circle"></i> Add Package
                                </button>
                            </div>
                        </div>
                        <!-- Live preview of description & bundled services for currently selected package in picker -->
                        <div id="orderPickerPkgServicesPreview" class="d-none mt-2 pt-2 border-top">
                            <div id="orderPickerPkgServicesRow" class="mb-1 d-none">
                                <small class="text-muted fw-semibold me-2"><i class="bi bi-box-seam text-primary me-1"></i>Included Services:</small>
                                <span id="orderPickerPkgServicesPills" class="d-inline-flex flex-wrap gap-1"></span>
                            </div>
                            <div id="orderPickerPkgDescRow" class="d-none">
                                <small class="text-muted fw-semibold me-2"><i class="bi bi-card-text text-primary me-1"></i>Package Description:</small>
                                <div id="orderPickerPkgDescText" class="text-secondary small mt-1" style="white-space: pre-line;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden container for tracking removed event package IDs -->
                    <div id="removedEventPackagesContainer"></div>
                    <input type="hidden" name="package_sell_price" id="orderPackageSellPrice" value="{{ old('package_sell_price', '0.00') }}">
                    <input type="hidden" name="package_name" id="orderPackageNameInput" value="{{ old('package_name', '') }}">

                    <!-- Packages Table -->
                    <div class="modern-table-container">
                        <table class="table modern-table align-middle" id="orderPackagesTable">
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
                            <tbody id="orderPackagesTbody">
                                <!-- Dynamic Rows -->
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold text-secondary">Total Packages Price:</td>
                                    <td class="text-end pe-4 fw-bold text-primary fs-6" id="orderPackagesTotalDisplay">0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="noOrderPackagesMsg" class="text-center py-4 bg-white border border-top-0 rounded-bottom text-muted small d-none">
                        <i class="bi bi-info-circle me-1"></i> No packages in this order. Select an event or add packages using the picker above.
                    </div>
                </div>

                <!-- Section 3: Additional Services & Deliverables -->
                <div class="custom-section-box">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div class="form-section-title mb-0">
                            <i class="bi bi-stars"></i>
                            <span>Additional Services & Deliverables</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-light-gray fw-semibold px-3" onclick="addOrderServiceRow()">
                            <i class="bi bi-plus-lg me-1"></i> Add Service
                        </button>
                    </div>
                    <p class="text-muted small mb-3">Extra add-on services for this event. Quantities, descriptions, and prices can be freely adjusted.</p>

                    <div class="modern-table-container">
                        <table class="table modern-table align-middle" id="orderServicesTable">
                            <thead>
                                <tr>
                                    <th>Service Preset / Item Description</th>
                                    <th style="width: 110px;" class="text-center">Qty</th>
                                    <th style="width: 160px;" class="text-end">Unit Price (Rs.)</th>
                                    <th style="width: 170px;" class="text-end pe-4">Line Total (Rs.)</th>
                                    <th style="width: 50px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="orderServicesTbody">
                                <!-- Dynamic Rows -->
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold text-secondary">Services Subtotal:</td>
                                    <td class="text-end pe-4 fw-bold text-dark fs-6" id="orderServicesTotalDisplay">0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="noOrderServicesMsg" class="text-center py-4 bg-white border border-top-0 rounded-bottom text-muted small d-none">
                        <i class="bi bi-info-circle me-1"></i> No services added yet. Click <strong>"Add Service"</strong> to include order line items.
                    </div>
                </div>

                <!-- Section 3: Other Charges (Transport, Surcharges) -->
                <div class="custom-section-box">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div class="form-section-title mb-0">
                            <i class="bi bi-truck"></i>
                            <span>Other Charges & Surcharges</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-light-gray fw-semibold px-3" onclick="addOrderChargeRow()">
                            <i class="bi bi-plus-lg me-1"></i> Add Charge
                        </button>
                    </div>
                    <p class="text-muted small mb-3">Transport, venue permissions, logistical fees or damages deposit.</p>

                    <div class="modern-table-container">
                        <table class="table modern-table align-middle" id="orderChargesTable">
                            <thead>
                                <tr>
                                    <th>Charge Description</th>
                                    <th style="width: 200px;" class="text-end pe-4">Amount (Rs.)</th>
                                    <th style="width: 50px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="orderChargesTbody">
                                <!-- Dynamic Rows -->
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td class="text-end fw-bold text-secondary">Charges Subtotal:</td>
                                    <td class="text-end pe-4 fw-bold text-dark fs-6" id="orderChargesTotalDisplay">0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="noOrderChargesMsg" class="text-center py-4 bg-white border border-top-0 rounded-bottom text-muted small d-none">
                        <i class="bi bi-info-circle me-1"></i> No other charges added. Click <strong>"Add Charge"</strong> for logistics or surcharges.
                    </div>
                </div>

                <!-- Section 4: Discount, Notes & Final Grand Total -->
                <div class="custom-section-box" style="border-color: #cbd5e1; background: #ffffff;">
                    <div class="form-section-title">
                        <i class="bi bi-calculator-fill"></i>
                        <span>Invoice Totals & Notes</span>
                    </div>

                    <div class="row g-4 align-items-start">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="custom-input-label">
                                    <i class="bi bi-card-text"></i>
                                    Order Notes / Terms & Conditions
                                </label>
                                <textarea name="notes" rows="4" class="form-control" placeholder="Specify payment terms, delivery schedule, special instructions or warranties...">{{ old('notes') }}</textarea>
                            </div>

                            <div class="p-3 bg-light rounded-3 border">
                                <div class="d-flex align-items-center gap-2 text-primary fw-bold small mb-1">
                                    <i class="bi bi-shield-check fs-5"></i> Billing Notice:
                                </div>
                                <p class="mb-0 text-muted small" style="line-height: 1.5;">
                                    This customer order generates an official invoice. Any payments recorded subsequently will automatically reduce the remaining customer balance.
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
                                    <span class="fw-semibold text-dark" id="calcServicesSum">Rs. 0.00</span>
                                </div>
                                <div class="financial-line-item">
                                    <span class="text-muted">Other Charges:</span>
                                    <span class="fw-semibold text-dark" id="calcChargesSum">Rs. 0.00</span>
                                </div>
                                <div class="financial-line-item pt-2 border-top">
                                    <span class="text-muted">Special Discount:</span>
                                    <div class="input-group input-group-sm" style="width: 160px;">
                                        <input type="number" step="0.01" min="0" name="discount" id="orderDiscountInput" 
                                               class="form-control text-end text-danger fw-bold" 
                                               value="{{ old('discount', '0.00') }}" 
                                               oninput="calculateOrderTotals()">
                                    </div>
                                </div>

                                <div class="financial-total-banner d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small fw-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">Total Order Value</div>
                                        <small class="text-muted">Net invoice amount</small>
                                    </div>
                                    <div class="fs-4 fw-bold text-primary" id="orderGrandTotalDisplay">
                                        Rs. 0.00
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sticky Action Footer Bar -->
                <div class="order-action-bar rounded-3">
                    <div class="d-flex align-items-center gap-2">
                        <!-- <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="bi bi-check-circle text-success me-1"></i> Order calculations verified live
                        </span> -->
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary px-4 fw-semibold">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm" id="btnSubmitOrder">
                            <i class="bi bi-check2-circle me-1"></i> Save Order
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
    const allEvents = @json($events);
    const initOrderPackages = @json($initOrderPackages ?? []);
    let orderPackageIndex = 0;
    let orderServiceIndex = 0;
    let orderChargeIndex = 0;
    let currentSelectedOrderPkgDetails = null;

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    // Category Change in Order Package Picker
    function onOrderPkgCategoryChange(categoryId) {
        const subSelect = document.getElementById('orderPkgSubCategoryId');
        subSelect.innerHTML = '<option value="">-- Loading Packages... --</option>';
        const previewBox = document.getElementById('orderPickerPkgServicesPreview');
        if (previewBox) previewBox.classList.add('d-none');
        const pillsSpan = document.getElementById('orderPickerPkgServicesPills');
        if (pillsSpan) pillsSpan.innerHTML = '';
        const descText = document.getElementById('orderPickerPkgDescText');
        if (descText) descText.innerText = '';
        const svcRow = document.getElementById('orderPickerPkgServicesRow');
        if (svcRow) svcRow.classList.add('d-none');
        const descRow = document.getElementById('orderPickerPkgDescRow');
        if (descRow) descRow.classList.add('d-none');
        currentSelectedOrderPkgDetails = null;

        if (!categoryId) {
            subSelect.innerHTML = '<option value="">-- Select Category First --</option>';
            subSelect.value = '';
            subSelect.selectedIndex = 0;
            if (typeof jQuery !== 'undefined' && jQuery(subSelect).hasClass('select2-hidden-accessible')) {
                jQuery(subSelect).trigger('change.select2');
            }
            document.getElementById('orderPkgPriceInput').value = '0.00';
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

    // Sub-Category Change in Order Package Picker
    function onOrderPkgSubCategoryChange(subCategoryId) {
        const previewBox = document.getElementById('orderPickerPkgServicesPreview');
        const pillsSpan = document.getElementById('orderPickerPkgServicesPills');
        const descText = document.getElementById('orderPickerPkgDescText');
        const svcRow = document.getElementById('orderPickerPkgServicesRow');
        const descRow = document.getElementById('orderPickerPkgDescRow');

        if (!subCategoryId) {
            document.getElementById('orderPkgPriceInput').value = '0.00';
            if (previewBox) previewBox.classList.add('d-none');
            if (pillsSpan) pillsSpan.innerHTML = '';
            if (descText) descText.innerText = '';
            if (svcRow) svcRow.classList.add('d-none');
            if (descRow) descRow.classList.add('d-none');
            currentSelectedOrderPkgDetails = null;
            return;
        }

        const subSelect = document.getElementById('orderPkgSubCategoryId');
        const selectedOpt = subSelect ? subSelect.options[subSelect.selectedIndex] : null;
        if (selectedOpt && selectedOpt.getAttribute('data-sell-price')) {
            document.getElementById('orderPkgPriceInput').value = parseFloat(selectedOpt.getAttribute('data-sell-price')).toFixed(2);
        }

        fetch(`/api/sub-category/${subCategoryId}`)
            .then(res => res.json())
            .then(data => {
                currentSelectedOrderPkgDetails = data;
                if (data.sell_price !== undefined && data.sell_price !== null) {
                    document.getElementById('orderPkgPriceInput').value = parseFloat(data.sell_price).toFixed(2);
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
                currentSelectedOrderPkgDetails = null;
            });
    }

    // Add Package from Picker to Order
    function addSelectedOrderPackage() {
        const catSelect = document.getElementById('orderPkgCategoryId');
        const subSelect = document.getElementById('orderPkgSubCategoryId');
        const priceInput = document.getElementById('orderPkgPriceInput');
        const qtyInput = document.getElementById('orderPkgQtyInput');

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
        const costPrice = currentSelectedOrderPkgDetails?.cost_price ? parseFloat(currentSelectedOrderPkgDetails.cost_price) : 0;
        const description = currentSelectedOrderPkgDetails?.description || '';

        addOrderPackageRow({
            event_package_id: null,
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
        currentSelectedOrderPkgDetails = null;

        const previewBox = document.getElementById('orderPickerPkgServicesPreview');
        if (previewBox) previewBox.classList.add('d-none');
        const pillsSpan = document.getElementById('orderPickerPkgServicesPills');
        if (pillsSpan) pillsSpan.innerHTML = '';
        const descText = document.getElementById('orderPickerPkgDescText');
        if (descText) descText.innerText = '';
        const svcRow = document.getElementById('orderPickerPkgServicesRow');
        if (svcRow) svcRow.classList.add('d-none');
        const descRow = document.getElementById('orderPickerPkgDescRow');
        if (descRow) descRow.classList.add('d-none');
    }

    // Render Order Package Row in Table
    function addOrderPackageRow(pkg) {
        const tbody = document.getElementById('orderPackagesTbody');
        const emptyMsg = document.getElementById('noOrderPackagesMsg');
        if (emptyMsg) emptyMsg.classList.add('d-none');

        const idx = orderPackageIndex++;
        const sellPrice = parseFloat(pkg.sell_price || 0);
        const qty = parseInt(pkg.qty || 1);
        const totalPrice = parseFloat(pkg.total_price || (sellPrice * qty));
        const pkgDesc = pkg.description || '';
        const eventPkgId = pkg.event_package_id || '';

        let descHtml = '';
        if (pkgDesc && pkgDesc.trim() !== '') {
            descHtml = `<div class="text-muted small mt-1" style="white-space: pre-line; font-size: 11.5px; line-height: 1.45;"><i class="bi bi-text-paragraph text-primary me-1"></i>${escapeHtml(pkgDesc)}</div>`;
        }

        const tr = document.createElement('tr');
        tr.id = `opkg_row_${idx}`;
        tr.innerHTML = `
            <td class="text-center text-muted fw-bold font-num">${tbody.children.length + 1}</td>
            <td>
                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                    <strong class="text-dark fs-6">${escapeHtml(pkg.package_name)}</strong>
                    ${pkg.category_name ? `<span class="badge bg-secondary-subtle text-secondary border small">${escapeHtml(pkg.category_name)}</span>` : ''}
                    ${eventPkgId ? `<span class="badge bg-primary-subtle text-primary border small" title="Synchronized from Event profile"><i class="bi bi-link-45deg"></i> From Event</span>` : `<span class="badge bg-success-subtle text-success border small" title="Custom package added in this order"><i class="bi bi-plus-circle"></i> Added In Order</span>`}
                </div>
                ${descHtml}
                <input type="hidden" name="packages[${idx}][event_package_id]" class="opkg-event-pkg-id" value="${eventPkgId}">
                <input type="hidden" name="packages[${idx}][event_sub_category_id]" value="${pkg.event_sub_category_id || ''}">
                <input type="hidden" name="packages[${idx}][package_name]" value="${escapeHtml(pkg.package_name)}">
                <input type="hidden" name="packages[${idx}][description]" value="${escapeHtml(pkgDesc)}">
                <input type="hidden" name="packages[${idx}][cost_price]" value="${pkg.cost_price || 0}">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="packages[${idx}][sell_price]" id="opkg_price_${idx}" class="form-control form-control-sm text-end fw-semibold" value="${sellPrice.toFixed(2)}" oninput="calcOrderPackageLine(${idx})">
            </td>
            <td>
                <input type="number" min="1" step="1" name="packages[${idx}][qty]" id="opkg_qty_${idx}" class="form-control form-control-sm text-center fw-semibold" value="${qty}" oninput="calcOrderPackageLine(${idx})">
            </td>
            <td class="text-end pe-4 fw-bold text-dark font-num fs-6" id="opkg_line_total_${idx}">
                ${totalPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                <input type="hidden" name="packages[${idx}][total_price]" id="opkg_total_input_${idx}" class="opkg-line-total-val" value="${totalPrice.toFixed(2)}">
            </td>
            <td class="text-center">
                <button type="button" class="btn-action btn-action-delete mx-auto" onclick="removeOrderPackageRow(${idx}, '${eventPkgId}')" title="${eventPkgId ? 'Remove package (will also delete from event in DB)' : 'Remove package'}">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        reindexOrderPackageRows();
        calculateOrderTotals();
    }

    function calcOrderPackageLine(idx) {
        const qty = parseInt(document.getElementById(`opkg_qty_${idx}`)?.value) || 1;
        const price = parseFloat(document.getElementById(`opkg_price_${idx}`)?.value) || 0;
        const lineTotal = qty * price;
        const cell = document.getElementById(`opkg_line_total_${idx}`);
        const hiddenTotal = document.getElementById(`opkg_total_input_${idx}`);
        if (cell) {
            cell.innerText = lineTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        if (hiddenTotal) {
            hiddenTotal.value = lineTotal.toFixed(2);
        }
        calculateOrderTotals();
    }

    function removeOrderPackageRow(idx, eventPackageId) {
        if (eventPackageId) {
            const container = document.getElementById('removedEventPackagesContainer');
            if (container) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'removed_event_package_ids[]';
                hiddenInput.value = eventPackageId;
                container.appendChild(hiddenInput);
            }
        }
        document.getElementById(`opkg_row_${idx}`)?.remove();
        reindexOrderPackageRows();
        calculateOrderTotals();
    }

    function reindexOrderPackageRows() {
        const tbody = document.getElementById('orderPackagesTbody');
        const rows = tbody.querySelectorAll('tr');
        const emptyMsg = document.getElementById('noOrderPackagesMsg');
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

    function onOrderCustomerChange(customerId) {
        filterEventsByCustomer(customerId);
    }

    function filterEventsByCustomer(customerId, targetEventId = null) {
        const eventSelect = document.getElementById('orderEventId');
        eventSelect.innerHTML = '';

        if (!customerId) {
            eventSelect.innerHTML = '<option value="">-- Select Customer First --</option>';
            resetOrderEventDetails();
            return;
        }

        const customerEvents = allEvents.filter(ev => String(ev.customer_id) === String(customerId));

        if (customerEvents.length === 0) {
            eventSelect.innerHTML = '<option value="">-- No registered events found for this customer --</option>';
            resetOrderEventDetails();
            return;
        }

        eventSelect.innerHTML = '<option value="">-- Choose Registered Event --</option>';
        customerEvents.forEach(ev => {
            const opt = document.createElement('option');
            opt.value = ev.id;
            const dateStr = ev.event_date ? new Date(ev.event_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '';
            opt.text = `${ev.event_no} • ${ev.event_name}${dateStr ? ' - ' + dateStr : ''}`;
            if (targetEventId && String(ev.id) === String(targetEventId)) {
                opt.selected = true;
            }
            eventSelect.appendChild(opt);
        });

        if (targetEventId) {
            eventSelect.value = targetEventId;
        } else if (customerEvents.length === 1) {
            eventSelect.value = customerEvents[0].id;
            onOrderEventChange(customerEvents[0].id);
        } else {
            resetOrderEventDetails();
        }
    }

    function resetOrderEventDetails() {
        document.getElementById('orderPackagesTbody').innerHTML = '';
        document.getElementById('removedEventPackagesContainer').innerHTML = '';
        document.getElementById('orderPackageSellPrice').value = '0.00';
        document.getElementById('orderPackageNameInput').value = '';
        orderPackageIndex = 0;
        reindexOrderPackageRows();

        document.getElementById('orderServicesTbody').innerHTML = '';
        document.getElementById('orderChargesTbody').innerHTML = '';
        orderServiceIndex = 0;
        orderChargeIndex = 0;
        const noSvc = document.getElementById('noOrderServicesMsg');
        if (noSvc) noSvc.classList.remove('d-none');
        const noCh = document.getElementById('noOrderChargesMsg');
        if (noCh) noCh.classList.remove('d-none');
        calculateOrderTotals();
    }

    function onOrderEventChange(eventId) {
        if (!eventId) {
            resetOrderEventDetails();
            return;
        }

        fetch(`/api/event/${eventId}/order-details`)
            .then(res => res.json())
            .then(data => {
                // Pre-select customer
                if (data.customer_id) {
                    document.getElementById('orderCustomerId').value = data.customer_id;
                }

                // Clear and populate packages
                document.getElementById('orderPackagesTbody').innerHTML = '';
                document.getElementById('removedEventPackagesContainer').innerHTML = '';
                orderPackageIndex = 0;

                if (data.packages && data.packages.length > 0) {
                    data.packages.forEach(pkg => {
                        addOrderPackageRow(pkg);
                    });
                } else if (data.package_name || (data.package_sell_price && parseFloat(data.package_sell_price) > 0)) {
                    addOrderPackageRow({
                        event_package_id: null,
                        event_sub_category_id: null,
                        category_name: data.category_name || 'General Category',
                        package_name: data.package_name || 'Event Package',
                        cost_price: 0,
                        sell_price: parseFloat(data.package_sell_price || 0),
                        qty: 1,
                        total_price: parseFloat(data.package_sell_price || 0),
                        services: data.package_services || []
                    });
                } else {
                    reindexOrderPackageRows();
                }

                // Set discount if any
                if (data.discount) {
                    document.getElementById('orderDiscountInput').value = data.discount;
                }

                // Clear current services & charges tables
                document.getElementById('orderServicesTbody').innerHTML = '';
                document.getElementById('orderChargesTbody').innerHTML = '';
                orderServiceIndex = 0;
                orderChargeIndex = 0;

                // Add extra / additional services
                if (data.additional_services && data.additional_services.length > 0) {
                    data.additional_services.forEach(svc => {
                        addOrderServiceRowCustom(svc.description, svc.qty || 1, svc.unit_price, svc.service_id);
                    });
                }

                // Add other charges
                if (data.other_charges && data.other_charges.length > 0) {
                    data.other_charges.forEach(ch => {
                        addOrderChargeRowCustom(ch.description, ch.amount);
                    });
                }

                calculateOrderTotals();
            });
    }

    function addOrderServiceRow() {
        addOrderServiceRowCustom('', 1, 0, null);
    }

    function addOrderServiceRowCustom(description = '', qty = 1, unitPrice = 0, serviceId = null) {
        const tbody = document.getElementById('orderServicesTbody');
        const emptyMsg = document.getElementById('noOrderServicesMsg');
        if (emptyMsg) emptyMsg.classList.add('d-none');

        const idx = orderServiceIndex++;

        let serviceOptions = '<option value="">-- Custom Service / Preset --</option>';
        availableServices.forEach(svc => {
            serviceOptions += `<option value="${svc.id}" data-price="${svc.cost_price}" data-name="${svc.name}" ${serviceId == svc.id ? 'selected' : ''}>${svc.name}</option>`;
        });

        const tr = document.createElement('tr');
        tr.id = `osvc_row_${idx}`;
        tr.innerHTML = `
            <td>
                <div class="row g-1">
                    <div class="col-md-5">
                        <select class="form-select form-select-sm" onchange="onSelectOrderServicePreset(this, ${idx})">
                            ${serviceOptions}
                        </select>
                    </div>
                    <div class="col-md-7">
                        <input type="hidden" name="services[${idx}][service_id]" id="osvc_id_${idx}" value="${serviceId || ''}">
                        <input type="text" name="services[${idx}][description]" id="osvc_desc_${idx}" class="form-control form-control-sm" value="${description}" placeholder="Description of service..." required>
                    </div>
                </div>
            </td>
            <td>
                <input type="number" min="1" step="1" name="services[${idx}][qty]" id="osvc_qty_${idx}" class="form-control form-control-sm text-center" value="${qty}" oninput="calcOrderServiceLine(${idx})">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="services[${idx}][unit_price]" id="osvc_price_${idx}" class="form-control form-control-sm text-end" value="${parseFloat(unitPrice).toFixed(2)}" oninput="calcOrderServiceLine(${idx})">
            </td>
            <td class="text-end pe-4 fw-bold text-dark" id="osvc_line_total_${idx}">
                ${(qty * unitPrice).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
            </td>
            <td class="text-center">
                <button type="button" class="btn-action btn-action-delete mx-auto" onclick="removeOrderServiceRow(${idx})" title="Remove service">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        calcOrderServiceLine(idx);
    }

    function onSelectOrderServicePreset(selectEl, idx) {
        const selectedOpt = selectEl.options[selectEl.selectedIndex];
        const svcId = selectedOpt.value;
        const svcName = selectedOpt.getAttribute('data-name');
        const svcPrice = selectedOpt.getAttribute('data-price');

        if (svcId) {
            document.getElementById(`osvc_id_${idx}`).value = svcId;
            document.getElementById(`osvc_desc_${idx}`).value = svcName;
            document.getElementById(`osvc_price_${idx}`).value = svcPrice || '0.00';
        }
        calcOrderServiceLine(idx);
    }

    function calcOrderServiceLine(idx) {
        const qty = parseInt(document.getElementById(`osvc_qty_${idx}`)?.value) || 1;
        const price = parseFloat(document.getElementById(`osvc_price_${idx}`)?.value) || 0;
        const lineTotal = qty * price;
        const cell = document.getElementById(`osvc_line_total_${idx}`);
        if (cell) {
            cell.innerText = lineTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        calculateOrderTotals();
    }

    function removeOrderServiceRow(idx) {
        document.getElementById(`osvc_row_${idx}`)?.remove();
        const tbody = document.getElementById('orderServicesTbody');
        const emptyMsg = document.getElementById('noOrderServicesMsg');
        if (tbody.querySelectorAll('tr').length === 0 && emptyMsg) {
            emptyMsg.classList.remove('d-none');
        }
        calculateOrderTotals();
    }

    function addOrderChargeRow() {
        addOrderChargeRowCustom('', 0);
    }

    function addOrderChargeRowCustom(description = '', amount = 0) {
        const tbody = document.getElementById('orderChargesTbody');
        const emptyMsg = document.getElementById('noOrderChargesMsg');
        if (emptyMsg) emptyMsg.classList.add('d-none');

        const idx = orderChargeIndex++;

        const tr = document.createElement('tr');
        tr.id = `ocharge_row_${idx}`;
        tr.innerHTML = `
            <td>
                <input type="text" name="other_charges[${idx}][description]" class="form-control form-control-sm" value="${description}" placeholder="Charge description..." required>
            </td>
            <td class="pe-4">
                <input type="number" step="0.01" min="0" name="other_charges[${idx}][amount]" class="form-control form-control-sm text-end order-charge-amount-input" value="${parseFloat(amount).toFixed(2)}" oninput="calculateOrderTotals()">
            </td>
            <td class="text-center">
                <button type="button" class="btn-action btn-action-delete mx-auto" onclick="removeOrderChargeRow(${idx})" title="Remove charge">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        calculateOrderTotals();
    }

    function removeOrderChargeRow(idx) {
        document.getElementById(`ocharge_row_${idx}`)?.remove();
        const tbody = document.getElementById('orderChargesTbody');
        const emptyMsg = document.getElementById('noOrderChargesMsg');
        if (tbody.querySelectorAll('tr').length === 0 && emptyMsg) {
            emptyMsg.classList.remove('d-none');
        }
        calculateOrderTotals();
    }

    function calculateOrderTotals() {
        let packagesTotal = 0;
        document.querySelectorAll('#orderPackagesTbody tr').forEach(row => {
            const lineVal = parseFloat(row.querySelector('.opkg-line-total-val')?.value) || 0;
            packagesTotal += lineVal;
        });

        const pkgSellInput = document.getElementById('orderPackageSellPrice');
        if (pkgSellInput) {
            pkgSellInput.value = packagesTotal.toFixed(2);
        }
        const pkgDisplay = document.getElementById('orderPackagesTotalDisplay');
        if (pkgDisplay) {
            pkgDisplay.innerText = packagesTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        let servicesTotal = 0;
        document.querySelectorAll('#orderServicesTbody tr').forEach(row => {
            const qty = parseInt(row.querySelector('[id^="osvc_qty_"]')?.value) || 0;
            const price = parseFloat(row.querySelector('[id^="osvc_price_"]')?.value) || 0;
            servicesTotal += qty * price;
        });

        let chargesTotal = 0;
        document.querySelectorAll('.order-charge-amount-input').forEach(input => {
            chargesTotal += parseFloat(input.value) || 0;
        });

        const discount = parseFloat(document.getElementById('orderDiscountInput')?.value) || 0;
        const subTotal = packagesTotal + servicesTotal + chargesTotal;
        const grandTotal = Math.max(0, subTotal - discount);

        document.getElementById('orderServicesTotalDisplay').innerText = servicesTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('orderChargesTotalDisplay').innerText = chargesTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        if (document.getElementById('calcPackageSum')) {
            document.getElementById('calcPackageSum').innerText = 'Rs. ' + packagesTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        document.getElementById('calcServicesSum').innerText = 'Rs. ' + servicesTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('calcChargesSum').innerText = 'Rs. ' + chargesTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('orderGrandTotalDisplay').innerText = 'Rs. ' + grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Initialize on load if customer or event already selected
    document.addEventListener('DOMContentLoaded', () => {
        const initialCustomerId = document.getElementById('orderCustomerId').value;
        const initialEventId = '{{ old("event_id", $selectedEvent?->id ?? "") }}';

        if (initialCustomerId) {
            filterEventsByCustomer(initialCustomerId, initialEventId);
            if (initialEventId) {
                onOrderEventChange(initialEventId);
            }
        } else if (initialEventId) {
            const matchedEv = allEvents.find(e => String(e.id) === String(initialEventId));
            if (matchedEv) {
                document.getElementById('orderCustomerId').value = matchedEv.customer_id;
                filterEventsByCustomer(matchedEv.customer_id, initialEventId);
                onOrderEventChange(initialEventId);
            }
        } else {
            document.getElementById('orderEventId').innerHTML = '<option value="">-- Select Customer First --</option>';
        }
    });
</script>
@endsection