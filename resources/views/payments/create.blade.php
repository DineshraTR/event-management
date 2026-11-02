@extends('layouts.app')
@section('title', 'Record Payment')
@section('page-title', 'Record Payment')

@section('content')
<style>
    /* Piliwela & Professional Card Styling */
    .payment-form-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }
    .form-section-header {
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
    .section-badge {
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
    .order-summary-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        transition: all 0.3s ease;
    }
    .stat-pill {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        height: 100%;
    }
    .amount-input-box {
        font-size: 20px !important;
        font-weight: 700 !important;
        color: #15803d !important;
        letter-spacing: -0.3px;
    }
    .method-hint-box {
        background: #f1f5f9;
        border-radius: 10px;
        padding: 14px;
        border-left: 3px solid #484646;
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
                <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.3px;">Record Customer Payment</h4>
                <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 11px; font-weight: 600;">
                    Official Receipt Entry
                </span>
            </div>
            <p class="text-muted small mb-0">Select an approved order, specify the amount, and record payments directly to cash/bank accounts.</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('reports.payments') }}" class="btn btn-outline-secondary shadow-sm" target="_blank">
            <i class="bi bi-receipt-cutoff me-1 text-primary"></i> Payments Report
        </a>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary shadow-sm" target="_blank">
            <i class="bi bi-box-seam me-1"></i> Orders
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 shadow-sm" role="alert" style="border-radius: 10px;">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div>
            <strong>Payment Recorded Successfully!</strong> {{ session('success') }}
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
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

