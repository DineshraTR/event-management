@extends('layouts.app')
@section('title', 'Cheque Management')
@section('page-title', 'Cheque Management')

@section('content')
<!-- Page Header (EventPro / Tritcal Style) -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 no-print">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0"
            style="width: 44px; height: 44px; background: linear-gradient(135deg, #484646 0%, #3a3838 100%); font-size: 20px;">
            <i class="bi bi-card-heading"></i>
        </div>
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.3px;">Cheque Management</h4>
                <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 11px; font-weight: 700;">
                    Inward &amp; Outward Ledger
                </span>
                <span class="badge bg-light text-dark border px-2 py-0.5" style="font-size: 11px;">
                    {{ $cheques->total() }} Records
                </span>
            </div>
            <p class="text-muted small mb-0">Track customer cheques received (In Hand, Deposited, Realized, Returned) &amp; cheques issued to vendors with clearance control.</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap page-header-actions">
        <a href="{{ route('reports.cheques') }}" class="btn btn-outline-secondary shadow-sm" title="View Cheques Audit & Clearance Ledger Report">
            <i class="bi bi-file-earmark-bar-graph me-1 text-primary"></i> Cheques Report
        </a>
        <button type="button" class="btn btn-outline-secondary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddChequeOut">
            <i class="bi bi-arrow-up-right-circle me-1 text-danger"></i> + Issue Cheque (Out)
        </button>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddChequeIn">
            <i class="bi bi-plus-lg me-1"></i> + Record Received Cheque (In)
        </button>
    </div>
</div>

<!-- Alert / Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 shadow-sm no-print" role="alert" style="border-radius: 10px;">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4 shadow-sm no-print" role="alert" style="border-radius: 10px;">
        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm no-print" role="alert" style="border-radius: 10px;">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle me-1"></i> Please check the form errors below:</div>
        <ul class="mb-0 ps-3 small">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<style>
    .kpi-card-hover {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .kpi-card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
    }
</style>

<!-- ============================================================== -->
<!-- 1. NAVIGATION TABS & OVERVIEW BANNER                          -->
<!-- ============================================================== -->
<div class="card shadow-sm border-0 mb-3 no-print" style="border-radius: 12px; border: 1px solid #edf2f7 !important;">
    <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <!-- Tabs Buttons -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('cheques.index', ['tab' => 'in']) }}"
               class="btn {{ $activeTab === 'in' ? 'btn-primary' : 'btn-outline-secondary' }} px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm"
               style="font-size: 13px; border-radius: 8px;">
                <i class="bi bi-arrow-down-left-circle {{ $activeTab === 'in' ? 'text-white' : 'text-success' }}"></i>
                <span>Cheque In (Received Cheques)</span>
                <span class="badge {{ $activeTab === 'in' ? 'bg-white text-primary' : 'bg-secondary text-white' }} rounded-pill ms-1" style="font-size: 11px;">
                    {{ $kpiTotalIn['cnt'] }}
                </span>
            </a>
            <a href="{{ route('cheques.index', ['tab' => 'out']) }}"
               class="btn {{ $activeTab === 'out' ? 'btn-primary' : 'btn-outline-secondary' }} px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm"
               style="font-size: 13px; border-radius: 8px;">
                <i class="bi bi-arrow-up-right-circle {{ $activeTab === 'out' ? 'text-white' : 'text-danger' }}"></i>
                <span>Cheque Out (Issued Cheques)</span>
                <span class="badge {{ $activeTab === 'out' ? 'bg-white text-primary' : 'bg-secondary text-white' }} rounded-pill ms-1" style="font-size: 11px;">
                    {{ $kpiTotalOut['cnt'] }}
                </span>
            </a>
        </div>

        <!-- Portfolio Totals Snapshot -->
        <div class="d-flex align-items-center gap-3 ms-auto text-nowrap">
            <div class="text-end border-end pe-3">
                <span class="text-muted small d-block" style="font-size: 11px;">Total Inward Received</span>
                <strong class="text-success" style="font-size: 13.5px;">Rs. {{ number_format($kpiTotalIn['total'], 2) }}</strong>
                <span class="text-muted small">({{ $kpiTotalIn['cnt'] }})</span>
            </div>
            <div class="text-end">
                <span class="text-muted small d-block" style="font-size: 11px;">Total Outward Issued</span>
                <strong class="text-danger" style="font-size: 13.5px;">Rs. {{ number_format($kpiTotalOut['total'], 2) }}</strong>
                <span class="text-muted small">({{ $kpiTotalOut['cnt'] }})</span>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- 2. KPI STAT CARDS (Tab-Specific & Interactive Filter Cards)    -->
