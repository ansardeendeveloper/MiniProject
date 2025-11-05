@extends('layouts.ownertemplates')

@section('title', 'My Profile')

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-0">My Profile</h1>
            <p class="text-muted small mb-0">Manage your account information</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Profile Information -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Profile Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('owner.profile.update') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name *</label>
                                <input type="text" class="form-control" name="name" 
                                       value="{{ old('name', $owner->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address *</label>
                                <input type="email" class="form-control" value="{{ $owner->email }}" disabled>
                                <small class="text-muted">Email cannot be changed</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control" name="phone" 
                                       value="{{ old('phone', $owner->phone) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vehicle Number *</label>
                                <input type="text" class="form-control" value="{{ $owner->vehicle_number }}" disabled>
                                <small class="text-muted">Vehicle number cannot be changed</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="address" rows="3">{{ old('address', $owner->address) }}</textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i> Update Profile
                                </button>
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Account Summary -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-person-badge me-2"></i>Account Summary</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px;">
                            <i class="bi bi-person-fill text-white" style="font-size: 2rem;"></i>
                        </div>
                        <h5 class="mt-3 mb-1">{{ $owner->name }}</h5>
                        <p class="text-muted small">{{ $owner->email }}</p>
                    </div>
                    
                    <div class="list-group list-group-flush small">
                        <div class="list-group-item d-flex justify-content-between px-0">
                            <span>Member Since</span>
                            <strong>{{ \Carbon\Carbon::parse($owner->created_at)->format('M Y') }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0">
                            <span>Vehicle Number</span>
                            <strong>{{ $owner->vehicle_number }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0">
                            <span>Total Services</span>
                            <strong>{{ $owner->services_count ?? 0 }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0">
                            <span>Completed Services</span>
                            <strong class="text-success">{{ $owner->completed_services_count ?? 0 }}</strong>
                        </div>
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