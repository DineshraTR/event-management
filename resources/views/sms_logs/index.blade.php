@extends('layouts.app')
@section('title', 'SMS History & Delivery Logs • CEYLON WEDS')
@section('page-title', 'SMS Delivery Logs')

@section('content')
<style>
    .sms-stat-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 18px 20px;
        border: 1px solid #edf2f7;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .sms-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
    }
    .sms-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .sms-type-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .message-bubble {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 12.5px;
        color: #1e293b;
        max-width: 420px;
        line-height: 1.45;
        white-space: pre-line;
    }
</style>

<!-- Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0"
            style="width: 44px; height: 44px; background: linear-gradient(135deg, #059669 0%, #047857 100%); font-size: 20px;">
            <i class="bi bi-chat-dots-fill"></i>
        </div>
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.3px;">Customer SMS Notifications</h4>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5" style="font-size: 11px; font-weight: 600;">
                    <i class="bi bi-broadcast me-1"></i> Mask: {{ config('services.fitsms.sender_id', 'CEYLON WEDS') }}
                </span>
                <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 11px;">
                    Fit SMS Gateway v4
                </span>
            </div>
            <p class="text-muted small mb-0">Automated SMS messages dispatched upon quotation creation, order generation, and payment receipts.</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary shadow-sm" style="font-size: 13px;">
            <i class="bi bi-file-earmark-text me-1"></i> Quotations
        </a>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary shadow-sm" style="font-size: 13px;">
            <i class="bi bi-box-seam me-1"></i> Orders
        </a>
        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary shadow-sm" style="font-size: 13px;">
            <i class="bi bi-credit-card me-1"></i> Payments
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 shadow-sm" role="alert" style="border-radius: 10px;">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4 shadow-sm" role="alert" style="border-radius: 10px;">
        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Statistics Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="sms-stat-card d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Total Dispatched</small>
                <h4 class="mb-0 fw-bold text-dark mt-1">{{ number_format($stats['total']) }}</h4>
            </div>
            <div class="sms-stat-icon bg-primary-subtle text-primary">
                <i class="bi bi-chat-square-text"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="sms-stat-card d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Sent Successfully</small>
                <h4 class="mb-0 fw-bold text-success mt-1">{{ number_format($stats['sent']) }}</h4>
            </div>
            <div class="sms-stat-icon bg-success-subtle text-success">
                <i class="bi bi-check2-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="sms-stat-card d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Failed / Blocked</small>
                <h4 class="mb-0 fw-bold text-danger mt-1">{{ number_format($stats['failed']) }}</h4>
            </div>
            <div class="sms-stat-icon bg-danger-subtle text-danger">
                <i class="bi bi-exclamation-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="sms-stat-card d-flex align-items-center justify-content-between">
            <div>
                <small class="text-muted text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Delivery Rate</small>
                <h4 class="mb-0 fw-bold text-dark mt-1">{{ $stats['success_rate'] }}%</h4>
            </div>
            <div class="sms-stat-icon bg-info-subtle text-info">
                <i class="bi bi-reception-4"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #edf2f7 !important;">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('sms-logs.index') }}" class="row g-2 align-items-end">
            <!-- Search Keyword -->
            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Search Keyword</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Phone, customer, message text..." value="{{ request('search') }}">
                </div>
            </div>

            <!-- SMS Type -->
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Message Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="quotation_created" @selected(request('type') === 'quotation_created')>📄 Quotation Created</option>
                    <option value="order_created" @selected(request('type') === 'order_created')>📦 Order Invoice</option>
                    <option value="payment_received" @selected(request('type') === 'payment_received')>💳 Payment Receipt</option>
                    <option value="custom" @selected(request('type') === 'custom')>✉️ Custom / Other</option>
                </select>
            </div>

            <!-- Status -->
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Gateway Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="sent" @selected(request('status') === 'sent')>✅ Sent</option>
                    <option value="failed" @selected(request('status') === 'failed')>❌ Failed</option>
                    <option value="pending" @selected(request('status') === 'pending')>⏳ Pending</option>
                </select>
            </div>

            <!-- From Date -->
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">From Date</label>
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
            </div>

            <!-- To Date -->
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">To Date</label>
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
            </div>

            <!-- Action Buttons -->
            <div class="col-12 col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-dark w-100" title="Apply Filters">
                    <i class="bi bi-funnel"></i>
                </button>
                <a href="{{ route('sms-logs.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filters">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid #edf2f7 !important;">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 13.5px;">
            <i class="bi bi-list-columns-reverse text-secondary"></i> SMS Transmission Log
        </h6>
        <div class="text-muted small">
            @if($logs->total() > 0)
                Showing <strong>{{ $logs->firstItem() }}-{{ $logs->lastItem() }}</strong> of <strong>{{ $logs->total() }}</strong> dispatches
            @else
                0 records
            @endif
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted" style="border-bottom: 2px solid #edf2f7; font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.5px;">
                <tr>
                    <th class="ps-4">Recipient</th>
                    <th>Customer</th>
                    <th>Type & Reference</th>
                    <th>Message Content</th>
                    <th class="text-center">Status</th>
                    <th>Dispatched Date & Time</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <!-- Recipient Phone & Mask -->
                        <td class="ps-4">
                            <div class="fw-bold text-dark font-monospace" style="font-size: 13px;">
                                <i class="bi bi-phone text-secondary me-1"></i>{{ $log->recipient }}
                            </div>
                            <small class="text-muted" style="font-size: 11px;">
                                Sender ID: <span class="fw-semibold text-secondary">{{ $log->sender_id }}</span>
                            </small>
                        </td>

                        <!-- Customer Details -->
                        <td>
                            @if($log->customer)
                                <a href="{{ route('customers.show', $log->customer) }}" class="text-dark fw-semibold text-decoration-none hover-primary" style="font-size: 13px;">
                                    {{ $log->customer->name }}
                                </a>
                                @if($log->customer->company_name)
                                    <div class="text-muted small" style="font-size: 11px;">{{ $log->customer->company_name }}</div>
                                @endif
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>

                        <!-- Type & Reference -->
                        <td>
                            @if($log->type === 'quotation_created')
                                <span class="sms-type-badge bg-info-subtle text-info border border-info-subtle">
                                    <i class="bi bi-file-earmark-text"></i> Quotation
                                </span>
                            @elseif($log->type === 'order_created')
                                <span class="sms-type-badge bg-primary-subtle text-primary border border-primary-subtle">
                                    <i class="bi bi-box-seam"></i> Order Invoice
                                </span>
                            @elseif($log->type === 'payment_received')
                                <span class="sms-type-badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="bi bi-cash-stack"></i> Payment Receipt
                                </span>
                            @else
                                <span class="sms-type-badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                    <i class="bi bi-envelope"></i> Custom
                                </span>
                            @endif

                            @if($log->reference_type && $log->reference_id)
                                <div class="mt-1">
                                    @if($log->reference_type === 'Quotation')
                                        <a href="{{ route('quotations.show', $log->reference_id) }}" class="small text-decoration-none fw-semibold font-monospace" style="font-size: 11px;" target="_blank">
                                            #{{ $log->reference_type }} #{{ $log->reference_id }} <i class="bi bi-box-arrow-up-right ms-0.5"></i>
                                        </a>
                                    @elseif($log->reference_type === 'Order')
                                        <a href="{{ route('orders.show', $log->reference_id) }}" class="small text-decoration-none fw-semibold font-monospace" style="font-size: 11px;" target="_blank">
                                            #{{ $log->reference_type }} #{{ $log->reference_id }} <i class="bi bi-box-arrow-up-right ms-0.5"></i>
                                        </a>
                                    @elseif($log->reference_type === 'Payment')
                                        <a href="{{ route('payments.index') }}" class="small text-decoration-none fw-semibold font-monospace" style="font-size: 11px;" target="_blank">
                                            #{{ $log->reference_type }} #{{ $log->reference_id }} <i class="bi bi-box-arrow-up-right ms-0.5"></i>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </td>

                        <!-- Message Content -->
                        <td>
                            <div class="message-bubble text-truncate-2" title="{{ $log->message }}">
                                {{ $log->message }}
                            </div>
                            @if($log->error_message)
                                <div class="text-danger small mt-1" style="font-size: 11px;">
                                    <i class="bi bi-exclamation-triangle me-1"></i>{{ $log->error_message }}
                                </div>
                            @endif
                        </td>

                        <!-- Status Badge -->
                        <td class="text-center">
                            @if($log->status === 'sent')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1" style="font-size: 11px; font-weight: 600;">
                                    <i class="bi bi-check-circle-fill me-1"></i> Sent
                                </span>
                            @elseif($log->status === 'failed')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1" style="font-size: 11px; font-weight: 600;">
                                    <i class="bi bi-x-circle-fill me-1"></i> Failed
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1" style="font-size: 11px; font-weight: 600;">
                                    <i class="bi bi-hourglass-split me-1"></i> Pending
                                </span>
                            @endif
                        </td>

                        <!-- Sent Timestamp -->
                        <td style="font-size: 12px;">
                            <div class="fw-semibold text-dark">
                                {{ $log->created_at->format('M d, Y') }}
                            </div>
                            <small class="text-muted">
                                {{ $log->created_at->format('h:i A') }} ({{ $log->created_at->diffForHumans() }})
                            </small>
                        </td>

                        <!-- Actions -->
                        <td class="text-end pe-4 text-nowrap">
                            <div class="d-inline-flex align-items-center gap-1">
                                <!-- View Payload Button -->
                                <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-1" title="View Message & Gateway Response"
                                    onclick="showSmsDetails({{ json_encode([
                                        'recipient' => $log->recipient,
                                        'sender_id' => $log->sender_id,
                                        'type' => $log->type,
                                        'status' => $log->status,
                                        'message' => $log->message,
                                        'response_code' => $log->response_code,
                                        'response_body' => $log->response_body,
                                        'error_message' => $log->error_message,
                                        'sent_at' => $log->sent_at ? $log->sent_at->format('Y-m-d H:i:s') : 'N/A',
                                        'created_at' => $log->created_at->format('Y-m-d H:i:s')
                                    ]) }})">
                                    <i class="bi bi-eye"></i>
                                </button>

                                <!-- Resend Button -->
                                <form method="POST" action="{{ route('sms-logs.resend', $log) }}" class="d-inline m-0 p-0" onsubmit="return confirm('Do you want to re-dispatch this SMS to {{ $log->recipient }}?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary px-2 py-1" title="Resend SMS">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-chat-square-dots fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <div class="fw-semibold" style="font-size: 14px;">No SMS logs found</div>
                            <small class="text-muted">Customer notification messages sent via Fit SMS will appear here automatically.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} dispatches
            </span>
            <div>
                {{ $logs->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Modal: SMS Details & Gateway Payload Inspector -->
<div class="modal fade" id="smsDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-dark text-white p-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-chat-text-fill text-warning"></i>
                    <h6 class="modal-title fw-bold text-white mb-0" id="smsModalTitle">SMS Dispatch Details</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Recipient Phone</small>
                        <div class="fw-bold text-dark font-monospace mt-1" id="modalRecipient"></div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Sender ID / Mask</small>
                        <div class="fw-bold text-primary mt-1" id="modalSenderId"></div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Gateway Status</small>
                        <div class="mt-1" id="modalStatusBadge"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Message Delivered</label>
                    <div class="p-3 bg-light rounded-3 border" style="font-size: 13px; line-height: 1.6; white-space: pre-line;" id="modalMessage"></div>
                </div>

                <div id="modalErrorSection" class="mb-3 d-none">
                    <label class="form-label small fw-bold text-danger mb-1">Error Diagnostics</label>
                    <div class="p-2.5 bg-danger-subtle text-danger rounded-3 border border-danger-subtle small font-monospace" id="modalErrorMessage"></div>
                </div>

                <div>
                    <label class="form-label small fw-bold text-dark mb-1">Fit SMS Gateway Raw Response Payload</label>
                    <pre class="bg-dark text-light p-3 rounded-3 small font-monospace mb-0" style="max-height: 180px; overflow-y: auto;" id="modalResponseBody"></pre>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 px-4 border-top">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showSmsDetails(data) {
        document.getElementById('modalRecipient').innerText = data.recipient;
        document.getElementById('modalSenderId').innerText = data.sender_id || 'CEYLON WEDS';
        document.getElementById('modalMessage').innerText = data.message;
        
        const statusEl = document.getElementById('modalStatusBadge');
        if (data.status === 'sent') {
            statusEl.innerHTML = '<span class="badge bg-success-subtle text-success border border-success-subtle">Sent</span>';
        } else if (data.status === 'failed') {
            statusEl.innerHTML = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Failed</span>';
        } else {
            statusEl.innerHTML = '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Pending</span>';
        }

        const errSection = document.getElementById('modalErrorSection');
        if (data.error_message) {
            errSection.classList.remove('d-none');
            document.getElementById('modalErrorMessage').innerText = data.error_message;
        } else {
            errSection.classList.add('d-none');
        }

        let respText = data.response_body;
        try {
            if (respText) {
                const parsed = JSON.parse(respText);
                respText = JSON.stringify(parsed, null, 2);
            } else {
                respText = '// No response payload recorded (code: ' + (data.response_code || 'N/A') + ')';
            }
        } catch (e) {
            // Keep bare text
        }
        document.getElementById('modalResponseBody').innerText = respText;

        const modal = new bootstrap.Modal(document.getElementById('smsDetailsModal'));
        modal.show();
    }
</script>
@endpush
@endsection
