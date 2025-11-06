@extends('layouts.ownertemplates')

@section('title', 'Service Details')

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-0">Service Details</h1>
            <p class="text-muted small mb-0">Service #{{ $service->id }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('owner.invoice.print', $service->id) }}" class="btn btn-success btn-sm">
                <i class="bi bi-receipt"></i> Print Invoice
            </a>
            <a href="{{ route('owner.services') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Service Information -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Service Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Service Type</label>
                            <p class="fw-semibold mb-0">{{ $service->service_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Status</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ 
                                    $service->status == 'completed' ? 'success' : 
                                    ($service->status == 'cancelled' ? 'danger' : 
                                    ($service->status == 'in_progress' ? 'warning' : 'secondary'))
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $service->status)) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Amount</label>
                            <p class="fw-semibold mb-0 text-primary">₹{{ number_format($service->amount, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Service Date</label>
                            <p class="fw-semibold mb-0">{{ \Carbon\Carbon::parse($service->created_at)->format('d M Y, h:i A') }}</p>
                        </div>
                        {{-- <div class="col-12">
                            <label class="form-label text-muted small mb-1">Description</label>
                            <p class="fw-semibold mb-0">{{ $service->description ?? 'No description provided' }}</p>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>

        <!-- Vehicle & Service Center Info -->
        <div class="col-lg-4">
            <!-- Vehicle Information -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-car-front me-2"></i>Vehicle Information</h6>
                </div>
                <div class="card-body">
                    @if($service->vehicle)
                        <div class="text-center">
                            <i class="bi bi-car-front-fill text-primary" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 mb-1">{{ $service->vehicle->model }}</h6>
                            <p class="text-muted small mb-1">{{ $service->vehicle->registration_number }}</p>
                            <p class="text-muted small mb-0">{{ $service->vehicle->year }} • {{ $service->vehicle->color }}</p>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No vehicle information</p>
                    @endif
                </div>
            </div>

            {{-- <!-- Service Center Information -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-building me-2"></i>Service Center</h6>
                </div>
                <div class="card-body">
                    @if($service->serviceCenter)
                        <h6 class="mb-1">{{ $service->serviceCenter->name }}</h6>
                        <p class="text-muted small mb-1">
                            <i class="bi bi-geo-alt"></i> {{ $service->serviceCenter->address }}
                        </p>
                        <p class="text-muted small mb-1">
                            <i class="bi bi-telephone"></i> {{ $service->serviceCenter->phone ?? 'N/A' }}
                        </p>
                    @else
                        <p class="text-muted text-center mb-0">No service center information</p>
                    @endif
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Service Timeline -->
    <div class="card border-0 shadow-sm rounded-3 mt-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Service Timeline</h5>
        </div>
        <div class="card-body">
            <div class="timeline">
                @php
                    $timeline = [
                        'created' => ['icon' => 'bi-plus-circle', 'color' => 'primary', 'text' => 'Service Created'],
                        'in_progress' => ['icon' => 'bi-gear', 'color' => 'warning', 'text' => 'Service In Progress'],
                        'completed' => ['icon' => 'bi-check-circle', 'color' => 'success', 'text' => 'Service Completed'],
                    ];
                @endphp

                @foreach($timeline as $status => $info)
                    <div class="timeline-item d-flex align-items-center mb-3">
                        <div class="timeline-icon me-3">
                            <i class="bi {{ $info['icon'] }} text-{{ $info['color'] }}" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="timeline-content grow">
                            <h6 class="mb-0">{{ $info['text'] }}</h6>
                            <small class="text-muted">
                                @if($service->status == $status || 
                                    ($status == 'created' && in_array($service->status, ['pending', 'in_progress', 'completed', 'cancelled'])) ||
                                    ($status == 'in_progress' && in_array($service->status, ['in_progress', 'completed', 'cancelled'])) ||
                                    ($status == 'completed' && $service->status == 'completed'))
                                    Completed
                                @else
                                    Pending
                                @endif
                            </small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection