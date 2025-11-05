@extends('layouts.stafftemplates')

@section('title', 'My Services')

@section('head')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<style>
    .material-icons {
        vertical-align: middle;
        font-size: 18px;
        margin-right: 4px;
    }
    .btn:disabled {
        opacity: 0.6 !important;
        cursor: not-allowed !important;
        pointer-events: none !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">My Services</h1>
        <div>
            <a href="{{ route('staff.services.create') }}" class="btn btn-primary me-2">
                <span class="material-icons">add</span> Add New
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Service List</h5>
        </div>
        <div class="card-body" id="printArea">
            @if($services->isEmpty())
                <p class="text-muted">No services found. <a href="{{ route('staff.services.create') }}">Create one</a>.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>JOB ID</th>
                                <th>Vehicle</th>
                                <th>Date</th>
                                <th>Types</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                                <tr id="serviceRow{{ $service->id }}">
                                    <td>{{ $service->job_id ?? 'N/A' }}</td>
                                    <td>{{ $service->vehicle->registration_no ?? 'N/A' }}</td>
                                    <td>
                                        @if(!empty($service->service_start_date))
                                            {{ \Carbon\Carbon::parse($service->service_start_date)->format('d/m/Y') }}
                                        @elseif(!empty($service->created_at))
                                            {{ \Carbon\Carbon::parse($service->created_at)->format('d/m/Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ implode(', ', json_decode($service->service_types, true) ?: []) }}</td>
                                    <td>₹{{ number_format($service->amount, 2) }}</td>
                                    <td>
                                        @php
                                            $badge = match($service->status) {
                                                'pending' => 'bg-danger',
                                                'completed' => 'bg-success',
                                                'cancelled' => 'bg-warning text-dark',
                                                default => 'bg-primary',
                                            };
                                        @endphp
                                        <span class="badge {{ $badge }}">{{ ucfirst($service->status) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('staff.services.show', $service->id) }}" class="btn btn-sm btn-info">
                                            <span class="material-icons">visibility</span>
                                        </a>

                                        @if($service->status === 'pending')
                                            <a href="{{ route('staff.services.edit', $service->id) }}" class="btn btn-sm btn-primary">
                                                <span class="material-icons">edit</span>
                                            </a>
                                            {{-- <form action="{{ route('staff.services.destroy', $service->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this service?')">
                                                    <span class="material-icons">delete</span>
                                                </button>
                                            </form> --}}
                                        @else
                                            <button class="btn btn-sm btn-primary" disabled>
                                                <span class="material-icons">edit</span>
                                            </button>
                                            {{-- <button class="btn btn-sm btn-danger" disabled>
                                                <span class="material-icons">delete</span>
                                            </button> --}}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
