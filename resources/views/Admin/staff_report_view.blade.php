@extends('layouts.admintemplates')

@section('title', 'Staff Report - {{ $staff->name }}')

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
                <i class="bi bi-clipboard-data"></i>
                Report for {{ $staff->name }}
            </h1>
            <p class="text-muted small mb-0">Performance and service summary</p>
        </div>
    </div>

    <!-- Filter Form -->
    <form method="GET" class="mb-4">
        <div class="d-flex flex-column flex-sm-row gap-2 align-items-start align-items-sm-center">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-calendar3 text-primary"></i>
                <select name="period" class="form-select w-auto">
                    <option value="day" {{ $period == 'day' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ $period == 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ $period == 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="year" {{ $period == 'year' ? 'selected' : '' }}>This Year</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="bi bi-funnel"></i>
                <span class="d-none d-sm-inline">Filter</span>
            </button>
        </div>
    </form>

    <!-- Metric Cards -->
    <div class="row g-3 g-md-4 mb-4">

        <!-- Total Services -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 rounded-3 bg-white">
                <div class="card-body p-3 p-md-4 text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;">
                        <i class="bi bi-list-check text-primary" style="font-size:1.5rem;"></i>
                    </div>
                    <h6 class="text-muted mb-1">Total Services</h6>
                    <h3 class="fw-bold text-dark mb-0">{{ $services->count() }}</h3>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 rounded-3 bg-white">
                <div class="card-body p-3 p-md-4 text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;">
                        <i class="bi bi-check-circle-fill text-success" style="font-size:1.5rem;"></i>
                    </div>
                    <h6 class="text-muted mb-1">Completed</h6>
                    <h3 class="fw-bold text-dark mb-0">{{ $completed }}</h3>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 rounded-3 bg-white">
                <div class="card-body p-3 p-md-4 text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;">
                        <i class="bi bi-hourglass-split text-warning" style="font-size:1.5rem;"></i>
                    </div>
                    <h6 class="text-muted mb-1">Pending</h6>
                    <h3 class="fw-bold text-dark mb-0">{{ $pending }}</h3>
                </div>
            </div>
        </div>

        <!-- Total Amount -->
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 rounded-3 bg-white">
                <div class="card-body p-3 p-md-4 text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;">
                        <i class="bi bi-currency-rupee text-info" style="font-size:1.5rem;"></i>
                    </div>
                    <h6 class="text-muted mb-1">Total Amount</h6>
                    <h3 class="fw-bold text-dark mb-0">₹{{ number_format($totalAmount, 2) }}</h3>
                </div>
            </div>
        </div>

    </div>

    <!-- Service Records Table -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-light fw-semibold d-flex align-items-center gap-2">
            <i class="bi bi-table"></i> Service Records
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4"><i class="bi bi-hash me-1"></i> Job ID</th>
                            <th><i class="bi bi-truck me-1"></i> Vehicle</th>
                            <th><i class="bi bi-info-circle me-1"></i> Status</th>
                            <th><i class="bi bi-currency-rupee me-1"></i> Amount</th>
                            <th><i class="bi bi-calendar-event me-1"></i> Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($services as $service)
                            <tr>
                                <td class="ps-4 fw-medium">{{ $service->job_id }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                            <i class="bi bi-truck text-secondary"></i>
                                        </div>
                                        <span>{{ $service->vehicle->registration_no ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $status = $service->status;
                                        $badge = match($status) {
                                            'completed' => 'bg-success',
                                            'pending' => 'bg-warning text-dark',
                                            default => 'bg-danger'
                                        };
                                    @endphp
                                    <span class="badge {{ $badge }} text-capitalize">{{ $status }}</span>
                                </td>
                                <td>₹{{ number_format($service->amount, 2) }}</td>
                                <td>{{ $service->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox display-6 d-block mb-3"></i>
                                    No services found for this period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection