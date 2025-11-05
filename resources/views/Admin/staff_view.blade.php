@extends('layouts.admintemplates')

@section('title', 'View Staff')

@section('head')
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (lightweight) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-eye"></i> Staff Details
            </h1>
            <p class="text-muted small mb-0">Complete profile information</p>
        </div>
    </div>

    <!-- Staff Card -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-body p-4 p-md-5">

            <div class="row g-4 align-items-start">

                <!-- Profile Image -->
                <div class="col-md-4 col-lg-3 text-center">
                    <div class="position-relative d-inline-block">
                        @if($staff->image)
                            <img 
                                src="{{ asset('storage/staff_images/' . $staff->image) }}" 
                                alt="{{ $staff->name }}"
                                class="img-fluid rounded-circle border border-3 border-white shadow-sm"
                                style="width: 160px; height: 160px; object-fit: cover;"
                            >
                        @else
                            <div class="bg-light border border-2 border-dashed rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm"
                                 style="width: 160px; height: 160px;">
                                <i class="bi bi-person text-muted" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                    </div>

                    <div class="mt-3">
                        <span class="badge bg-success-subtle text-success-emphasis px-3 py-2">
                            <i class="bi bi-check-circle-fill me-1"></i> Active
                        </span>
                    </div>
                </div>

                <!-- Staff Details -->
                <div class="col-md-8 col-lg-9">
                    <div class="row g-3">
                        <div class="col-sm-6 col-lg-4">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <small class="text-muted text-uppercase fw-medium">ID</small>
                                <p class="fw-bold mb-0">#{{ $staff->id }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-8">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <small class="text-muted text-uppercase fw-medium">Full Name</small>
                                <p class="fw-bold mb-0">{{ $staff->name }}</p>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <small class="text-muted text-uppercase fw-medium">Email</small>
                                <p class="mb-0">
                                    <a href="mailto:{{ $staff->email }}" class="text-decoration-none">
                                        {{ $staff->email }}
                                    </a>
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <small class="text-muted text-uppercase fw-medium">Phone</small>
                                <p class="fw-bold mb-0">{{ $staff->phone ?? '—' }}</p>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <small class="text-muted text-uppercase fw-medium">Address</small>
                                <p class="mb-0">{{ $staff->address ?? '—' }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <small class="text-muted text-uppercase fw-medium">Date of Birth</small>
                                <p class="fw-bold mb-0">
                                    {{ $staff->date_of_birth ? \Carbon\Carbon::parse($staff->date_of_birth)->format('d M Y') : '—' }}
                                </p>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <small class="text-muted text-uppercase fw-medium">Age</small>
                                <p class="fw-bold mb-0">{{ $staff->age ?? '—' }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <small class="text-muted text-uppercase fw-medium">Role</small>
                                <p class="mb-0">
                                    <span class="badge bg-primary-subtle text-primary-emphasis">
                                        {{ ucfirst($staff->role ?? 'staff') }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="bg-light rounded-3 p-3">
                                <small class="text-muted text-uppercase fw-medium">Joined On</small>
                                <p class="fw-bold mb-0">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    {{ $staff->created_at->format('d F Y \a\t g:i A') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-4 pt-3 border-top d-flex flex-column flex-sm-row gap-2">
                <a href="{{ route('admin.staff.edit', $staff->id) }}" 
                   class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-pencil"></i> Edit Staff
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection