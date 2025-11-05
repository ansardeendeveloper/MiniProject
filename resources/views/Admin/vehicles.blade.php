@extends('layouts.admintemplates')

@section('title', 'Vehicles Overview')

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
                <i class="bi bi-truck"></i> Vehicles Overview
            </h1>
            <p class="text-muted small mb-0">Manage and track all registered vehicles</p>
        </div>
    </div>

    <!-- Total Vehicles Card -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 rounded-3 bg-white">
                <div class="card-body p-3 p-md-4 text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;">
                        <i class="bi bi-truck text-primary" style="font-size:2rem;"></i>
                    </div>
                    <h6 class="text-muted mb-1">Total Vehicles</h6>
                    <h3 class="fw-bold text-dark mb-0">{{ $totalVehicles ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicle List Card -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-light fw-semibold d-flex justify-content-between align-items-center flex-column flex-sm-row gap-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-garage"></i>
                Vehicle List
            </div>

            <!-- Search Form -->
            <form action="{{ route('admin.vehicles') }}" method="GET" class="d-flex gap-2 w-100 w-sm-auto">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    class="form-control" 
                    placeholder="Reg no or customer"
                >
                <button type="submit" class="btn btn-outline-primary d-flex align-items-center gap-2">
                    <i class="bi bi-search"></i>
                    <span class="d-none d-sm-inline">Search</span>
                </button>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th><i class="bi bi-pin-angle me-1"></i> Vehicle Number</th>
                            <th><i class="bi bi-car-front me-1"></i> Model</th>
                            <th><i class="bi bi-person me-1"></i> Customer</th>
                            <th class="text-center pe-4"><i class="bi bi-gear me-1"></i> Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vehicles ?? [] as $vehicle)
                            <tr>
                                <td class="ps-4 fw-medium">{{ $vehicle->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                            <i class="bi bi-truck text-secondary"></i>
                                        </div>
                                        <span class="fw-semibold">{{ $vehicle->registration_no }}</span>
                                    </div>
                                </td>
                                <td>{{ $vehicle->model }}</td>
                                <td>
                                    @if($vehicle->customer)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                                <i class="bi bi-person text-primary"></i>
                                            </div>
                                            <span>{{ $vehicle->customer->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <a href="{{ route('admin.vehicles.view', $vehicle->id) }}" 
                                       class="btn btn-primary btn-sm d-inline-flex align-items-center justify-content-center" 
                                       style="width:36px;height:36px;" 
                                       title="View Vehicle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox display-6 d-block mb-3"></i>
                                    No vehicles available.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if(method_exists($vehicles, 'links'))
            <div class="card-footer bg-white border-0 py-3">
                {{ $vehicles->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection