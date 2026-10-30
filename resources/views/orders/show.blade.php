@extends('layouts.app')
@section('title', 'Order ' . $order->order_no . ' Details')
@section('page-title', 'Order Details')

@section('content')
<style>
    .order-show-header {
        background: #ffffff;
        border-radius: 14px;
        padding: 20px 24px;
        border: 1px solid #edf2f7;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.03);
    }

    .section-card {
        background: #ffffff;
        border: 1px solid #edf2f7;
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .section-card-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .section-card-title {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .financial-line-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        font-size: 13.5px;
    }

    .financial-total-banner {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
        margin: 14px 0;
    }
</style>

<!-- Top Header (Tritcal Style) -->
<div class="order-show-header mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0"
                style="width: 46px; height: 46px; background: linear-gradient(135deg, #484646 0%, #3a3838 100%); font-size: 22px;">
                <i class="bi bi-receipt"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.3px;">Order {{ $order->order_no }}</h4>
                    @if($order->payment_status === 'paid')
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1" style="font-size: 11.5px; font-weight: 600;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981; display: inline-block; margin-right: 4px;"></span> Fully Paid
                        </span>
                    @elseif($order->payment_status === 'partial')
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1" style="font-size: 11.5px; font-weight: 600;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #3b82f6; display: inline-block; margin-right: 4px;"></span> Partial Payment
                        </span>
                    @else
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1" style="font-size: 11.5px; font-weight: 600;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #f59e0b; display: inline-block; margin-right: 4px;"></span> Payment Pending
                        </span>
                    @endif
                    @if($order->is_auto_created)
                        <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 11px;">
                            Auto-Generated
                        </span>
                    @endif
                </div>
                <p class="text-muted small mb-0">
                    Customer: <strong class="text-dark">{{ $order->customer?->name ?? 'Walk-in Customer' }}</strong>
                    &bull; Order Date: <span>{{ $order->order_date?->format('M d, Y') ?? 'N/A' }}</span>
                    @if($order->event)
                        &bull; Linked Event: <a href="{{ route('events.show', $order->event) }}" class="text-decoration-none fw-semibold text-primary"><i class="bi bi-calendar2-event me-1"></i>{{ $order->event->event_name }}</a>
                    @endif
                </p>
            </div>
        </div>

        <!-- Header Action Buttons -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Orders
            </a>
            <a href="{{ route('orders.print', $order) }}" target="_blank" class="btn btn-outline-dark shadow-sm">
                <i class="bi bi-printer me-1"></i> Print Invoice
            </a>
            <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-pencil me-1"></i> Edit Order
            </a>
            @if($order->balance > 0)
                <a href="{{ route('payments.create-for-order', $order) }}" class="btn btn-success shadow-sm">
                    <i class="bi bi-cash-stack me-1"></i> Record Payment
                </a>
            @endif
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Linked Event, Services & Items, Other Charges, Payments -->
    <div class="col-12 col-lg-8">
        <!-- 1. Linked Event Information Card (if applicable) -->
        @if($order->event)
            <div class="section-card">
                <div class="section-card-header">
                    <h6 class="section-card-title">
                        <i class="bi bi-calendar-event text-primary"></i> Linked Event Information
                    </h6>
                    <a href="{{ route('events.show', $order->event) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                        <i class="bi bi-box-arrow-up-right me-1"></i> View Event Details
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="text-muted d-block" style="font-size: 11.5px;">EVENT TITLE</small>
                            <div class="fw-semibold text-dark fs-6 mt-1">{{ $order->event->event_name }}</div>
                            <span class="badge bg-light text-dark border font-monospace mt-1" style="font-size: 11px;">
                                {{ $order->event->event_no }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block" style="font-size: 11.5px;">EVENT DATE & DAY</small>
                            <div class="fw-semibold text-dark mt-1">
                                <i class="bi bi-calendar3 text-primary me-1"></i>
                                {{ $order->event->event_date?->format('l, F j, Y') ?? 'N/A' }}
                            </div>
                            <small class="text-muted" style="font-size: 11px;">{{ $order->event->event_date?->diffForHumans() }}</small>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block" style="font-size: 11.5px;">BOOKED PACKAGE</small>
                            <div class="fw-semibold text-dark mt-1">
                                <i class="bi bi-box-seam text-secondary me-1"></i>
                                {{ $order->event->subCategory?->name ?? 'Custom Package' }}
                                @if($order->event->subCategory?->category)
                                    <span class="badge bg-light text-secondary border ms-1" style="font-size: 11px;">{{ $order->event->subCategory->category->name }}</span>
                                @endif
                            </div>
                            @if($order->event->package_sell_price > 0)
                                <small class="text-primary fw-bold" style="font-size: 11.5px;">Base Price: Rs. {{ number_format($order->event->package_sell_price, 2) }}</small>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block" style="font-size: 11.5px;">VENUE / LOCATION</small>
                            <div class="fw-semibold text-dark mt-1">
                                @if($order->event->venue)
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $order->event->venue }}
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </div>
                        </div>

                        <!-- Included Services in Selected Package -->
                        @if($order->event->subCategory?->subCategoryServices && $order->event->subCategory->subCategoryServices->isNotEmpty())
                            <div class="col-12 mt-3 pt-3 border-top">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <small class="fw-bold text-dark text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                                        <i class="bi bi-check-circle-fill text-success me-1"></i> Included in Selected Package:
                                    </small>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 10.5px;">
                                        {{ $order->event->subCategory->subCategoryServices->count() }} Services
                                    </span>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($order->event->subCategory->subCategoryServices as $ps)
                                        <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 11.5px;">
                                            <i class="bi bi-check2-circle text-success"></i>
                                            <span>{{ $ps->service?->name ?? 'Service' }} &bull; Qty: <strong>{{ $ps->qty }}</strong></span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- 2. Order Services & Items Card -->
        <div class="section-card">
            <div class="section-card-header">
                <h6 class="section-card-title">
                    <i class="bi bi-box-seam text-primary"></i> Order Items & Quoted Services
                </h6>
                <span class="badge bg-light text-dark border">
                    {{ $order->services->count() }} Line Items
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4 py-3">Item / Service Description</th>
                            <th class="py-3 text-center" style="width: 100px;">Qty</th>
                            <th class="py-3 text-end" style="width: 160px;">Unit Price</th>
                            <th class="py-3 text-end pe-4" style="width: 170px;">Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->services as $line)
                            <tr>
                                <td class="ps-4 fw-medium text-dark">
                                    {{ $line->description }}
                                    @if($line->service)
                                        <small class="text-muted d-block" style="font-size: 11px;">
                                            Service: {{ $line->service->name }}
                                        </small>
                                    @endif
                                </td>
                                <td class="text-center">{{ $line->qty }}</td>
                                <td class="text-end">Rs. {{ number_format($line->unit_price, 2) }}</td>
                                <td class="text-end pe-4 fw-bold text-dark">Rs. {{ number_format($line->total_price, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4 small">
                                    <i class="bi bi-info-circle me-1"></i> No service lines recorded on this order.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($order->services->isNotEmpty())
                        @php
                            $servicesSum = $order->services->sum('total_price');
                        @endphp
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold text-secondary">Items Subtotal:</td>
                                <td class="text-end pe-4 fw-bold text-dark">
                                    Rs. {{ number_format($servicesSum, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <!-- 3. Other Charges Card (if applicable) -->
        @if($order->otherCharges && $order->otherCharges->isNotEmpty())
            <div class="section-card">
                <div class="section-card-header">
                    <h6 class="section-card-title">
                        <i class="bi bi-truck text-primary"></i> Other Charges (Transport, Surcharges)
                    </h6>
                    <span class="badge bg-light text-dark border">
                        {{ $order->otherCharges->count() }} Charges
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                            <tr>
                                <th class="ps-4 py-3">Charge Description</th>
                                <th class="py-3 text-end pe-4" style="width: 200px;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->otherCharges as $charge)
                                <tr>
                                    <td class="ps-4 fw-medium text-dark">{{ $charge->description }}</td>
                                    <td class="text-end pe-4 fw-bold text-dark">Rs. {{ number_format($charge->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td class="text-end fw-bold text-secondary">Charges Subtotal:</td>
                                <td class="text-end pe-4 fw-bold text-dark">
                                    Rs. {{ number_format($order->otherCharges->sum('amount'), 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif

        <!-- 4. Payment History & Transaction Receipts -->
        <div class="section-card">
            <div class="section-card-header">
                <h6 class="section-card-title">
                    <i class="bi bi-credit-card-2-front text-success"></i> Payment History & Receipts
                </h6>
                @if($order->balance > 0)
                    <a href="{{ route('payments.create-for-order', $order) }}" class="btn btn-sm btn-success">
                        <i class="bi bi-plus-lg me-1"></i> Record New Payment
                    </a>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4 py-3">Payment #</th>
                            <th class="py-3">Date</th>
                            <th class="py-3">Payment Method</th>
                            <th class="py-3">Reference / Notes</th>
                            <th class="py-3 text-end" style="width: 150px;">Amount Paid</th>
                            <th class="py-3 text-center pe-4" style="width: 70px;">Receipt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->payments as $payment)
                            <tr>
                                <td class="ps-4 fw-bold font-monospace text-dark" style="font-size: 12px;">
                                    {{ $payment->payment_no ?? ('PAY-' . str_pad($payment->id, 5, '0', STR_PAD_LEFT)) }}
                                </td>
                                <td>
                                    <span class="text-dark small fw-medium">
                                        {{ $payment->payment_date?->format('M d, Y') ?? '—' }}
                                    </span>
                                    <small class="text-muted d-block" style="font-size: 11px;">
                                        {{ $payment->payment_date?->diffForHumans() }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11px;">
                                        <i class="bi bi-cash me-1 text-secondary"></i>
                                        {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        {{ $payment->reference_no ?: ($payment->notes ?: '—') }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-success fs-6">
                                    Rs. {{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="text-center pe-4">
                                    <a href="{{ route('payments.print', $payment) }}" target="_blank" class="btn btn-sm btn-light border px-2 py-1" title="Print Receipt">
                                        <i class="bi bi-printer text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4 small">
                                    <i class="bi bi-cash-stack fs-2 d-block mb-1 text-secondary opacity-50"></i>
                                    <span>No payments recorded for this order yet.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($order->payments->isNotEmpty())
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold text-secondary">Total Settled Payments:</td>
                                <td class="text-end fw-bold text-success fs-6">
                                    Rs. {{ number_format($order->paid_amount, 2) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <!-- 5. Order Notes / Special Remarks (if any) -->
        @if($order->notes)
            <div class="section-card">
                <div class="section-card-header">
                    <h6 class="section-card-title">
                        <i class="bi bi-card-text text-secondary"></i> Order Notes & Instructions
                    </h6>
                </div>
                <div class="card-body p-4 text-secondary" style="font-size: 13.5px; line-height: 1.6;">
                    {{ $order->notes }}
                </div>
            </div>
        @endif
    </div>

    <!-- Right Column: Customer Info, Financial Summary & Actions -->
    <div class="col-12 col-lg-4">
        <!-- 1. Customer Contact Card -->
        <div class="section-card">
            <div class="section-card-header">
                <h6 class="section-card-title">
                    <i class="bi bi-person text-primary"></i> Customer Contact
                </h6>
            </div>
            <div class="card-body p-4">
                @if($order->customer)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm flex-shrink-0"
                            style="width: 44px; height: 44px; background: linear-gradient(135deg, #484646 0%, #3a3838 100%); font-size: 17px;">
                            {{ strtoupper(substr($order->customer->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-6">{{ $order->customer->name }}</div>
                            <small class="text-muted">Client Account</small>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2.5 pt-2 border-top">
                        <div class="d-flex align-items-center gap-2 text-dark small">
                            <i class="bi bi-telephone text-primary" style="width: 18px;"></i>
                            @if($order->customer->phone)
                                <a href="tel:{{ $order->customer->phone }}" class="text-decoration-none text-dark fw-medium">
                                    {{ $order->customer->phone }}
                                </a>
                            @else
                                <span class="text-muted">No phone provided</span>
                            @endif
                        </div>

                        <div class="d-flex align-items-center gap-2 text-dark small">
                            <i class="bi bi-envelope text-primary" style="width: 18px;"></i>
                            @if($order->customer->email)
                                <a href="mailto:{{ $order->customer->email }}" class="text-decoration-none text-dark">
                                    {{ $order->customer->email }}
                                </a>
                            @else
                                <span class="text-muted">No email provided</span>
                            @endif
                        </div>

                        @if($order->customer->address)
                            <div class="d-flex align-items-start gap-2 text-dark small">
                                <i class="bi bi-geo-alt text-primary mt-0.5" style="width: 18px;"></i>
                                <span class="text-secondary">{{ $order->customer->address }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="bi bi-person-x fs-2 d-block mb-1 text-secondary opacity-50"></i>
                        <span>Walk-in Customer</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- 2. Financial Summary Card -->
        <div class="section-card">
            <div class="section-card-header">
                <h6 class="section-card-title">
                    <i class="bi bi-calculator text-primary"></i> Financial Summary
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="financial-line-item">
                    <span class="text-muted">Gross Subtotal:</span>
                    <strong class="text-dark">Rs. {{ number_format($order->sub_total, 2) }}</strong>
                </div>

                <div class="financial-line-item">
                    <span class="text-muted">Special Discount:</span>
                    <strong class="text-danger">- Rs. {{ number_format($order->discount, 2) }}</strong>
                </div>

                <div class="financial-total-banner d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small fw-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">Final Total</div>
                        <small class="text-muted">Net order amount</small>
                    </div>
                    <div class="fs-4 fw-bold text-primary">
                        Rs. {{ number_format($order->total_amount, 2) }}
                    </div>
                </div>

                <div class="financial-line-item">
                    <div>
                        <span class="text-dark fw-semibold">Paid Amount:</span>
                        <div class="text-muted" style="font-size: 11px;">Total settled to date</div>
                    </div>
                    <strong class="text-success fs-6">Rs. {{ number_format($order->paid_amount, 2) }}</strong>
                </div>

                <!-- Payment Progress Bar -->
                @php
                    $percentPaid = $order->total_amount > 0 ? min(100, round(($order->paid_amount / $order->total_amount) * 100)) : 100;
                @endphp
                <div class="my-3">
                    <div class="d-flex justify-content-between text-muted small mb-1">
                        <span>Payment Progress</span>
                        <span><strong>{{ $percentPaid }}%</strong> Settled</span>
                    </div>
                    <div class="progress" style="height: 7px; border-radius: 6px; background-color: #f1f5f9;">
                        <div class="progress-bar bg-{{ $percentPaid >= 100 ? 'success' : ($percentPaid > 0 ? 'primary' : 'secondary') }}" 
                             role="progressbar" 
                             style="width: {{ $percentPaid }}%;" 
                             aria-valuenow="{{ $percentPaid }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-2.5 mt-2 border-top">
                    <span class="fw-bold text-dark">Remaining Balance:</span>
                    @if($order->balance > 0)
                        <strong class="text-danger fs-5">
                            Rs. {{ number_format($order->balance, 2) }}
                        </strong>
                    @else
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1" style="font-size: 12px; font-weight: 700;">
                            <i class="bi bi-check2 me-1"></i> Settled Full
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. Quick Actions Card -->
        <div class="section-card">
            <div class="section-card-header">
                <h6 class="section-card-title">
                    <i class="bi bi-lightning-charge text-warning"></i> Quick Operations
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="d-grid gap-2">
                    <a href="{{ route('orders.edit', $order) }}" class="btn btn-outline-primary py-2 fw-semibold">
                        <i class="bi bi-pencil me-1.5"></i> Edit Order Details
                    </a>

                    @if($order->balance > 0)
                        <a href="{{ route('payments.create-for-order', $order) }}" class="btn btn-success py-2 fw-semibold">
                            <i class="bi bi-credit-card me-1.5"></i> Record New Payment
                        </a>
                    @endif

                    @if($order->event)
                        <a href="{{ route('events.show', $order->event) }}" class="btn btn-outline-dark py-2 fw-semibold" target="_blank">
                            <i class="bi bi-calendar2-event me-1.5"></i> Open Associated Event
                        </a>
                    @endif

                    <form method="POST" action="{{ route('orders.destroy', $order) }}" class="mt-2" onsubmit="return confirm('Are you sure you want to delete this order? All associated payment records may be affected.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100 py-2 fw-semibold">
                            <i class="bi bi-trash3 me-1.5"></i> Delete Order
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection