@extends('layouts.admintemplates')
@section('title', 'Add New Service & Assign Work')

@section('head')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .readonly-input { background-color: #f8f9fa; pointer-events: none; }
    .card { margin-bottom: 20px; }
    .stats-card { background-color: #f8f9fa; border-left: 4px solid #007bff; }
    .staff-stats { background-color: #e9ecef; padding: 15px; border-radius: 5px; margin-top: 10px; }
</style>

@endsection

@section('content')
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4">Service Management</h2>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

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
        
        // Calculate today's stats
        $today = \Carbon\Carbon::today();
        $pendingToday = \App\Models\ServiceRecord::whereDate('created_at', $today)->where('status', 'pending')->count();
        $completedToday = \App\Models\ServiceRecord::whereDate('created_at', $today)->where('status', 'completed')->count();
        
        // Staff statistics (initialize empty)
        $staffPending = 0;
        $staffCompleted = 0;
        $selectedStaffId = old('staff_id', request('staff_id'));
        
        if ($selectedStaffId) {
            $staffPending = \App\Models\ServiceRecord::whereDate('created_at', $today)
                ->where('staff_id', $selectedStaffId)
                ->where('status', 'pending')
                ->count();
            $staffCompleted = \App\Models\ServiceRecord::whereDate('created_at', $today)
                ->where('staff_id', $selectedStaffId)
                ->where('status', 'completed')
                ->count();
        }
    @endphp


    <!-- Add New Service Card -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Add New Service</h5>
        </div>
        
        <div class="card-body">
            <form id="serviceForm" action="{{ route('staff.services.store') }}" method="POST">
                @csrf
                <input type="hidden" name="vehicle_id" id="vehicle_id">
                <input type="hidden" name="service_start_datetime" id="service_start_datetime_hidden">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Vehicle Number * 
                            <span class="material-icons" style="vertical-align: middle; font-size: 18px;">directions_car</span>
                        </label>
                        <input type="text" name="vehicle_number" id="vehicle_number" class="form-control" placeholder="PY**VC****" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">KM Run *</label>
                        <input type="number" name="km_run" id="km_run" class="form-control" placeholder="e.g., 32500" required min="0" max="999999">
                        <small class="form-text text-muted">Maximum: 999,999 KM</small>
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
                        <input type="text" name="year" id="year" class="form-control" required min="1900" max="{{ date('Y') + 1 }}">
                        <small class="form-text text-muted">Range: 1900 - {{ date('Y') + 1 }}</small>
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

                <!-- Save Service Button Removed -->
            </form>
        </div>
    </div>

    <!-- Assign Work Card -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Assign Work to Staff</h5>
        </div>

        <div class="card-body">
            <form id="assignForm" action="{{ route('admin.assign.work') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Select Staff *</label>
                        <select name="staff_id" id="staff_id" class="form-select" required>
                            <option value="">-- Choose Staff Member --</option>
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}" {{ $selectedStaffId == $member->id ? 'selected' : '' }}>
                                    {{ $member->name }}
                                </option>
                            @endforeach
                        </select>
                        
                        <!-- Staff Statistics -->
                        <div id="staffStatistics" class="staff-stats mt-2" style="display: {{ $selectedStaffId ? 'block' : 'none' }};">
                            <h6 class="mb-2">Today's Staff Performance:</h6>
                            <div class="row">
                                <div class="col-6">
                                    <strong class="text-warning">Pending:</strong> 
                                    <span id="staffPendingCount">{{ $staffPending }}</span>
                                </div>
                                <div class="col-6">
                                    <strong class="text-success">Completed:</strong> 
                                    <span id="staffCompletedCount">{{ $staffCompleted }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Pending Services Today</label>
                        <input type="text" class="form-control readonly-input" value="{{ $pendingToday }} services" readonly>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label">Completed Services Today</label>
                        <input type="text" class="form-control readonly-input" value="{{ $completedToday }} services" readonly>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Assign Work</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(function () {
    const prices = @json($servicePrices);
    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1000 });
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

    // Amount autocalc
    function calcAmount() {
        let total = 0;
        $('input[name="service_types[]"]:checked').each(function () {
            total += prices[$(this).val()] || 0;
        });
        $('#amount').val(total.toFixed(2));
    }
    $('input[name="service_types[]"]').on('change', calcAmount);
    calcAmount();

    // Mobile number validation
    $('#mobile_number').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Uppercase fields
    $('#vehicle_name, #manufacturer, #customer_name').on('input', function () {
        this.value = this.value.toUpperCase();
    });

    // Year validation
    $('#year').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
        // Validate year range
        const year = parseInt(this.value);
        if (this.value.length === 4 && (year < 1900 || year > new Date().getFullYear() + 1)) {
            Swal.fire('Warning', 'Please enter a valid year between 1900 and ' + (new Date().getFullYear() + 1), 'warning');
            this.value = '';
        }
    });

    // KM Run validation
    $('#km_run').on('blur', function () {
        const km = parseInt(this.value);
        if (km > 999999) {
            Swal.fire('Error', 'KM Run cannot exceed 999,999', 'error');
            this.value = '';
        } else if (km < 0) {
            Swal.fire('Error', 'KM Run cannot be negative', 'error');
            this.value = '';
        }
    });

    // Service Form Submission - Auto-save when form is complete
    $('#serviceForm').on('change', function () {
        // Check if all required fields are filled
        const vehicleNumber = $('#vehicle_number').val().trim();
        const kmRun = $('#km_run').val().trim();
        const vehicleName = $('#vehicle_name').val().trim();
        const manufacturer = $('#manufacturer').val().trim();
        const year = $('#year').val().trim();
        const customerName = $('#customer_name').val().trim();
        const mobileNumber = $('#mobile_number').val().trim();
        const serviceTypes = $('input[name="service_types[]"]:checked').length;
        const serviceDateTime = $('#service_start_datetime_local').val();

        // Validate KM Run
        if (kmRun && (parseInt(kmRun) > 999999 || parseInt(kmRun) < 0)) {
            return;
        }

        if (vehicleNumber && kmRun && vehicleName && manufacturer && year && 
            customerName && mobileNumber && serviceTypes > 0 && serviceDateTime) {
            
            // Validate mobile number
            if (!/^[0-9]{10,15}$/.test(mobileNumber)) {
                return;
            }

            // Validate year
            const yearNum = parseInt(year);
            if (year.length !== 4 || yearNum < 1900 || yearNum > new Date().getFullYear() + 1) {
                return;
            }

            // Auto-submit the form
            $('#mobile_number').val('+91' + mobileNumber);
            $('#service_start_datetime_hidden').val(serviceDateTime);
            
            // Submit via AJAX
            $.ajax({
                url: '{{ route('staff.services.store') }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    Toast.fire({ icon: 'success', title: 'Service saved automatically!' });
                    // Clear form for next entry
                    $('#serviceForm')[0].reset();
                    setNow(); // Reset datetime to now
                    // Reload page to update statistics
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                },
                error: function(xhr) {
                    Toast.fire({ icon: 'error', title: 'Error saving service' });
                }
            });
        }
    });

    // Vehicle number lookup
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
                    Toast.fire({ icon: 'success', title: 'Vehicle loaded!' });
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
        $('#vehicle_name, #manufacturer, #year, #customer_name, #mobile_number, #km_run')
            .val('').removeClass('readonly-input');
    }

    // Staff selection change - Load staff statistics
    $('#staff_id').on('change', function() {
        const staffId = $(this).val();
        const statsDiv = $('#staffStatistics');
        
        if (staffId) {
            // Show loading
            statsDiv.show().html('<div class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Loading statistics...</div>');
            
            // Use direct URL instead of named route to avoid errors
            const statsUrl = '/admin/staff-statistics';
            
            // Fetch staff statistics via AJAX
            $.ajax({
                url: statsUrl,
                method: 'GET',
                data: { staff_id: staffId },
                success: function(response) {
                    statsDiv.html(`
                        <h6 class="mb-2">Today's Staff Performance:</h6>
                        <div class="row">
                            <div class="col-6">
                                <strong class="text-warning">Pending:</strong> 
                                <span id="staffPendingCount">${response.pending}</span>
                            </div>
                            <div class="col-6">
                                <strong class="text-success">Completed:</strong> 
                                <span id="staffCompletedCount">${response.completed}</span>
                            </div>
                        </div>
                    `);
                },
                error: function() {
                    // Fallback: reload the page to show stats
                    statsDiv.html('<div class="text-info">Please wait, refreshing page...</div>');
                    setTimeout(() => {
                        window.location.href = window.location.href.split('?')[0] + '?staff_id=' + staffId;
                    }, 1000);
                }
            });
        } else {
            statsDiv.hide();
        }
    });

    // Assign Form Submission
    $('#assignForm').on('submit', function(e) {
        e.preventDefault();

        const staff = $('#staff_id').val();

        if (!staff) {
            Swal.fire('Error', 'Please select staff member.', 'error');
            return;
        }

        Swal.fire({
            title: 'Confirm Assignment',
            text: 'Are you sure you want to assign work to this staff member?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Assign',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
                Toast.fire({ icon: 'success', title: 'Assigning work...' });
            }
        });
    });
});
</script>
@endsection