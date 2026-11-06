@extends('layouts.app')
@section('title', 'Quotations Management')
@section('page-title', 'Quotations')

@section('content')
<!-- Page Header (Tritcal Style) -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0"
            style="width: 44px; height: 44px; background: linear-gradient(135deg, #484646 0%, #3a3838 100%); font-size: 20px;">
            <i class="bi bi-file-earmark-text"></i>
        </div>
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.3px;">Quotations Management</h4>
                <span class="badge bg-light text-dark border px-2 py-0.5" style="font-size: 11px; font-weight: 700;">
                    {{ $quotations->total() }} Total Quotations
                </span>
            </div>
            <p class="text-muted small mb-0">Create, track, and manage event estimates, package pricing proposals, and client approvals.</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('quotations.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg"></i> Create New Quotation
        </a>
    </div>
</div>

<!-- Search & Filter Card (Tritcal Style) -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #edf2f7 !important;">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 13.5px;">
            <i class="bi bi-funnel text-secondary"></i> Search & Filters
        </h6>
        @if(request()->anyFilled(['search', 'customer_id', 'sub_category_id', 'status', 'from_date', 'to_date']))
            <a href="{{ route('quotations.index') }}" class="btn btn-sm btn-link text-decoration-none text-danger p-0" style="font-size: 12px;">
                <i class="bi bi-x-circle me-1"></i> Clear Filters
            </a>
        @endif
    </div>

    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('quotations.index') }}" id="quotationsFilterForm">
            <div class="row g-3">
                <!-- 1. Search Keyword -->
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-search me-1"></i> Quick Search
                    </label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Quotation #, Event, Venue, Customer...">
                </div>

                <!-- 2. Customer Select -->
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-person me-1"></i> Customer
                    </label>
                    <select name="customer_id" class="form-select">
                        <option value="">All Customers</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" @selected(request('customer_id') == $c->id)>
                                {{ $c->name }} ({{ $c->phone ?: 'No phone' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Package Select -->
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-box-seam me-1"></i> Package / Category
                    </label>
                    <select name="sub_category_id" class="form-select">
                        <option value="">All Packages</option>
                        @foreach($subCategories as $pkg)
                            <option value="{{ $pkg->id }}" @selected(request('sub_category_id') == $pkg->id)>
                                {{ $pkg->category?->name ?? 'General' }} &bull; {{ $pkg->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Quotation Status -->
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-check2-circle me-1"></i> Status
                    </label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                        <option value="sent" @selected(request('status') === 'sent')>Sent</option>
                        <option value="accepted" @selected(request('status') === 'accepted')>Accepted</option>
                        <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                    </select>
                </div>

                <!-- 5. From Date -->
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-calendar-date me-1"></i> From Date
                    </label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>

                <!-- 6. To Date -->
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-calendar-date-fill me-1"></i> To Date
                    </label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>

                <!-- 7. Filter & Reset Buttons -->
                <div class="col-12 col-md-6 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary shadow-sm flex-grow-1">
                        <i class="bi bi-funnel-fill me-1"></i> Filter Quotations
                    </button>
                    <a href="{{ route('quotations.index') }}" class="btn btn-secondary shadow-sm">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Quotations Table Card -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid #edf2f7 !important;">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 13.5px;">
            <i class="bi bi-list-check text-secondary"></i> Quotations List
        </h6>
        <div class="text-muted small">
            @if($quotations->total() > 0)
                Showing <strong>{{ $quotations->firstItem() }}-{{ $quotations->lastItem() }}</strong> of <strong>{{ $quotations->total() }}</strong> records
            @else
                0 records found
            @endif
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0;">
            <thead class="table-light text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                <tr>
                    <th class="ps-4 py-3">Quotation # & Event</th>
                    <th class="py-3">Customer</th>
                    <th class="py-3">Package / Scope</th>
                    <th class="py-3">Quotation Date</th>
                    <th class="py-3">Valid Until</th>
                    <th class="py-3 text-end">Total Amount</th>
                    <th class="py-3 text-center">Status</th>
                    <th class="py-3 text-end pe-4" style="min-width: 155px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quotations as $quotation)
                    <tr>
                        <!-- Quotation # & Event -->
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark border font-monospace px-2 py-1" style="font-size: 12px; font-weight: 700;">
                                    <a href="{{ route('quotations.show', $quotation) }}" class="text-decoration-none text-dark" target="_blank">
                                        {{ $quotation->quotation_no }}
                                    </a>
                                </span>
                                <a href="{{ route('quotations.print', $quotation) }}" target="_blank" class="text-secondary small" title="Quick Print">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                            <div class="fw-semibold text-dark mt-1" style="font-size: 13px;">
                                {{ $quotation->event_name ?: 'General Event Proposal' }}
                            </div>
                            @if($quotation->venue)
                                <small class="text-muted d-block" style="font-size: 11px;">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $quotation->venue }}
                                </small>
                            @endif
                        </td>

                        <!-- Customer -->
                        <td>
                            @if($quotation->customer)
                                <div class="fw-semibold text-dark" style="font-size: 13px;">
                                    {{ $quotation->customer->name }}
                                </div>
                                <small class="text-muted d-block" style="font-size: 11px;">
                                    <i class="bi bi-telephone me-1"></i>{{ $quotation->customer->phone ?: 'No phone' }}
                                </small>
                            @else
                                <span class="text-muted small">Walk-in Customer</span>
                            @endif
                        </td>

                        <!-- Package / Scope -->
                        <td>
                            @if($quotation->packages && $quotation->packages->isNotEmpty())
                                <div class="fw-semibold text-dark" style="font-size: 12.5px;">
                                    {{ $quotation->packages->pluck('package_name')->join(', ') }}
                                </div>
                                <small class="text-muted" style="font-size: 11px;">
                                    {{ $quotation->packages->first()?->category_name ?? 'Package' }}
                                </small>
                            @elseif($quotation->subCategory || $quotation->package_name)
                                <div class="fw-semibold text-dark" style="font-size: 12.5px;">
                                    {{ $quotation->package_name ?? $quotation->subCategory?->name ?? 'Custom Package' }}
                                </div>
                                <small class="text-muted" style="font-size: 11px;">
                                    {{ $quotation->subCategory?->category?->name ?? 'Package' }}
                                </small>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-0.5" style="font-size: 11px;">
                                    Custom Services
                                </span>
                            @endif
                        </td>

                        <!-- Quotation Date -->
                        <td>
                            <div class="text-dark small fw-semibold">
                                {{ $quotation->quotation_date?->format('M d, Y') ?? 'N/A' }}
                            </div>
                            <small class="text-muted" style="font-size: 11px;">
                                {{ $quotation->quotation_date?->diffForHumans() }}
                            </small>
                        </td>

                        <!-- Valid Until -->
                        <td>
                            @if($quotation->valid_until)
                                <div class="text-dark small fw-semibold">
                                    {{ $quotation->valid_until->format('M d, Y') }}
                                </div>
                                @if($quotation->valid_until->isPast())
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-1.5 py-0.5" style="font-size: 10px;">
                                        Expired
                                    </span>
                                @else
                                    <small class="text-muted" style="font-size: 11px;">
                                        {{ $quotation->valid_until->diffForHumans() }}
                                    </small>
                                @endif
                            @else
                                <span class="text-muted small">No expiry</span>
                            @endif
                        </td>

                        <!-- Total Amount -->
                        <td class="text-end">
                            <span class="fw-bold text-dark" style="font-size: 13.5px;">
                                Rs. {{ number_format($quotation->total_amount, 2) }}
                            </span>
                            @if($quotation->discount > 0)
                                <div class="text-muted" style="font-size: 11px;">
                                    Disc: Rs. {{ number_format($quotation->discount, 2) }}
                                </div>
                            @endif
                        </td>

                        <!-- Status Badge -->
                        <td class="text-center">
                            @if($quotation->status === 'accepted')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1" style="font-size: 11px; font-weight: 600;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981; display: inline-block; margin-right: 4px;"></span> Accepted
                                </span>
                            @elseif($quotation->status === 'sent')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1" style="font-size: 11px; font-weight: 600;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #3b82f6; display: inline-block; margin-right: 4px;"></span> Sent
                                </span>
                            @elseif($quotation->status === 'rejected')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1" style="font-size: 11px; font-weight: 600;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #ef4444; display: inline-block; margin-right: 4px;"></span> Rejected
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1" style="font-size: 11px; font-weight: 600;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #64748b; display: inline-block; margin-right: 4px;"></span> Draft
                                </span>
                            @endif
                        </td>

                        <!-- Actions (2x2 Grid, 2 buttons per row, 70px x 28px) -->
                        <td class="text-end pe-4 text-nowrap">
                            <div class="d-inline-grid justify-content-end" style="display: inline-grid; grid-template-columns: repeat(2, 70px); gap: 4px;">
                                <!-- Row 1: View & Edit -->
                                <a href="{{ route('quotations.show', $quotation) }}" class="btn-action btn-action-view" style="width: 70px; height: 28px;" title="View Details" target="_blank">
                                    <i class="bi bi-eye"></i> <span>View</span>
                                </a>
                                <a href="{{ route('quotations.edit', $quotation) }}" class="btn-action btn-action-edit" style="width: 70px; height: 28px;" title="Edit Quotation">
                                    <i class="bi bi-pencil"></i> <span>Edit</span>
                                </a>

                                <!-- Row 2: Convert to Event & Delete -->
                                <a href="{{ route('quotations.convert-to-event', $quotation) }}" class="btn-action btn-action-order" style="width: 70px; height: 28px;" title="Convert to Event" target="_blank">
                                    <i class="bi bi-calendar-check"></i> <span>Event</span>
                                </a>
                                <form method="POST" action="{{ route('quotations.destroy', $quotation) }}" class="m-0 p-0" style="width: 70px;" onsubmit="handleDeleteConfirmation(event, this, {
                                    type: 'Quotation',
                                    title: '{{ $quotation->quotation_no }} - {{ addslashes($quotation->event_name) }}',
                                    hasPayments: false
                                });">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-action-delete" style="width: 70px; height: 28px;" title="Delete Quotation">
                                        <i class="bi bi-trash3"></i> <span>Delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-file-earmark-text fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <div class="fw-semibold" style="font-size: 14px;">No quotations match your search criteria</div>
                            <small class="text-muted">Try adjusting your keyword, customer, package, or status filters.</small>
                            <div class="mt-3">
                                <a href="{{ route('quotations.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($quotations->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                Showing {{ $quotations->firstItem() }} to {{ $quotations->lastItem() }} of {{ $quotations->total() }} quotations
            </span>
            <div>
                {{ $quotations->links() }}
            </div>
        </div>
    @endif
</div>
@endsection