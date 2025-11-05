@extends('layouts.admintemplates')

@section('title', 'Edit Staff')

@section('head')
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square"></i> Edit Staff
            </h1>
            <p class="text-muted small mb-0">Update staff member details</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4 p-md-5">

            <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')

                <!-- Name & Email -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input 
                                type="text" 
                                id="name"
                                name="name" 
                                class="form-control @error('name') is-invalid @enderror" 
                                value="{{ old('name', $staff->name) }}" 
                                required
                                placeholder="Enter full name"
                            >
                        </div>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label fw-medium">Email Address <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input 
                                type="email" 
                                id="email"
                                name="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                value="{{ old('email', $staff->email) }}" 
                                required
                                placeholder="name@example.com"
                            >
                        </div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Phone & Role -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-medium">Phone Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-phone"></i></span>
                            <input 
                                type="text" 
                                id="phone"
                                name="phone" 
                                class="form-control @error('phone') is-invalid @enderror" 
                                value="{{ old('phone', $staff->phone) }}" 
                                required
                                placeholder="+91 98765 43210"
                            >
                        </div>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="role" class="form-label fw-medium">Role <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                            <select 
                                id="role"
                                name="role" 
                                class="form-select @error('role') is-invalid @enderror" 
                                required
                            >
                                <option value="">Select role</option>
                                <option value="staff" {{ old('role', $staff->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="technician" {{ old('role', $staff->role) == 'technician' ? 'selected' : '' }}>Technician</option>
                                <option value="supervisor" {{ old('role', $staff->role) == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                            </select>
                        </div>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Address -->
                <div class="mb-3">
                    <label for="address" class="form-label fw-medium">Address <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                        <textarea 
                            id="address"
                            name="address" 
                            class="form-control @error('address') is-invalid @enderror" 
                            rows="3" 
                            required
                            placeholder="Enter full address"
                        >{{ old('address', $staff->address) }}</textarea>
                    </div>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- DOB & Image -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="date_of_birth" class="form-label fw-medium">Date of Birth <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                            <input 
                                type="date" 
                                id="date_of_birth"
                                name="date_of_birth" 
                                class="form-control @error('date_of_birth') is-invalid @enderror" 
                                value="{{ old('date_of_birth', $staff->date_of_birth) }}" 
                                required
                            >
                        </div>
                        @error('date_of_birth')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Profile Image -->
                    <div class="col-md-6">
                        <label for="image" class="form-label fw-medium">Profile Image</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-image"></i></span>
                            <input 
                                type="file" 
                                id="image"
                                name="image" 
                                class="form-control @error('image') is-invalid @enderror" 
                                accept=".jpg,.jpeg,.png,.webp"
                            >
                        </div>

                        @if($staff->image)
                            <div class="mt-2">
                                <small class="text-muted">Current:</small><br>
                                <img 
                                    src="{{ asset('storage/staff_images/' . $staff->image) }}" 
                                    alt="Current profile" 
                                    class="rounded shadow-sm" 
                                    style="width: 80px; height: 80px; object-fit: cover;"
                                >
                            </div>
                        @endif

                        @error('image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Buttons -->
                <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                    <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-check-circle"></i> Update Staff
                    </button>
                    <a href="{{ route('admin.staff') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
(() => {
    'use strict';

    // Bootstrap validation
    const forms = document.querySelectorAll('form[novalidate]');
    forms.forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Image file validation
    const imageInput = document.getElementById('image');
    if (imageInput) {
        imageInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('❌ Invalid file type! Please upload only JPG, JPEG, PNG, or WEBP images.');
                this.value = '';
                return;
            }

            const maxSize = 2 * 1024 * 1024; // 2MB limit
            if (file.size > maxSize) {
                alert('⚠️ File too large! Maximum allowed size is 2MB.');
                this.value = '';
                return;
            }
        });
    }
})();
</script>
@endsection
