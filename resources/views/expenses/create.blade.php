@extends('layouts.app')
@section('title', 'Record Expense')
@section('page-title', 'Record Expense')

@section('content')
<style>
    .expense-type-card {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #ffffff;
        position: relative;
    }
    .expense-type-card:hover {
        border-color: #cbd5e1;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .expense-type-card.active {
        border-color: #484646;
        background: #f8fafc;
        box-shadow: 0 4px 14px rgba(72, 70, 70, 0.12);
    }
    .expense-type-card.active .type-icon {
        background: #484646;
        color: #ffffff;
    }
    .type-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #e2e8f0;
        color: #484646;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 8px;
        transition: all 0.2s ease;
    }

    /* Customer Order Card (Direct Expense) */
    .order-select-card {
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 14px;
        cursor: pointer;
        transition: all 0.15s ease;
        background: #ffffff;
    }
    .order-select-card:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    .order-select-card.selected {
        border-color: #16a34a;
        background: #f0fdf4;
        box-shadow: 0 2px 8px rgba(22, 163, 74, 0.15);
    }

    .form-section-title {
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 8px;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 16px;
    }
    .form-section-badge {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #484646;
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .amount-input-box {
        font-size: 20px !important;
        font-weight: 700 !important;
        color: #dc2626 !important;
        letter-spacing: -0.3px;
    }
    #normalCategoryWrapper .select2-container,
    #sectionNormalExpense .select2-container {
        width: 100% !important;
    }
</style>

<!-- Page Header (Tritcal Style) -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0"
            style="width: 44px; height: 44px; background: linear-gradient(135deg, #484646 0%, #3a3838 100%); font-size: 20px;">
            <i class="bi bi-wallet2"></i>
        </div>
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.3px;">Record Expense</h4>
                <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 11px; font-weight: 600;">
                    Voucher Entry
                </span>
            </div>
            <p class="text-muted small mb-0">Record direct event-related expenditures or general company operational expenses.</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('reports.expenses') }}" class="btn btn-outline-secondary shadow-sm" target="_blank">
            <i class="bi bi-receipt-cutoff me-1 text-primary"></i> View Expenses Report
        </a>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary shadow-sm" target="_blank">
            <i class="bi bi-box-seam me-1"></i> Orders Ledger
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 shadow-sm" role="alert" style="border-radius: 10px;">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div>
            <strong>Success!</strong> {{ session('success') }}
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert" style="border-radius: 10px;">
        <div class="d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong>Please resolve the following errors:</strong>
        </div>
        <ul class="mb-0 small ps-4">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Main Form Container -->
