@extends('layouts.app')
@section('title', 'Events Management')
@section('page-title', 'Events')

@section('content')
<!-- Page Header (Tritcal Style) -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0"
            style="width: 44px; height: 44px; background: linear-gradient(135deg, #484646 0%, #3a3838 100%); font-size: 20px;">
            <i class="bi bi-calendar-event"></i>
        </div>
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.3px;">Events Management</h4>
                <span class="badge bg-light text-dark border px-2 py-0.5" style="font-size: 11px; font-weight: 700;">
                    {{ $events->total() }} Total Events
                </span>
            </div>
            <p class="text-muted small mb-0">Monitor customer event schedules, booked packages, and payment milestones.</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('events.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg"></i> Register New Event
        </a>
    </div>
</div>

<!-- Advanced Search & Filter Card (Tritcal Project Management Style) -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border: 1px solid #edf2f7 !important;">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 13.5px;">
            <i class="bi bi-funnel text-secondary"></i> Search & Filters
        </h6>
        @if(request()->anyFilled(['search', 'customer_id', 'event_id', 'sub_category_id', 'from_date', 'to_date', 'status']))
            <a href="{{ route('events.index', ['tab' => $tab]) }}" class="btn btn-sm btn-link text-decoration-none text-danger p-0" style="font-size: 12px;">
                <i class="bi bi-x-circle me-1"></i> Clear Filters
            </a>
        @endif
    </div>

    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('events.index') }}" id="eventsFilterForm">
            <!-- Retain Active Tab Filter -->
            <input type="hidden" name="tab" value="{{ $tab }}">

            <div class="row g-3">
                <!-- 1. Search Keyword -->
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-search me-1"></i> Quick Search
                    </label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Event No, Name, Venue...">
                </div>

                <!-- 2. Customer Select (Searchable) -->
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

                <!-- 3. Event / Package Select (Searchable) -->
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-box-seam me-1"></i> Event Package
                    </label>
                    <select name="sub_category_id" class="form-select searchable-select" data-placeholder="All Event Packages">
                        <option value="">All Event Packages</option>
                        @foreach($subCategories as $sub)
                            <option value="{{ $sub->id }}" @selected(request('sub_category_id') == $sub->id)>
                                {{ $sub->name }} ({{ $sub->category->name ?? 'General' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Specific Event Selection (Searchable) -->
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-calendar2-check me-1"></i> Specific Event
                    </label>
                    <select name="event_id" class="form-select searchable-select" data-placeholder="All Specific Events">
                        <option value="">All Specific Events</option>
                        @foreach($eventList as $evItem)
                            <option value="{{ $evItem->id }}" @selected(request('event_id') == $evItem->id)>
                                {{ $evItem->event_no }} &bull; {{ $evItem->event_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 5. From Date -->
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-calendar-event me-1"></i> From Date
                    </label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>

                <!-- 6. To Date -->
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-calendar-check me-1"></i> To Date
                    </label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>

                <!-- 7. Status Filter (Searchable) -->
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="bi bi-tag me-1"></i> Status
                    </label>
                    <select name="status" class="form-select searchable-select" data-placeholder="All Statuses">
                        <option value="">All Statuses</option>
                        <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                        <option value="confirmed" @selected(request('status') === 'confirmed')>Confirmed</option>
                        <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                        <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                    </select>
                </div>

                <!-- 8. Filter & Reset Buttons -->
                <div class="col-12 col-sm-6 col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary shadow-sm flex-grow-1">
                        <i class="bi bi-funnel-fill me-1"></i> Filter
                    </button>
                    <a href="{{ route('events.index', ['tab' => $tab]) }}" class="btn btn-secondary shadow-sm">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Order Status Tabs Navigation -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <div class="nav nav-pills gap-2 p-1.5 bg-white border rounded-3 shadow-sm" style="border-radius: 12px; border-color: #edf2f7 !important;">
        <!-- Tab 1: Orders Placed -->
        <a href="{{ route('events.index', array_merge(request()->except('tab', 'page'), ['tab' => 'ordered'])) }}"
           class="nav-link px-3 py-2 rounded-2 d-inline-flex align-items-center gap-2 {{ $tab === 'ordered' ? 'active' : '' }}"
           style="{{ $tab === 'ordered' ? 'background: #0f172a; color: #ffffff !important;' : 'color: #475569; font-weight: 600;' }}">
            <span>Orders Placed</span>
            <span class="badge {{ $tab === 'ordered' ? 'bg-white text-dark' : 'bg-light text-dark border' }}" style="font-size: 11px; font-weight: 700;">
                {{ $countOrdered }}
            </span>
        </a>

        <!-- Tab 2: No Orders Placed -->
        <a href="{{ route('events.index', array_merge(request()->except('tab', 'page'), ['tab' => 'no_order'])) }}"
           class="nav-link px-3 py-2 rounded-2 d-inline-flex align-items-center gap-2 {{ $tab === 'no_order' ? 'active' : '' }}"
           style="{{ $tab === 'no_order' ? 'background: #0f172a; color: #ffffff !important;' : 'color: #475569; font-weight: 600;' }}">
            <span>No Order Placed</span>
            <span class="badge {{ $tab === 'no_order' ? 'bg-white text-dark' : 'bg-light text-dark border' }}" style="font-size: 11px; font-weight: 700;">
                {{ $countNoOrder }}
            </span>
        </a>

        <!-- Tab 3: All Events -->
        <a href="{{ route('events.index', array_merge(request()->except('tab', 'page'), ['tab' => 'all'])) }}"
           class="nav-link px-3 py-2 rounded-2 d-inline-flex align-items-center gap-2 {{ $tab === 'all' ? 'active' : '' }}"
           style="{{ $tab === 'all' ? 'background: #0f172a; color: #ffffff !important;' : 'color: #475569; font-weight: 600;' }}">
            <span>All Events</span>
            <span class="badge {{ $tab === 'all' ? 'bg-white text-dark' : 'bg-light text-dark border' }}" style="font-size: 11px; font-weight: 700;">
                {{ $countAll }}
            </span>
        </a>
    </div>

    <div>
        @if($tab === 'ordered')
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill" style="font-size: 12px; font-weight: 600;">
                <i class="bi bi-check-circle-fill me-1"></i> Showing events with created orders
            </span>
        @elseif($tab === 'no_order')
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill" style="font-size: 12px; font-weight: 600;">
                <i class="bi bi-hourglass-split me-1"></i> Showing pending events requiring an order
            </span>
        @else
            <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill" style="font-size: 12px; font-weight: 600;">
                <i class="bi bi-layers me-1"></i> Showing all registered events
            </span>
        @endif
    </div>
</div>

<!-- Events Table Card (Tritcal Style) -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid #edf2f7 !important;">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 13.5px;">
                <i class="bi bi-list-check text-secondary"></i> Events List
            </h6>
            @if($tab === 'ordered')
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5" style="font-size: 11px;">Orders Placed</span>
            @elseif($tab === 'no_order')
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-0.5" style="font-size: 11px;">No Orders</span>
            @endif
        </div>
        <div class="text-muted small">
            @if($events->total() > 0)
                Showing <strong>{{ $events->firstItem() }}-{{ $events->lastItem() }}</strong> of <strong>{{ $events->total() }}</strong> records
            @else
                0 records found
            @endif
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted" style="border-bottom: 2px solid #edf2f7;">
                <tr>
                    <th class="ps-4">Event Details</th>
                    <th>Customer</th>
                    <th>Package / Category</th>
                    <th>Event Date</th>
                    <th class="text-end">Financials</th>
                    <th class="text-center">Order Status</th>
                    <th class="text-center">Event Status</th>
                    <th class="text-end pe-4 text-nowrap" style="width: 1%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                    <tr>
                        <!-- Event Details -->
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark border font-monospace px-2 py-0.5" style="font-size: 11px; font-weight: 700;">
                                    {{ $event->event_no }}
                                </span>
                            </div>
                            <div class="fw-bold text-dark mt-1" style="font-size: 13.5px;">
                                <a href="{{ route('events.show', $event) }}" class="text-dark text-decoration-none hover-primary">
                                    {{ $event->event_name }}
                                </a>
                            </div>
                            @if($event->venue)
                                <small class="text-muted" style="font-size: 11.5px;">
                                    <i class="bi bi-geo-alt text-secondary me-1"></i>{{ $event->venue }}
                                </small>
                            @endif
                        </td>

                        <!-- Customer -->
                        <td>
                            @if($event->customer)
                                <div class="fw-semibold text-dark" style="font-size: 13px;">
                                    {{ $event->customer->name }}
                                </div>
                                <small class="text-muted" style="font-size: 11px;">
                                    <i class="bi bi-telephone text-secondary me-1"></i>{{ $event->customer->phone ?: 'No phone' }}
                                </small>
                            @else
                                <span class="text-muted fst-italic">No Customer</span>
                            @endif
                        </td>

                        <!-- Package / Category -->
                        <td>
                            <div class="fw-semibold text-dark" style="font-size: 12.5px;">
                                @if($event->packages && $event->packages->isNotEmpty())
                                    {{ $event->packages->pluck('package_name')->join(', ') }}
                                @else
                                    {{ $event->subCategory->name ?? $event->package_name ?? 'Custom Package' }}
                                @endif
                            </div>
                            <span class="badge bg-light text-dark border px-2 py-0.5" style="font-size: 10.5px; font-weight: 600;">
                                <i class="bi bi-folder2 me-1 text-secondary"></i>{{ $event->packages?->first()?->category_name ?? $event->subCategory?->category?->name ?? 'General' }}
                            </span>
                        </td>

                        <!-- Event Date -->
                        <td style="font-size: 12.5px;">
                            <div class="fw-semibold text-dark">
                                {{ $event->event_date ? $event->event_date->format('M d, Y') : '—' }}
                            </div>
                            @if($event->event_date)
                                <small class="text-muted" style="font-size: 11px;">
                                    {{ $event->event_date->diffForHumans() }}
                                </small>
                            @endif
                        </td>

                        <!-- Financials -->
                        <td class="text-end">
                            <div class="fw-bold text-dark" style="font-size: 13px;">
                                Rs. {{ number_format((float)$event->total_amount, 2) }}
                            </div>
                            @if((float)$event->balance > 0)
                                <small class="text-danger fw-semibold" style="font-size: 11px;">
                                    Bal: Rs. {{ number_format((float)$event->balance, 2) }}
                                </small>
                            @else
                                <small class="text-success fw-semibold" style="font-size: 11px;">
                                    <i class="bi bi-check2"></i> Paid Full
                                </small>
                            @endif
                        </td>

                        <!-- Order Status -->
                        <td class="text-center">
                            @if($event->order)
                                <a href="{{ route('orders.show', $event->order) }}" class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 text-decoration-none d-inline-flex align-items-center gap-1" title="View Order #{{ $event->order->order_no }}" target="_blank">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>{{ $event->order->order_no }}</span>
                                </a>
                            @else
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-hourglass-split"></i>
                                    <span>No Order</span>
                                </span>
                            @endif
                        </td>

                        <!-- Event Status -->
                        <td class="text-center">
                            @if($event->status === 'confirmed')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1" style="font-size: 11px; font-weight: 600;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #22c55e; display: inline-block; margin-right: 4px;"></span> Confirmed
                                </span>
                            @elseif($event->status === 'pending')
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1" style="font-size: 11px; font-weight: 600;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #f59e0b; display: inline-block; margin-right: 4px;"></span> Pending
                                </span>
                            @elseif($event->status === 'completed')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1" style="font-size: 11px; font-weight: 600;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #3b82f6; display: inline-block; margin-right: 4px;"></span> Completed
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1" style="font-size: 11px; font-weight: 600;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #ef4444; display: inline-block; margin-right: 4px;"></span> Cancelled
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="text-end pe-4 text-nowrap">
                            <div class="d-inline-grid justify-content-end" style="display: inline-grid; grid-template-columns: repeat(2, 70px); gap: 4px;">
                                <a href="{{ route('events.show', $event) }}" class="btn-action btn-action-view" style="width: 70px; height: 28px;" title="View Details" target="_blank">
                                    <i class="bi bi-eye"></i> <span>View</span>
                                </a>
                                <a href="{{ route('events.edit', $event) }}" class="btn-action btn-action-edit" style="width: 70px; height: 28px;" title="Edit Event">
                                    <i class="bi bi-pencil"></i> <span>Edit</span>
                                </a>
                                @if(!$event->order)
                                    <a href="{{ route('events.create-order', $event) }}" class="btn-action btn-action-order" style="width: 70px; height: 28px;" title="Convert to Order" target="_blank">
                                        <i class="bi bi-cart-plus"></i> <span>Order</span>
                                    </a>
                                @else
                                    <a href="{{ route('orders.show', $event->order) }}" class="btn-action" style="width: 70px; height: 28px; background-color: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 4px; font-size: 11px; font-weight: 600; border-radius: 6px;" title="View Order #{{ $event->order->order_no }}" target="_blank">
                                        <i class="bi bi-receipt"></i> <span>Order</span>
                                    </a>
                                @endif
                                @if(!$event->hasPayments())
                                    <form method="POST" action="{{ route('events.destroy', $event) }}" class="m-0 p-0" style="width: 70px;" onsubmit="handleDeleteConfirmation(event, this, {
                                        type: 'Event',
                                        title: '{{ $event->event_no }} - {{ addslashes($event->event_name) }}',
                                        hasPayments: false,
                                        extraNote: '{{ $event->order ? 'Associated Order #' . $event->order->order_no . ' and all its items will also be permanently deleted.' : '' }}'
                                    });">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-action-delete" style="width: 70px; height: 28px;" title="Delete Event">
                                            <i class="bi bi-trash3"></i> <span>Delete</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <div class="fw-semibold" style="font-size: 14px;">
                                @if($tab === 'ordered')
                                    No events with orders placed found
                                @elseif($tab === 'no_order')
                                    No pending events without orders found
                                @else
                                    No events match your criteria
                                @endif
                            </div>
                            <small class="text-muted">
                                @if($tab === 'ordered')
                                    You can convert pending events to orders from the "No Order Placed" tab.
                                @elseif($tab === 'no_order')
                                    All current events already have orders created!
                                @else
                                    Try adjusting your keyword, customer, package, or date range filters.
                                @endif
                            </small>
                            <div class="mt-3">
                                <a href="{{ route('events.index', ['tab' => $tab]) }}" class="btn btn-sm btn-secondary shadow-sm">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($events->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                Showing {{ $events->firstItem() }} to {{ $events->lastItem() }} of {{ $events->total() }} events
            </span>
            <div>
                {{ $events->links() }}
            </div>
        </div>
    @endif
</div>
@endsection