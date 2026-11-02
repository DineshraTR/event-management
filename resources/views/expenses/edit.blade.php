@extends('layouts.app')
@section('title', 'Edit Expense - ' . $expense->expense_no)
@section('page-title', 'Edit Expense')

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
</style>

<!-- Page Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0"
            style="width: 44px; height: 44px; background: linear-gradient(135deg, #484646 0%, #3a3838 100%); font-size: 20px;">
            <i class="bi bi-pencil-square"></i>
        </div>
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.3px;">Edit Expense Voucher</h4>
                <span class="badge bg-dark text-white font-monospace px-2.5 py-1" style="font-size: 11px; font-weight: 700;">
                    {{ $expense->expense_no }}
                </span>
            </div>
            <p class="text-muted small mb-0">Modify expense details. Associated cash, bank, or cheque ledgers will automatically re-synchronize.</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('reports.expenses') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Expenses Report
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
                        <i class="bi bi-receipt-cutoff text-primary"></i> Expense Information Form
                    </h6>
                    <small class="text-muted" style="font-size: 11.5px;">Update classification, amount, payment method, or linked project.</small>
                </div>
                <span class="badge bg-light text-dark border px-2.5 py-1" style="font-size: 11px;">
                    Original Date: {{ $expense->expense_date?->format('d M Y') ?? 'N/A' }}
                </span>
            </div>

            <form method="POST" action="{{ route('expenses.update', $expense) }}" id="expenseEditForm">
                @csrf
                @method('PUT')

                <div class="card-body p-4">

                    <!-- ============================================== -->
                    <!-- EXPENSE TYPE SELECTOR: DIRECT VS NORMAL        -->
                    <!-- ============================================== -->
                    @php($currentType = old('type', $expense->type))
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-dark mb-2">
                            Select Expense Classification <span class="text-danger">*</span>
                        </label>
                        <input type="hidden" name="type" id="expenseTypeInput" value="{{ $currentType }}">

                        <div class="row g-3">
                            <!-- Option 1: Direct Expense -->
                            <div class="col-12 col-md-6">
                                <div class="expense-type-card {{ $currentType === 'direct' ? 'active' : '' }}" 
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
                                <div class="expense-type-card {{ $currentType === 'normal' ? 'active' : '' }}" 
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
                    <!-- SECTION: DIRECT EXPENSE (Customer & Order)     -->
                    <!-- ============================================== -->
                    <div id="sectionDirectExpense" class="{{ $currentType === 'direct' ? '' : 'd-none' }}">
                        <div class="form-section-title">
                            <span class="form-section-badge">1</span>
                            <span>Target Customer Order & Event Link</span>
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- Order Selector -->
                            <div class="col-12">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    Target Customer Order <span class="text-danger">*</span>
                                </label>
                                <select name="order_id" id="directOrderId" class="form-select searchable-select">
                                    <option value="">-- Select Order --</option>
                                    @foreach($orders as $o)
                                        <option value="{{ $o->id }}" 
                                            {{ (old('order_id', $expense->order_id) == $o->id) ? 'selected' : '' }}>
                                            {{ $o->order_no }} &bull; {{ $o->customer?->name ?? 'Walk-in' }} 
                                            ({{ $o->event?->event_name ?? 'Event' }}) - Net Total: Rs. {{ number_format($o->total_amount, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                    <i class="bi bi-info-circle me-1"></i> Linking to an order assigns the cost directly to the client's event P&L ledger.
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- SECTION: NORMAL EXPENSE (Category)             -->
                    <!-- ============================================== -->
                    <div id="sectionNormalExpense" class="{{ $currentType === 'normal' ? '' : 'd-none' }}">
                        <div class="form-section-title">
                            <span class="form-section-badge">1</span>
                            <span>General Overhead Category</span>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Expense Category <span class="text-danger">*</span>
                            </label>
                            <select name="category" id="normalCategorySelect" class="form-select searchable-select">
                                <option value="">-- Choose Category --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->name }}" {{ (old('category', $expense->category) == $cat->name) ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                Operational category classification.
                            </small>
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- SECTION: COMMON PAYMENT & VOUCHER DETAILS      -->
                    <!-- ============================================== -->
                    <div class="form-section-title">
                        <span class="form-section-badge">2</span>
                        <span>Payment & Voucher Details</span>
                    </div>

                    <!-- Amount Box (Prominent) -->
                    <div class="mb-3 p-3 rounded-3" style="background: #fef2f2; border: 1.5px dashed #fca5a5;">
                        <div class="row align-items-center">
                            <div class="col-12 col-md-5 mb-2 mb-md-0">
                                <label class="form-label small fw-bold text-danger mb-0">
                                    <i class="bi bi-cash-stack me-1"></i> Expense Disbursement Amount (Rs.) <span class="text-danger">*</span>
                                </label>
                                <small class="text-muted d-block" style="font-size: 11px;">Total value disbursed or settled.</small>
                            </div>
                            <div class="col-12 col-md-7">
                                <div class="input-group">
                                    <span class="input-group-text fw-bold bg-white text-danger border-end-0">Rs.</span>
                                    <input type="number" step="0.01" min="0.01" name="amount" id="expenseAmountInput" 
                                           class="form-control amount-input-box" 
                                           value="{{ old('amount', $expense->amount) }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Description -->
                        <div class="col-12 col-md-8">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Expense Description / Payee <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="description" class="form-control" 
                                value="{{ old('description', $expense->description) }}" placeholder="e.g. Advance paid to Hall or Vendor..." required>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">Clear description of what this payment was for.</small>
                        </div>

                        <!-- Expense Date -->
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Expense Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="expense_date" class="form-control" 
                                value="{{ old('expense_date', $expense->expense_date?->format('Y-m-d')) }}" required>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">Date when expense was incurred.</small>
                        </div>

                        <!-- Payment Method -->
                        @php($currentMethod = old('payment_method', $expense->payment_method ?? 'cash'))
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Payment Method <span class="text-danger">*</span>
                            </label>
                            <select name="payment_method" id="expensePaymentMethod" class="form-select fw-semibold" required onchange="onExpenseMethodChange(this.value)">
                                <option value="cash" {{ $currentMethod === 'cash' ? 'selected' : '' }}>💵 Cash Payment</option>
                                <option value="bank_transfer" {{ $currentMethod === 'bank_transfer' ? 'selected' : '' }}>🏦 Bank Transfer / Deposit</option>
                                <option value="cheque" {{ $currentMethod === 'cheque' ? 'selected' : '' }}>📑 Cheque Payment</option>
                            </select>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">Payment source for ledger posting.</small>
                        </div>

                        <!-- Dynamic Bank Account -->
                        <div class="col-12 col-md-8 {{ $currentMethod === 'bank_transfer' ? '' : 'd-none' }}" id="expenseBankCol">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label small fw-bold text-dark mb-0">
                                    Debited Bank Account <span class="text-danger">*</span>
                                </label>
                                <button type="button" class="btn btn-sm btn-light border py-0 px-2 fw-semibold" style="font-size: 11px;" data-bs-toggle="modal" data-bs-target="#quickBankModal">
                                    <i class="bi bi-plus-lg me-1 text-primary"></i> Add Bank
                                </button>
                            </div>
                            <select name="bank_name" id="expenseBankSelect" class="form-select">
                                <option value="">-- Choose Debited Bank Account --</option>
                                @foreach($banks as $b)
                                    @php($bankFormatted = "{$b->bank_name} - {$b->account_number} ({$b->branch})")
                                    <option value="{{ $bankFormatted }}"
                                        @selected(old('bank_name', $expense->bank_name) == $bankFormatted || (old('bank_name') == '' && $expense->bank_id == $b->id))>
                                        {{ $b->bank_name }} &bull; {{ $b->account_number }} ({{ $b->branch ?: 'Main' }}) - Bal: Rs. {{ number_format($b->current_balance, 2) }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">Account from which funds are debited.</small>
                        </div>

                        <!-- Dynamic Cheque Number -->
                        <div class="col-12 col-md-4 {{ $currentMethod === 'cheque' ? '' : 'd-none' }}" id="expenseChequeCol">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Cheque Serial Number <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="cheque_no" id="expenseChequeInput" class="form-control font-monospace" 
                                value="{{ old('cheque_no', $expense->cheque_no) }}" placeholder="e.g. CHQ-901824">
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">Cheque serial leaf number.</small>
                        </div>

                        <!-- Reference / Voucher No -->
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Reference / Bill / Slip No
                            </label>
                            <input type="text" name="reference" class="form-control" 
                                value="{{ old('reference', $expense->reference) }}" placeholder="e.g. INV-8819 or Slip #">
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">Optional vendor invoice or slip number.</small>
                        </div>

                        <!-- Notes / Remarks -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Internal Notes / Remarks
                            </label>
                            <textarea name="notes" rows="2" class="form-control" placeholder="Any internal notes or vendor details...">{{ old('notes', $expense->notes) }}</textarea>
                        </div>
                    </div>

                </div>

                <!-- Footer Action Buttons -->
                <div class="card-footer bg-light py-3 px-4 border-top d-flex align-items-center justify-content-between">
                    <a href="{{ route('reports.expenses') }}" class="btn btn-outline-secondary shadow-sm">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary shadow-sm px-4 py-2 fw-semibold">
                        <i class="bi bi-check2-circle me-1.5"></i> Update Expense & Sync Ledgers
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function switchExpenseType(type) {
        document.getElementById('expenseTypeInput').value = type;

        const cardDirect = document.getElementById('cardTypeDirect');
        const cardNormal = document.getElementById('cardTypeNormal');
        const secDirect = document.getElementById('sectionDirectExpense');
        const secNormal = document.getElementById('sectionNormalExpense');

        if (type === 'direct') {
            cardDirect.classList.add('active');
            cardNormal.classList.remove('active');
            secDirect.classList.remove('d-none');
            secNormal.classList.add('d-none');
        } else {
            cardNormal.classList.add('active');
            cardDirect.classList.remove('active');
            secNormal.classList.remove('d-none');
            secDirect.classList.add('d-none');
        }
    }

    function onExpenseMethodChange(method) {
        const bankCol = document.getElementById('expenseBankCol');
        const chequeCol = document.getElementById('expenseChequeCol');
        const bankSelect = document.getElementById('expenseBankSelect');
        const chequeInput = document.getElementById('expenseChequeInput');

        if (method === 'bank_transfer') {
            bankCol.classList.remove('d-none');
            chequeCol.classList.add('d-none');
            bankSelect.setAttribute('required', 'required');
            chequeInput.removeAttribute('required');
        } else if (method === 'cheque') {
            bankCol.classList.add('d-none');
            chequeCol.classList.remove('d-none');
            bankSelect.removeAttribute('required');
            chequeInput.setAttribute('required', 'required');
        } else {
            bankCol.classList.add('d-none');
            chequeCol.classList.add('d-none');
            bankSelect.removeAttribute('required');
            chequeInput.removeAttribute('required');
        }
    }

    function saveQuickBankAjax(targetSelectId) {
        const bankName = document.getElementById('modalBankName').value.trim();
        const accountName = document.getElementById('modalAccountName').value.trim();
        const accountNumber = document.getElementById('modalAccountNumber').value.trim();
        const branch = document.getElementById('modalBranch').value.trim();
        const openingBalance = document.getElementById('modalOpeningBalance').value.trim() || '0';
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
                <button type="button" class="btn btn-primary btn-sm fw-semibold" id="btnSaveQuickBank" onclick="saveQuickBankAjax('expenseBankSelect')">
                    <i class="bi bi-check-lg me-1"></i> Save & Select Bank
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