<div class="row justify-content-center">
    <div class="col-12 col-lg-10 col-xl-9">
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 14px; border: 1px solid #edf2f7 !important;">
            
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 14px;">
                        <i class="bi bi-pencil-square text-primary"></i> Expense Entry Terminal
                    </h6>
                    <small class="text-muted" style="font-size: 11.5px;">Choose the expense type and fill in the payment details.</small>
                </div>
                <span class="badge bg-light text-dark border px-2.5 py-1" style="font-size: 11px;">
                    <i class="bi bi-calendar3 me-1"></i> {{ date('d M Y') }}
                </span>
            </div>

            <form method="POST" action="{{ route('expenses.store') }}" id="expenseForm">
                @csrf
                <div class="card-body p-4">

                    <!-- ============================================== -->
                    <!-- EXPENSE TYPE SELECTOR: DIRECT VS NORMAL        -->
                    <!-- ============================================== -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-dark mb-2">
                            Select Expense Classification <span class="text-danger">*</span>
                        </label>
                        <input type="hidden" name="type" id="expenseTypeInput" value="{{ old('type', 'direct') }}">

                        <div class="row g-3">
                            <!-- Option 1: Direct Expense -->
                            <div class="col-12 col-md-6">
                                <div class="expense-type-card {{ old('type', 'direct') === 'direct' ? 'active' : '' }}" 
                                     id="cardTypeDirect" onclick="switchExpenseType('direct')">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="type-icon flex-shrink-0">
                                            <i class="bi bi-calendar-event"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark" style="font-size: 13.5px;">Direct Event Expense</h6>
                                            <p class="text-muted small mb-0" style="font-size: 11.5px; line-height: 1.4;">
                                                Incurred directly for a customer event or order (e.g. Venue, Catering, Sounds, Decor).
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Option 2: Normal Expense -->
                            <div class="col-12 col-md-6">
                                <div class="expense-type-card {{ old('type') === 'normal' ? 'active' : '' }}" 
                                     id="cardTypeNormal" onclick="switchExpenseType('normal')">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="type-icon flex-shrink-0">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark" style="font-size: 13.5px;">Normal Operational Expense</h6>
                                            <p class="text-muted small mb-0" style="font-size: 11.5px; line-height: 1.4;">
                                                General business overheads (e.g. Office Rent, Electricity, Staff, Fuel, Stationery).
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- SECTION: DIRECT EXPENSE (Customer & Orders)    -->
                    <!-- ============================================== -->
                    <div id="sectionDirectExpense" class="{{ old('type', 'direct') === 'direct' ? '' : 'd-none' }}">
                        <div class="form-section-title">
                            <span class="form-section-badge">1</span>
                            <span>Customer & Related Order Information</span>
                        </div>

                        <!-- Customer Select -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label small fw-bold text-dark mb-0">
                                    Customer <span class="text-danger">*</span>
                                </label>
                                <button type="button" class="btn btn-sm btn-light border py-0 px-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#quickCustomerModal" style="font-size: 11px; height: 25px;">
                                    <i class="bi bi-person-plus-fill me-1 text-primary"></i> Quick Add Customer
                                </button>
                            </div>
                            <select id="directCustomerId" class="form-select searchable-select" onchange="onDirectCustomerChange(this.value)">
                                <option value="">-- Select Customer --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ (old('customer_id', $preselectedCustomerId) == $c->id) ? 'selected' : '' }}>
                                        {{ $c->name }} ({{ $c->phone ?: 'No phone' }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                <i class="bi bi-info-circle me-1"></i> Select customer to inspect their active orders.
                            </small>
                        </div>

                        <!-- Customer Orders Container (Appears in front) -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Select Target Order / Event <span class="text-danger">*</span>
                            </label>

                            <!-- Placeholder when no customer selected -->
                            <div id="ordersEmptyPlaceholder" class="p-3 text-center border rounded-3 bg-light text-muted small" style="border-style: dashed !important;">
                                <i class="bi bi-arrow-up-circle me-1"></i> Please select a customer above to view and link their orders.
                            </div>

                            <!-- Dynamic Orders Grid -->
                            <div id="customerOrdersList" class="d-none">
                                <div class="row g-2" id="ordersGridCards">
                                    <!-- Populated via JavaScript -->
                                </div>
                            </div>

                            <!-- Hidden inputs for selected order and event -->
                            <input type="hidden" name="order_id" id="selectedOrderId" value="{{ old('order_id', $preselectedOrderId) }}">
                            <input type="hidden" name="event_id" id="selectedEventId" value="{{ old('event_id') }}">
                        </div>

                    </div>

                    <!-- ============================================== -->
                    <!-- SECTION: EXPENSE CATEGORY & AMOUNT ROW         -->
                    <!-- ============================================== -->
                    <div class="row g-3 mb-3 align-items-start">
                        <!-- Category Selector (Direct or Normal) -->
                        <div class="col-12 col-md-6">
                            <!-- Direct Expense Category (visible when Direct) -->
                            <div id="directCategoryWrapper" class="{{ old('type', 'direct') === 'direct' ? '' : 'd-none' }}">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    Direct Expense Category / Type <span class="text-danger">*</span>
                                </label>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="flex: 1 1 auto; min-width: 0;">
                                        <select name="direct_category" id="directCategorySelect" class="form-select" onchange="syncDirectCategory(this.value)">
                                            <option value="Venue & Hall Hire">Venue & Hall Hire</option>
                                            <option value="Catering & Food Service">Catering & Food Service</option>
                                            <option value="Decorations & Florist">Decorations & Florist</option>
                                            <option value="Sounds, DJ & Lighting">Sounds, DJ & Lighting</option>
                                            <option value="Photography & Videography">Photography & Videography</option>
                                            <option value="Vehicle & Transport Logistics">Vehicle & Transport Logistics</option>
                                            <option value="Equipment & Prop Rental">Equipment & Prop Rental</option>
                                            <option value="Event Crew & Daily Wages">Event Crew & Daily Wages</option>
                                            <option value="Cake & Confectionery">Cake & Confectionery</option>
                                            <option value="Entertainment & Artists">Entertainment & Artists</option>
                                            <option value="Other Direct Cost">Other Direct Cost</option>
                                            @foreach($categories as $cat)
                                                @if(!in_array($cat->name, [
                                                    'Venue & Hall Hire', 'Catering & Food Service', 'Decorations & Florist',
                                                    'Sounds, DJ & Lighting', 'Photography & Videography', 'Vehicle & Transport Logistics',
                                                    'Equipment & Prop Rental', 'Event Crew & Daily Wages', 'Cake & Confectionery',
                                                    'Entertainment & Artists', 'Other Direct Cost'
                                                ]))
                                                    <option value="{{ $cat->name }}" {{ old('category') == $cat->name ? 'selected' : '' }}>
                                                        {{ $cat->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-primary d-flex align-items-center gap-1.5 px-3 flex-shrink-0 text-nowrap text-white" 
                                            data-bs-toggle="modal" data-bs-target="#addCategoryModal" title="Create New Category" style="height: 38px; border-radius: 8px;">
                                        <i class="bi bi-plus-lg text-white"></i>
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">Categorizes the direct event cost in profit & loss accounts.</small>
                            </div>

                            <!-- Normal Operational Expense Category (visible when Normal) -->
                            <div id="normalCategoryWrapper" class="{{ old('type') === 'normal' ? '' : 'd-none' }}">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    Expense Category <span class="text-danger">*</span>
                                </label>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="flex: 1 1 auto; min-width: 0;">
                                        <select name="normal_category" id="normalCategorySelect" class="form-select" onchange="syncNormalCategory(this.value)">
                                            <option value="">-- Choose Category --</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->name }}" {{ old('category') == $cat->name ? 'selected' : '' }}>
                                                    {{ $cat->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-primary d-flex align-items-center gap-1.5 px-3 flex-shrink-0 text-nowrap text-white" 
                                            data-bs-toggle="modal" data-bs-target="#addCategoryModal" title="Create New Category" style="height: 38px; border-radius: 8px;">
                                        <i class="bi bi-plus-lg text-white"></i>
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                    Click <strong>+</strong> to add a custom category via popup without leaving this page.
                                </small>
                            </div>
                        </div>

                        <!-- Amount to Pay (Rs.) * (Right beside Expense Category) -->
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Amount to Pay (Rs.) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0.01" name="amount" class="form-control amount-input-box" 
                                    value="{{ old('amount') }}" placeholder="0.00" required style="height: 38px;">
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">Total disbursed voucher value.</small>
                        </div>
                    </div>

                    <!-- Hidden Unified Category Field submitted to server -->
                    <input type="hidden" name="category" id="unifiedCategoryInput" value="{{ old('category', 'Venue & Hall Hire') }}">

                    <!-- ============================================== -->
                    <!-- SECTION 2: PAYMENT & TRANSACTION DETAILS       -->
                    <!-- ============================================== -->
                    <div class="mt-4">
                        <div class="form-section-title">
                            <span class="form-section-badge">2</span>
                            <span>Payment & Transaction Details</span>
                        </div>

                        <div class="row g-3">
                            <!-- Description -->
                            <div class="col-12 col-md-8">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    Expense Description / Payee <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="description" class="form-control" 
                                    value="{{ old('description') }}" placeholder="e.g. Advance paid to Grand Monarch Hall, or Diesel for logistics van..." required>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">Clear description of what this payment was for.</small>
                            </div>

                            <!-- Expense Date -->
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    Expense Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', $today) }}" required>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">Date when expense was incurred.</small>
                            </div>

                            <!-- Payment Method -->
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    Payment Method <span class="text-danger">*</span>
                                </label>
                                <select name="payment_method" id="expensePaymentMethod" class="form-select fw-semibold" required onchange="onExpenseMethodChange(this.value)">
                                    <option value="cash" {{ old('payment_method', 'cash') === 'cash' ? 'selected' : '' }}>💵 Cash Payment</option>
                                    <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>🏦 Bank Transfer / Deposit</option>
                                    <option value="cheque" {{ old('payment_method') === 'cheque' ? 'selected' : '' }}>📑 Cheque Payment</option>
                                </select>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">Payment source for ledger posting.</small>
                            </div>

                            <!-- Dynamic Bank Account -->
                            <div class="col-12 col-md-8 d-none" id="expenseBankCol">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    <span id="bankLabelText">Company Bank Account</span> <span class="text-danger">*</span>
                                </label>
                                <div class="d-flex gap-2">
                                    <select name="bank_name" id="expenseBankSelect" class="form-select">
                                        <option value="">-- Choose Debited Bank Account --</option>
                                        @foreach($banks as $b)
                                            <option value="{{ $b->bank_name }} - {{ $b->account_number }} ({{ $b->branch }})"
                                                @selected(old('bank_name') == "{$b->bank_name} - {$b->account_number} ({$b->branch})")>
                                                {{ $b->bank_name }} &bull; {{ $b->account_number }} ({{ $b->branch ?: 'Main' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-outline-secondary d-flex align-items-center text-nowrap" style="font-size: 12px;" data-bs-toggle="modal" data-bs-target="#quickBankModal">
                                        <i class="bi bi-plus-lg me-1"></i> Add Bank
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">Account from which funds were withdrawn.</small>
                            </div>

                            <!-- Dynamic Cheque Number -->
                            <div class="col-12 col-md-4 d-none" id="expenseChequeCol">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    Cheque Serial Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="cheque_no" id="expenseChequeInput" class="form-control font-monospace" 
                                    value="{{ old('cheque_no') }}" placeholder="e.g. CHQ-901824">
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">Cheque serial leaf number.</small>
                            </div>

                            <!-- Reference / Voucher No -->
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    Reference / Voucher / Bill No
                                </label>
                                <input type="text" name="reference" class="form-control" value="{{ old('reference') }}" placeholder="e.g. INV-8819 or Slip #">
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">Optional vendor invoice or slip number.</small>
                            </div>

                            <!-- Notes / Remarks -->
                            <div class="col-12">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    Internal Notes / Remarks
                                </label>
                                <textarea name="notes" rows="2" class="form-control" placeholder="Any internal notes or vendor details...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Footer Action Buttons -->
                <div class="card-footer bg-light py-3 px-4 border-top d-flex align-items-center justify-content-between">
                    <button type="reset" class="btn btn-outline-secondary shadow-sm" onclick="resetExpenseForm()">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Form
                    </button>
                    <button type="submit" class="btn btn-primary shadow-sm px-4 py-2 fw-semibold">
                        <i class="bi bi-check2-circle me-1.5"></i> Confirm & Record Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- POPUP MODAL: ADD NEW EXPENSE CATEGORY          -->
<!-- ============================================== -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <h6 class="modal-title fw-bold text-white mb-0 d-flex align-items-center gap-2" id="addCategoryModalLabel">
                    <i class="bi bi-folder-plus text-warning"></i> Add New Expense Category
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickCategoryForm" onsubmit="event.preventDefault(); saveCategoryAjax();">
                <div class="modal-body p-4">
                    <div id="categoryModalAlert" class="alert alert-danger alert-permanent d-none py-2 px-3 small" style="border-radius: 8px;"></div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">
                            Category Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="modalCategoryName" class="form-control" placeholder="e.g. Office Refreshments, Staff Uniforms..." required autocomplete="off">
                        <small class="text-muted" style="font-size: 11px;">Unique category name for accounting & reporting.</small>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark mb-1">
                            Description (Optional)
                        </label>
                        <textarea id="modalCategoryDesc" class="form-control" rows="2" placeholder="Brief note about what this category covers..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-semibold" id="btnSaveCategory">
                        <i class="bi bi-check2 me-1"></i> Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- POPUP MODAL: QUICK ADD CUSTOMER                -->
<!-- ============================================== -->
<div class="modal fade" id="quickCustomerModal" tabindex="-1" aria-labelledby="quickCustomerModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <h6 class="modal-title fw-bold text-white mb-0 d-flex align-items-center gap-2" id="quickCustomerModalLabel">
                    <i class="bi bi-person-plus-fill text-warning"></i> Quick Add New Customer
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="quickCustomerAlert" class="alert alert-danger alert-permanent d-none py-2 px-3 small" style="border-radius: 8px;"></div>
                <form id="quickCustomerForm" onsubmit="event.preventDefault(); saveQuickCustomerAjax();">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" id="quickCustName" class="form-control" placeholder="e.g. Ruwan Silva" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Phone Number</label>
                        <input type="text" id="quickCustPhone" class="form-control" placeholder="e.g. +94 77 123 4567">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark mb-1">Email Address</label>
                        <input type="email" id="quickCustEmail" class="form-control" placeholder="e.g. ruwan@example.com">
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light py-2 px-4">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm fw-semibold" id="btnSaveQuickCust" onclick="saveQuickCustomerAjax()">
                    <i class="bi bi-check-lg me-1"></i> Save & Select Customer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- POPUP MODAL: QUICK ADD BANK ACCOUNT            -->
<!-- ============================================== -->
<div class="modal fade" id="quickBankModal" tabindex="-1" aria-labelledby="quickBankModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <h6 class="modal-title fw-bold text-white mb-0 d-flex align-items-center gap-2" id="quickBankModalLabel">
                    <i class="bi bi-bank text-warning"></i> Quick Register Bank Account
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="quickBankModalAlert" class="alert alert-danger alert-permanent d-none py-2 px-3 small" style="border-radius: 8px;"></div>
                <form id="quickBankForm" onsubmit="event.preventDefault(); saveQuickBankAjax('expenseBankSelect');">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Bank Name <span class="text-danger">*</span></label>
                        <input type="text" id="modalBankName" class="form-control" placeholder="e.g. Bank of Ceylon, Commercial Bank..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Account Holder Name <span class="text-danger">*</span></label>
                        <input type="text" id="modalAccountName" class="form-control" placeholder="e.g. PUBUDU ISHANTHA" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Account Number <span class="text-danger">*</span></label>
                        <input type="text" id="modalAccountNumber" class="form-control font-monospace" placeholder="e.g. 86952315" required>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark mb-1">Branch Name</label>
                            <input type="text" id="modalBranch" class="form-control" placeholder="e.g. Colombo Main">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark mb-1">Opening Balance (Rs.)</label>
                            <input type="number" step="0.01" min="0" id="modalOpeningBalance" class="form-control font-monospace" placeholder="0.00" value="0">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light py-2 px-4">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm fw-semibold" id="btnSaveQuickBank" onclick="saveQuickBankAjax('expenseBankSelect')">
                    <i class="bi bi-check-lg me-1"></i> Save & Select Bank
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Dataset of all orders with customer and event info
    const allOrders = [
        @foreach($orders as $ord)
        {
            id: "{{ $ord->id }}",
            orderNo: "{{ $ord->order_no }}",
            customerId: "{{ $ord->customer_id }}",
            customerName: "{{ addslashes($ord->customer?->name ?? 'Customer') }}",
            eventId: "{{ $ord->event_id ?? '' }}",
            eventName: "{{ addslashes($ord->event?->event_name ?? 'Event') }}",
            eventDate: "{{ $ord->event?->event_date?->format('M d, Y') ?? 'N/A' }}",
            venue: "{{ addslashes($ord->event?->venue ?? 'No Venue') }}",
            total: {{ (float)$ord->total_amount }},
            paid: {{ (float)$ord->paid_amount }},
            balance: {{ (float)$ord->balance }}
        },
        @endforeach
    ];

    // Switch between Direct Expense and Normal Expense
    function switchExpenseType(type) {
        document.getElementById('expenseTypeInput').value = type;

        const cardDirect = document.getElementById('cardTypeDirect');
        const cardNormal = document.getElementById('cardTypeNormal');
        const sectionDirect = document.getElementById('sectionDirectExpense');
        const directCatWrap = document.getElementById('directCategoryWrapper');
        const normalCatWrap = document.getElementById('normalCategoryWrapper');

        if (type === 'direct') {
            cardDirect.classList.add('active');
            cardNormal.classList.remove('active');
            sectionDirect.classList.remove('d-none');
            if (directCatWrap) directCatWrap.classList.remove('d-none');
            if (normalCatWrap) normalCatWrap.classList.add('d-none');
            syncDirectCategory(document.getElementById('directCategorySelect').value);
        } else {
            cardNormal.classList.add('active');
            cardDirect.classList.remove('active');
            sectionDirect.classList.add('d-none');
            if (directCatWrap) directCatWrap.classList.add('d-none');
            if (normalCatWrap) normalCatWrap.classList.remove('d-none');
            syncNormalCategory(document.getElementById('normalCategorySelect').value);
        }
    }

    function syncDirectCategory(val) {
        if (document.getElementById('expenseTypeInput').value === 'direct') {
            document.getElementById('unifiedCategoryInput').value = val;
        }
    }

    function syncNormalCategory(val) {
        if (document.getElementById('expenseTypeInput').value === 'normal') {
            document.getElementById('unifiedCategoryInput').value = val;
        }
    }

    // Direct Expense: Customer change handler
    function onDirectCustomerChange(customerId) {
        const placeholder = document.getElementById('ordersEmptyPlaceholder');
        const listContainer = document.getElementById('customerOrdersList');
        const grid = document.getElementById('ordersGridCards');

        grid.innerHTML = '';
        document.getElementById('selectedOrderId').value = '';
        document.getElementById('selectedEventId').value = '';

        if (!customerId) {
            placeholder.classList.remove('d-none');
            placeholder.innerHTML = '<i class="bi bi-arrow-up-circle me-1"></i> Please select a customer above to view and link their orders.';
            listContainer.classList.add('d-none');
            return;
        }

        const filtered = allOrders.filter(o => String(o.customerId) === String(customerId));

        if (filtered.length === 0) {
            placeholder.classList.remove('d-none');
            placeholder.innerHTML = '<span class="text-danger fw-semibold"><i class="bi bi-exclamation-circle me-1"></i> No approved orders found for this customer.</span>';
            listContainer.classList.add('d-none');
            return;
        }

        placeholder.classList.add('d-none');
        listContainer.classList.remove('d-none');

        filtered.forEach((o, index) => {
            const isFirst = index === 0;
            const col = document.createElement('div');
            col.className = 'col-12 col-md-6';
            col.innerHTML = `
                <div class="order-select-card ${isFirst ? 'selected' : ''}" onclick="selectDirectOrder('${o.id}', '${o.eventId}', this)">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="badge bg-dark font-monospace" style="font-size: 11px;">${o.orderNo}</span>
                        <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>${o.eventDate}</small>
                    </div>
                    <div class="fw-bold text-dark text-truncate" style="font-size: 13px;">${o.eventName}</div>
                    <div class="text-muted small text-truncate mb-2" style="font-size: 11px;"><i class="bi bi-geo-alt me-0.5"></i>${o.venue}</div>
                    <div class="d-flex justify-content-between align-items-center pt-1 border-top" style="font-size: 11.5px;">
                        <span class="text-muted">Total: Rs. ${o.total.toLocaleString()}</span>
                        <span class="fw-bold text-danger">Bal: Rs. ${o.balance.toLocaleString()}</span>
                    </div>
                </div>
            `;
            grid.appendChild(col);

            if (isFirst) {
                document.getElementById('selectedOrderId').value = o.id;
                document.getElementById('selectedEventId').value = o.eventId;
            }
        });
    }

    function selectDirectOrder(orderId, eventId, cardEl) {
        document.querySelectorAll('.order-select-card').forEach(c => c.classList.remove('selected'));
        cardEl.classList.add('selected');
        document.getElementById('selectedOrderId').value = orderId;
        document.getElementById('selectedEventId').value = eventId;
    }

    // Dynamic Payment Method Fields
    function onExpenseMethodChange(method) {
        const bankCol = document.getElementById('expenseBankCol');
        const chequeCol = document.getElementById('expenseChequeCol');
        const bankSelect = document.getElementById('expenseBankSelect');
        const chequeInput = document.getElementById('expenseChequeInput');
        const bankLabel = document.getElementById('bankLabelText');

        if (method === 'bank_transfer') {
            bankCol.classList.remove('d-none');
            bankCol.classList.remove('col-md-4');
            bankCol.classList.add('col-md-8');
            chequeCol.classList.add('d-none');
            bankLabel.innerText = 'Debited Bank Account';
            bankSelect.required = true;
            chequeInput.required = false;
        } else if (method === 'cheque') {
            bankCol.classList.remove('d-none');
            bankCol.classList.remove('col-md-8');
            bankCol.classList.add('col-md-4');
            chequeCol.classList.remove('d-none');
            bankLabel.innerText = 'Bank Account';
            bankSelect.required = true;
            chequeInput.required = true;
        } else {
            // Cash
            bankCol.classList.add('d-none');
            chequeCol.classList.add('d-none');
            bankSelect.required = false;
            chequeInput.required = false;
            bankSelect.value = '';
            chequeInput.value = '';
        }
    }

    // AJAX Save New Category
    window.saveCategoryAjax = function() {
        const nameInput = document.getElementById('modalCategoryName');
        const descInput = document.getElementById('modalCategoryDesc');
        const alertBox = document.getElementById('categoryModalAlert');
        const btnSave = document.getElementById('btnSaveCategory');

        if (!nameInput) {
            console.error('modalCategoryName element not found');
            return;
        }

        const name = nameInput.value.trim();
        const desc = descInput ? descInput.value.trim() : '';

        const showAlert = function(msg) {
            if (alertBox) {
                alertBox.classList.remove('d-none');
                alertBox.innerText = msg;
            } else {
                alert(msg);
            }
        };

        if (!name) {
            showAlert('Please enter a category name.');
            nameInput.focus();
            return;
        }

        if (alertBox) alertBox.classList.add('d-none');
        if (btnSave) {
            btnSave.disabled = true;
            btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...';
        }

        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '{{ csrf_token() }}';

        fetch("{{ route('expense-categories.quick-store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                _token: csrfToken,
                name: name,
                description: desc
            })
        })
        .then(async function(res) {
            const rawText = await res.text();
            try {
                return JSON.parse(rawText);
            } catch (parseErr) {
                console.error('Response is not valid JSON:', rawText);
                throw new Error('Server returned an unexpected response. Please try again.');
            }
        })
        .then(function(data) {
            if (btnSave) {
                btnSave.disabled = false;
                btnSave.innerHTML = '<i class="bi bi-check2 me-1"></i> Save Category';
            }

            if (data && data.success && data.category) {
                const newCat = data.category;
                const catName = newCat.name;

                // 1. Update Normal Category Select
                const normalSelect = document.getElementById('normalCategorySelect');
                if (normalSelect) {
                    let exists = false;
                    for (let i = 0; i < normalSelect.options.length; i++) {
                        if (normalSelect.options[i].value === catName) {
                            exists = true;
                            break;
                        }
                    }
                    if (!exists) {
                        normalSelect.add(new Option(catName, catName, true, true));
                    }
                    normalSelect.value = catName;
                    if (typeof jQuery !== 'undefined') {
                        jQuery(normalSelect).val(catName).trigger('change');
                        try { jQuery(normalSelect).trigger('change.select2'); } catch(e){}
                    } else {
                        normalSelect.dispatchEvent(new Event('change'));
                    }
                }

                // 2. Update Direct Category Select
                const directSelect = document.getElementById('directCategorySelect');
                if (directSelect) {
                    let exists = false;
                    for (let i = 0; i < directSelect.options.length; i++) {
                        if (directSelect.options[i].value === catName) {
                            exists = true;
                            break;
                        }
                    }
                    if (!exists) {
                        directSelect.add(new Option(catName, catName, true, true));
                    }
                    directSelect.value = catName;
                    if (typeof jQuery !== 'undefined') {
                        jQuery(directSelect).val(catName).trigger('change');
                        try { jQuery(directSelect).trigger('change.select2'); } catch(e){}
                    } else {
                        directSelect.dispatchEvent(new Event('change'));
                    }
                }

                // 3. Update the hidden unified category input submitted with the form
                const unifiedInput = document.getElementById('unifiedCategoryInput');
                if (unifiedInput) {
                    unifiedInput.value = catName;
                }

                // Reset modal inputs & close
                nameInput.value = '';
                if (descInput) descInput.value = '';
                
                const modalEl = document.getElementById('addCategoryModal');
                if (modalEl) {
                    if (window.bootstrap && bootstrap.Modal) {
                        const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        if (modalInstance) modalInstance.hide();
                    } else if (typeof jQuery !== 'undefined') {
                        jQuery(modalEl).modal('hide');
                    }
                }
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');
                document.body.style.removeProperty('overflow');
            } else {
                let errMsg = (data && data.message) ? data.message : 'Failed to save category.';
                if (data && data.errors) {
                    errMsg = Object.values(data.errors).flat().join(', ');
                }
                showAlert(errMsg);
            }
        })
        .catch(function(err) {
            console.error('Error in saveCategoryAjax:', err);
            if (btnSave) {
                btnSave.disabled = false;
                btnSave.innerHTML = '<i class="bi bi-check2 me-1"></i> Save Category';
            }
            showAlert(err.message || 'Network error while saving category.');
        });
    };

    // AJAX Save Quick Customer
    window.saveQuickCustomerAjax = function() {
        const nameInput = document.getElementById('quickCustName');
        const phoneInput = document.getElementById('quickCustPhone');
        const emailInput = document.getElementById('quickCustEmail');
        const alertBox = document.getElementById('quickCustomerAlert');
        const btnSave = document.getElementById('btnSaveQuickCust');

        const name = nameInput ? nameInput.value.trim() : '';
        const phone = phoneInput ? phoneInput.value.trim() : '';
        const email = emailInput ? emailInput.value.trim() : '';

        const showAlert = function(msg) {
            if (alertBox) {
                alertBox.classList.remove('d-none');
                alertBox.innerText = msg;
            } else {
                alert(msg);
            }
        };

        if (!name) {
            showAlert('Customer name is required.');
            if (nameInput) nameInput.focus();
            return;
        }

        if (alertBox) alertBox.classList.add('d-none');
        if (btnSave) {
            btnSave.disabled = true;
            btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
        }

        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '{{ csrf_token() }}';

        fetch('{{ route("customers.quick-store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name, phone, email, _token: csrfToken })
        })
        .then(async function(res) {
            const rawText = await res.text();
            try {
                return JSON.parse(rawText);
            } catch (e) {
                throw new Error('Server returned an unexpected response.');
            }
        })
        .then(function(data) {
            if (btnSave) {
                btnSave.disabled = false;
                btnSave.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save & Select Customer';
            }

            if (data && data.success && data.customer) {
                const customerSelect = document.getElementById('directCustomerId');
                const optText = `${data.customer.name} (${data.customer.phone || 'No phone'})`;

                if (customerSelect) {
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
                }

                onDirectCustomerChange(data.customer.id);

                const modalEl = document.getElementById('quickCustomerModal');
                if (modalEl) {
                    if (window.bootstrap && bootstrap.Modal) {
                        const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        if (modalInstance) modalInstance.hide();
                    } else if (typeof jQuery !== 'undefined') {
                        jQuery(modalEl).modal('hide');
                    }
                }
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                const formEl = document.getElementById('quickCustomerForm');
                if (formEl) formEl.reset();
            } else {
                showAlert((data && data.message) ? data.message : 'Failed to save customer.');
            }
        })
        .catch(function(err) {
            console.error('Error in saveQuickCustomerAjax:', err);
            if (btnSave) {
                btnSave.disabled = false;
                btnSave.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save & Select Customer';
            }
            showAlert(err.message || 'Network error while saving customer.');
        });
    };

    // AJAX Save Quick Bank
    window.saveQuickBankAjax = function(targetSelectId = 'expenseBankSelect') {
        const bankNameInput = document.getElementById('modalBankName');
        const accountNameInput = document.getElementById('modalAccountName');
        const accountNumberInput = document.getElementById('modalAccountNumber');
        const branchInput = document.getElementById('modalBranch');
        const openingBalanceInput = document.getElementById('modalOpeningBalance');
        const alertBox = document.getElementById('quickBankModalAlert');
        const btnSave = document.getElementById('btnSaveQuickBank');

        const bankName = bankNameInput ? bankNameInput.value.trim() : '';
        const accountName = accountNameInput ? accountNameInput.value.trim() : '';
        const accountNumber = accountNumberInput ? accountNumberInput.value.trim() : '';
        const branch = branchInput ? branchInput.value.trim() : '';
        const openingBalance = (openingBalanceInput && openingBalanceInput.value.trim()) ? openingBalanceInput.value.trim() : 0;

        const showAlert = function(msg) {
            if (alertBox) {
                alertBox.classList.remove('d-none');
                alertBox.innerText = msg;
            } else {
                alert(msg);
            }
        };

        if (!bankName || !accountName || !accountNumber) {
            showAlert('Bank Name, Account Holder Name, and Account Number are required.');
            return;
        }

        if (alertBox) alertBox.classList.add('d-none');
        if (btnSave) {
            btnSave.disabled = true;
            btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
        }

        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '{{ csrf_token() }}';

        fetch("{{ route('banks.quick-store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                _token: csrfToken,
                bank_name: bankName,
                account_name: accountName,
                account_number: accountNumber,
                branch: branch,
                opening_balance: openingBalance
            })
        })
        .then(async function(res) {
            const rawText = await res.text();
            try {
                return JSON.parse(rawText);
            } catch (e) {
                throw new Error('Server returned an unexpected response.');
            }
        })
        .then(function(data) {
            if (btnSave) {
                btnSave.disabled = false;
                btnSave.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save & Select Bank';
            }

            if (data && data.success && data.bank) {
                const selectEl = document.getElementById(targetSelectId);
                if (selectEl) {
                    const optVal = data.display_value;
                    const optText = data.display_text;
                    if (typeof jQuery !== 'undefined') {
                        const $select = jQuery(selectEl);
                        $select.append(new Option(optText, optVal, true, true));
                        $select.val(optVal).trigger('change');
                    } else {
                        selectEl.add(new Option(optText, optVal, true, true));
                        selectEl.value = optVal;
                        selectEl.dispatchEvent(new Event('change'));
                    }
                }

                const modalEl = document.getElementById('quickBankModal');
                if (modalEl) {
                    if (window.bootstrap && bootstrap.Modal) {
                        const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        if (modalInstance) modalInstance.hide();
                    } else if (typeof jQuery !== 'undefined') {
                        jQuery(modalEl).modal('hide');
                    }
                }
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                const formEl = document.getElementById('quickBankForm');
                if (formEl) formEl.reset();
            } else {
                showAlert((data && data.message) ? data.message : 'Failed to save bank.');
            }
        })
        .catch(function(err) {
            console.error('Error in saveQuickBankAjax:', err);
            if (btnSave) {
                btnSave.disabled = false;
                btnSave.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save & Select Bank';
            }
            showAlert(err.message || 'Network error while saving bank account.');
        });
    };

    function resetExpenseForm() {
        document.getElementById('customerOrdersList').classList.add('d-none');
        document.getElementById('ordersEmptyPlaceholder').classList.remove('d-none');
        document.getElementById('ordersEmptyPlaceholder').innerHTML = '<i class="bi bi-arrow-up-circle me-1"></i> Please select a customer above to view and link their orders.';
        document.getElementById('selectedOrderId').value = '';
        document.getElementById('selectedEventId').value = '';
        switchExpenseType('direct');
        onExpenseMethodChange('cash');
    }

    // Initializer
    document.addEventListener('DOMContentLoaded', () => {
        const initialType = document.getElementById('expenseTypeInput').value || 'direct';
        switchExpenseType(initialType);

        const custSelect = document.getElementById('directCustomerId');
        if (custSelect.value) {
            onDirectCustomerChange(custSelect.value);
        }

        onExpenseMethodChange(document.getElementById('expensePaymentMethod').value);
    });
</script>
@endsection