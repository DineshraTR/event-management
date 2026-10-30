@extends('layouts.app')

@section('title', $resourceTitle)
@section('page-title', $resourceTitle)

@section('content')
<!-- Project Management Header -->
<div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
            <h4 class="fw-bold mb-0" style="color: #0f172a; letter-spacing: -0.3px;">{{ $resourceTitle }}</h4>
            @if(method_exists($resourceItems, 'total'))
                <span class="badge bg-light text-dark border px-2 py-0.5" style="font-size: 11.5px; font-weight: 700;">
                    <i class="bi bi-layers me-1 text-secondary"></i>{{ $resourceItems->total() }} {{ strtolower($resourceTitle) }}
                </span>
            @elseif(is_countable($resourceItems))
                <span class="badge bg-light text-dark border px-2 py-0.5" style="font-size: 11.5px; font-weight: 700;">
                    <i class="bi bi-layers me-1 text-secondary"></i>{{ count($resourceItems) }} {{ strtolower($resourceTitle) }}
                </span>
            @endif
        </div>
        <p class="text-muted small mb-0">{{ $resourceDescription ?? 'Browse, filter, and manage ' . strtolower($resourceTitle) . ' across your system.' }}</p>
    </div>
    @if(!empty($createRoute))
        <a href="{{ route($createRoute) }}" class="btn btn-primary shadow-sm d-inline-flex align-items-center gap-1.5">
            <i class="bi bi-plus-lg"></i> {{ $createLabel ?? 'Add new' }}
        </a>
    @endif
</div>

<!-- Project Management Filter & Toolbar Card -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 14px; overflow: hidden; border: 1px solid #edf2f7 !important;">
    <div class="card-body border-bottom p-3 bg-white">
        <form method="GET" action="{{ route($resourceRoute . '.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-sm-6 col-md-5 col-lg-4">
                <label class="form-label small fw-bold text-muted mb-1" for="search">
                    <i class="bi bi-search me-1"></i> Quick Search
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input id="search" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Search {{ strtolower($resourceTitle) }}...">
                </div>
            </div>
            @if(!empty($statusFilter))
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1" for="status">
                        <i class="bi bi-funnel me-1"></i> Status Filter
                    </label>
                    <select id="status" name="status" class="form-select">
                        <option value="">All statuses</option>
                        @foreach($statusFilter as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-12 col-sm-auto d-flex align-items-center gap-2 mt-2 mt-sm-0">
                <button class="btn btn-primary px-3" type="submit">
                    <i class="bi bi-funnel-fill me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route($resourceRoute . '.index') }}" class="btn btn-outline-secondary px-3">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted" style="border-bottom: 1px solid #e2e8f0;">
                <tr>
                    @foreach($columns as $column)
                        <th class="text-nowrap px-3 py-2.5" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.6px; font-weight: 700; color: #64748b;">
                            {{ $column['label'] }}
                        </th>
                    @endforeach
                    <th class="text-end text-nowrap px-3 py-2.5" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.6px; font-weight: 700; color: #64748b; width: 1%;">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($resourceItems as $item)
                    <tr style="transition: background-color 0.15s ease;">
                        @foreach($columns as $column)
                            @php($value = data_get($item, $column['key']))
                            <td class="px-3 py-3 {{ ($column['type'] ?? '') === 'status' ? 'text-nowrap' : '' }}">
                                @if(($column['type'] ?? '') === 'money')
                                    <strong class="text-dark">Rs. {{ number_format((float) $value, 2) }}</strong>
                                @elseif(($column['type'] ?? '') === 'date')
                                    <span class="text-nowrap text-dark small"><i class="bi bi-calendar3 me-1 text-muted"></i>{{ $value ? \Illuminate\Support\Carbon::parse($value)->format('M j, Y') : '—' }}</span>
                                @elseif(($column['type'] ?? '') === 'status')
                                    <span class="badge-status badge-{{ $value }}"><i class="bi bi-dot"></i> {{ ucfirst((string) $value) }}</span>
                                @elseif(($column['type'] ?? '') === 'boolean')
                                    <span class="badge-status badge-{{ $value ? 'accepted' : 'draft' }}">{{ $value ? 'Active' : 'Inactive' }}</span>
                                @else
                                    <span class="fw-medium text-dark">{{ $value ?: '—' }}</span>
                                @endif
                            </td>
                        @endforeach
                        <td class="text-end text-nowrap px-3 py-3">
                            <div class="action-btns-wrapper justify-content-end">
                                @if(!empty($showRoute))
                                    <a href="{{ route($resourceRoute . '.show', $item) }}" class="btn-action btn-action-view" title="View details">
                                        <i class="bi bi-eye"></i> <span>View</span>
                                    </a>
                                @endif
                                @if(!empty($editRoute))
                                    <a href="{{ route($resourceRoute . '.edit', $item) }}" class="btn-action btn-action-edit" title="Edit record">
                                        <i class="bi bi-pencil"></i> <span>Edit</span>
                                    </a>
                                @endif
                                @if(!empty($deleteRoute))
                                    <form method="POST" action="{{ route($resourceRoute . '.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-action-delete" title="Delete record">
                                            <i class="bi bi-trash3"></i> <span>Delete</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + 1 }}" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <h6 class="fw-bold mb-1 text-dark">No records found</h6>
                            <p class="small text-muted mb-0">There are no {{ strtolower($resourceTitle) }} matching your filter criteria.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($resourceItems, 'hasPages') && $resourceItems->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted small">
                Showing {{ $resourceItems->firstItem() }} to {{ $resourceItems->lastItem() }} of {{ $resourceItems->total() }} records
            </span>
            <div>
                {{ $resourceItems->withQueryString()->links() }}
            </div>
        </div>
    @endif
</div>
@endsection