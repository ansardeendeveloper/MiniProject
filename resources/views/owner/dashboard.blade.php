@extends('layouts.ownertemplates')

@section('title', 'Owner Dashboard')

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .card-hover:hover {
            transform: translateY(-2px);
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .stat-icon {
            font-size: 1.75rem;
            opacity: 0.8;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-0">Dashboard</h1>
            <p class="text-muted small mb-0">Welcome back, {{ $owner->name }}!</p>
            @if($vehicle)
                <small class="text-muted">Vehicle: {{ $vehicle->registration_no }} • {{ $vehicle->model }}</small>
            @endif
        </div>
        <div class="text-end">
            <small class="text-muted d-block">{{ now()->format('D, M j, Y') }}</small>
            <small class="text-muted">{{ now()->format('g:i A') }}</small>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 g-md-4 mb-5">
        <!-- Total Services -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 card-hover bg-primary bg-opacity-10">
                <div class="card-body p-3 p-md-4 text-center">
                    <i class="bi bi-tools stat-icon text-primary mb-2"></i>
                    <h6 class="mb-1 text-muted">Total Services</h6>
                    <h3 class="fw-bold mb-0 text-primary">{{ $totalServices }}</h3>
                </div>
            </div>
        </div>

        <!-- Completed Services -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 card-hover bg-success bg-opacity-10">
                <div class="card-body p-3 p-md-4 text-center">
                    <i class="bi bi-check-circle-fill stat-icon text-success mb-2"></i>
                    <h6 class="mb-1 text-muted">Completed</h6>
                    <h3 class="fw-bold mb-0 text-success">{{ $completedServices }}</h3>
                </div>
            </div>
        </div>

        <!-- Cancelled Services -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 card-hover bg-danger bg-opacity-10">
                <div class="card-body p-3 p-md-4 text-center">
                    <i class="bi bi-x-circle-fill stat-icon text-danger mb-2"></i>
                    <h6 class="mb-1 text-muted">Cancelled</h6>
                    <h3 class="fw-bold mb-0 text-danger">{{ $cancelledServices }}</h3>
                </div>
            </div>
        </div>

        <!-- Total Amount Paid -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 card-hover bg-warning bg-opacity-10">
                <div class="card-body p-3 p-md-4 text-center">
                    <i class="bi bi-wallet2 stat-icon text-warning mb-2"></i>
                    <h6 class="mb-1 text-muted">Total Paid</h6>
                    <h3 class="fw-bold mb-0 text-warning">₹{{ number_format($totalAmountPaid) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Services</h5>
            @if($recentServices->count() > 0)
                <a href="{{ route('owner.services') }}" class="btn btn-sm btn-light">
                    <i class="bi bi-list-ul me-1"></i> View All
                </a>
            @endif
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Job ID</th>
                            <th>Service Types</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentServices as $service)
                            <tr>
                                <td class="ps-4">{{ $loop->iteration }}</td>
                                <td>
                                    <strong class="text-primary">{{ $service->job_id ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    @php
                                        $serviceTypes = json_decode($service->service_types, true) ?: [];
                                    @endphp
                                    @if(!empty($serviceTypes))
                                        <small>{{ implode(', ', array_slice($serviceTypes, 0, 2)) }}</small>
                                        @if(count($serviceTypes) > 2)
                                            <br><small class="text-muted">+{{ count($serviceTypes) - 2 }} more</small>
                                        @endif
                                    @else
                                        <small class="text-muted">No services specified</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $service->status == 'completed' ? 'success' : 
                                        ($service->status == 'cancelled' ? 'danger' : 
                                        ($service->status == 'in_progress' ? 'info' : 'warning'))
                                    }}">
                                        {{ ucfirst($service->status) }}
                                    </span>
                                </td>
                                <td>
                                    <strong>₹{{ number_format($service->amount, 2) }}</strong>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($service->created_at)->format('d M Y') }}
                                    <br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($service->created_at)->format('g:i A') }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('owner.services.show', $service->id) }}" 
                                           class="btn btn-outline-primary" 
                                           title="View Details"
                                           data-bs-toggle="tooltip">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('owner.invoice.print', $service->id) }}" 
                                           class="btn btn-outline-success"
                                           title="Print Invoice"
                                           data-bs-toggle="tooltip">
                                            <i class="bi bi-printer-fill"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-info-circle me-1"></i> No services found for your vehicle.
                                    @if(!$vehicle)
                                        <br><small class="mt-2">Please register your vehicle first.</small>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @if($vehicle)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 bg-light rounded-3">
                <div class="card-body py-3">
                    <div class="row text-center">
                        <div class="col-4 border-end">
                            <small class="text-muted d-block">Vehicle Model</small>
                            <strong class="text-dark">{{ $vehicle->model }}</strong>
                        </div>
                        <div class="col-4 border-end">
                            <small class="text-muted d-block">Manufacturer</small>
                            <strong class="text-dark">{{ $vehicle->manufacturer }}</strong>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">Vehicle Year</small>
                            <strong class="text-dark">{{ $vehicle->year }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection  

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
@endsection