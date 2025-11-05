@extends('layouts.admintemplates')

@section('title', 'Vehicle - ' . $vehicle->registration_no)

@section('content')
<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-primary text-white d-flex align-items-center gap-2">
            <i class="bi bi-truck"></i>
            <h4 class="mb-0">Vehicle Details</h4>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6"><strong>ID:</strong> #{{ $vehicle->id }}</div>
                <div class="col-md-6"><strong>Registration:</strong> {{ $vehicle->registration_no }}</div>
                <div class="col-md-6"><strong>Model:</strong> {{ $vehicle->model }}</div>
                <div class="col-md-6"><strong>Make:</strong> {{ $vehicle->manufacturer }}</div>
                <div class="col-md-6"><strong>Year:</strong> {{ $vehicle->year ?? 'N/A' }}</div>
                <div class="col-md-6"><strong>Owner:</strong> {{ $vehicle->customer->name ?? 'N/A' }}</div>
                <div class="col-md-6">
                    <strong>Mobile:</strong>
                    @if($vehicle->customer && $vehicle->customer->mobile_number)
                        <a href="tel:{{ $vehicle->customer->mobile_number }}" class="text-decoration-none">
                            {{ $vehicle->customer->mobile_number }}
                        </a>
                    @else
                        <span class="text-muted">N/A</span>
                    @endif
                </div>
            </div>
            <hr class="my-4">
        </div>
    </div>
</div>
@endsection

@section('head')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection