@extends('layouts.app')
@section('title', 'Order History')
@section('page-title', 'Order History')

@section('content')
<div class="page-header">
    <div>
        <h4>Order History</h4>
        <p>Comprehensive historical log of all customer orders, billing, and settlements.</p>
    </div>
</div>

<!-- Filters Card -->
<div class="card mb-3">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label" for="search">Quick Search</label>
                <input class="form-control" type="text" id="search" name="search" value="{{ $search }}" placeholder="Order #, Customer, Event...">
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label" for="date_from">From Date</label>
                <input class="form-control" type="date" id="date_from" name="date_from" value="{{ $dateFrom }}">
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label" for="date_to">To Date</label>
                <input class="form-control" type="date" id="date_to" name="date_to" value="{{ $dateTo }}">
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label" for="customer_id">Customer</label>
                <select class="form-select" id="customer_id" name="customer_id">
                    <option value="">All Customers</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" @selected((string) $customerId === (string) $customer->id)>{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <label class="form-label" for="status">Payment Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    @foreach(['pending', 'partial', 'paid'] as $option)
                        <option value="{{ $option }}" @selected($status === $option)>{{ ucfirst($option) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-lg-auto d-flex align-items-center gap-2 mt-2 mt-lg-0">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel-fill me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'date_from', 'date_to', 'customer_id', 'status']))
                    <a href="{{ route('reports.order-history') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Orders Table Card -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Order No</th>
                    <th>Customer</th>
                    <th>Event</th>
                    <th>Date</th>
                    <th class="text-end">Total Amount</th>
                    <th class="text-end">Paid Amount</th>
                    <th class="text-end">Balance</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('orders.show', $order) }}" class="fw-bold" target="_blank">
                                {{ $order->order_no }}
                            </a>
                        </td>
                        <td>{{ $order->customer?->name ?? '—' }}</td>
                        <td>{{ $order->event?->event_name ?? '—' }}</td>
                        <td>
                            <span class="text-nowrap">{{ $order->order_date?->format('M j, Y') ?? '—' }}</span>
                        </td>
                        <td class="text-end fw-semibold text-dark">Rs. {{ number_format($order->total_amount, 2) }}</td>
                        <td class="text-end text-success">Rs. {{ number_format($order->paid_amount, 2) }}</td>
                        <td class="text-end {{ $order->balance > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                            Rs. {{ number_format($order->balance, 2) }}
                        </td>
                        <td class="text-center">
                            <span class="badge-status badge-{{ $order->payment_status }}">
                                <i class="bi bi-dot"></i> {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                            No matching orders found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted small">
                Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} records
            </span>
            <div>
                {{ $orders->withQueryString()->links() }}
            </div>
        </div>
    @endif
</div>
@endsection