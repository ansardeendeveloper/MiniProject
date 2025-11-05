@extends('layouts.admintemplates')

@section('title', 'Service Details - ' . $service->job_id)

@section('head')
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">

    <!-- Breadcrumb & Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.services') }}" class="text-decoration-none">
                        <i class="bi bi-tools me-1"></i> Services
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    {{ $service->job_id }}
                </li>
            </ol>
        </nav>
        {{-- <a href="{{ route('admin.services') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span class="d-none d-sm-inline">Back</span>
        </a> --}}
    </div>

    <!-- Service Details Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-primary text-white d-flex align-items-center gap-2">
            <i class="bi bi-gear-fill"></i>
            <h4 class="mb-0">Service Details</h4>
        </div>

        <div class="card-body p-4 p-md-5">
            <div class="row g-4">

                <!-- Job Information -->
                <div class="col-lg-4">
                    <div class="bg-light rounded-3 p-4 h-100">
                        <h5 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle"></i> Job Information
                        </h5>
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th class="text-muted pe-3" style="width: 120px;">Job ID</th>
                                    <td class="fw-semibold">{{ $service->job_id }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted pe-3">Status</th>
                                    <td>
                                        @php
                                            $statusBadge = match($service->status){
                                                'completed' => 'bg-success',
                                                'pending'   => 'bg-warning text-dark',
                                                'cancelled' => 'bg-danger',
                                                default     => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $statusBadge }} text-capitalize">{{ $service->status }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted pe-3">Start Date</th>
                                    <td>
                                        {{ $service->service_start_datetime
                                            ? \Carbon\Carbon::parse($service->service_start_datetime)
                                                ->setTimezone('Asia/Kolkata')
                                                ->format('d-m-Y')
                                            : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted pe-3">Start Time</th>
                                    <td>
                                        {{ $service->service_start_datetime
                                            ? \Carbon\Carbon::parse($service->service_start_datetime)
                                                ->setTimezone('Asia/Kolkata')
                                                ->format('h:i A')
                                            : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted pe-3">End Time</th>
                                    <td>
                                        {{ $service->service_end_datetime
                                            ? \Carbon\Carbon::parse($service->service_end_datetime)
                                                ->setTimezone('Asia/Kolkata')
                                                ->format('h:i A')
                                            : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted pe-3">Amount</th>
                                    <td class="fw-bold text-success">₹{{ number_format($service->amount, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Vehicle Information -->
                <div class="col-lg-4">
                    <div class="bg-light rounded-3 p-4 h-100">
                        <h5 class="fw-bold text-secondary mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-truck"></i> Vehicle Details
                        </h5>
                        @if($service->vehicle)
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                                     style="width:56px;height:56px;">
                                    <i class="bi bi-truck text-secondary" style="font-size:1.75rem;"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $service->vehicle->registration_no }}</div>
                                    <div class="text-muted small">{{ $service->vehicle->model }}</div>
                                </div>
                            </div>
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <th class="text-muted pe-3" style="width: 120px;">Reg No</th>
                                        <td>{{ $service->vehicle->registration_no }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted pe-3">Model</th>
                                        <td>{{ $service->vehicle->model }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted pe-3">Year</th>
                                        <td>{{ $service->vehicle->year ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted pe-3">Owner</th>
                                        <td>{{ $service->vehicle->customer->name ?? 'N/A' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            <p class="text-muted mb-0">
                                <i class="bi bi-exclamation-triangle me-1"></i> No vehicle assigned.
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Staff Information -->
                <div class="col-lg-4">
                    <div class="bg-light rounded-3 p-4 h-100">
                        <h5 class="fw-bold text-info mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-person-gear"></i> Staff Details
                        </h5>
                        @if($service->staff)
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                                     style="width:56px;height:56px;">
                                    <i class="bi bi-person text-info" style="font-size:1.75rem;"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $service->staff->name }}</div>
                                    <div class="text-muted small">{{ $service->staff->role ?? 'Technician' }}</div>
                                </div>
                            </div>
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <th class="text-muted pe-3" style="width: 120px;">Name</th>
                                        <td>{{ $service->staff->name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted pe-3">Email</th>
                                        <td><a href="mailto:{{ $service->staff->email }}" class="text-decoration-none">{{ $service->staff->email }}</a></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted pe-3">Phone</th>
                                        <td><a href="tel:{{ $service->staff->phone }}" class="text-decoration-none">{{ $service->staff->phone }}</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            <p class="text-muted mb-0">
                                <i class="bi bi-exclamation-triangle me-1"></i> No staff assigned.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection