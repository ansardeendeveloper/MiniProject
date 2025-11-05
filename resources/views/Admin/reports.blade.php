@extends('layouts.admintemplates')

@section('title', 'Reports Overview')

@section('head')
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 12px;
        }
        .card-header {
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">

    <!-- Page Header -->
    <div class="d-flex align-items-center mb-4">
        <i class="bi bi-bar-chart-fill text-primary fs-3 me-2"></i>
        <h1 class="h4 fw-bold mb-0">Reports Overview</h1>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
            <form method="GET" class="row g-3" id="filter-form">
                <!-- Time Period -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Time Period</label>
                    <select name="period" class="form-select" id="period-select">
                        <option value="day"   {{ request('period') == 'day' ? 'selected' : '' }}>Today</option>
                        <option value="week"  {{ request('period') == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="year"  {{ request('period') == 'year' ? 'selected' : '' }}>This Year</option>
                        <option value="custom" {{ request('start_date') ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>

                <!-- Custom Date Range -->
                <div class="col-md-2 custom-date-range" style="display: {{ request('start_date') ? 'block' : 'none' }};">
                    <label class="form-label fw-semibold">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2 custom-date-range" style="display: {{ request('start_date') ? 'block' : 'none' }};">
                    <label class="form-label fw-semibold">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>

                <!-- Staff -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Staff Member</label>
                    <select name="staff_id" class="form-select">
                        <option value="">All Staff</option>
                        @foreach ($staffList as $staff)
                            <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Search -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Job ID or Vehicle..." value="{{ request('search') }}">
                </div>

                <!-- Status -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-funnel"></i> Apply
                    </button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('admin.reports') }}" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('admin.exportReportsPdf', request()->query()) }}"
                       class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-file-earmark-pdf"></i> Export PDF
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <i class="bi bi-file-earmark-text text-primary fs-2 mb-2"></i>
                    <h6 class="text-muted">Total Reports</h6>
                    <h3 class="fw-bold">{{ $totalReports }}</h3>
                    <small class="text-muted">Filtered results</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <i class="bi bi-check-circle-fill text-success fs-2 mb-2"></i>
                    <h6 class="text-muted">Completed</h6>
                    <h3 class="fw-bold text-success">{{ $completed }}</h3>
                    <small class="text-muted">{{ $totalReports > 0 ? round(($completed / $totalReports) * 100, 1) : 0 }}%</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <i class="bi bi-hourglass-split text-warning fs-2 mb-2"></i>
                    <h6 class="text-muted">Pending</h6>
                    <h3 class="fw-bold text-warning">{{ $pending }}</h3>
                    <small class="text-muted">{{ $totalReports > 0 ? round(($pending / $totalReports) * 100, 1) : 0 }}%</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <i class="bi bi-x-circle-fill text-danger fs-2 mb-2"></i>
                    <h6 class="text-muted">Cancelled</h6>
                    <h3 class="fw-bold text-danger">{{ $cancelled }}</h3>
                    <small class="text-muted">{{ $totalReports > 0 ? round(($cancelled / $totalReports) * 100, 1) : 0 }}%</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Chart -->
    {{-- <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-light fw-semibold d-flex align-items-center gap-2">
            <i class="bi bi-pie-chart-fill text-primary"></i> Reports by Status
        </div>
        <div class="card-body">
            <canvas id="statusChart" height="160"></canvas>
        </div>
    </div> --}}

    <!-- Report Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-light fw-semibold d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-table text-secondary"></i>
                Report Details
                <small class="text-muted ms-2">(Showing {{ $reports->count() }} records)</small>
            </div>
            <div class="text-muted small">
                Total Revenue: <strong>₹{{ number_format($reports->sum('amount'), 2) }}</strong>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Job ID</th>
                            <th>Vehicle</th>
                            <th>Staff</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reports as $report)
                            <tr>
                                <td><strong>{{ $report->job_id }}</strong></td>
                                <td>{{ $report->vehicle->registration_no ?? 'N/A' }}</td>
                                <td>{{ $report->staff->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge 
                                        {{ $report->status == 'completed' ? 'bg-success' : 
                                           ($report->status == 'pending' ? 'bg-warning text-dark' : 
                                           ($report->status == 'cancelled' ? 'bg-danger' : 'bg-secondary')) }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </td>
                                <td>₹{{ number_format($report->amount, 2) }}</td>
                                <td>{{ $report->created_at->format('d M Y') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.services.view', $report->id) }}" class="btn btn-info btn-sm" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    No reports found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white border-0 py-3">
            @if($reports->hasPages())
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ $reports->total() }} entries
                    </div>
                    {{ $reports->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const periodSelect = document.getElementById('period-select');
    const customFields = document.querySelectorAll('.custom-date-range');

    function toggleCustomFields() {
        const isCustom = periodSelect.value === 'custom';
        customFields.forEach(f => f.style.display = isCustom ? 'block' : 'none');
    }

    periodSelect.addEventListener('change', toggleCustomFields);
    toggleCustomFields();

    // Status Doughnut Chart
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'Pending', 'Cancelled'],
            datasets: [{
                data: [{{ $completed }}, {{ $pending }}, {{ $cancelled }}],
                backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            plugins: { legend: { position: 'bottom' } },
            maintainAspectRatio: false
        }
    });
});
</script>
@endsection
