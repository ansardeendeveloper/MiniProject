@extends('layouts.admintemplates')
@section('title', 'Vehicle Owners')
@section('head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
@endsection
@section('content')

<div class="container-fluid py-4 px-3 px-md-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-0">Vehicle Owners</h1>
            <p class="text-muted small mb-0">Manage and monitor registered vehicle owners</p>
        </div>
        
        <div class="input-group" style="max-width: 320px;">
            <input type="text" id="searchInput" class="form-control form-control-sm shadow-sm border-0"
                   placeholder="Search owners..." value="{{ request('search') }}">
            <button class="btn btn-sm btn-primary px-3 shadow-sm" id="searchBtn">
                    <i class="bi bi-search"></i>
            </button>
        </div>
    </div>


        <div class="row g-3 g-md-4 mb-4">
        <div class="col-6 col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 bg-primary-subtle text-primary-emphasis">
                <div class="card-body p-3 p-md-4 text-center">
                    <i class="bi bi-people-fill mb-2" style="font-size:1.75rem;"></i>
                    <h6 class="mb-1">Total Owners</h6>
                    <h3 class="fw-bold mb-0">{{ $totalOwners ?? 0 }}</h3>
                </div>
            </div>
        </div>



        <div class="col-6 col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 bg-warning-subtle text-warning-emphasis">
                <div class="card-body p-3 p-md-4 text-center">
                    <i class="bi bi-tools mb-2" style="font-size:1.75rem;"></i>
                    <h6 class="mb-1">Total Services</h6>
                    <h3 class="fw-bold mb-0">{{ $totalServices ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 bg-success-subtle text-success-emphasis">
                <div class="card-body p-3 p-md-4 text-center">
                    <i class="bi bi-person-check-fill mb-2" style="font-size:1.75rem;"></i>
                    <h6 class="mb-1">Active Owners</h6>
                    <h3 class="fw-bold mb-0">{{ $totalOwners ?? 0 }}</h3>
                </div>
            </div>
        </div>
        </div>
    
    
        <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold">Registered Owners</h5>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">#</th>
                            <th class="border-0">Owner</th>
                            <th class="border-0">Contact</th>
                            <th class="border-0">Vehicle</th>
                            <th class="border-0">Registered</th>
                            <th class="border-0 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($owners as $index => $owner)
                            <tr>
                                <td class="align-middle">
                                    {{ method_exists($owners, 'currentPage') 
                                        ? ($owners->currentPage() - 1) * $owners->perPage() + $index + 1 
                                        : $index + 1 }}
                                </td>

                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary d-flex justify-content-center align-items-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="bi bi-person text-white"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0 fw-semibold">{{ $owner->name ?? 'N/A' }}</h6>
                                            <small class="text-muted">ID: {{ $owner->id }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td class="align-middle">
                                    <div class="small">
                                        <div><i class="bi bi-envelope text-primary me-1"></i>{{ $owner->email ?? 'N/A' }}</div>
                                        <div class="mt-1"><i class="bi bi-telephone text-success me-1"></i>{{ $owner->phone ?? 'N/A' }}</div>
                                    </div>
                                </td>

                                <td class="align-middle">
                                    @php
                                        $vehicle = \App\Models\Vehicle::where('registration_no', $owner->vehicle_number)->first();
                                    @endphp
                                    @if($vehicle)
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-car-front me-1"></i>{{ $vehicle->registration_no }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $vehicle->model }} {{ $vehicle->manufacturer }}</small>
                                    @else
                                        <span class="badge bg-secondary">No vehicle</span>
                                    @endif
                                </td>

                                <td class="align-middle">
                                    <div class="text-muted small">
                                        {{ $owner->created_at->format('d M Y, h:i A') }}
                                    </div>
                                </td>

                                <td class="align-middle text-center">
                                    <a href="{{ route('admin.owners.view', $owner->id) }}" 
                                       class="btn btn-sm btn-outline-primary rounded-circle"
                                       data-bs-toggle="tooltip" title="View Details">
                                       <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-people display-6 d-block mb-3 text-muted"></i>
                                    <h5>No Owners Found</h5>
                                    <p class="mb-0">No vehicle owners have registered yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($owners, 'hasPages') && $owners->hasPages())
                <div class="card-footer bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Showing {{ $owners->firstItem() }} to {{ $owners->lastItem() }} of {{ $owners->total() }} entries
                    </small>
                    {{ $owners->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    [...document.querySelectorAll('[data-bs-toggle="tooltip"]')].map(el => new bootstrap.Tooltip(el));
    const input = document.getElementById('searchInput');
    const btn = document.getElementById('searchBtn');

    btn.addEventListener('click', search);
    input.addEventListener('keypress', e => e.key === 'Enter' && search());

    function search() {
        const term = input.value.trim();
        window.location.href = term
            ? `{{ route('admin.owners') }}?search=${encodeURIComponent(term)}`
            : `{{ route('admin.owners') }}`;
    }
});
</script>
@endsection