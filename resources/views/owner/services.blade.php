@extends('layouts.ownertemplates')

@section('title', 'My Services')

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-0">My Services</h1>
            <p class="text-muted small mb-0">Manage and track your vehicle services</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="bi bi-funnel"></i> Filter
            </button>
        </div>
    </div>

    <!-- Services Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-tools me-2"></i>Service History</h5>
            <span class="small">Total: {{ $services->count() }} services</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Vehicle</th>
                            <th>Service Type</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-car-front-fill text-primary me-2"></i>
                                        <div>
                                            <strong>{{ $service->vehicle->model ?? 'N/A' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $service->vehicle->registration_no ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if(is_array($service->service_types))
                                        {{ implode(', ', $service->service_types) }}
                                    @else
                                        {{ $service->service_types ?? 'General Service' }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $service->status == 'completed' ? 'success' : 
                                        ($service->status == 'cancelled' ? 'danger' : 
                                        ($service->status == 'in_progress' ? 'warning' : 'secondary'))
                                    }}">
                                        {{ ucfirst(str_replace('_', ' ', $service->status)) }}
                                    </span>
                                </td>
                                <td>₹{{ number_format($service->amount, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($service->created_at)->format('d M Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('owner.services.show', $service->id) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('owner.invoice.print', $service->id) }}" 
                                           class="btn btn-outline-success">
                                            <i class="bi bi-receipt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-info-circle me-1"></i> No services found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination - Only show if it's a paginator and has pages -->
            @if(method_exists($services, 'hasPages') && $services->hasPages())
                <div class="card-footer bg-transparent">
                    {{ $services->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Services</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="GET" action="{{ route('owner.services') }}">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                        <a href="{{ route('owner.services') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection