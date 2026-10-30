@extends('layouts.app')
@section('title', 'Orders Management')
@section('page-title', 'Orders')

@section('content')
<!-- Page Header (Tritcal Style) -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0"
            style="width: 44px; height: 44px; background: linear-gradient(135deg, #484646 0%, #3a3838 100%); font-size: 20px;">
            <i class="bi bi-receipt"></i>
        </div>
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.3px;">Orders Management</h4>
                <span class="badge bg-light text-dark border px-2 py-0.5" style="font-size: 11px; font-weight: 700;">
                    {{ $orders->total() }} Total Orders
                </span>
            </div>
            <p class="text-muted small mb-0">Track customer sales orders, billings, payment settlements, and balances.</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('orders.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg"></i> Create New Order
        </a>
    </div>
</div>

<!-- Search & Filter Card (Tritcal Style) -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #edf2f7 !important;">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 13.5px;">
            <i class="bi bi-funnel text-secondary"></i> Search & Filters
        </h6>
        @if(request()->anyFilled(['search', 'customer_id', 'event_id', 'payment_status', 'status', 'from_date', 'to_date']))
            <a href="{{ route('orders.index') }}" class="btn btn-sm btn-link text-decoration-none text-danger p-0" style="font-size: 12px;">
                <i class="bi bi-x-circle me-1"></i> Clear Filters
            </a>
        @endif
    </div>

    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('orders.index') }}" id="ordersFilterForm">
            <div class="row g-3">
                <!-- 1. Search Keyword -->
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-search me-1"></i> Quick Search
                    </label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Order No, Customer, Event...">
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

                <!-- 3. Event Select -->
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-calendar-event me-1"></i> Event
                    </label>
                    <select name="event_id" class="form-select">
                        <option value="">All Events</option>
                        @foreach($events as $ev)
                            <option value="{{ $ev->id }}" @selected(request('event_id') == $ev->id)>
                                {{ $ev->event_no }} &bull; {{ $ev->event_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Payment Status -->
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-credit-card me-1"></i> Payment Status
                    </label>
                    @php($currentPaymentStatus = request('payment_status', request('status')))
                    <select name="payment_status" class="form-select">
                        <option value="">All Payment Statuses</option>
                        <option value="pending" @selected($currentPaymentStatus === 'pending')>Pending (Unpaid)</option>
                        <option value="partial" @selected($currentPaymentStatus === 'partial')>Partial (Part Paid)</option>
                        <option value="paid" @selected($currentPaymentStatus === 'paid')>Paid (Settled)</option>
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
                        <i class="bi bi-funnel-fill me-1"></i> Filter Orders
                    </button>
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary shadow-sm">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table Card -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid #edf2f7 !important;">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 13.5px;">
            <i class="bi bi-list-check text-secondary"></i> Orders List
        </h6>
        <div class="text-muted small">
            @if($orders->total() > 0)
                Showing <strong>{{ $orders->firstItem() }}-{{ $orders->lastItem() }}</strong> of <strong>{{ $orders->total() }}</strong> records
            @else
                0 records found
            @endif
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted" style="border-bottom: 2px solid #edf2f7;">
                <tr>
                    <th class="ps-4">Order Details</th>
                    <th>Customer</th>
                    <th>Linked Event</th>
                    <th>Order Date</th>
                    <th class="text-end">Total Amount</th>
                    <th class="text-end">Paid Amount</th>
                    <th class="text-end">Balance Due</th>
                    <th class="text-center">Status</th>
                    <th class="text-end pe-4 text-nowrap" style="width: 1%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <!-- Order Details -->
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark border font-monospace px-2 py-0.5" style="font-size: 11px; font-weight: 700;">
                                    {{ $order->order_no }}
                                </span>
                                @if($order->is_auto_created)
                                    <span class="badge bg-secondary-subtle text-secondary border px-1.5 py-0.5" style="font-size: 10px;" title="Auto-created from event advance payment">
                                        Auto
                                    </span>
                                @endif
                            </div>
                            <div class="fw-bold text-dark mt-1" style="font-size: 13px;">
                                <a href="{{ route('orders.show', $order) }}" class="text-dark text-decoration-none">
                                    View Invoice
                                </a>
                            </div>
                        </td>

                        <!-- Customer -->
                        <td>
                            @if($order->customer)
                                <div class="fw-semibold text-dark" style="font-size: 13px;">
                                    {{ $order->customer->name }}
                                </div>
                                <small class="text-muted" style="font-size: 11px;">
                                    <i class="bi bi-telephone text-secondary me-1"></i>{{ $order->customer->phone ?: 'No phone' }}
                                </small>
                            @else
                                <span class="text-muted small">N/A</span>
                            @endif
                        </td>

                        <!-- Linked Event -->
                        <td>
                            @if($order->event)
                                <div class="fw-semibold text-dark" style="font-size: 12.5px;">
                                    <a href="{{ route('events.show', $order->event) }}" class="text-decoration-none text-dark">
                                        {{ $order->event->event_name }}
                                    </a>
                                </div>
                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                    <small class="text-muted font-monospace" style="font-size: 11px;">
                                        {{ $order->event->event_no }}
                                    </small>
                                    @if($order->package_name || $order->event->subCategory)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-1.5 py-0.5" style="font-size: 10px;">
                                            <i class="bi bi-box-seam me-1"></i>{{ $order->package_name ?: $order->event->subCategory->name }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted small">No event linked</span>
                            @endif
                        </td>

                        <!-- Order Date -->
                        <td>
                            <div class="text-dark small fw-semibold">
                                {{ $order->order_date?->format('M d, Y') ?? 'N/A' }}
                            </div>
                            <small class="text-muted" style="font-size: 11px;">
                                {{ $order->order_date?->diffForHumans() }}
                            </small>
                        </td>

                        <!-- Financials: Total Amount -->
                        <td class="text-end">
                            <span class="fw-bold text-dark" style="font-size: 13.5px;">
                                Rs. {{ number_format($order->total_amount, 2) }}
                            </span>
                            @if($order->discount > 0)
                                <div class="text-muted" style="font-size: 11px;">
                                    Disc: Rs. {{ number_format($order->discount, 2) }}
                                </div>
                            @endif
                        </td>

                        <!-- Financials: Paid Amount -->
                        <td class="text-end">
                            <span class="fw-bold text-success" style="font-size: 13.5px;">
                                Rs. {{ number_format($order->paid_amount, 2) }}
                            </span>
                        </td>

                        <!-- Financials: Balance -->
                        <td class="text-end">
                            @if($order->balance > 0)
                                <span class="fw-bold text-danger" style="font-size: 13.5px;">
                                    Rs. {{ number_format($order->balance, 2) }}
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5" style="font-size: 11px;">
                                    Settled
                                </span>
                            @endif
                        </td>

                        <!-- Payment Status -->
                        <td class="text-center">
                            @if($order->payment_status === 'paid')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1" style="font-size: 11px; font-weight: 600;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981; display: inline-block; margin-right: 4px;"></span> Paid
                                </span>
                            @elseif($order->payment_status === 'partial')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1" style="font-size: 11px; font-weight: 600;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #3b82f6; display: inline-block; margin-right: 4px;"></span> Partial
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1" style="font-size: 11px; font-weight: 600;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #f59e0b; display: inline-block; margin-right: 4px;"></span> Pending
                                </span>
                            @endif
                        </td>

                        <!-- Actions (2x2 Grid, 2 buttons per row, 70px x 28px) -->
                        <td class="text-end pe-4 text-nowrap">
                            <div class="d-inline-grid justify-content-end" style="display: inline-grid; grid-template-columns: repeat(2, 70px); gap: 4px;">
                                <!-- Row 1: View & Edit -->
                                <a href="{{ route('orders.show', $order) }}" class="btn-action btn-action-view" style="width: 70px; height: 28px;" title="View Details" target="_blank">
                                    <i class="bi bi-eye"></i> <span>View</span>
                                </a>
                                <a href="{{ route('orders.edit', $order) }}" class="btn-action btn-action-edit" style="width: 70px; height: 28px;" title="Edit Order">
                                    <i class="bi bi-pencil"></i> <span>Edit</span>
                                </a>
                                <!-- Row 2: Pay (if balance > 0) & Delete -->
                                @if($order->balance > 0)
                                    <a href="{{ route('payments.create-for-order', $order->id) }}" class="btn-action btn-action-order" style="width: 70px; height: 28px;" title="Record Payment" target="_blank">
                                        <i class="bi bi-credit-card"></i> <span>Pay</span>
                                    </a>
                                @else
                                    <div style="width: 70px; height: 28px;"></div>
                                @endif
                                <form method="POST" action="{{ route('orders.destroy', $order) }}" class="m-0 p-0" style="width: 70px;" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-action-delete" style="width: 70px; height: 28px;" title="Delete Order">
                                        <i class="bi bi-trash3"></i> <span>Delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-receipt fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <div class="fw-semibold" style="font-size: 14px;">No orders match your search criteria</div>
                            <small class="text-muted">Try adjusting your keyword, customer, event, or payment status filters.</small>
                            <div class="mt-3">
                                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} orders
            </span>
            <div>
                {{ $orders->links() }}
            </div>
        </div>
    @endif
</div>
@endsection