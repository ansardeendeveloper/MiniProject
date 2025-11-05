@extends('layouts.admintemplates')

@section('title', 'Admin Dashboard')

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-0">Dashboard</h1>
            <p class="text-muted small mb-0">Welcome back, Admin!</p>
        </div>
        <small class="text-muted d-none d-sm-block">{{ now()->format('D, M j, Y') }}</small>
    </div>

    <div class="row g-3 g-md-4">
                              
        <div class="col-6 col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 bg-primary-subtle text-primary-emphasis">
                <div class="card-body p-3 p-md-4 text-center">
                    <i class="bi bi-people-fill mb-2" style="font-size:1.75rem;"></i>
                    <h6 class="mb-1">Total Staff</h6>
                    <h3 class="fw-bold mb-0">{{ $totalStaff ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 bg-success-subtle text-success-emphasis">
                <div class="card-body p-3 p-md-4 text-center">
                    <i class="bi bi-truck-front mb-2" style="font-size:1.75rem;"></i>
                    <h6 class="mb-1">Total Vehicles</h6>
                    <h3 class="fw-bold mb-0">{{ $totalVehicles ?? 0 }}</h3>
                </div>
            </div>
        </div>

        
        <div class="col-6 col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 bg-info-subtle text-info-emphasis">
                <div class="card-body p-3 p-md-4 text-center">
                    <i class="bi bi-person-badge mb-2" style="font-size:1.75rem;"></i>
                    <h6 class="mb-1">Vehicle Owners</h6>
                    <h3 class="fw-bold mb-0">{{ $totalOwners ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 bg-warning-subtle text-warning-emphasis">
                <div class="card-body p-3 p-md-4 text-center">
                    <i class="bi bi-tools mb-2" style="font-size:1.75rem;"></i>
                    <h6 class="mb-1">Total Services</h6>
                    <h3 class="fw-bold mb-0">{{ $totalServices ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 bg-info-subtle text-info-emphasis">
                <div class="card-body p-3 p-md-4 text-center">
                    <i class="bi bi-check-circle-fill mb-2" style="font-size:1.75rem;"></i>
                    <h6 class="mb-1">Completed</h6>
                    <h3 class="fw-bold mb-0">{{ $totalCompletedServices ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 bg-orange-subtle text-orange-emphasis">
                <div class="card-body p-3 p-md-4 text-center">
                    <i class="bi bi-hourglass-split mb-2" style="font-size:1.75rem;"></i>
                    <h6 class="mb-1">Pending</h6>
                    <h3 class="fw-bold mb-0">{{ $totalPendingServices ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 bg-purple-subtle text-purple-emphasis">
                <div class="card-body p-3 p-md-4 text-center">
                    <i class="bi bi-currency-rupee mb-2" style="font-size:1.75rem;"></i>
                    <h6 class="mb-1">Earnings</h6>
                    <h3 class="fw-bold mb-0">₹{{ number_format($totalAmount ?? 0) }}</h3>
                </div>
            </div>
        </div>

    </div>

    <!-- Recent Services Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Recent Services</h5>
                </div>
                <div class="card-body">
                    @if($recentServices && $recentServices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Job ID</th>
                                        <th>Vehicle</th>
                                        <th>Owner</th>
                                        <th>Service Types</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentServices as $service)
                                        <tr>
                                            <td>{{ $service->job_id }}</td>
                                            <td>{{ $service->vehicle->registration_no ?? 'N/A' }}</td>
                                            <td>{{ $service->vehicle->customer->name ?? 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $serviceTypes = json_decode($service->service_types, true) ?: [];
                                                @endphp
                                                {{ implode(', ', $serviceTypes) }}
                                            </td>
                                            <td>₹{{ number_format($service->amount, 2) }}</td>
                                            <td>
                                                <span class="badge 
                                                    @if($service->status == 'completed') bg-success
                                                    @elseif($service->status == 'pending') bg-warning
                                                    @elseif($service->status == 'cancelled') bg-danger
                                                    @else bg-secondary @endif">
                                                    {{ ucfirst($service->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $service->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
    <i class="bi bi-inbox display-6 d-block mb-3 text-muted"></i>
    No recent services found.
</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection  

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection