@extends('layouts.admintemplates')

@section('title', 'Service Management')

@section('head')
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .badge { font-size: 0.75rem; }
        .table td, .table th { vertical-align: middle; }
        .btn-group-sm > .btn { padding: 0.25rem 0.5rem; }
    </style>
@endsection

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-tools"></i> Service Management
            </h1>
            <p class="text-muted small mb-0">Track and manage all service jobs</p>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-3 p-md-4 text-center">
                    <i class="bi bi-wrench text-primary fs-3 mb-2"></i>
                    <h6 class="text-muted mb-1">Total Services</h6>
                    <h3 id="total-services" class="fw-bold text-dark mb-0">{{ $totalServices }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-3 p-md-4 text-center">
                    <i class="bi bi-hourglass-split text-warning fs-3 mb-2"></i>
                    <h6 class="text-muted mb-1">Pending</h6>
                    <h3 id="pending-services" class="fw-bold text-dark mb-0">{{ $totalPendingServices }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-3 p-md-4 text-center">
                    <i class="bi bi-check-circle-fill text-success fs-3 mb-2"></i>
                    <h6 class="text-muted mb-1">Completed</h6>
                    <h3 id="completed-services" class="fw-bold text-dark mb-0">{{ $totalCompletedServices }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-3 p-md-4 text-center">
                    <i class="bi bi-x-circle-fill text-danger fs-3 mb-2"></i>
                    <h6 class="text-muted mb-1">Cancelled</h6>
                    <h3 id="cancelled-services" class="fw-bold text-dark mb-0">{{ $totalCancelledServices }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Table -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-light fw-semibold d-flex justify-content-between align-items-center flex-column flex-sm-row gap-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-list-check"></i> Service Records
            </div>
            <input 
                type="text" 
                id="search-box" 
                class="form-control w-100 w-sm-25" 
                placeholder="Search by Job ID, Vehicle, Status"
                autocomplete="off"
            >
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Job ID</th>
                            <th>Vehicle</th>
                            <th>Service Date</th>
                            <th>Status</th>
                            <th>Amount (₹)</th>
                            <th class="text-center pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="service-tbody">
                        @forelse ($services as $service)
                            <tr>
                                <td class="ps-4 fw-medium">{{ $service->id }}</td>
                                <td>{{ $service->job_id }}</td>
                                <td>
                                    <i class="bi bi-truck text-secondary me-2"></i>
                                    {{ $service->vehicle->registration_no ?? 'N/A' }}
                                </td>
                                <td>
                                    {{ $service->service_start_datetime 
                                        ? \Carbon\Carbon::parse($service->service_start_datetime)->format('d-m-Y') 
                                        : '-' }}
                                </td>
                                <td>
                                    @php
                                        $badge = match($service->status){
                                            'completed' => 'bg-success',
                                            'pending'   => 'bg-warning text-dark',
                                            'cancelled' => 'bg-danger',
                                            default     => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badge }} text-capitalize">{{ $service->status }}</span>
                                </td>
                                <td>{{ number_format($service->amount, 2) }}</td>
                                <td class="text-center pe-4">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.services.view', $service->id) }}"
                                           class="btn btn-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.services.print', $service->id) }}" 
                                           class="btn btn-secondary" title="Print" target="_blank">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    No service records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchBox = document.getElementById('search-box');
    const tbody = document.getElementById('service-tbody');
    let timer = null;

    searchBox.addEventListener('keyup', function () {
        clearTimeout(timer);
        timer = setTimeout(() => {
            const query = encodeURIComponent(this.value.trim());
            fetch(`{{ route('admin.services.search') }}?search=${query}`)
                .then(res => res.json())
                .then(data => {
                    tbody.innerHTML = '';
                    if (!data.services || data.services.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    No results found.
                                </td>
                            </tr>`;
                        return;
                    }

                    data.services.forEach(s => {
                        const date = s.service_start_datetime
                            ? new Date(s.service_start_datetime).toLocaleDateString('en-GB')
                            : '-';

                        const badge = {
                            completed: 'bg-success',
                            pending: 'bg-warning text-dark',
                            cancelled: 'bg-danger'
                        }[s.status] || 'bg-secondary';

                        tbody.innerHTML += `
                            <tr>
                                <td class="ps-4 fw-medium">${s.id}</td>
                                <td>${s.job_id}</td>
                                <td><i class="bi bi-truck text-secondary me-2"></i> ${s.vehicle?.registration_no ?? 'N/A'}</td>
                                <td>${date}</td>
                                <td><span class="badge ${badge} text-capitalize">${s.status}</span></td>
                                <td>${Number(s.amount).toFixed(2)}</td>
                                <td class="text-center pe-4">
                                    <div class="btn-group btn-group-sm">
                                        <a href="/admin/services/${s.id}" class="btn btn-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="/admin/services/print/${s.id}" class="btn btn-secondary" title="Print" target="_blank">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>`;
                    });

                    // Update metrics
                    document.getElementById('total-services').textContent = data.total ?? 0;
                    document.getElementById('pending-services').textContent = data.pending ?? 0;
                    document.getElementById('completed-services').textContent = data.completed ?? 0;
                    document.getElementById('cancelled-services').textContent = data.cancelled ?? 0;
                })
                .catch(() => {
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-5">Error loading data.</td></tr>`;
                });
        }, 400);
    });
});
</script>
@endsection