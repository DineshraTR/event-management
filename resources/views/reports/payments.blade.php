@extends('layouts.app')
@section('title', 'Payments Report')
@section('page-title', 'Payments Report')

@section('content')
<!-- Page Header (Tritcal Style) -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0"
            style="width: 44px; height: 44px; background: linear-gradient(135deg, #484646 0%, #3a3838 100%); font-size: 20px;">
            <i class="bi bi-receipt-cutoff"></i>
        </div>
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.3px;">Payments Report</h4>
                <span class="badge bg-light text-dark border px-2 py-0.5" style="font-size: 11px; font-weight: 700;">
                    {{ $totalReceipts }} Total Receipts
                </span>
            </div>
            <p class="text-muted small mb-0">Comprehensive financial log of all customer payments, transaction methods, and settled amounts.</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <button type="button" class="btn btn-outline-secondary shadow-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print Report
        </button>
        <a href="{{ route('payments.index') }}" class="btn btn-primary shadow-sm" target="_blank">
            <i class="bi bi-plus-circle me-1"></i> Record New Payment
        </a>
    </div>
</div>

<!-- KPI Metrics Overview Cards -->
<div class="row g-3 mb-4">
    <!-- Total Collected -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; border: 1px solid #edf2f7 !important;">
            <div class="card-body p-3.5 d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width: 48px; height: 48px; background-color: #ecfdf5; color: #059669; font-size: 22px;">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Total Collections</span>
                    <h5 class="fw-bold text-dark mb-0 mt-0.5" style="font-size: 18px;">
                        Rs. {{ number_format((float)$totalCollected, 2) }}
                    </h5>
                    <small class="text-success fw-medium" style="font-size: 11px;">
                        <i class="bi bi-check-circle me-0.5"></i> {{ $totalReceipts }} transaction records
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Collections -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; border: 1px solid #edf2f7 !important;">
            <div class="card-body p-3.5 d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width: 48px; height: 48px; background-color: #eff6ff; color: #2563eb; font-size: 22px;">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Cash Payments</span>
                    <h5 class="fw-bold text-dark mb-0 mt-0.5" style="font-size: 18px;">
                        Rs. {{ number_format((float)$cashTotal, 2) }}
                    </h5>
                    <small class="text-muted" style="font-size: 11px;">Direct cash in hand</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank Transfer Collections -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; border: 1px solid #edf2f7 !important;">
            <div class="card-body p-3.5 d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width: 48px; height: 48px; background-color: #f0fdfa; color: #0d9488; font-size: 22px;">
                    <i class="bi bi-bank2"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Bank Transfers</span>
                    <h5 class="fw-bold text-dark mb-0 mt-0.5" style="font-size: 18px;">
                        Rs. {{ number_format((float)$bankTotal, 2) }}
                    </h5>
                    <small class="text-muted" style="font-size: 11px;">Online deposits & transfers</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Cheque Collections -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; border: 1px solid #edf2f7 !important;">
            <div class="card-body p-3.5 d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width: 48px; height: 48px; background-color: #fffbeb; color: #d97706; font-size: 22px;">
                    <i class="bi bi-journal-check"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Cheque Cleared</span>
                    <h5 class="fw-bold text-dark mb-0 mt-0.5" style="font-size: 18px;">
                        Rs. {{ number_format((float)$chequeTotal, 2) }}
                    </h5>
                    <small class="text-muted" style="font-size: 11px;">Bank cheques collected</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Advanced Search & Filter Card -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #edf2f7 !important;">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 13.5px;">
            <i class="bi bi-funnel text-secondary"></i> Search & Report Filters
        </h6>
        @if(request()->anyFilled(['search', 'customer_id', 'payment_method', 'payment_type', 'date_from', 'date_to']))
            <a href="{{ route('reports.payments') }}" class="btn btn-sm btn-link text-decoration-none text-danger p-0" style="font-size: 12px;">
                <i class="bi bi-x-circle me-1"></i> Clear Filters
            </a>
        @endif
    </div>

    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('reports.payments') }}" id="paymentsReportFilterForm">
            <div class="row g-3">
                <!-- 1. Search Keyword -->
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-search me-1"></i> Quick Search
                    </label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Receipt #, Order #, Cheque #, Notes...">
                </div>

                <!-- 2. Customer Select -->
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-person me-1"></i> Customer
                    </label>
                    <select name="customer_id" class="form-select searchable-select" data-placeholder="All Customers">
                        <option value="">All Customers</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" @selected(request('customer_id') == $c->id)>
                                {{ $c->name }} ({{ $c->phone ?: 'No phone' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Payment Method -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-wallet2 me-1"></i> Payment Method
                    </label>
                    <select name="payment_method" class="form-select">
                        <option value="">All Methods</option>
                        <option value="cash" @selected(request('payment_method') === 'cash')>Cash</option>
                        <option value="bank_transfer" @selected(request('payment_method') === 'bank_transfer')>Bank Transfer</option>
                        <option value="cheque" @selected(request('payment_method') === 'cheque')>Cheque</option>
                    </select>
                </div>

                <!-- 4. Payment Type -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-tag me-1"></i> Payment Type
                    </label>
                    <select name="payment_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="advance" @selected(request('payment_type') === 'advance')>Advance</option>
                        <option value="partial" @selected(request('payment_type') === 'partial')>Partial</option>
                        <option value="full" @selected(request('payment_type') === 'full')>Full Payment</option>
                    </select>
                </div>

                <!-- 5. From Date -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-calendar-event me-1"></i> From Date
                    </label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>

                <!-- 6. To Date -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-calendar-check me-1"></i> To Date
                    </label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>

                <!-- 7. Buttons -->
                <div class="col-12 col-lg-4 d-flex align-items-end gap-2 ms-auto">
                    <button type="submit" class="btn btn-primary shadow-sm flex-grow-1">
                        <i class="bi bi-funnel-fill me-1"></i> Filter Report
                    </button>
                    <a href="{{ route('reports.payments') }}" class="btn btn-secondary shadow-sm">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Payments Report Table Card -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid #edf2f7 !important;">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 13.5px;">
                <i class="bi bi-table text-secondary"></i> Payments Ledger & Receipts
            </h6>
        </div>
        <div class="text-muted small">
            @if($payments->total() > 0)
                Showing <strong>{{ $payments->firstItem() }}-{{ $payments->lastItem() }}</strong> of <strong>{{ $payments->total() }}</strong> records
            @else
                0 records found
            @endif
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted" style="border-bottom: 2px solid #edf2f7;">
                <tr>
                    <th class="ps-4">Receipt Details</th>
                    <th>Customer</th>
                    <th>Linked Order & Event</th>
                    <th>Payment Type</th>
                    <th>Method & Channel</th>
                    <th>Remarks</th>
                    <th class="text-end">Amount</th>
                    <th class="text-end pe-4 text-nowrap" style="width: 1%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <!-- Receipt Details -->
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('payments.print', $payment) }}" target="_blank" class="badge bg-light text-dark border font-monospace px-2 py-0.5 text-decoration-none hover-primary shadow-xs" style="font-size: 11px; font-weight: 700;" title="View / Print Receipt in new tab">
                                    <i class="bi bi-receipt me-1 text-primary"></i>{{ $payment->payment_no }}
                                </a>
                            </div>
                            <div class="fw-semibold text-dark mt-1" style="font-size: 12.5px;">
                                <i class="bi bi-calendar-event me-1 text-secondary"></i>
                                {{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : '—' }}
                            </div>
                            @if($payment->payment_date)
                                <small class="text-muted" style="font-size: 11px;">
                                    {{ $payment->payment_date->diffForHumans() }}
                                </small>
                            @endif
                        </td>

                        <!-- Customer -->
                        <td>
                            @if($payment->customer)
                                <div class="fw-semibold text-dark" style="font-size: 13px;">
                                    <a href="{{ route('customers.show', $payment->customer) }}" class="text-dark text-decoration-none hover-primary">
                                        {{ $payment->customer->name }}
                                    </a>
                                </div>
                                <small class="text-muted" style="font-size: 11px;">
                                    <i class="bi bi-telephone text-secondary me-1"></i>{{ $payment->customer->phone ?: 'No phone' }}
                                </small>
                            @else
                                <span class="text-muted fst-italic">No Customer</span>
                            @endif
                        </td>

                        <!-- Linked Order & Event -->
                        <td>
                            @if($payment->order)
                                <div class="fw-semibold" style="font-size: 12.5px;">
                                    <a href="{{ route('orders.show', $payment->order) }}" class="text-primary text-decoration-none" target="_blank">
                                        <i class="bi bi-receipt me-1"></i>{{ $payment->order->order_no }}
                                    </a>
                                </div>
                                @if($payment->order->event)
                                    <small class="text-muted d-block text-truncate" style="max-width: 200px; font-size: 11.5px;" title="{{ $payment->order->event->event_name }}">
                                        {{ $payment->order->event->event_name }}
                                    </small>
                                @endif
                            @else
                                <span class="text-muted fst-italic">Direct Payment</span>
                            @endif
                        </td>

                        <!-- Payment Type -->
                        <td>
                            @if($payment->payment_type === 'advance')
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2.5 py-1" style="font-size: 11px; font-weight: 600;">
                                    Advance
                                </span>
                            @elseif($payment->payment_type === 'full')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1" style="font-size: 11px; font-weight: 600;">
                                    Full Payment
                                </span>
                            @else
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1" style="font-size: 11px; font-weight: 600;">
                                    Partial
                                </span>
                            @endif
                        </td>

                        <!-- Method & Channel -->
                        <td>
                            <div class="d-flex align-items-center gap-1.5 fw-semibold text-dark" style="font-size: 12.5px;">
                                @if($payment->payment_method === 'cash')
                                    <i class="bi bi-wallet2 text-success"></i> Cash
                                @elseif($payment->payment_method === 'bank_transfer')
                                    <i class="bi bi-bank2 text-primary"></i> Bank Transfer
                                @elseif($payment->payment_method === 'cheque')
                                    <i class="bi bi-journal-check text-warning"></i> Cheque
                                @else
                                    {{ ucfirst($payment->payment_method) }}
                                @endif
                            </div>
                            @if($payment->bank_name)
                                <small class="text-muted d-block" style="font-size: 11px;">
                                    <i class="bi bi-building me-1"></i>{{ $payment->bank_name }}
                                </small>
                            @endif
                            @if($payment->cheque_no)
                                <small class="text-muted d-block" style="font-size: 11px;">
                                    Chq #: <code>{{ $payment->cheque_no }}</code>
                                </small>
                            @endif
                        </td>

                        <!-- Remarks -->
                        <td>
                            @if($payment->notes)
                                <span class="text-muted small d-inline-block text-truncate" style="max-width: 180px;" title="{{ $payment->notes }}">
                                    {{ $payment->notes }}
                                </span>
                            @else
                                <span class="text-muted fst-italic small">—</span>
                            @endif
                        </td>

                        <!-- Amount -->
                        <td class="text-end">
                            <div class="fw-bold text-success" style="font-size: 14px;">
                                Rs. {{ number_format((float)$payment->amount, 2) }}
                            </div>
                        </td>

                        <!-- Actions -->
                        <td class="text-end pe-4 text-nowrap">
                            <div class="d-inline-grid justify-content-end" style="display: inline-grid; grid-template-columns: repeat(2, 70px); gap: 4px;">
                                <a href="{{ route('payments.print', $payment) }}" target="_blank" class="btn-action btn-action-view" style="width: 70px; height: 28px;" title="View & Print Payment Receipt in New Tab">
                                    <i class="bi bi-eye"></i> <span>View</span>
                                </a>
                                <form method="POST" action="{{ route('payments.destroy', $payment) }}" class="m-0 p-0" style="width: 70px;" onsubmit="return confirm('Are you sure you want to delete this payment record? This will recalculate the order balance.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-action-delete" style="width: 70px; height: 28px;" title="Delete Payment">
                                        <i class="bi bi-trash3"></i> <span>Delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-receipt fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <div class="fw-semibold" style="font-size: 14px;">No payment records match your criteria</div>
                            <small class="text-muted">Try adjusting your date range, customer, or payment method filters.</small>
                            <div class="mt-3">
                                <a href="{{ route('reports.payments') }}" class="btn btn-sm btn-secondary shadow-sm">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($payments->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} payments
            </span>
            <div>
                {{ $payments->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
