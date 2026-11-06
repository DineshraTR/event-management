@extends('layouts.app')
@section('title', 'Add Bank Account')
@section('page-title', 'Add Bank Account')

@section('content')
<!-- Page Header (Tritcal Style) -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0"
            style="width: 44px; height: 44px; background: linear-gradient(135deg, #484646 0%, #3a3838 100%); font-size: 20px;">
            <i class="bi bi-bank2"></i>
        </div>
        <div>
            <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.3px;">Add New Bank Account</h4>
            <p class="text-muted small mb-0">Register a company or commercial bank account for accepting deposits, transfers, and cheques.</p>
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
                    <i class="bi bi-building text-primary"></i> Bank Account Information
                </h6>
                <small class="text-muted"><i class="bi bi-asterisk text-danger" style="font-size: 8px;"></i> Required fields</small>
            </div>

            <form method="POST" action="{{ route('banks.store') }}">
                @csrf
                <div class="card-body p-3 p-md-4">
                    <div class="row g-3">
                        <!-- Bank Name -->
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">
                                Bank Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="bank_name" class="form-control" list="commonBanksList" 
                                value="{{ old('bank_name') }}" placeholder="e.g. Commercial Bank of Ceylon" required autofocus>
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
                                value="{{ old('account_name') }}" placeholder="e.g. Company Main Operations A/C" required>
                        </div>

                        <!-- Account Number -->
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">
                                Account Number <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="account_number" class="form-control font-monospace" 
                                value="{{ old('account_number') }}" placeholder="e.g. 100029384910" required>
                        </div>

                        <!-- Branch -->
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">
                                Branch Name / Location
                            </label>
                            <input type="text" name="branch" class="form-control" 
                                value="{{ old('branch') }}" placeholder="e.g. Location">
                        </div>

                        <!-- Swift Code -->
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-muted mb-1">
                                SWIFT / Branch Code
                            </label>
                            <input type="text" name="swift_code" class="form-control font-monospace" 
                                value="{{ old('swift_code') }}" placeholder="e.g. CCEYLKLX">
                        </div>

                        <!-- Contact Number -->
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-muted mb-1">
                                Branch Contact Number
                            </label>
                            <input type="text" name="contact_number" class="form-control" 
                                value="{{ old('contact_number') }}" placeholder="e.g. 011-2345678">
                        </div>

                        <!-- Opening Balance -->
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold text-muted mb-1">
                                Opening Balance (Rs.)
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="opening_balance" class="form-control" 
                                    value="{{ old('opening_balance', '0.00') }}" placeholder="0.00">
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted mb-1">
                                Notes & Special Instructions
                            </label>
                            <textarea name="notes" rows="2" class="form-control" placeholder="Any specific account remarks or online banking reference details...">{{ old('notes') }}</textarea>
                        </div>

                        <!-- Active Toggle -->
                        <div class="col-12">
                            <div class="form-check form-switch pt-2">
                                <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="isActiveSwitch" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-semibold text-dark" for="isActiveSwitch">
                                    Account is Active (Available for recording transactions & deposits)
                                </label>
                            </div>
                        </div>

                        <!-- Primary Bank Toggle (Only shown if NO primary bank currently exists) -->
                        @if(!$hasPrimaryBank)
                            <div class="col-12">
                                <div class="form-check form-switch pt-1">
                                    <input class="form-check-input" type="checkbox" role="switch" name="is_primary" id="isPrimarySwitch" value="1" {{ old('is_primary') ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold text-dark" for="isPrimarySwitch">
                                        <i class="bi bi-star-fill text-warning me-1"></i> Designate as Primary Bank Account (Displayed on Invoices)
                                    </label>
                                </div>
                                <small class="text-muted d-block ps-4 ms-2" style="font-size: 11.5px;">
                                    When enabled, this account's details will appear at the bottom of client invoice printouts. Only one bank can be primary.
                                </small>
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
                        <i class="bi bi-check2-circle me-1"></i> Save Bank Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
