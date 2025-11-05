@extends('layouts.stafftemplates')
@section('title', 'Edit Service')

@section('head')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .readonly-input {
            background-color: #f8f9fa;
            pointer-events: none;
            cursor: not-allowed;
        }
        .form-check-label {
            font-weight: normal;
        }
        #clock {
            font-weight: bold;
            color: #007bff;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4">Edit Service #{{ $service->id }}</h2>
        <a href="{{ route('staff.services.index') }}" class="btn btn-secondary">Back</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $servicePrices = [
            'General Service' => 1800,
            'Light Change' => 400,
            'Spark plug replacement' => 550,
            'Suspension check' => 880,
            'Cooling system check' => 650,
            'Oil Change' => 500,
            'Tire Replacement' => 3000,
            'Brake Repair' => 1500,
            'Engine Tune-up' => 4000,
            'Battery Replacement' => 4500
        ];
        $selected = json_decode($service->service_types, true) ?: [];
    @endphp

    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Service Details</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('staff.services.update', $service->id) }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="vehicle_id" value="{{ $service->vehicle_id }}">
                <input type="hidden" name="service_start_datetime"
                       value="{{ \Carbon\Carbon::parse($service->service_start_datetime)
                           ->timezone('Asia/Kolkata')->format('Y-m-d\TH:i') }}">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Job ID</label>
                        <input type="text" value="{{ $service->job_id }}" class="form-control readonly-input" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Vehicle Number</label>
                        <input type="text" value="{{ $service->vehicle->registration_no }}" class="form-control readonly-input" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Customer Email</label>
                        <input type="text" 
                               value="{{ $service->vehicle->customer->email ?? 'No email' }}" 
                               class="form-control readonly-input" readonly>
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <label class="form-label">Service Start Date & Time</label>
                        <input type="text"
                               value="{{ \Carbon\Carbon::parse($service->service_start_datetime)
                                   ->timezone('Asia/Kolkata')->format('d/m/Y H:i') }}"
                               class="form-control readonly-input" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Service End Date & Time *</label>
                        <input type="datetime-local" name="service_end_datetime" id="service_end_local" class="form-control" required>
                        <small class="form-text text-muted">DD/MM/YYYY HH:mm (24h)</small>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" id="nowBtn" class="btn btn-primary w-100">Set Now</button>
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <label class="form-label">Status *</label>
                        <select name="status" class="form-select" required>
                            <option value="completed" {{ $service->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $service->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Service Types *</label>
                    @foreach ($servicePrices as $type => $price)
                        <div class="form-check">
                            <input type="checkbox"
                                   name="service_types[]"
                                   value="{{ $type }}"
                                   class="form-check-input"
                                   {{ in_array($type, $selected) ? 'checked' : '' }}>
                            <label class="form-check-label">{{ $type }} (₹{{ number_format($price) }})</label>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3">
                    <label class="form-label">Amount (INR) *</label>
                    <input type="number"
                           name="amount"
                           id="amount"
                           value="{{ $service->amount }}"
                           class="form-control readonly-input"
                           readonly
                           required>
                </div>

                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-success">Update Service</button>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-3 text-center text-muted">
        <small>Current Time: <span id="clock"></span></small>
    </div>
</div>

<script>
$(function () {
    const prices = @json($servicePrices);
    const kolkataTZ = 'Asia/Kolkata';
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2500
    });

    function toLocalISODateTime(date) {
        const opts = {
            timeZone: kolkataTZ,
            year: 'numeric', month: '2-digit', day: '2-digit',
            hour: '2-digit', minute: '2-digit', hour12: false
        };
        const parts = new Intl.DateTimeFormat('en-CA', opts).formatToParts(date);
        const y = parts.find(p => p.type === 'year').value;
        const m = parts.find(p => p.type === 'month').value;
        const d = parts.find(p => p.type === 'day').value;
        const h = parts.find(p => p.type === 'hour').value;
        const min = parts.find(p => p.type === 'minute').value;
        return `${y}-${m}-${d}T${h}:${min}`;
    }

    function updateClock() {
        const now = new Date();
        const time = now.toLocaleTimeString('en-GB', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false,
            timeZone: kolkataTZ
        });
        $('#clock').text(time);
    }
    updateClock();
    setInterval(updateClock, 1000);

    $('#nowBtn').on('click', function () {
        const now = new Date();
        const iso = toLocalISODateTime(now);
        $('#service_end_local').val(iso);
        Toast.fire({ icon: 'success', title: 'Time set: ' + iso.replace('T', ' ') });
    });

    @if($service->service_end_datetime)
        const endDate = new Date('{{ \Carbon\Carbon::parse($service->service_end_datetime)
            ->timezone("Asia/Kolkata")->format("Y-m-d\TH:i") }}');
        $('#service_end_local').val(toLocalISODateTime(endDate));
    @else
        setTimeout(() => $('#nowBtn').click(), 100);
    @endif

    function calcAmount() {
        let total = 0;
        $('input[name="service_types[]"]:checked').each(function () {
            total += prices[$(this).val()] || 0;
        });
        $('#amount').val(total.toFixed(2));
    }

    $('input[name="service_types[]"]').on('change', calcAmount);
    calcAmount();
});
</script>
@endsection