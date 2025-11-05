@extends('layouts.admintemplates')

@section('title', 'Owner Details - ' . ($owner->name ?? 'N/A'))

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">
    
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-0">Owner Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.owners') }}">Vehicle Owners</a></li>
                    <li class="breadcrumb-item active">{{ $owner->name ?? 'Owner' }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.owners') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Owners
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Owner Information -->
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-user me-2 text-primary"></i>Owner Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-user text-white" style="font-size: 2rem;"></i>
                        </div>
                        <h4 class="mt-3 mb-1">{{ $owner->name ?? 'N/A' }}</h4>
                        <p class="text-muted mb-0">Owner ID: {{ $owner->id }}</p>
                    </div>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <div>
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <strong>Email</strong>
                            </div>
                            <span>{{ $owner->email ?? 'N/A' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <div>
                                <i class="fas fa-phone text-success me-2"></i>
                                <strong>Phone</strong>
                            </div>
                            <span>{{ $owner->phone ?? 'N/A' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <div>
                                <i class="fas fa-car text-info me-2"></i>
                                <strong>Vehicle Number</strong>
                            </div>
                            <span>{{ $owner->vehicle_number ?? 'N/A' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <div>
                                <i class="fas fa-calendar text-info me-2"></i>
                                <strong>Registered</strong>
                            </div>
                            <span>{{ $owner->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>

                    @if($owner->address)
                    <div class="mt-3">
                        <h6 class="fw-bold mb-2">
                            <i class="fas fa-map-marker-alt text-danger me-2"></i>Address
                        </h6>
                        <p class="text-muted mb-0">{{ $owner->address }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Vehicle Information -->
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-car me-2 text-success"></i>Vehicle Information
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $vehicle = \App\Models\Vehicle::where('registration_no', $owner->vehicle_number)->first();
                    @endphp
                    @if($vehicle)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Vehicle No</th>
                                        <th>Model</th>
                                        <th>Manufacturer</th>
                                        <th>Year</th>
                                        <th>Services</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <strong>{{ $vehicle->registration_no }}</strong>
                                        </td>
                                        <td>{{ $vehicle->model ?? 'N/A' }}</td>
                                        <td>{{ $vehicle->manufacturer ?? 'N/A' }}</td>
                                        <td>{{ $vehicle->year ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $vehicle->services_count ?? 0 }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">Active</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Recent Services -->
                        <div class="mt-4">
                            <h6 class="fw-bold mb-3">
                                <i class="fas fa-tools me-2 text-warning"></i>Recent Services
                            </h6>
                            @php
                                $recentServices = \App\Models\ServiceRecord::where('vehicle_id', $vehicle->id)
                                    ->with('vehicle')
                                    ->latest()
                                    ->take(5)
                                    ->get();
                            @endphp
                            
                            @if($recentServices->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Job ID</th>
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
                                                    <td>
                                                        @php
                                                            $serviceTypes = json_decode($service->service_types, true) ?: [];
                                                        @endphp
                                                        {{ implode(', ', array_slice($serviceTypes, 0, 2)) }}
                                                        @if(count($serviceTypes) > 2)
                                                            <span class="text-muted">+{{ count($serviceTypes) - 2 }} more</span>
                                                        @endif
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
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-tools fa-2x mb-3 d-block"></i>
                                    <p>No service history found for this vehicle.</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-car fa-3x mb-3 d-block"></i>
                            <h5>No Vehicle Registered</h5>
                            <p>This owner hasn't registered their vehicle yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection