@extends('layouts.ownertemplates')

@section('title', 'My Profile')

@section('head')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background-color: #f8fafc; }
    .card { border-radius: 16px; }
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.15rem rgba(13,110,253,.25);
    }
    .avatar {
        width: 100px; height: 100px;
        border-radius: 50%;
        background: #0d6efd;
        display: flex; align-items: center; justify-content: center;
        font-size: 2.5rem; color: white;
        margin: 0 auto;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-0"><i class="bi bi-person-circle me-2"></i>My Profile</h1>
            <p class="text-muted small mb-0">Manage your account information and security</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Profile Information -->
        <div class="col-lg-8">
            <!-- Profile Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="bi bi-person-badge me-2"></i>
                    <h5 class="mb-0">Profile Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('owner.profile.update') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Full Name *</label>
                                <input type="text" class="form-control" name="name" 
                                       value="{{ old('name', $owner->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email *</label>
                                <input type="email" class="form-control" value="{{ $owner->email }}" disabled>
                                <small class="text-muted">Email cannot be changed</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone *</label>
                                <input type="tel" class="form-control" name="phone" 
                                       value="{{ old('phone', $owner->phone) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Vehicle Number *</label>
                                <input type="text" class="form-control" value="{{ $owner->vehicle_number }}" disabled>
                                <small class="text-muted">Vehicle number cannot be changed</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Address</label>
                                <textarea class="form-control" name="address" rows="3">{{ old('address', $owner->address) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-check-circle me-1"></i>Update Profile
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise me-1"></i>Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark d-flex align-items-center">
                    <i class="bi bi-shield-lock me-2"></i>
                    <h5 class="mb-0">Change Password</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('owner.password.update') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Current Password *</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">New Password *</label>
                                <input type="password" class="form-control" name="new_password" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirm Password *</label>
                                <input type="password" class="form-control" name="new_password_confirmation" required>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-warning text-dark">
                                <i class="bi bi-key me-1"></i>Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Account Summary -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-dark text-white">
                    <i class="bi bi-person-lines-fill me-2"></i>Account Summary
                </div>
                <div class="card-body text-center">
                    <div class="avatar mb-3">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h5 class="mb-0">{{ $owner->name }}</h5>
                    <p class="text-muted small">{{ $owner->email }}</p>

                    <ul class="list-group list-group-flush text-start small mt-3">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>Member Since</span>
                            <strong>{{ \Carbon\Carbon::parse($owner->created_at)->format('M Y') }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>Vehicle Number</span>
                            <strong>{{ $owner->vehicle_number }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>Total Services</span>
                            <strong>{{ $owner->services_count ?? 0 }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>Completed</span>
                            <strong class="text-success">{{ $owner->completed_services_count ?? 0 }}</strong>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm">
                {{-- <div class="card-header bg-info text-white">
                    <i class="bi bi-lightning-charge me-2"></i>Quick Actions
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('owner.dashboard') }}" class="btn btn-outline-dark btn-sm">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                    <a href="{{ route('owner.services') }}" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-tools me-1"></i> Services
                    </a>
                    <a href="{{ route('owner.vehicles') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-car-front me-1"></i> Vehicles
                    </a>
                    <a href="{{ route('owner.invoices') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-receipt me-1"></i> Invoices
                    </a>
                </div>
            </div>
        </div> --}}
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const requiredFields = this.querySelectorAll('input[required]');
        let valid = true;
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                valid = false;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });
        if (!valid) {
            e.preventDefault();
            alert('⚠️ Please fill all required fields.');
        }
    });
});

// Password confirmation check
const passwordForm = document.querySelector('form[action*="password.update"]');
if (passwordForm) {
    passwordForm.addEventListener('submit', function(e) {
        const newPassword = this.querySelector('input[name="new_password"]');
        const confirmPassword = this.querySelector('input[name="new_password_confirmation"]');
        if (newPassword.value !== confirmPassword.value) {
            e.preventDefault();
            confirmPassword.classList.add('is-invalid');
            alert('❌ New password and confirmation do not match.');
        }
    });
}
</script>
@endsection
