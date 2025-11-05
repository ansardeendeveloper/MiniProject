@extends('layouts.admintemplates')

@section('title', 'Staff Management')

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
            <h1 class="h4 fw-bold mb-1">
                <i class="bi bi-people-fill me-2"></i> Staff Management
            </h1>
            <p class="text-muted small mb-0">Manage your team members</p>
        </div>
        <a href="{{ route('admin.staff.register') }}" class="btn btn-success d-flex align-items-center gap-2">
            <i class="bi bi-person-plus-fill"></i> Add Staff
        </a>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search & Table Card -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-body p-0">

            <!-- Search Bar -->
            <div class="p-3 p-md-4 border-bottom bg-light">
                <form method="GET" class="d-flex flex-column flex-sm-row gap-2">
                    <input 
                        type="text" 
                        name="search" 
                        class="form-control flex-grow-1" 
                        placeholder="Search by name or email"
                        value="{{ request('search') }}"
                    >
                    <button type="submit" class="btn btn-outline-primary d-flex align-items-center gap-2">
                        <i class="bi bi-search"></i>
                        <span class="d-none d-sm-inline">Search</span>
                    </button>
                </form>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staff as $member)
                            <tr>
                                <td class="ps-4 fw-medium">{{ $member->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 36px; height: 36px;">
                                            <i class="bi bi-person text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $member->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $member->email }}" class="text-decoration-none text-muted">
                                        {{ $member->email }}
                                    </a>
                                </td>
                                <td>{{ $member->phone ?? '—' }}</td>
                                <td class="text-end pe-4">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.staff.view', $member->id) }}" 
                                           class="btn btn-sm btn-outline-secondary" 
                                           title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.staff.edit', $member->id) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox display-6 d-block mb-3 text-muted"></i>
                                    No staff found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination (if using Laravel Pagination) -->
            @if(method_exists($staff, 'links'))
                <div class="card-footer bg-white border-0 py-3">
                    {{ $staff->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection