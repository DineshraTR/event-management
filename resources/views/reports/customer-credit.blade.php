@extends('layouts.app')
@section('title', 'Customer Credit Report')
@section('page-title', 'Customer Credit Report')

@section('content')
<div class="page-header">
    <div>
        <h4>Customer Credit Report</h4>
        <p>Outstanding receivables, total billed values, and settlement balances by customer.</p>
    </div>
</div>

<!-- Search Filter Card -->
<div class="card mb-3">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-sm-6 col-md-5 col-lg-4">
                <label class="form-label" for="search">Search Customer</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input id="search" class="form-control border-start-0" name="search" value="{{ $search }}" placeholder="Search by customer name...">
                </div>
            </div>
            <div class="col-12 col-sm-auto d-flex align-items-center gap-2 mt-2 mt-sm-0">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i> Search
                </button>
                @if(request('search'))
                    <a href="{{ route('reports.customer-credit') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Customers Table Card -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th class="text-end">Total Billed</th>
                    <th class="text-end">Total Paid</th>
                    <th class="text-end">Outstanding Balance</th>
                    <th class="text-center">Credit Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $row)
                    <tr>
                        <td>
                            <a href="{{ route('customers.show', $row['customer']) }}" class="fw-bold" target="_blank">
                                {{ $row['customer']->name }}
                            </a>
                        </td>
                        <td class="text-end fw-medium text-dark">Rs. {{ number_format($row['total_billed'], 2) }}</td>
                        <td class="text-end text-success fw-medium">Rs. {{ number_format($row['total_paid'], 2) }}</td>
                        <td class="text-end fw-bold {{ $row['balance'] > 0 ? 'text-danger' : 'text-muted' }}">
                            Rs. {{ number_format($row['balance'], 2) }}
                        </td>
                        <td class="text-center">
                            <span class="badge-status badge-{{ $row['status'] === 'cleared' ? 'paid' : 'partial' }}">
                                <i class="bi bi-dot"></i> {{ ucfirst($row['status']) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                            No customer balances found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($customers, 'hasPages') && $customers->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted small">
                Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} records
            </span>
            <div>
                {{ $customers->withQueryString()->links() }}
            </div>
        </div>
    @endif
</div>
@endsection