<!-- Centered, Clean, Piliwela Form -->
<div class="row justify-content-center">
    <div class="col-12 col-lg-10 col-xl-9">
        <div class="card payment-form-card shadow-sm border-0 mb-4">
            
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 14px;">
                        <i class="bi bi-receipt text-primary"></i> Payment Entry Form
                    </h6>
                    <small class="text-muted" style="font-size: 11.5px;">All details entered here update customer order balances and transaction ledger.</small>
                </div>
                <span class="badge bg-light text-dark border px-2.5 py-1" style="font-size: 11px;">
                    <i class="bi bi-calendar3 me-1"></i> {{ date('d M Y') }}
                </span>
            </div>

            <form method="POST" action="{{ route('payments.store') }}" id="paymentForm">
                @csrf
                <div class="card-body p-4">

                    <!-- ============================================== -->
                    <!-- STEP 1: CUSTOMER & ORDER SELECTION            -->
                    <!-- ============================================== -->
                    <div class="mb-4">
                        <div class="form-section-header">
                            <span class="section-badge">1</span>
                            <span>Customer & Target Order Information</span>
                        </div>

                        <div class="row g-3">
                            <!-- Customer Select -->
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    <i class="bi bi-person me-1 text-secondary"></i> Customer <span class="text-danger">*</span>
                                </label>
                                <select name="customer_id" id="paymentCustomerId" class="form-select searchable-select" required>
                                    <option value="">-- Select Customer --</option>
                                    @foreach($customers as $cust)
                                        <option value="{{ $cust->id }}" {{ (old('customer_id', $selectedCustomer?->id ?? $selectedOrder?->customer_id) == $cust->id) ? 'selected' : '' }}>
                                            {{ $cust->name }} ({{ $cust->phone ?: 'No phone' }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                    <i class="bi bi-funnel me-1"></i> Select customer first to filter orders.
                                </small>
                            </div>

                            <!-- Order Select -->
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    <i class="bi bi-file-earmark-text me-1 text-secondary"></i> Target Order <span class="text-danger">*</span>
                                </label>
                                <select name="order_id" id="paymentOrderId" class="form-select searchable-select" required>
                                    <option value="">-- Select Order --</option>
                                    @foreach($orders as $ord)
                                        <option value="{{ $ord->id }}" 
                                                data-customer-id="{{ $ord->customer_id }}"
                                                data-order-no="{{ $ord->order_no }}"
                                                data-event-name="{{ $ord->event?->event_name ?? 'Event' }}"
                                                data-total="{{ $ord->total_amount }}"
                                                data-paid="{{ $ord->paid_amount }}"
                                                data-balance="{{ $ord->balance }}"
                                                {{ (old('order_id', $selectedOrder?->id) == $ord->id) ? 'selected' : '' }}>
                                            {{ $ord->order_no }} &bull; {{ $ord->customer?->name }} (Bal: Rs. {{ number_format($ord->balance, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;" id="orderFilterNote">
                                    <i class="bi bi-info-circle me-1"></i> Choose customer's pending order.
                                </small>
                            </div>
                        </div>

                        <!-- Live Order Financial Summary Box -->
                        <div id="orderBalanceCard" class="order-summary-box mt-3 d-none">
                            <div class="d-flex flex-wrap align-items-center justify-content-between pb-2 mb-2 border-bottom gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-dark text-white font-monospace px-2 py-1" id="dispOrderNoBadge" style="font-size: 11.5px;">ORDER</span>
                                    <strong class="text-dark" id="dispEventName" style="font-size: 13px;">Event Name</strong>
                                </div>
                                <button type="button" onclick="setFullBalance()" class="btn btn-sm btn-outline-success fw-semibold" style="font-size: 11.5px; border-radius: 6px;">
                                    <i class="bi bi-arrow-down-circle me-1"></i> Pay Full Balance (<span id="dispFillBalance">Rs. 0.00</span>)
                                </button>
                            </div>

                            <div class="row g-2 text-center text-md-start">
                                <div class="col-12 col-md-4">
                                    <div class="stat-pill">
                                        <div class="text-muted text-uppercase" style="font-size: 10px; font-weight: 700; letter-spacing: 0.3px;">Total Invoice Value</div>
                                        <div class="fw-bold text-dark fs-6 mt-1" id="dispTotal">Rs. 0.00</div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="stat-pill">
                                        <div class="text-muted text-uppercase" style="font-size: 10px; font-weight: 700; letter-spacing: 0.3px;">Already Settled (Paid)</div>
                                        <div class="fw-bold text-success fs-6 mt-1" id="dispPaid">Rs. 0.00</div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="stat-pill" style="background: #fff5f5; border-color: #fed7d7;">
                                        <div class="text-danger text-uppercase" style="font-size: 10px; font-weight: 700; letter-spacing: 0.3px;">Outstanding Balance</div>
                                        <div class="fw-bold text-danger fs-6 mt-1" id="dispBalance">Rs. 0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- STEP 2: PAYMENT AMOUNT & DETAILS              -->
                    <!-- ============================================== -->
                    <div class="mb-4">
                        <div class="form-section-header">
                            <span class="section-badge">2</span>
                            <span>Payment Amount & Settlement</span>
                        </div>

                        <div class="row g-3">
                            <!-- Payment Type -->
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    Payment Type <span class="text-danger">*</span>
                                </label>
                                <select name="payment_type" id="paymentType" class="form-select" required onchange="onPaymentTypeChange(this.value)">
                                    <option value="partial" {{ old('payment_type', 'partial') === 'partial' ? 'selected' : '' }}>Partial (Installment)</option>
                                    <option value="advance" {{ old('payment_type') === 'advance' ? 'selected' : '' }}>Advance Deposit</option>
                                    <option value="full" {{ old('payment_type') === 'full' ? 'selected' : '' }}>Full Payment (Clear Balance)</option>
                                </select>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">Classification of this receipt.</small>
                            </div>

                            <!-- Payment Amount -->
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    Amount to Pay (Rs.) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0.01" name="amount" id="paymentAmountInput" 
                                        class="form-control amount-input-box" 
                                        value="{{ old('amount') }}" placeholder="0.00" required oninput="calculateLiveRemaining()">
                                </div>
                                <div id="liveRemainingNote" class="text-muted mt-1" style="font-size: 11px;">
                                    Enter collected amount.
                                </div>
                            </div>

                            <!-- Payment Date -->
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    Payment Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', $today) }}" required>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">Date recorded in ledger books.</small>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- STEP 3: PAYMENT METHOD & BANK DETAILS         -->
                    <!-- ============================================== -->
                    <div class="mb-3">
                        <div class="form-section-header">
                            <span class="section-badge">3</span>
                            <span>Payment Method & Transaction Details</span>
                        </div>

                        <div class="row g-3">
                            <!-- Payment Method Dropdown -->
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    Payment Method <span class="text-danger">*</span>
                                </label>
                                <select name="payment_method" id="paymentMethod" class="form-select fw-semibold" required onchange="onPaymentMethodChange(this.value)">
                                    <option value="cash" {{ old('payment_method', 'cash') === 'cash' ? 'selected' : '' }}>💵 Cash Payment</option>
                                    <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>🏦 Bank Transfer / Deposit</option>
                                    <option value="cheque" {{ old('payment_method') === 'cheque' ? 'selected' : '' }}>📑 Cheque Payment</option>
                                </select>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">How the payment was received.</small>
                            </div>

                            <!-- Dynamic Bank Account (For Bank Transfer and Cheque) -->
                            <div class="col-12 col-md-8 d-none" id="bankSelectCol">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    <span id="bankLabelText">Company Bank Account</span> <span class="text-danger">*</span>
                                </label>
                                <div class="d-flex gap-2">
                                    <select name="bank_name" id="bankNameSelect" class="form-select">
                                        <option value="">-- Choose Deposited Bank Account --</option>
                                        @if(isset($banks) && $banks->isNotEmpty())
                                            @foreach($banks as $b)
                                                <option value="{{ $b->bank_name }} - {{ $b->account_number }} ({{ $b->branch }})"
                                                    @selected(old('bank_name') == "{$b->bank_name} - {$b->account_number} ({$b->branch})")>
                                                    {{ $b->bank_name }} &bull; {{ $b->account_number }} ({{ $b->branch ?: 'Main' }})
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <button type="button" class="btn btn-outline-secondary d-flex align-items-center text-nowrap" style="font-size: 12px;" data-bs-toggle="modal" data-bs-target="#quickBankModal">
                                        <i class="bi bi-plus-lg me-1"></i> Add Bank
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">Select the bank account where money was credited.</small>
                            </div>

                            <!-- Dynamic Cheque Number (For Cheque Only) -->
                            <div class="col-12 col-md-4 d-none" id="chequeNoCol">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    Cheque Serial Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="cheque_no" id="chequeNoInput" class="form-control font-monospace" 
                                    value="{{ old('cheque_no') }}" placeholder="e.g. CHQ-829102">
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">Serial number printed on cheque leaf.</small>
                            </div>

                            <!-- Notes / Remarks -->
                            <div class="col-12">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    Payment Notes / Reference Slip Number
                                </label>
                                <textarea name="notes" rows="2" class="form-control" placeholder="Payer name, transaction slip number, bank branch, or internal notes...">{{ old('notes') }}</textarea>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">Optional notes for future reference or receipt audit.</small>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Footer Action Buttons -->
                <div class="card-footer bg-light py-3 px-4 border-top d-flex align-items-center justify-content-between">
                    <button type="reset" class="btn btn-outline-secondary shadow-sm" onclick="resetFormState()">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Form
                    </button>
                    <button type="submit" class="btn btn-primary shadow-sm px-4 py-2 fw-semibold">
                        <i class="bi bi-check2-circle me-1.5"></i> Confirm & Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- POPUP MODAL: QUICK ADD BANK ACCOUNT -->
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
                <div id="quickBankModalAlert" class="alert alert-danger d-none py-2 px-3 small" style="border-radius: 8px;"></div>
                <form id="quickBankForm">
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
                <button type="button" class="btn btn-primary btn-sm fw-semibold" id="btnSaveQuickBank" onclick="saveQuickBankAjax('bankNameSelect')">
                    <i class="bi bi-check-lg me-1"></i> Save & Select Bank
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // All Orders dataset for accurate customer filtering
    const allOrders = [
        @foreach($orders as $ord)
        {
            id: "{{ $ord->id }}",
            customerId: "{{ $ord->customer_id }}",
            orderNo: "{{ $ord->order_no }}",
            customerName: "{{ addslashes($ord->customer?->name ?? 'No Customer') }}",
            eventName: "{{ addslashes($ord->event?->event_name ?? 'Event') }}",
            total: {{ (float)$ord->total_amount }},
            paid: {{ (float)$ord->paid_amount }},
            balance: {{ (float)$ord->balance }}
        },
        @endforeach
    ];

    let currentOrderBalance = 0;

    // Filter order select options strictly by customer
    function filterOrdersByCustomer(customerId) {
        const orderSelect = document.getElementById('paymentOrderId');
        const $orderSelect = (typeof jQuery !== 'undefined') ? jQuery(orderSelect) : null;
        const currentVal = orderSelect.value;

        // Clear existing options
        orderSelect.innerHTML = '<option value="">-- Select Order --</option>';

        const filtered = customerId 
            ? allOrders.filter(o => String(o.customerId) === String(customerId))
            : allOrders;

        if (customerId && filtered.length === 0) {
            const noOpt = document.createElement('option');
            noOpt.value = '';
            noOpt.disabled = true;
            noOpt.innerText = 'No pending orders found for this customer';
            orderSelect.appendChild(noOpt);
            document.getElementById('orderFilterNote').innerHTML = '<span class="text-danger fw-semibold"><i class="bi bi-exclamation-circle me-1"></i>No approved orders found for this customer.</span>';
        } else {
            document.getElementById('orderFilterNote').innerHTML = customerId 
                ? `<span class="text-success fw-semibold"><i class="bi bi-check-circle me-1"></i>Showing ${filtered.length} order(s) for this customer.</span>` 
                : '<i class="bi bi-info-circle me-1"></i>Choose customer\'s order.';

            filtered.forEach(o => {
                const opt = document.createElement('option');
                opt.value = o.id;
                opt.setAttribute('data-customer-id', o.customerId);
                opt.setAttribute('data-order-no', o.orderNo);
                opt.setAttribute('data-event-name', o.eventName);
                opt.setAttribute('data-total', o.total);
                opt.setAttribute('data-paid', o.paid);
                opt.setAttribute('data-balance', o.balance);
                opt.innerText = `${o.orderNo} • ${o.customerName} (Bal: Rs. ${o.balance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })})`;

                if (String(currentVal) === String(o.id)) {
                    opt.selected = true;
                }
                orderSelect.appendChild(opt);
            });
        }

        // Notify Select2 if initialized
        if ($orderSelect && $orderSelect.hasClass('select2-hidden-accessible')) {
            $orderSelect.trigger('change.select2');
        }

        // Auto select if only 1 order exists for this customer
        if (customerId && filtered.length === 1) {
            orderSelect.value = filtered[0].id;
            if ($orderSelect && $orderSelect.hasClass('select2-hidden-accessible')) {
                $orderSelect.val(filtered[0].id).trigger('change.select2');
            }
        }

        onPaymentOrderChange(orderSelect);
    }

    // When an order is selected
    function onPaymentOrderChange(selectEl) {
        const selectedOpt = selectEl.options[selectEl.selectedIndex];
        const card = document.getElementById('orderBalanceCard');

        if (!selectedOpt || !selectedOpt.value) {
            card.classList.add('d-none');
            currentOrderBalance = 0;
            calculateLiveRemaining();
            return;
        }

        // Auto sync customer select if not matching
        const custId = selectedOpt.getAttribute('data-customer-id');
        if (custId) {
            const custSelect = document.getElementById('paymentCustomerId');
            if (String(custSelect.value) !== String(custId)) {
                custSelect.value = custId;
                if (typeof jQuery !== 'undefined' && jQuery(custSelect).hasClass('select2-hidden-accessible')) {
                    jQuery(custSelect).trigger('change.select2');
                }
            }
        }

        const eventName = selectedOpt.getAttribute('data-event-name') || 'Event';
        const orderNo = selectedOpt.getAttribute('data-order-no') || 'ORDER';
        const total = parseFloat(selectedOpt.getAttribute('data-total')) || 0;
        const paid = parseFloat(selectedOpt.getAttribute('data-paid')) || 0;
        const balance = parseFloat(selectedOpt.getAttribute('data-balance')) || 0;
        currentOrderBalance = balance;

        document.getElementById('dispOrderNoBadge').innerText = orderNo;
        document.getElementById('dispEventName').innerText = eventName;
        document.getElementById('dispTotal').innerText = 'Rs. ' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('dispPaid').innerText = 'Rs. ' + paid.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('dispBalance').innerText = 'Rs. ' + balance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('dispFillBalance').innerText = 'Rs. ' + balance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        card.classList.remove('d-none');

        // If payment type is full, auto-fill balance
        if (document.getElementById('paymentType').value === 'full') {
            document.getElementById('paymentAmountInput').value = balance.toFixed(2);
        }

        calculateLiveRemaining();
    }

    function onPaymentTypeChange(type) {
        if (type === 'full') {
            setFullBalance();
        }
    }

    function setFullBalance() {
        if (currentOrderBalance > 0) {
            document.getElementById('paymentAmountInput').value = currentOrderBalance.toFixed(2);
            document.getElementById('paymentType').value = 'full';
            calculateLiveRemaining();
        }
    }

    function calculateLiveRemaining() {
        const noteEl = document.getElementById('liveRemainingNote');
        const amtInput = document.getElementById('paymentAmountInput');
        const enteredAmt = parseFloat(amtInput.value) || 0;

        if (currentOrderBalance > 0 && enteredAmt > 0) {
            const remaining = currentOrderBalance - enteredAmt;
            if (remaining === 0) {
                noteEl.innerHTML = '<span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Full payment: Order balance will be 100% settled!</span>';
            } else if (remaining < 0) {
                const excess = Math.abs(remaining);
                noteEl.innerHTML = `<span class="text-warning fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i>Amount exceeds balance by Rs. ${excess.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>`;
            } else {
                noteEl.innerHTML = `Balance due after this payment: <strong class="text-danger fw-bold">Rs. ${remaining.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong>`;
            }
        } else if (currentOrderBalance > 0) {
            noteEl.innerHTML = `Order balance due: <strong class="text-danger fw-bold">Rs. ${currentOrderBalance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong>`;
        } else {
            noteEl.innerHTML = 'Enter collected amount.';
        }
    }

    // Dynamic payment method fields
    function onPaymentMethodChange(method) {
        const bankCol = document.getElementById('bankSelectCol');
        const chequeCol = document.getElementById('chequeNoCol');
        const bankSelect = document.getElementById('bankNameSelect');
        const chequeInput = document.getElementById('chequeNoInput');
        const bankLabel = document.getElementById('bankLabelText');

        if (method === 'bank_transfer') {
            bankCol.classList.remove('d-none');
            bankCol.classList.remove('col-md-4');
            bankCol.classList.add('col-md-8');
            chequeCol.classList.add('d-none');
            bankLabel.innerText = 'Deposited Bank Account';
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

    function resetFormState() {
        document.getElementById('orderBalanceCard').classList.add('d-none');
        document.getElementById('bankSelectCol').classList.add('d-none');
        document.getElementById('chequeNoCol').classList.add('d-none');
        currentOrderBalance = 0;
        document.getElementById('liveRemainingNote').innerText = 'Enter collected amount.';
    }

    // Initializer
    document.addEventListener('DOMContentLoaded', () => {
        const custSelect = document.getElementById('paymentCustomerId');
        const orderSelect = document.getElementById('paymentOrderId');

        if (typeof jQuery !== 'undefined') {
            jQuery(custSelect).on('change', function() {
                filterOrdersByCustomer(this.value);
            });
            jQuery(orderSelect).on('change', function() {
                onPaymentOrderChange(this);
            });
        } else {
            custSelect.addEventListener('change', function() {
                filterOrdersByCustomer(this.value);
            });
            orderSelect.addEventListener('change', function() {
                onPaymentOrderChange(this);
            });
        }

        if (custSelect.value) {
            const initialOrderId = orderSelect.value;
            filterOrdersByCustomer(custSelect.value);
            if (initialOrderId) {
                orderSelect.value = initialOrderId;
                if (typeof jQuery !== 'undefined' && jQuery(orderSelect).hasClass('select2-hidden-accessible')) {
                    jQuery(orderSelect).val(initialOrderId).trigger('change.select2');
                }
            }
        }

        if (orderSelect.value) {
            onPaymentOrderChange(orderSelect);
        }

        onPaymentMethodChange(document.getElementById('paymentMethod').value);
    });

    // AJAX Save Quick Bank
    function saveQuickBankAjax(targetSelectId = 'bankNameSelect') {
        const bankName = document.getElementById('modalBankName').value.trim();
        const accountName = document.getElementById('modalAccountName').value.trim();
        const accountNumber = document.getElementById('modalAccountNumber').value.trim();
        const branch = document.getElementById('modalBranch').value.trim();
        const openingBalance = document.getElementById('modalOpeningBalance').value.trim() || 0;
        const alertBox = document.getElementById('quickBankModalAlert');
        const btnSave = document.getElementById('btnSaveQuickBank');

        if (!bankName || !accountName || !accountNumber) {
            alertBox.classList.remove('d-none');
            alertBox.innerText = 'Bank Name, Account Holder Name, and Account Number are required.';
            return;
        }

        alertBox.classList.add('d-none');
        btnSave.disabled = true;
        btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        fetch("{{ route('banks.quick-store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                bank_name: bankName,
                account_name: accountName,
                account_number: accountNumber,
                branch: branch,
                opening_balance: openingBalance
            })
        })
        .then(res => res.json())
        .then(data => {
            btnSave.disabled = false;
            btnSave.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save & Select Bank';

            if (data.success && data.bank) {
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
                if (window.bootstrap && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                } else if (typeof jQuery !== 'undefined') {
                    jQuery(modalEl).modal('hide');
                }
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.getElementById('quickBankForm').reset();
            } else {
                alertBox.classList.remove('d-none');
                alertBox.innerText = data.message || 'Failed to save bank.';
            }
        })
        .catch(err => {
            btnSave.disabled = false;
            btnSave.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save & Select Bank';
            alertBox.classList.remove('d-none');
            alertBox.innerText = 'Network error while saving bank account.';
        });
    }
</script>
@endsection