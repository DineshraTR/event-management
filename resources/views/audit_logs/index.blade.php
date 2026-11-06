@extends('layouts.app')

@section('title', 'System Audit Logs')
@section('page-title', 'System Audit Logs & Activity Trail')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-shield-check text-primary me-2"></i> System Audit Logs &amp; Activity Trail
            </h4>
            <p class="text-muted small mb-0">Professional real-time tracking of all logins, creations, edits, deletions, and payments.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">
                <i class="bi bi-database me-1 text-primary"></i> Total Recorded Events: {{ number_format($totalLogs) }}
            </span>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Activities</div>
                        <h3 class="fw-bold text-dark mt-1 mb-0">{{ number_format($totalLogs) }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #e0f2fe; color: #0284c7; font-size: 22px;">
                        <i class="bi bi-journal-text"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Actions Today</div>
                        <h3 class="fw-bold text-primary mt-1 mb-0">{{ number_format($actionsToday) }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #f0fdf4; color: #16a34a; font-size: 22px;">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Logins Today</div>
                        <h3 class="fw-bold text-info mt-1 mb-0">{{ number_format($loginsToday) }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #f5f3ff; color: #7c3aed; font-size: 22px;">
                        <i class="bi bi-box-arrow-in-right"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Deletions</div>
                        <h3 class="fw-bold text-danger mt-1 mb-0">{{ number_format($deletionsCount) }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #fee2e2; color: #dc2626; font-size: 22px;">
                        <i class="bi bi-trash3-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('audit-logs.index') }}">
                <div class="row g-3">
                    <div class="col-12 col-md-4 col-lg-3">
                        <label class="form-label small fw-bold text-muted mb-1">
                            <i class="bi bi-search me-1"></i> Search Query
                        </label>
                        <input type="text" name="search" class="form-control" placeholder="User, description, IP, record..." value="{{ request('search') }}">
                    </div>

                    <div class="col-6 col-md-4 col-lg-2">
                        <label class="form-label small fw-bold text-muted mb-1">
                            <i class="bi bi-layers me-1"></i> Module
                        </label>
                        <select name="module" class="form-select">
                            <option value="">All Modules</option>
                            @foreach($modules as $m)
                                <option value="{{ $m }}" @selected(request('module') === $m)>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-md-4 col-lg-2">
                        <label class="form-label small fw-bold text-muted mb-1">
                            <i class="bi bi-lightning-charge me-1"></i> Action
                        </label>
                        <select name="action" class="form-select">
                            <option value="">All Actions</option>
                            @foreach($actions as $act)
                                <option value="{{ $act }}" @selected(strtolower(request('action')) === strtolower($act))>{{ ucfirst($act) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-md-4 col-lg-2">
                        <label class="form-label small fw-bold text-muted mb-1">
                            <i class="bi bi-person me-1"></i> User
                        </label>
                        <select name="user_id" class="form-select">
                            <option value="">All Users</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-md-4 col-lg-3">
                        <label class="form-label small fw-bold text-muted mb-1">
                            <i class="bi bi-calendar3 me-1"></i> Date Range
                        </label>
                        <div class="input-group">
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" title="From Date">
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" title="To Date">
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary px-3">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="bi bi-funnel me-1"></i> Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table modern-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 170px;">Date &amp; Time</th>
                        <th style="width: 200px;">User &amp; IP</th>
                        <th style="width: 120px;">Action</th>
                        <th style="width: 120px;">Module</th>
                        <th>Activity Description / Scope</th>
                        <th style="width: 100px;" class="text-center">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark small">
                                    {{ $log->created_at?->format('M d, Y') }}
                                </div>
                                <small class="text-muted font-monospace" style="font-size: 11px;">
                                    {{ $log->created_at?->format('h:i:s A') }}
                                </small>
                            </td>

                            <td>
                                <div class="fw-bold text-dark" style="font-size: 13px;">
                                    {{ $log->user_name ?: 'System' }}
                                </div>
                                <div class="small text-muted font-monospace" style="font-size: 11px;">
                                    <i class="bi bi-globe me-1"></i>{{ $log->ip_address ?: '127.0.0.1' }}
                                </div>
                            </td>

                            <td>
                                @php
                                    $actionStyles = match(strtolower($log->action)) {
                                        'login' => 'background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;',
                                        'logout' => 'background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;',
                                        'failed_login' => 'background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca;',
                                        'created' => 'background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;',
                                        'updated' => 'background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd;',
                                        'deleted', 'deactivated' => 'background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;',
                                        'payment_received' => 'background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;',
                                        'converted' => 'background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff;',
                                        default => 'background: #f4f4f5; color: #3f3f46; border: 1px solid #e4e4e7;'
                                    };
                                @endphp
                                <span class="badge rounded-pill fw-semibold px-2.5 py-1 text-uppercase" style="{{ $actionStyles }}; font-size: 10px;">
                                    {{ $log->action }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-light text-dark border px-2.5 py-1 fw-semibold" style="font-size: 11px;">
                                    <i class="bi bi-folder2 me-1 text-secondary"></i>{{ $log->module }}
                                </span>
                            </td>

                            <td>
                                <div class="text-dark small" style="line-height: 1.45;">
                                    {{ $log->description }}
                                </div>
                                @if($log->record_title && $log->record_title !== $log->description)
                                    <small class="text-muted d-block mt-0.5" style="font-size: 11px;">
                                        Target: <strong class="text-dark">{{ $log->record_title }}</strong>
                                    </small>
                                @endif
                            </td>

                            <td class="text-center">
                                @if($log->old_values || $log->new_values || $log->user_agent)
                                    <button type="button" class="btn btn-sm btn-outline-secondary px-2.5 py-1" onclick="viewAuditLogDetails({{ $log->id }})" title="View complete details & JSON diff">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-shield-x fs-1 d-block mb-2 text-muted"></i>
                                <div class="fw-semibold">No audit log records found</div>
                                <small>System activities, logins, and changes will be recorded here automatically.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-end">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal to View Audit Log Deep Details / Diffs -->
<div class="modal fade" id="auditLogDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom p-3 px-4">
                <h5 class="modal-title fw-bold text-dark fs-6">
                    <i class="bi bi-shield-check text-primary me-1.5"></i> Audit Activity Inspector
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="auditLogModalContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="small text-muted mt-2">Loading activity details...</div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top p-3 px-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function viewAuditLogDetails(id) {
        const modalEl = document.getElementById('auditLogDetailsModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        const contentEl = document.getElementById('auditLogModalContent');

        contentEl.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="small text-muted mt-2">Loading activity details...</div>
            </div>
        `;
        modal.show();

        fetch(`/audit-logs/${id}`)
            .then(res => res.json())
            .then(data => {
                let oldValHtml = data.old_values ? `<pre class="bg-light p-3 rounded-3 border text-danger small mb-0" style="max-height: 250px; overflow-y: auto;">${JSON.stringify(data.old_values, null, 2)}</pre>` : '<span class="text-muted fst-italic small">No previous values (New record)</span>';
                let newValHtml = data.new_values ? `<pre class="bg-light p-3 rounded-3 border text-success small mb-0" style="max-height: 250px; overflow-y: auto;">${JSON.stringify(data.new_values, null, 2)}</pre>` : '<span class="text-muted fst-italic small">No new values (Deleted record)</span>';

                contentEl.innerHTML = `
                    <div class="row g-3 mb-3 pb-3 border-bottom">
                        <div class="col-sm-6">
                            <small class="text-muted d-block" style="font-size: 11px;">PERFORMED BY</small>
                            <div class="fw-bold text-dark mt-0.5">${data.user_name || 'System'} (${data.user_email || 'No email'})</div>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block" style="font-size: 11px;">TIMESTAMP</small>
                            <div class="fw-semibold text-dark mt-0.5">${data.created_at || 'N/A'}</div>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block" style="font-size: 11px;">MODULE & ACTION</small>
                            <div class="fw-semibold text-dark mt-0.5">
                                <span class="badge bg-light text-dark border me-1">${data.module}</span>
                                <span class="badge bg-primary-subtle text-primary border">${data.action.toUpperCase()}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block" style="font-size: 11px;">IP ADDRESS & CLIENT</small>
                            <div class="small text-dark font-monospace mt-0.5">${data.ip_address || '127.0.0.1'}</div>
                            <small class="text-muted text-truncate d-block" style="font-size: 10px;" title="${data.user_agent || ''}">${data.user_agent || ''}</small>
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block" style="font-size: 11px;">ACTIVITY SUMMARY</small>
                            <div class="p-2.5 bg-light rounded-3 text-dark small mt-1 fw-medium">${data.description}</div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-danger small mb-2"><i class="bi bi-dash-circle me-1"></i> Previous State (Old Values)</h6>
                            ${oldValHtml}
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-success small mb-2"><i class="bi bi-plus-circle me-1"></i> New State (New Values)</h6>
                            ${newValHtml}
                        </div>
                    </div>
                `;
            })
            .catch(() => {
                contentEl.innerHTML = '<div class="alert alert-danger py-2 small mb-0">Failed to load audit log details.</div>';
            });
    }
</script>
@endsection
