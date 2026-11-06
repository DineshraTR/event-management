@extends('layouts.app')
@section('title', 'Edit Bank Account - ' . $bank->bank_name)
@section('page-title', 'Edit Bank Account')

@section('content')
<!-- Page Header (Tritcal Style) -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0"
            style="width: 44px; height: 44px; background: linear-gradient(135deg, #484646 0%, #3a3838 100%); font-size: 20px;">
            <i class="bi bi-pencil-square"></i>
        </div>
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.3px;">Edit Bank Account</h4>
                <span class="badge bg-light text-dark border px-2 py-0.5 font-monospace" style="font-size: 11px;">
                    {{ $bank->account_number }}
                </span>
            </div>
            <p class="text-muted small mb-0">Update account credentials, branch location, contact info, or operational status.</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('banks.index') }}" class="btn btn-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Bank Accounts
        </a>
    </div>
</div>

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

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #edf2f7 !important;">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 13.5px;">
                    <i class="bi bi-building text-primary"></i> Edit Account Details
                </h6>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small">Current Ledger Balance:</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 fw-bold" style="font-size: 12px;">
                        Rs. {{ number_format((float)$bank->current_balance, 2) }}
                    </span>
                </div>
            </div>

            <form method="POST" action="{{ route('banks.update', $bank) }}">
                @csrf
                @method('PUT')
                <div class="card-body p-3 p-md-4">
                    <div class="row g-3">
                        <!-- Bank Name -->
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">
                                Bank Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="bank_name" class="form-control" list="commonBanksList" 
                                value="{{ old('bank_name', $bank->bank_name) }}" placeholder="e.g. Commercial Bank of Ceylon" required autofocus>
                            <datalist id="commonBanksList">
                                <option value="Commercial Bank of Ceylon">
                                <option value="Bank of Ceylon (BOC)">
                                <option value="People's Bank">
                                <option value="Hatton National Bank (HNB)">
                                <option value="Sampath Bank">
                                <option value="Seylan Bank">
                                <option value="Nations Trust Bank (NTB)">
                                <option value="National Development Bank (NDB)">
                                <option value="DFCC Bank">
                                <option value="Pan Asia Bank">
                                <option value="Union Bank">
                                <option value="Standard Chartered Bank">
                                <option value="HSBC Sri Lanka">
                            </datalist>
                        </div>

                        <!-- Account Name / Holder -->
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">
                                Account Name / Holder <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="account_name" class="form-control" 
                                value="{{ old('account_name', $bank->account_name) }}" placeholder="e.g. Company Main Operations A/C" required>
                        </div>

                        <!-- Account Number -->
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">
                                Account Number <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="account_number" class="form-control font-monospace" 
                                value="{{ old('account_number', $bank->account_number) }}" placeholder="e.g. 100029384910" required>
                        </div>

                        <!-- Branch -->
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">
                                Branch Name / Location
                            </label>
                            <input type="text" name="branch" class="form-control" 
                                value="{{ old('branch', $bank->branch) }}" placeholder="e.g. Colombo 03 - Kollupitiya">
                        </div>

                        <!-- Swift Code -->
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">
                                SWIFT / Branch Code
                            </label>
                            <input type="text" name="swift_code" class="form-control font-monospace" 
                                value="{{ old('swift_code', $bank->swift_code) }}" placeholder="e.g. CCEYLKLX">
                        </div>

                        <!-- Contact Number -->
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">
                                Branch Contact Number
                            </label>
                            <input type="text" name="contact_number" class="form-control" 
                                value="{{ old('contact_number', $bank->contact_number) }}" placeholder="e.g. 011-2345678">
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted mb-1">
                                Notes & Special Instructions
                            </label>
                            <textarea name="notes" rows="2" class="form-control" placeholder="Any specific account remarks or online banking reference details...">{{ old('notes', $bank->notes) }}</textarea>
                        </div>

                        <!-- Active Toggle -->
                        <div class="col-12">
                            <div class="form-check form-switch pt-2">
                                <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="isActiveSwitch" value="1" {{ old('is_active', $bank->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-semibold text-dark" for="isActiveSwitch">
                                    Account is Active (Available for recording transactions & deposits)
                                </label>
                            </div>
                        </div>

                        <!-- Primary Bank Toggle -->
                        @if(!$hasOtherPrimary)
                            <div class="col-12">
                                <div class="form-check form-switch pt-1">
                                    <input class="form-check-input" type="checkbox" role="switch" name="is_primary" id="isPrimarySwitch" value="1" {{ old('is_primary', $bank->is_primary) ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold text-dark" for="isPrimarySwitch">
                                        <i class="bi bi-star-fill text-warning me-1"></i> Designate as Primary Bank Account (Displayed on Invoices)
                                    </label>
                                </div>
                                <small class="text-muted d-block ps-4 ms-2" style="font-size: 11.5px;">
                                    When enabled, this account's details will appear at the bottom of client invoice printouts. Only one bank can be primary.
                                </small>
                            </div>
                        @else
                            <div class="col-12">
                                <div class="p-2.5 rounded-3 bg-light border d-flex align-items-center gap-2 text-muted small">
                                    <i class="bi bi-info-circle text-primary fs-6"></i>
                                    <span>Another bank account is currently designated as the Primary Bank. To make this bank primary, remove primary status from the existing one or use "Make Primary" from the banks list.</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="card-footer bg-white py-3 border-top d-flex align-items-center justify-content-between">
                    <a href="{{ route('banks.index') }}" class="btn btn-secondary shadow-sm">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary shadow-sm px-4">
                        <i class="bi bi-check2-circle me-1"></i> Update Bank Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