<!-- ============================================================== -->
<div class="row g-3 mb-4">
    @if($activeTab === 'in')
        <!-- CHEQUE IN CARDS -->
        <!-- 1. Cheques In Hand -->
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('cheques.index', ['tab' => 'in', 'status' => 'In Hand']) }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100 kpi-card-hover" 
                     style="border-radius: 12px; border: {{ $statusFilter === 'In Hand' ? '2.5px solid #d97706' : '1px solid #edf2f7' }} !important; background: {{ $statusFilter === 'In Hand' ? '#fffdf7' : '#ffffff' }};">
                    <div class="card-body p-3.5 d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                            style="width: 48px; height: 48px; background-color: #fffbeb; color: #d97706; font-size: 22px;">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Cheques In Hand</span>
                                @if($statusFilter === 'In Hand')
                                    <span class="badge bg-warning text-dark px-1.5 py-0.5" style="font-size: 9.5px;">Active</span>
                                @endif
                            </div>
                            <h5 class="fw-bold text-dark mb-0 mt-0.5 text-truncate" style="font-size: 18px;">
                                Rs. {{ number_format($kpiInHand['total'], 2) }}
                            </h5>
                            <small class="text-muted" style="font-size: 11px;">{{ $kpiInHand['cnt'] }} Cheque(s) in office/safe</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- 2. Deposited (In Clearing) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('cheques.index', ['tab' => 'in', 'status' => 'Deposited']) }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100 kpi-card-hover" 
                     style="border-radius: 12px; border: {{ $statusFilter === 'Deposited' ? '2.5px solid #2563eb' : '1px solid #edf2f7' }} !important; background: {{ $statusFilter === 'Deposited' ? '#f8faff' : '#ffffff' }};">
                    <div class="card-body p-3.5 d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                            style="width: 48px; height: 48px; background-color: #eff6ff; color: #2563eb; font-size: 22px;">
                            <i class="bi bi-bank2"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Deposited (In Clearing)</span>
                                @if($statusFilter === 'Deposited')
                                    <span class="badge bg-primary text-white px-1.5 py-0.5" style="font-size: 9.5px;">Active</span>
                                @endif
                            </div>
                            <h5 class="fw-bold text-primary mb-0 mt-0.5 text-truncate" style="font-size: 18px;">
                                Rs. {{ number_format($kpiDeposited['total'], 2) }}
                            </h5>
                            <small class="text-muted" style="font-size: 11px;">{{ $kpiDeposited['cnt'] }} Cheque(s) in bank pipeline</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- 3. Realized (Cleared In) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('cheques.index', ['tab' => 'in', 'status' => 'Realized']) }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100 kpi-card-hover" 
                     style="border-radius: 12px; border: {{ $statusFilter === 'Realized' ? '2.5px solid #059669' : '1px solid #edf2f7' }} !important; background: {{ $statusFilter === 'Realized' ? '#f6fdf9' : '#ffffff' }};">
                    <div class="card-body p-3.5 d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                            style="width: 48px; height: 48px; background-color: #ecfdf5; color: #059669; font-size: 22px;">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Realized (Cleared In)</span>
                                @if($statusFilter === 'Realized')
                                    <span class="badge bg-success text-white px-1.5 py-0.5" style="font-size: 9.5px;">Active</span>
                                @endif
                            </div>
                            <h5 class="fw-bold text-success mb-0 mt-0.5 text-truncate" style="font-size: 18px;">
                                Rs. {{ number_format($kpiRealizedIn['total'], 2) }}
                            </h5>
                            <small class="text-muted" style="font-size: 11px;">{{ $kpiRealizedIn['cnt'] }} Cheque(s) credited to bank</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- 4. Returned (Bounced In) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('cheques.index', ['tab' => 'in', 'status' => 'Returned']) }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100 kpi-card-hover" 
                     style="border-radius: 12px; border: {{ $statusFilter === 'Returned' ? '2.5px solid #e11d48' : '1px solid #edf2f7' }} !important; background: {{ $statusFilter === 'Returned' ? '#fdf8f8' : '#ffffff' }};">
                    <div class="card-body p-3.5 d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                            style="width: 48px; height: 48px; background-color: #fff1f2; color: #e11d48; font-size: 22px;">
                            <i class="bi bi-arrow-return-left"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Returned Cheques</span>
                                @if($statusFilter === 'Returned')
                                    <span class="badge bg-danger text-white px-1.5 py-0.5" style="font-size: 9.5px;">Active</span>
                                @endif
                            </div>
                            <h5 class="fw-bold text-danger mb-0 mt-0.5 text-truncate" style="font-size: 18px;">
                                Rs. {{ number_format($kpiReturnedIn['total'], 2) }}
                            </h5>
                            <small class="text-danger fw-semibold" style="font-size: 11px;">{{ $kpiReturnedIn['cnt'] }} Bounced / Due back</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @else
        <!-- CHEQUE OUT CARDS -->
        <!-- 1. Issued (Pending Clearance) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('cheques.index', ['tab' => 'out', 'status' => 'Issued']) }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100 kpi-card-hover" 
                     style="border-radius: 12px; border: {{ $statusFilter === 'Issued' ? '2.5px solid #d97706' : '1px solid #edf2f7' }} !important; background: {{ $statusFilter === 'Issued' ? '#fffdf7' : '#ffffff' }};">
                    <div class="card-body p-3.5 d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                            style="width: 48px; height: 48px; background-color: #fffbeb; color: #d97706; font-size: 22px;">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Issued (Pending Clearance)</span>
                                @if($statusFilter === 'Issued')
                                    <span class="badge bg-warning text-dark px-1.5 py-0.5" style="font-size: 9.5px;">Active</span>
                                @endif
                            </div>
                            <h5 class="fw-bold text-dark mb-0 mt-0.5 text-truncate" style="font-size: 18px;">
                                Rs. {{ number_format($kpiIssuedOut['total'], 2) }}
                            </h5>
                            <small class="text-muted" style="font-size: 11px;">{{ $kpiIssuedOut['cnt'] }} Cheque(s) pending debit</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- 2. Realized (Cleared Out) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('cheques.index', ['tab' => 'out', 'status' => 'Realized']) }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100 kpi-card-hover" 
                     style="border-radius: 12px; border: {{ $statusFilter === 'Realized' ? '2.5px solid #059669' : '1px solid #edf2f7' }} !important; background: {{ $statusFilter === 'Realized' ? '#f6fdf9' : '#ffffff' }};">
                    <div class="card-body p-3.5 d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                            style="width: 48px; height: 48px; background-color: #ecfdf5; color: #059669; font-size: 22px;">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Realized (Cleared Out)</span>
                                @if($statusFilter === 'Realized')
                                    <span class="badge bg-success text-white px-1.5 py-0.5" style="font-size: 9.5px;">Active</span>
                                @endif
                            </div>
                            <h5 class="fw-bold text-success mb-0 mt-0.5 text-truncate" style="font-size: 18px;">
                                Rs. {{ number_format($kpiRealizedOut['total'], 2) }}
                            </h5>
                            <small class="text-muted" style="font-size: 11px;">{{ $kpiRealizedOut['cnt'] }} Cheque(s) debited from bank</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- 3. Returned (Bounced Out) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('cheques.index', ['tab' => 'out', 'status' => 'Returned']) }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100 kpi-card-hover" 
                     style="border-radius: 12px; border: {{ $statusFilter === 'Returned' ? '2.5px solid #e11d48' : '1px solid #edf2f7' }} !important; background: {{ $statusFilter === 'Returned' ? '#fdf8f8' : '#ffffff' }};">
                    <div class="card-body p-3.5 d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                            style="width: 48px; height: 48px; background-color: #fff1f2; color: #e11d48; font-size: 22px;">
                            <i class="bi bi-arrow-return-left"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Returned Cheques</span>
                                @if($statusFilter === 'Returned')
                                    <span class="badge bg-danger text-white px-1.5 py-0.5" style="font-size: 9.5px;">Active</span>
                                @endif
                            </div>
                            <h5 class="fw-bold text-danger mb-0 mt-0.5 text-truncate" style="font-size: 18px;">
                                Rs. {{ number_format($kpiReturnedOut['total'], 2) }}
                            </h5>
                            <small class="text-danger fw-semibold" style="font-size: 11px;">{{ $kpiReturnedOut['cnt'] }} Bounced / Returned</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- 4. Cancelled / Void Cheques -->
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('cheques.index', ['tab' => 'out', 'status' => 'Cancelled']) }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100 kpi-card-hover" 
                     style="border-radius: 12px; border: {{ $statusFilter === 'Cancelled' ? '2.5px solid #64748b' : '1px solid #edf2f7' }} !important; background: {{ $statusFilter === 'Cancelled' ? '#f8fafc' : '#ffffff' }};">
                    <div class="card-body p-3.5 d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                            style="width: 48px; height: 48px; background-color: #f1f5f9; color: #64748b; font-size: 22px;">
                            <i class="bi bi-x-circle"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Cancelled / Void</span>
                                @if($statusFilter === 'Cancelled')
                                    <span class="badge bg-secondary text-white px-1.5 py-0.5" style="font-size: 9.5px;">Active</span>
                                @endif
                            </div>
                            <h5 class="fw-bold text-secondary mb-0 mt-0.5 text-truncate" style="font-size: 18px;">
                                Rs. {{ number_format($kpiCancelledOut['total'], 2) }}
                            </h5>
                            <small class="text-muted" style="font-size: 11px;">{{ $kpiCancelledOut['cnt'] }} Cheque(s) cancelled/void</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endif
