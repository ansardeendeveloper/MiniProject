@extends('layouts.stafftemplates')
@section('title', 'Staff Dashboard')

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-0">Dashboard</h1>
            <p class="text-muted small mb-0">Welcome back, {{ session('staff_name') ?? 'Staff' }}!</p>
        </div>
        <a href="{{ route('staff.services.create') }}" class="btn btn-success d-flex align-items-center gap-1">
            <i class="bi bi-plus-circle"></i> Add New Service
        </a>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 g-md-4">
        <div class="col-6 col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 bg-warning-subtle text-warning-emphasis text-center">
                <div class="card-body p-3 p-md-4">
                    <i class="bi bi-hourglass-split mb-2" style="font-size:1.75rem;"></i>
                    <h6 class="mb-1">Pending Services</h6>
                    <h3 class="fw-bold mb-0">{{ $pendingServices ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 bg-success-subtle text-success-emphasis text-center">
                <div class="card-body p-3 p-md-4">
                    <i class="bi bi-check-circle-fill mb-2" style="font-size:1.75rem;"></i>
                    <h6 class="mb-1">Completed Services</h6>
                    <h3 class="fw-bold mb-0">{{ $completedServices ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 bg-danger-subtle text-danger-emphasis text-center">
                <div class="card-body p-3 p-md-4">
                    <i class="bi bi-x-circle mb-2" style="font-size:1.75rem;"></i>
                    <h6 class="mb-1">Cancelled Services</h6>
                    <h3 class="fw-bold mb-0">{{ $cancelledServices ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Services -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Recent Services</h5>
                    <a href="{{ route('staff.services.index') }}" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-list-ul"></i> View All
                    </a>
                </div>

                <div class="card-body">
                    @if(empty($recentServices) || $recentServices->isEmpty())
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-inbox display-6 d-block mb-3 text-muted"></i>
                            No recent services found.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Job ID</th>
                                        <th>Vehicle</th>
                                        <th>Date</th>
                                        <th>Service Types</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentServices as $service)
                                        <tr>
                                            <td>{{ $service->job_id ?? 'N/A' }}</td>
                                            <td>{{ $service->vehicle->registration_no ?? 'N/A' }}</td>
                                            <td>
                                                @if($service->service_start_datetime)
                                                    {{ \Carbon\Carbon::parse($service->service_start_datetime)
                                                        ->timezone('Asia/Kolkata')
                                                        ->format('M d, Y H:i') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $types = json_decode($service->service_types, true) ?: [];
                                                @endphp
                                                {{ implode(', ', $types) ?: 'N/A' }}
                                            </td>
                                            <td>₹{{ number_format($service->amount ?? 0, 2) }}</td>
                                            <td>
                                                <span class="badge 
                                                    @if($service->status == 'completed') bg-success
                                                    @elseif($service->status == 'pending') bg-warning
                                                    @elseif($service->status == 'cancelled') bg-danger
                                                    @elseif($service->status == 'assigned') bg-info
                                                    @else bg-secondary @endif">
                                                    {{ ucfirst($service->status ?? 'Unknown') }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('staff.services.show', $service->id) }}" 
                                                   class="btn btn-sm btn-info text-white">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
