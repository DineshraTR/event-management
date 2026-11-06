@extends('layouts.app')
@section('title', 'Bank Accounts')
@section('page-title', 'Bank Accounts')

@section('content')
<!-- Page Header (Tritcal Style) -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0"
            style="width: 44px; height: 44px; background: linear-gradient(135deg, #484646 0%, #3a3838 100%); font-size: 20px;">
            <i class="bi bi-bank2"></i>
        </div>
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.3px;">Bank Accounts</h4>
                <span class="badge bg-light text-dark border px-2 py-0.5" style="font-size: 11px; font-weight: 700;">
                    {{ $totalBanksCount }} Accounts
                </span>
            </div>
            <p class="text-muted small mb-0">Manage company bank accounts, branches, account numbers, and payment deposit channels.</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('banks.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Add Bank Account
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 shadow-sm" role="alert" style="border-radius: 10px;">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div>
            {{ session('success') }}
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- KPI Statistics Overview -->
<div class="row g-3 mb-4">
    <!-- Total Banks -->
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; border: 1px solid #edf2f7 !important;">
            <div class="card-body p-3.5 d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width: 48px; height: 48px; background-color: #f1f5f9; color: #334155; font-size: 22px;">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Registered Banks</span>
                    <h5 class="fw-bold text-dark mb-0 mt-0.5" style="font-size: 20px;">
                        {{ $totalBanksCount }}
                    </h5>
                    <small class="text-muted" style="font-size: 11px;">All institutional accounts</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Accounts -->
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; border: 1px solid #edf2f7 !important;">
            <div class="card-body p-3.5 d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width: 48px; height: 48px; background-color: #ecfdf5; color: #059669; font-size: 22px;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Active Accounts</span>
                    <h5 class="fw-bold text-success mb-0 mt-0.5" style="font-size: 20px;">
                        {{ $activeBanksCount }}
                    </h5>
                    <small class="text-success fw-medium" style="font-size: 11px;">Available for payment transactions</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Balance Recorded -->
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; border: 1px solid #edf2f7 !important;">
            <div class="card-body p-3.5 d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width: 48px; height: 48px; background-color: #eff6ff; color: #2563eb; font-size: 22px;">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Total Liquid Balance</span>
                    <h5 class="fw-bold text-dark mb-0 mt-0.5" style="font-size: 19px;">
                        Rs. {{ number_format((float)$totalBalance, 2) }}
                    </h5>
                    <small class="text-muted" style="font-size: 11px;">Recorded ledger balances</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #edf2f7 !important;">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 13.5px;">
            <i class="bi bi-funnel text-secondary"></i> Search & Filters
        </h6>
        @if(request()->anyFilled(['search', 'status']))
            <a href="{{ route('banks.index') }}" class="btn btn-sm btn-link text-decoration-none text-danger p-0" style="font-size: 12px;">
                <i class="bi bi-x-circle me-1"></i> Clear Filters
            </a>
        @endif
    </div>
    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('banks.index') }}">
            <div class="row g-3">
                <div class="col-12 col-md-7 col-lg-8">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-search me-1"></i> Search Bank
                    </label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Bank name, account number, account holder, branch...">
                </div>
                <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-toggle-on me-1"></i> Status
                    </label>
                    <select name="status" class="form-select">
                        <option value="all">All Accounts</option>
                        <option value="active" @selected(request('status') === 'active')>Active Only</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive Only</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-2 col-lg-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary shadow-sm flex-grow-1">
                        <i class="bi bi-funnel-fill me-1"></i> Filter
                    </button>
                    <a href="{{ route('banks.index') }}" class="btn btn-secondary shadow-sm">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Banks Table Card -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid #edf2f7 !important;">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 13.5px;">
                <i class="bi bi-list-check text-secondary"></i> Bank Accounts List
            </h6>
        </div>
        <div class="text-muted small">
            @if($banks->total() > 0)
                Showing <strong>{{ $banks->firstItem() }}-{{ $banks->lastItem() }}</strong> of <strong>{{ $banks->total() }}</strong> records
            @else
                0 records found
            @endif
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted" style="border-bottom: 2px solid #edf2f7;">
                <tr>
                    <th class="ps-4">Bank & Branch</th>
                    <th>Account Name / Holder</th>
                    <th>Account Number</th>
                    <th>Swift / Branch Code</th>
                    <th class="text-end">Balance</th>
                    <th class="text-center">Status</th>
                    <th class="text-end pe-4 text-nowrap" style="width: 1%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($banks as $bank)
                    <tr>
                        <!-- Bank Name & Branch -->
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-3 d-flex align-items-center justify-content-center text-primary flex-shrink-0"
                                    style="width: 36px; height: 36px; background-color: #eff6ff; font-size: 17px;">
                                    <i class="bi bi-bank"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark d-flex align-items-center gap-1.5 flex-wrap" style="font-size: 13.5px;">
                                        <span>{{ $bank->bank_name }}</span>
                                        @if($bank->is_primary)
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-0.5" style="font-size: 10px; font-weight: 700;">
                                                <i class="bi bi-star-fill text-warning me-0.5"></i> Primary
                                            </span>
                                        @endif
                                    </div>
                                    @if($bank->branch)
                                        <small class="text-muted" style="font-size: 11.5px;">
                                            <i class="bi bi-geo-alt me-1 text-secondary"></i>{{ $bank->branch }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Account Name -->
                        <td>
                            <div class="fw-semibold text-dark" style="font-size: 13px;">
                                {{ $bank->account_name }}
                            </div>
                            @if($bank->contact_number)
                                <small class="text-muted" style="font-size: 11px;">
                                    <i class="bi bi-telephone me-1"></i>{{ $bank->contact_number }}
                                </small>
                            @endif
                        </td>

                        <!-- Account Number -->
                        <td>
                            <div class="d-flex align-items-center gap-1.5 font-monospace">
                                <span class="badge bg-light text-dark border px-2.5 py-1" style="font-size: 12px; letter-spacing: 0.5px;">
                                    {{ $bank->account_number }}
                                </span>
                            </div>
                        </td>

                        <!-- Swift Code -->
                        <td>
                            @if($bank->swift_code)
                                <span class="badge bg-light text-secondary border px-2 py-0.5 font-monospace" style="font-size: 11px;">
                                    {{ $bank->swift_code }}
                                </span>
                            @else
                                <span class="text-muted fst-italic small">—</span>
                            @endif
                        </td>

                        <!-- Current Balance -->
                        <td class="text-end">
                            <div class="fw-bold text-dark" style="font-size: 13px;">
                                Rs. {{ number_format((float)$bank->current_balance, 2) }}
                            </div>
                            @if((float)$bank->opening_balance > 0)
                                <small class="text-muted" style="font-size: 10.5px;">
                                    Opening: Rs. {{ number_format((float)$bank->opening_balance, 2) }}
                                </small>
                            @endif
                        </td>

                        <!-- Status -->
                        <td class="text-center">
                            @if($bank->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1" style="font-size: 11px; font-weight: 600;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #22c55e; display: inline-block; margin-right: 4px;"></span> Active
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1" style="font-size: 11px; font-weight: 600;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #94a3b8; display: inline-block; margin-right: 4px;"></span> Inactive
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="text-end pe-4 text-nowrap">
                            <div class="d-inline-flex align-items-center justify-content-end gap-1.5">
                                @if(!$bank->is_primary && $bank->is_active)
                                    <!-- Set as Primary -->
                                    <form method="POST" action="{{ route('banks.set-primary', $bank) }}" class="m-0 p-0" onsubmit="return confirm('Set {{ addslashes($bank->bank_name) }} as the Primary Bank for invoices & quotations?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning text-dark px-2 py-0.5 d-inline-flex align-items-center gap-1" style="height: 28px; font-size: 11px; font-weight: 600;" title="Designate as Primary Bank">
                                            <i class="bi bi-star"></i> Set Primary
                                        </button>
                                    </form>
                                @endif

                                <!-- Edit -->
                                <a href="{{ route('banks.edit', $bank) }}" class="btn-action btn-action-edit" style="width: 70px; height: 28px;" title="Edit Bank Details">
                                    <i class="bi bi-pencil"></i> <span>Edit</span>
                                </a>

                                <!-- Status Toggle (Deactivate / Activate) -->
                                <form method="POST" action="{{ route('banks.toggle-status', $bank) }}" class="m-0 p-0" onsubmit="return confirm('Are you sure you want to {{ $bank->is_active ? 'deactivate' : 'activate' }} this bank account?');">
                                    @csrf
                                    @if($bank->is_active)
                                        <button type="submit" class="btn-action btn-action-delete" style="width: 95px; height: 28px;" title="Deactivate Bank Account">
                                            <i class="bi bi-slash-circle"></i> <span>Deactivate</span>
                                        </button>
                                    @else
                                        <button type="submit" class="btn-action btn-action-order" style="width: 85px; height: 28px;" title="Activate Bank Account">
                                            <i class="bi bi-check-circle"></i> <span>Activate</span>
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-bank2 fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <div class="fw-semibold" style="font-size: 14px;">No bank accounts registered</div>
                            <small class="text-muted">Register your first company bank account to manage deposits, cheques, and online payments.</small>
                            <div class="mt-3">
                                <a href="{{ route('banks.create') }}" class="btn btn-sm btn-primary shadow-sm">
                                    <i class="bi bi-plus-lg me-1"></i> Add Bank Account
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($banks->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                Showing {{ $banks->firstItem() }} to {{ $banks->lastItem() }} of {{ $banks->total() }} accounts
            </span>
            <div>
                {{ $banks->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