</div>

<!-- ============================================================== -->
<!-- 3. SEARCH & FILTER CARD (EventPro Standard Form Styling)       -->
<!-- ============================================================== -->
<div class="card shadow-sm border-0 mb-4 no-print" style="border-radius: 12px; border: 1px solid #edf2f7 !important;">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 13.5px;">
            <i class="bi bi-funnel text-secondary"></i> Search &amp; Filter {{ $activeTab === 'in' ? 'Received Cheques' : 'Issued Cheques' }}
        </h6>
        @if(request()->anyFilled(['search', 'status', 'from_date', 'to_date', 'bank_id']))
            <a href="{{ route('cheques.index', ['tab' => $activeTab]) }}" class="btn btn-sm btn-link text-decoration-none text-danger p-0" style="font-size: 12px;">
                <i class="bi bi-x-circle me-1"></i> Clear Filters
            </a>
        @endif
    </div>

    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('cheques.index') }}" id="chequesFilterForm">
            <input type="hidden" name="tab" value="{{ $activeTab }}">
            <div class="row g-3">
                <!-- 1. Search Keyword -->
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-search me-1"></i> Search Cheque # / {{ $activeTab === 'in' ? 'Customer' : 'Payee' }}
                    </label>
                    <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="Cheque number, customer, notes...">
                </div>

                <!-- 2. Cheque Status -->
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-tag me-1"></i> Cheque Status
                    </label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        @if($activeTab === 'in')
                            <option value="In Hand" @selected($statusFilter === 'In Hand')>In Hand (In Office / Safe)</option>
                            <option value="Deposited" @selected($statusFilter === 'Deposited')>Deposited (In Bank Clearing)</option>
                            <option value="Realized" @selected($statusFilter === 'Realized')>Realized (Cleared to Bank)</option>
                            <option value="Returned" @selected($statusFilter === 'Returned')>Returned (Bounced)</option>
                            <option value="Cancelled" @selected($statusFilter === 'Cancelled')>Cancelled</option>
                        @else
                            <option value="Issued" @selected($statusFilter === 'Issued')>Issued (Pending Clearance)</option>
                            <option value="Realized" @selected($statusFilter === 'Realized')>Realized (Cleared & Debited)</option>
                            <option value="Returned" @selected($statusFilter === 'Returned')>Returned (Bounced Out)</option>
                            <option value="Cancelled" @selected($statusFilter === 'Cancelled')>Cancelled / Void</option>
                        @endif
                    </select>
                </div>

                <!-- 3. Company Bank -->
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-bank2 me-1"></i> Company Bank
                    </label>
                    <select name="bank_id" class="form-select">
                        <option value="">All Accounts</option>
                        @foreach($banks as $b)
                            <option value="{{ $b->id }}" @selected($bankFilter == $b->id)>
                                {{ $b->bank_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Date Range -->
                <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-calendar-event me-1"></i> From Date
                    </label>
                    <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}">
                </div>

                <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-calendar-check me-1"></i> To Date
                    </label>
                    <div class="d-flex gap-2">
                        <input type="date" name="to_date" class="form-control" value="{{ $toDate }}">
                        <button type="submit" class="btn btn-primary shadow-sm" style="min-width: 42px;" title="Apply Filter">
                            <i class="bi bi-filter"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- 4. CHEQUES DATA TABLE CARD (EventPro Unified Styling)           -->
<!-- ============================================================== -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #edf2f7 !important;">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 13.5px;">
            <i class="bi bi-table text-secondary"></i>
            {{ $activeTab === 'in' ? 'Received Cheques Ledger' : 'Issued Cheques Ledger' }}
            <span class="text-muted fw-normal" style="font-size: 12px;">({{ $cheques->total() }} records found)</span>
        </h6>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
            <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                <tr>
                    <th class="text-uppercase fw-bold text-secondary" style="font-size: 11px; letter-spacing: 0.5px; padding: 12px 16px; width: 140px;">Cheque Details</th>
                    <th class="text-uppercase fw-bold text-secondary" style="font-size: 11px; letter-spacing: 0.5px; padding: 12px 16px;">{{ $activeTab === 'in' ? 'Customer / Order' : 'Payee / Expense' }}</th>
                    <th class="text-uppercase fw-bold text-secondary" style="font-size: 11px; letter-spacing: 0.5px; padding: 12px 16px;">Bank Details</th>
                    <th class="text-uppercase fw-bold text-secondary text-end" style="font-size: 11px; letter-spacing: 0.5px; padding: 12px 16px; width: 140px;">Amount</th>
                    <th class="text-uppercase fw-bold text-secondary" style="font-size: 11px; letter-spacing: 0.5px; padding: 12px 16px; width: 170px;">Timeline</th>
                    <th class="text-uppercase fw-bold text-secondary text-center" style="font-size: 11px; letter-spacing: 0.5px; padding: 12px 16px; width: 130px;">Status</th>
                    <th class="text-uppercase fw-bold text-secondary text-center no-print" style="font-size: 11px; letter-spacing: 0.5px; padding: 12px 16px; width: 170px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cheques as $c)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <!-- 1. Cheque Details -->
                        <td style="padding: 12px 16px;">
                            <div class="fw-bold text-dark font-monospace" style="font-size: 13.5px;">#{{ $c->cheque_number }}</div>
                            <div class="text-muted small mt-0.5">
                                <i class="bi bi-calendar3 me-1"></i>{{ $c->cheque_date ? $c->cheque_date->format('d M Y') : '-' }}
                            </div>
                        </td>

                        <!-- 2. Customer / Payee -->
                        <td style="padding: 12px 16px;">
                            @if($c->type === 'IN')
                                <div class="fw-bold text-dark">{{ $c->customer?->name ?? 'General Customer' }}</div>
                                @if($c->order)
                                    <a href="{{ route('orders.show', $c->order) }}" class="badge bg-light text-dark border text-decoration-none mt-1" style="font-size: 11px;">
                                        <i class="bi bi-receipt me-1"></i>Order: {{ $c->order->order_no }}
                                    </a>
                                @endif
                            @else
                                <div class="fw-bold text-dark">{{ $c->payee_name }}</div>
                                @if($c->expense)
                                    <a href="{{ route('expenses.show', $c->expense) }}" class="badge bg-light text-dark border text-decoration-none mt-1" style="font-size: 11px;">
                                        <i class="bi bi-receipt-cutoff me-1"></i>Exp: {{ $c->expense->expense_no }}
                                    </a>
                                @endif
                            @endif
                        </td>

                        <!-- 3. Bank Information -->
                        <td style="padding: 12px 16px;">
                            @if($c->type === 'IN')
                                <div class="fw-semibold text-secondary" style="font-size: 12.5px;">{{ $c->payer_bank ?: 'Customer Bank' }}</div>
                                @if($c->payer_branch)
                                    <small class="text-muted d-block" style="font-size: 11px;">Branch: {{ $c->payer_branch }}</small>
                                @endif
                                @if($c->bank)
                                    <small class="text-primary fw-semibold d-block mt-0.5" style="font-size: 11.5px;">
                                        <i class="bi bi-building me-1"></i>Banked in {{ $c->bank->bank_name }}
                                    </small>
                                @endif
                            @else
                                <div class="fw-semibold text-primary" style="font-size: 12.5px;">{{ $c->bank?->bank_name ?: 'Company Bank' }}</div>
                                <small class="text-muted d-block" style="font-size: 11px;">Acc: {{ $c->bank?->account_number ?: '-' }}</small>
                            @endif
                        </td>

                        <!-- 4. Amount -->
                        <td class="text-end" style="padding: 12px 16px;">
                            <div class="fw-bold {{ $c->status === 'Returned' ? 'text-danger' : 'text-dark' }}" style="font-size: 14px;">
                                Rs. {{ number_format($c->amount, 2) }}
                            </div>
                        </td>

                        <!-- 5. Timeline -->
                        <td style="padding: 12px 16px;">
                            <div class="small lh-sm" style="font-size: 11.5px;">
                                <div><span class="text-muted">{{ $c->type === 'IN' ? 'Recv' : 'Issued' }}:</span> {{ $c->received_or_issued_date ? $c->received_or_issued_date->format('d M Y') : '-' }}</div>
                                @if($c->deposit_date)
                                    <div class="text-primary mt-0.5"><span class="text-muted">Banked:</span> {{ $c->deposit_date->format('d M Y') }}</div>
                                @endif
                                @if($c->clearance_date)
                                    <div class="text-success fw-semibold mt-0.5"><i class="bi bi-check-lg me-1"></i>Cleared: {{ $c->clearance_date->format('d M Y') }}</div>
                                @endif
                                @if($c->return_date)
                                    <div class="text-danger fw-bold mt-0.5"><i class="bi bi-x-lg me-1"></i>Ret: {{ $c->return_date->format('d M Y') }}</div>
                                @endif
                            </div>
                        </td>

                        <!-- 6. Status Badge (Soft Pill Style) -->
                        <td class="text-center" style="padding: 12px 16px;">
                            @if($c->status === 'In Hand')
                                <span class="badge" style="background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a; font-weight: 700; font-size: 11px; padding: 4px 8px; border-radius: 6px;">
                                    <i class="bi bi-wallet2 me-1"></i> In Hand
                                </span>
                            @elseif($c->status === 'Deposited')
                                <span class="badge" style="background-color: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-weight: 700; font-size: 11px; padding: 4px 8px; border-radius: 6px;">
                                    <i class="bi bi-hourglass-split me-1"></i> Deposited
                                </span>
                            @elseif($c->status === 'Realized')
                                <span class="badge" style="background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-weight: 700; font-size: 11px; padding: 4px 8px; border-radius: 6px;">
                                    <i class="bi bi-check2-circle me-1"></i> Realized
                                </span>
                            @elseif($c->status === 'Returned')
                                <span class="badge" style="background-color: #fff1f2; color: #be123c; border: 1px solid #fecdd3; font-weight: 700; font-size: 11px; padding: 4px 8px; border-radius: 6px;">
                                    <i class="bi bi-arrow-return-left me-1"></i> Returned
                                </span>
                                @if($c->return_reason)
                                    <div class="text-danger mt-1" style="font-size: 10.5px; max-width: 140px; margin: 0 auto; line-height: 1.2;">
                                        {{ Str::limit($c->return_reason, 36) }}
                                    </div>
                                @endif
                            @elseif($c->status === 'Issued')
                                <span class="badge" style="background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; font-weight: 700; font-size: 11px; padding: 4px 8px; border-radius: 6px;">
                                    <i class="bi bi-arrow-up-right me-1"></i> Issued
                                </span>
                            @else
                                <span class="badge bg-secondary text-white">{{ $c->status }}</span>
                            @endif
                        </td>

                        <!-- 7. Action Buttons -->
                        <td class="text-center no-print" style="padding: 12px 16px;">
                            <div class="d-flex flex-column gap-1 align-items-stretch" style="max-width: 155px; margin: 0 auto;">
                                @if($c->type === 'IN')
                                    @if($c->status === 'In Hand')
                                        <button type="button" class="btn btn-primary btn-sm py-1 shadow-sm" style="font-size: 11.5px; font-weight: 600;"
                                                onclick="openDepositModal({{ $c->id }}, '{{ $c->cheque_number }}', 'Rs. {{ number_format($c->amount, 2) }}')">
                                            <i class="bi bi-bank2 me-1"></i> Deposit Cheque
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm py-1 shadow-sm" style="font-size: 11.5px; font-weight: 600;"
                                                onclick="openRealizeModal({{ $c->id }}, '{{ $c->cheque_number }}', 'Rs. {{ number_format($c->amount, 2) }}', 'IN', {{ $c->bank_id ?? 'null' }})">
                                            <i class="bi bi-check2 me-1 text-success"></i> Clear Directly
                                        </button>
                                    @elseif($c->status === 'Deposited')
                                        <button type="button" class="btn btn-success btn-sm py-1 shadow-sm" style="font-size: 11.5px; font-weight: 600;"
                                                onclick="openRealizeModal({{ $c->id }}, '{{ $c->cheque_number }}', 'Rs. {{ number_format($c->amount, 2) }}', 'IN', {{ $c->bank_id ?? 'null' }})">
                                            <i class="bi bi-check2-all me-1"></i> Mark Cleared
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm py-1 shadow-sm" style="font-size: 11.5px; font-weight: 600;"
                                                onclick="openReturnModal({{ $c->id }}, '{{ $c->cheque_number }}', 'Rs. {{ number_format($c->amount, 2) }}')">
                                            <i class="bi bi-arrow-return-left me-1"></i> Return
                                        </button>
                                    @elseif($c->status === 'Realized')
                                        <button type="button" class="btn btn-outline-danger btn-sm py-1 shadow-sm" style="font-size: 11.5px; font-weight: 600;"
                                                onclick="openReturnModal({{ $c->id }}, '{{ $c->cheque_number }}', 'Rs. {{ number_format($c->amount, 2) }}')"
                                                title="Cheque bounced after realization - reverses funds">
                                            <i class="bi bi-arrow-return-left me-1"></i> Mark Returned
                                        </button>
                                    @endif
                                @else <!-- OUT -->
                                    @if($c->status === 'Issued')
                                        <button type="button" class="btn btn-success btn-sm py-1 shadow-sm" style="font-size: 11.5px; font-weight: 600;"
                                                onclick="openRealizeModal({{ $c->id }}, '{{ $c->cheque_number }}', 'Rs. {{ number_format($c->amount, 2) }}', 'OUT', {{ $c->bank_id ?? 'null' }})">
                                            <i class="bi bi-check2-all me-1"></i> Mark Cleared
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm py-1 shadow-sm" style="font-size: 11.5px; font-weight: 600;"
                                                onclick="openReturnModal({{ $c->id }}, '{{ $c->cheque_number }}', 'Rs. {{ number_format($c->amount, 2) }}')">
                                            <i class="bi bi-x-circle me-1"></i> Mark Returned
                                        </button>
                                    @elseif($c->status === 'Realized')
                                        <button type="button" class="btn btn-outline-danger btn-sm py-1 shadow-sm" style="font-size: 11.5px; font-weight: 600;"
                                                onclick="openReturnModal({{ $c->id }}, '{{ $c->cheque_number }}', 'Rs. {{ number_format($c->amount, 2) }}')">
                                            <i class="bi bi-arrow-return-left me-1"></i> Cancel / Reverse
                                        </button>
                                    @endif
                                @endif

                                <button type="button" class="btn btn-light border btn-sm py-1 shadow-sm" style="font-size: 11px; font-weight: 600; color: #475569;"
                                        onclick="openViewModal({{ json_encode($c) }}, '{{ $c->customer?->name ?? '' }}', '{{ $c->bank?->bank_name ?? '' }}')">
                                    <i class="bi bi-eye me-1"></i> View Details
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            No {{ $activeTab === 'in' ? 'received' : 'issued' }} cheques found matching the criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($cheques->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center flex-wrap gap-2 no-print">
            <span class="text-muted small">Showing {{ $cheques->firstItem() }} to {{ $cheques->lastItem() }} of {{ $cheques->total() }} cheques</span>
            {{ $cheques->links() }}
        </div>
    @endif
</div>

<!-- ============================================================== -->
<!-- MODALS (EventPro Charcoal Header Style)                        -->
<!-- ============================================================== -->

<!-- MODAL 1: ADD RECEIVED CHEQUE (IN) -->
<div class="modal fade" id="modalAddChequeIn" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <form action="{{ route('cheques.store-in') }}" method="POST">
                @csrf
                <div class="modal-header py-3" style="background: linear-gradient(135deg, #484646 0%, #3a3838 100%); color: #ffffff;">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2" style="font-size: 15px;">
                        <i class="bi bi-arrow-down-left-circle text-success fs-5"></i>
                        <span>Record Received Cheque (Inward)</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 mb-3 rounded-2" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; font-size: 12.5px;">
                        <i class="bi bi-info-circle-fill me-1"></i> <strong>Bank Control:</strong> This cheque will enter as <strong>In Hand</strong> or <strong>Deposited</strong>. Your institutional bank balance will <strong>NOT</strong> change until you realize the cheque in Cheque Management.
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">Cheque Number <span class="text-danger">*</span></label>
                            <input type="text" name="cheque_number" class="form-control" required placeholder="e.g. 784512">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">Amount (Rs.) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" class="form-control" required placeholder="0.00">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">Cheque Date <span class="text-danger">*</span></label>
                            <input type="date" name="cheque_date" class="form-control" value="{{ $today }}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">Received Date <span class="text-danger">*</span></label>
                            <input type="date" name="received_date" class="form-control" value="{{ $today }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">Customer <span class="text-danger">*</span></label>
                            <select name="customer_id" class="form-select" required>
                                <option value="">-- Select Customer --</option>
                                @foreach($customers as $cust)
                                    <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->phone ?: 'No Phone' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">Linked Order (Optional)</label>
                            <select name="order_id" class="form-select">
                                <option value="">-- None / General Advance --</option>
                                @foreach($orders as $ord)
                                    <option value="{{ $ord->id }}">
                                        {{ $ord->order_no }} &bull; {{ $ord->customer?->name }} (Due: Rs. {{ number_format($ord->balance, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">Customer's Bank (Drawn On)</label>
                            <input type="text" name="payer_bank" class="form-control" placeholder="e.g. Commercial Bank / Sampath Bank">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">Customer's Branch</label>
                            <input type="text" name="payer_branch" class="form-control" placeholder="e.g. Colombo Fort">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">Initial Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" id="selectInitialStatus" onchange="toggleDepositFields()">
                                <option value="In Hand" selected>In Hand (In Office / Safe)</option>
                                <option value="Deposited">Deposited (Sent to Bank for Clearing)</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-6 d-none" id="divDepositBank">
                            <label class="form-label small fw-bold text-muted mb-1">Company Bank Account <span class="text-danger">*</span></label>
                            <select name="deposit_bank_id" class="form-select" id="selectDepositBank">
                                <option value="">-- Select Company Bank Account --</option>
                                @foreach($banks as $b)
                                    <option value="{{ $b->id }}">{{ $b->bank_name }} - {{ $b->account_number }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6 d-none" id="divDepositDate">
                            <label class="form-label small fw-bold text-muted mb-1">Deposit Date <span class="text-danger">*</span></label>
                            <input type="date" name="deposit_date" class="form-control" value="{{ $today }}">
                        </div>

                        <div class="col-12">
                            <div class="form-check p-2.5 rounded border bg-light">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="create_payment" value="1" id="chkCreatePayment" checked>
                                <label class="form-check-label small fw-semibold text-dark" for="chkCreatePayment">
                                    Record Order Payment receipt (deducts from Order outstanding balance)
                                </label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted mb-1">Remarks / Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2.5">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary shadow-sm px-4">Save Cheque</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL 2: ISSUE CHEQUE (OUT) -->
<div class="modal fade" id="modalAddChequeOut" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <form action="{{ route('cheques.store-out') }}" method="POST">
                @csrf
                <div class="modal-header py-3" style="background: linear-gradient(135deg, #484646 0%, #3a3838 100%); color: #ffffff;">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2" style="font-size: 15px;">
                        <i class="bi bi-arrow-up-right-circle text-danger fs-5"></i>
                        <span>Issue Cheque to Payee (Outward)</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 mb-3 rounded-2" style="background-color: #fffbeb; border: 1px solid #fde68a; color: #92400e; font-size: 12.5px;">
                        <i class="bi bi-info-circle-fill me-1"></i> <strong>Bank Control:</strong> This cheque will be saved as <strong>Issued</strong>. Bank balance will <strong>NOT</strong> be deducted immediately. Funds are only deducted when marked as Realized / Cleared.
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">Cheque Number <span class="text-danger">*</span></label>
                            <input type="text" name="cheque_number" class="form-control" required placeholder="e.g. 102450">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">Amount (Rs.) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" class="form-control" required placeholder="0.00">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">Payee Name <span class="text-danger">*</span></label>
                            <input type="text" name="payee_name" class="form-control" required placeholder="e.g. Sound System Vendor / ABC Caterers">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">Company Bank Account <span class="text-danger">*</span></label>
                            <select name="bank_id" class="form-select" required>
                                <option value="">-- Select Bank Account --</option>
                                @foreach($banks as $b)
                                    <option value="{{ $b->id }}">{{ $b->bank_name }} - {{ $b->account_number }} (Bal: Rs. {{ number_format($b->current_balance, 2) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">Cheque Date <span class="text-danger">*</span></label>
                            <input type="date" name="cheque_date" class="form-control" value="{{ $today }}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">Issue Date <span class="text-danger">*</span></label>
                            <input type="date" name="issue_date" class="form-control" value="{{ $today }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted mb-1">Remarks / Reason</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Advance for Event decoration stage..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2.5">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary shadow-sm px-4">Issue Cheque</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL 3: DEPOSIT CHEQUE -->
<div class="modal fade" id="modalDepositCheque" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <form id="formDepositCheque" method="POST">
                @csrf
                <div class="modal-header py-3" style="background: linear-gradient(135deg, #484646 0%, #3a3838 100%); color: #ffffff;">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2" style="font-size: 15px;">
                        <i class="bi bi-bank2 me-1 text-primary"></i>
                        <span>Deposit Cheque in Bank Clearing</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-2 border mb-3">
                        <div class="small text-muted">Cheque Number: <strong id="depositChequeNumber" class="text-dark"></strong></div>
                        <div class="small text-muted mt-1">Amount: <strong id="depositChequeAmount" class="text-primary fs-6"></strong></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">Company Bank Account <span class="text-danger">*</span></label>
                        <select name="deposit_bank_id" class="form-select" required>
                            <option value="">-- Select Bank Account --</option>
                            @foreach($banks as $b)
                                <option value="{{ $b->id }}">{{ $b->bank_name }} - {{ $b->account_number }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">Deposit Date <span class="text-danger">*</span></label>
                        <input type="date" name="deposit_date" class="form-control" value="{{ $today }}" required>
                    </div>

                    <div class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i> Marking this cheque as Deposited places it in the bank clearing queue without altering balances.
                    </div>
                </div>
                <div class="modal-footer bg-light py-2.5">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary shadow-sm px-4">Confirm Deposit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL 4: MARK CHEQUE REALIZED / CLEARED -->
<div class="modal fade" id="modalRealizeCheque" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <form id="formRealizeCheque" method="POST">
                @csrf
                <div class="modal-header py-3" style="background: linear-gradient(135deg, #15803d 0%, #166534 100%); color: #ffffff;">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2" style="font-size: 15px;">
                        <i class="bi bi-check2-all me-1"></i>
                        <span>Mark Cheque Realized / Cleared</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 mb-3 rounded-2" style="background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; font-size: 12.5px;">
                        <i class="bi bi-lightning-charge-fill me-1"></i> <strong>Bank Balance Action:</strong> This will officially <strong id="realizeActionText">CREDIT</strong> your bank account balance and create a registered <strong>BankTransaction</strong> ledger entry.
                    </div>

                    <div class="p-3 bg-light rounded-2 border mb-3">
                        <div class="small text-muted">Cheque Number: <strong id="realizeChequeNumber" class="text-dark"></strong></div>
                        <div class="small text-muted mt-1">Amount: <strong id="realizeChequeAmount" class="text-success fs-6"></strong></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">Clearance Date <span class="text-danger">*</span></label>
                        <input type="date" name="clearance_date" class="form-control" value="{{ $today }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">Company Bank Account <span class="text-danger">*</span></label>
                        <select name="bank_id" class="form-select" id="realizeSelectBank">
                            @foreach($banks as $b)
                                <option value="{{ $b->id }}">{{ $b->bank_name }} - {{ $b->account_number }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2.5">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success shadow-sm px-4">Confirm &amp; Update Balance</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL 5: RETURN CHEQUE -->
<div class="modal fade" id="modalReturnCheque" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <form id="formReturnCheque" method="POST">
                @csrf
                <div class="modal-header py-3" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color: #ffffff;">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2" style="font-size: 15px;">
                        <i class="bi bi-arrow-return-left me-1"></i>
                        <span>Mark Cheque as Returned (Bounced)</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 mb-3 rounded-2" style="background-color: #fff1f2; border: 1px solid #fecdd3; color: #be123c; font-size: 12.5px;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <strong>Reversal Logic:</strong> If this cheque was previously Realized into the bank, its funds will be safely <strong>reversed</strong>. If linked to an order, the customer's due balance will be accurately <strong>restored</strong>.
                    </div>

                    <div class="p-3 bg-light rounded-2 border mb-3">
                        <div class="small text-muted">Cheque Number: <strong id="returnChequeNumber" class="text-dark"></strong></div>
                        <div class="small text-muted mt-1">Amount: <strong id="returnChequeAmount" class="text-danger fs-6"></strong></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">Return Date <span class="text-danger">*</span></label>
                        <input type="date" name="return_date" class="form-control" value="{{ $today }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">Return Reason <span class="text-danger">*</span></label>
                        <input type="text" name="return_reason" class="form-control" placeholder="e.g. Insufficient Funds / Signature Mismatch" required>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2.5">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger shadow-sm px-4">Confirm Return</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL 6: VIEW CHEQUE DETAILS -->
<div class="modal fade" id="modalViewCheque" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header py-3" style="background: linear-gradient(135deg, #484646 0%, #3a3838 100%); color: #ffffff;">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" style="font-size: 15px;">
                    <i class="bi bi-file-earmark-text me-1"></i>
                    <span>Cheque Audit Details</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="viewChequeBody">
                <!-- Dynamically populated via Javascript -->
            </div>
            <div class="modal-footer bg-light py-2.5">
                <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleDepositFields() {
    const status = document.getElementById('selectInitialStatus').value;
    const divBank = document.getElementById('divDepositBank');
    const divDate = document.getElementById('divDepositDate');
    const selBank = document.getElementById('selectDepositBank');

    if (status === 'Deposited') {
        divBank.classList.remove('d-none');
        divDate.classList.remove('d-none');
        selBank.setAttribute('required', 'required');
    } else {
        divBank.classList.add('d-none');
        divDate.classList.add('d-none');
        selBank.removeAttribute('required');
    }
}

function openDepositModal(id, chequeNumber, amount) {
    document.getElementById('depositChequeNumber').innerText = '#' + chequeNumber;
    document.getElementById('depositChequeAmount').innerText = amount;
    document.getElementById('formDepositCheque').action = '{{ url("cheques") }}/' + id + '/deposit';
    new bootstrap.Modal(document.getElementById('modalDepositCheque')).show();
}

function openRealizeModal(id, chequeNumber, amount, type, bankId) {
    document.getElementById('realizeChequeNumber').innerText = '#' + chequeNumber;
    document.getElementById('realizeChequeAmount').innerText = amount;
    document.getElementById('realizeActionText').innerText = (type === 'IN') ? 'CREDIT (+)' : 'DEBIT (-)';
    
    if (bankId && document.getElementById('realizeSelectBank')) {
        document.getElementById('realizeSelectBank').value = bankId;
    }

    document.getElementById('formRealizeCheque').action = '{{ url("cheques") }}/' + id + '/realize';
    new bootstrap.Modal(document.getElementById('modalRealizeCheque')).show();
}

function openReturnModal(id, chequeNumber, amount) {
    document.getElementById('returnChequeNumber').innerText = '#' + chequeNumber;
    document.getElementById('returnChequeAmount').innerText = amount;
    document.getElementById('formReturnCheque').action = '{{ url("cheques") }}/' + id + '/return';
    new bootstrap.Modal(document.getElementById('modalReturnCheque')).show();
}

function openViewModal(cheque, customerName, bankName) {
    let typeBadge = cheque.type === 'IN' 
        ? '<span class="badge" style="background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; font-size:11px;">Inward (Received)</span>'
        : '<span class="badge" style="background:#fff1f2; color:#be123c; border:1px solid #fecdd3; font-size:11px;">Outward (Issued)</span>';

    let html = `
        <div class="list-group list-group-flush" style="font-size: 13px;">
            <div class="list-group-item d-flex justify-content-between px-0 py-2">
                <span class="text-muted">Cheque Number:</span>
                <strong class="font-monospace text-dark">#${cheque.cheque_number}</strong>
            </div>
            <div class="list-group-item d-flex justify-content-between px-0 py-2">
                <span class="text-muted">Type:</span>
                ${typeBadge}
            </div>
            <div class="list-group-item d-flex justify-content-between px-0 py-2">
                <span class="text-muted">Status:</span>
                <span class="badge bg-light text-dark border">${cheque.status}</span>
            </div>
            <div class="list-group-item d-flex justify-content-between px-0 py-2">
                <span class="text-muted">Amount:</span>
                <strong class="text-dark fs-6">Rs. ${parseFloat(cheque.amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong>
            </div>
            <div class="list-group-item d-flex justify-content-between px-0 py-2">
                <span class="text-muted">${cheque.type === 'IN' ? 'Customer' : 'Payee'}:</span>
                <strong class="text-dark">${cheque.type === 'IN' ? customerName : cheque.payee_name}</strong>
            </div>
            <div class="list-group-item d-flex justify-content-between px-0 py-2">
                <span class="text-muted">Company Bank Account:</span>
                <span class="text-dark">${bankName || '-'}</span>
            </div>
            <div class="list-group-item d-flex justify-content-between px-0 py-2">
                <span class="text-muted">Cheque Date:</span>
                <span>${cheque.cheque_date ? cheque.cheque_date.substring(0, 10) : '-'}</span>
            </div>
            <div class="list-group-item d-flex justify-content-between px-0 py-2">
                <span class="text-muted">${cheque.type === 'IN' ? 'Received Date' : 'Issue Date'}:</span>
                <span>${cheque.received_or_issued_date ? cheque.received_or_issued_date.substring(0, 10) : '-'}</span>
            </div>
            ${cheque.deposit_date ? `
            <div class="list-group-item d-flex justify-content-between px-0 py-2">
                <span class="text-muted">Deposit Date:</span>
                <span class="text-primary">${cheque.deposit_date.substring(0, 10)}</span>
            </div>` : ''}
            ${cheque.clearance_date ? `
            <div class="list-group-item d-flex justify-content-between px-0 py-2">
                <span class="text-muted">Clearance Date:</span>
                <span class="text-success fw-bold">${cheque.clearance_date.substring(0, 10)}</span>
            </div>` : ''}
            ${cheque.return_date ? `
            <div class="list-group-item d-flex justify-content-between px-0 py-2">
                <span class="text-muted">Return Date:</span>
                <span class="text-danger fw-bold">${cheque.return_date.substring(0, 10)}</span>
            </div>
            <div class="list-group-item d-flex justify-content-between px-0 py-2">
                <span class="text-muted">Return Reason:</span>
                <span class="text-danger fw-semibold">${cheque.return_reason || '-'}</span>
            </div>` : ''}
            ${cheque.notes ? `
            <div class="list-group-item px-0 pt-2 pb-0">
                <div class="text-muted small mb-1">Remarks:</div>
                <div class="p-2 bg-light rounded text-dark small border">${cheque.notes}</div>
            </div>` : ''}
        </div>
    `;

    document.getElementById('viewChequeBody').innerHTML = html;
    new bootstrap.Modal(document.getElementById('modalViewCheque')).show();
}
</script>
@endsection
