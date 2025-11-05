@extends('layouts.ownertemplates')

@section('title', 'Invoices')

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .invoice-card {
            border-left: 4px solid #0d6efd;
            transition: all 0.3s ease;
        }
        .invoice-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-0">Invoices</h1>
            <p class="text-muted small mb-0">Your service invoices and payment history</p>
        </div>
        <button class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2" 
                data-bs-toggle="modal" data-bs-target="#filterModal">
            <i class="bi bi-funnel"></i> Filter
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 bg-primary bg-opacity-10">
                <div class="card-body p-3 text-center">
                    <i class="bi bi-receipt text-primary mb-2" style="font-size:1.75rem;"></i>
                    <h6 class="mb-1 text-muted">Total Invoices</h6>
                    <h3 class="fw-bold mb-0 text-primary">{{ $invoices->total() }}</h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 bg-info bg-opacity-10">
                <div class="card-body p-3 text-center">
                    <i class="bi bi-check-circle-fill text-info mb-2" style="font-size:1.75rem;"></i>
                    <h6 class="mb-1 text-muted">Completed Services</h6>
                    <h3 class="fw-bold mb-0 text-info">{{ $invoices->where('status', 'completed')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices List -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-receipt-cutoff me-2"></i>Invoice History</h5>
            <span class="text-muted small">
                Showing {{ $invoices->firstItem() ?? 0 }}-{{ $invoices->lastItem() ?? 0 }} of {{ $invoices->total() }} invoices
            </span>
        </div>
        <div class="card-body p-0">
            @if($invoices->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Invoice #</th>
                                <th>Service Details</th>
                                <th>Vehicle</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $invoice)
                                <tr>
                                    <td class="ps-4">
                                        <strong class="text-primary">#INV-{{ str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $invoice->job_id ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $invoice->service_name ?? 'General Service' }}</strong>
                                            @if($invoice->description)
                                                <br>
                                                <small class="text-muted">{{ Str::limit($invoice->description, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($invoice->vehicle)
                                            <small class="text-muted">
                                                {{ $invoice->vehicle->model ?? 'N/A' }}<br>
                                                {{ $invoice->vehicle->registration_no }}
                                            </small>
                                        @else
                                            <small class="text-muted">N/A</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($invoice->created_at)->format('M d, Y') }}
                                        <br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($invoice->created_at)->format('g:i A') }}</small>
                                    </td>
                                    <td class="fw-semibold text-primary">₹{{ number_format($invoice->amount, 2) }}</td>
                                    <td>
                                        <span class="badge status-badge 
                                            @if($invoice->status == 'completed') bg-success
                                            @elseif($invoice->status == 'cancelled') bg-danger
                                            @elseif($invoice->status == 'in_progress') bg-info
                                            @else bg-warning text-dark @endif">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge status-badge 
                                            @if($invoice->payment_status == 'paid') bg-success
                                            @elseif($invoice->payment_status == 'failed') bg-danger
                                            @else bg-warning text-dark @endif">
                                            {{ ucfirst($invoice->payment_status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('owner.services.show', $invoice->id) }}" 
                                               class="btn btn-outline-primary" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('owner.invoice.print', $invoice->id) }}" 
                                               class="btn btn-outline-success" title="Print" target="_blank">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                            {{-- <a href="{{ route('owner.invoice.download', $invoice->id) }}" 
                                               class="btn btn-outline-info" title="Download PDF">
                                                <i class="bi bi-download"></i>
                                            </a> --}}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($invoices->hasPages())
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Showing {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} of {{ $invoices->total() }} entries
                            </div>
                            {{ $invoices->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-receipt display-1 text-muted opacity-50"></i>
                    <h5 class="mt-3">No Invoices Found</h5>
                    <p class="mb-0">You don't have any invoices yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content rounded-3 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold"><i class="bi bi-funnel me-2"></i>Filter Invoices</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="GET" action="{{ route('owner.invoices') }}">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Status</label>
                        <select name="payment_status" class="form-select">
                            <option value="">All Payments</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Service Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2-circle"></i> Apply Filter
                        </button>
                        <a href="{{ route('owner.invoices') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
@endsection