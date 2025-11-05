@extends('layouts.stafftemplates')
@section('title', 'Add New Service')

@section('head')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .readonly-input { background-color: #f8f9fa; pointer-events: none; }
    .owner-info { background-color: #e7f3ff; border-left: 4px solid #007bff; padding: 10px; margin: 10px 0; }
</style>

@endsection

@section('content')
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4">Add New Service</h2>
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
            'General Service' => 1800, 'Light Change' => 400, 'Spark plug replacement' => 550,
            'Suspension check' => 880, 'Cooling system check' => 650, 'Oil Change' => 500,
            'Tire Replacement' => 3000, 'Brake Repair' => 1500, 'Engine Tune-up' => 4000,
            'Battery Replacement' => 4500
        ];
    @endphp

    <div class="card">
        
        <div class="card-header bg-light">
            <h5 class="mb-0">Service Details</h5>
        </div>
        
        <div class="card-body">
            <form id="serviceForm" action="{{ route('staff.services.store') }}" method="POST">
                @csrf
                <input type="hidden" name="vehicle_id" id="vehicle_id">
                <input type="hidden" name="service_start_datetime" id="service_start_datetime_hidden">

                <div id="ownerInfo" class="owner-info" style="display: none;">
                    <h6><i class="material-icons" style="vertical-align: middle;">info</i> Vehicle Found in Owners Database</h6>
                    <p id="ownerMessage"></p>
                </div>

                <div class="row g-3">
                    
                    <div class="col-md-6">
                        <label class="form-label">Vehicle Number * 
                            <span class="material-icons" style="vertical-align: middle; font-size: 18px;">directions_car</span>
                        </label>
                        <input type="text" name="vehicle_number" id="vehicle_number" class="form-control" placeholder="PY**VC****" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">KM Run *</label>
                        <input type="number" name="km_run" id="km_run" class="form-control" placeholder="e.g., 32500" required min="0">
                    </div>

                </div>

                <div class="row g-3 mt-1">
                    
                    <div class="col-md-6">
                        <label class="form-label">Model *</label>
                        <input type="text" name="vehicle_name" id="vehicle_name" class="form-control" required>
                    </div>

                </div>

                <div class="row g-3 mt-1">
                    
                    <div class="col-md-6">
                        <label class="form-label">Manufacturer *</label>
                        <input type="text" name="manufacturer" id="manufacturer" class="form-control" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Year *</label>
                        <input type="text" name="year" id="year" class="form-control" required>
                    </div>

                </div>

                <div class="row g-3 mt-1">
                    
                    <div class="col-md-6">
                        <label class="form-label">Customer Name *</label>
                        <input type="text" name="customer_name" id="customer_name" class="form-control" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Mobile Number *</label>
                        <div class="input-group">
                            <span class="input-group-text">+91</span>
                            <input type="text" name="mobile_number" id="mobile_number" class="form-control" placeholder="10–15 digits" required>
                        </div>
                    </div>

                </div>

                <div class="row g-3 mt-1">
                    
                    <div class="col-md-12">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="customer@example.com">
                        <small class="form-text text-muted">Invoice will be sent to this email</small>
                    </div>

                </div>
                
                <div class="row g-3 mt-3">
                
                    <div class="col-md-8">
                        <label class="form-label">Service Start Date & Time *</label>
                        <input type="datetime-local" id="service_start_datetime_local" class="form-control" required>
                        <small class="form-text text-muted">DD/MM/YYYY HH:mm (24h)</small>
                    </div>
                
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" id="nowBtn" class="btn btn-primary w-100">Set Now</button>
                    </div>
                </div>
                
                <div class="mt-3">
                    <label class="form-label">Service Types *</label>
                    @foreach($servicePrices as $type => $price)
                        <div class="form-check">
                            <input type="checkbox" name="service_types[]" value="{{ $type }}" class="form-check-input">
                            <label class="form-check-label">{{ $type }} (₹{{ number_format($price) }})</label>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3">
                    <label class="form-label">Amount (INR) *</label>
                    <input type="number" name="amount" id="amount" class="form-control readonly-input" readonly required>
                </div>

                <div class="mt-3">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-select" required>
                        <option value="pending" selected>Pending</option>
                    </select>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Save Service</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
$(function () {

    const prices = @json($servicePrices);
    //sweetalert toastr
    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1000 });
    //india time
    const kolkataTZ = 'Asia/Kolkata';

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

    function setNow() {
        const now = new Date();
        const iso = toLocalISODateTime(now);
        $('#service_start_datetime_local').val(iso);
        $('#service_start_datetime_hidden').val(iso);
        const display = iso.replace('T', ' ').substring(0, 16);
        Toast.fire({ icon: 'success', title: 'Set: ' + display });
    }

    $('#nowBtn').on('click', setNow);
    setNow(); // auto-fill on load

    $('#service_start_datetime_local').on('change', function () {
        $('#service_start_datetime_hidden').val(this.value);
    });

    //amount autocalc
    function calcAmount() {
        let total = 0;
        $('input[name="service_types[]"]:checked').each(function () {
            total += prices[$(this).val()] || 0;
        });
        $('#amount').val(total.toFixed(2));
    }
    $('input[name="service_types[]"]').on('change', calcAmount);
    calcAmount();

    //mobile number
    $('#mobile_number').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    //
    $('#vehicle_name, #manufacturer, #customer_name').on('input', function () {
        this.value = this.value.toUpperCase();
    });

    //year
    $('#year').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    //sweet alert2 toastr
    $('#serviceForm').on('submit', function (e) {
        const mobile = $('#mobile_number').val().trim();
        if (!/^[0-9]{10,15}$/.test(mobile)) {
            e.preventDefault();
            Swal.fire('Error', 'Mobile number must be 10–15 digits.', 'error');
            return false;
        }
        $('#mobile_number').val('+91' + mobile);
        //km run 
        const km = parseInt($('#km_run').val(), 10);
        if (isNaN(km) || km <= 0) {
            e.preventDefault();
            Swal.fire('Error', 'Please enter a valid KM Run value.', 'error');
            return false;
        }

        
        if (!$('input[name="service_types[]"]:checked').length) {
            e.preventDefault();
            Swal.fire('Error', 'Select at least one service type.', 'error');
            return false;
        }

        if (!$('#service_start_datetime_local').val()) {
            e.preventDefault();
            Swal.fire('Error', 'Please set service start date & time.', 'error');
            return false;
        }

        $('#service_start_datetime_hidden').val($('#service_start_datetime_local').val());
        Toast.fire({ icon: 'info', title: 'Saving service...' });
    });

    $('#vehicle_number').on('blur', function () {
        const reg = $(this).val().trim().toUpperCase();
        if (reg.length < 6) return;

        $.ajax({
            url: '{{ route("staff.vehicles.fetch", ":reg") }}'.replace(':reg', reg),
            success: function (res) {
                if (res.status === 'pending') {
                    Swal.fire('Warning', res.message, 'warning');
                    clearFields();
                } else if (res.status === 'success') {
                    $('#vehicle_id').val(res.vehicle_id);
                    $('#vehicle_name').val(res.model).addClass('readonly-input');
                    $('#manufacturer').val(res.manufacturer).addClass('readonly-input');
                    $('#year').val(res.year).addClass('readonly-input');
                    $('#customer_name').val(res.customer_name).addClass('readonly-input');
                    $('#mobile_number').val(res.mobile_number.replace('+91', '')).addClass('readonly-input');
                    $('#email').val(res.email || '').addClass('readonly-input');
                    $('#ownerInfo').hide();
                    Toast.fire({ icon: 'success', title: 'Vehicle loaded!' });
                } else if (res.status === 'owner_found') {
                    $('#ownerInfo').show();
                    $('#ownerMessage').html(
                        `<strong>Owner:</strong> ${res.owner_name}<br>` +
                        `<strong>Email:</strong> ${res.email}<br>` +
                        `<strong>Phone:</strong> ${res.phone}<br>` +
                        `<strong>Address:</strong> ${res.address}`
                    );
                    $('#customer_name').val(res.owner_name);
                    $('#mobile_number').val(res.phone.replace('+91', ''));
                    $('#email').val(res.email);
                    Toast.fire({ icon: 'info', title: 'Owner details loaded!' });
                } else {
                    Swal.fire('Info', res.message, 'info');
                    clearFields();
                }
            },
            error: function () {
                Swal.fire('Error', 'Failed to fetch vehicle.', 'error');
            }
        });
    });

    function clearFields() {
        $('#vehicle_id').val('');
        $('#vehicle_name, #manufacturer, #year, #customer_name, #mobile_number, #km_run, #email')
            .val('').removeClass('readonly-input');
        $('#ownerInfo').hide();
    }
});
</script>
@endsection