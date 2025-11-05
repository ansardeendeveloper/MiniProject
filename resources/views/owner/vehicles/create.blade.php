@extends('layouts.ownertemplates')
@section('title', 'Register Vehicle')

@section('head')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .readonly-input { background-color: #f8f9fa; pointer-events: none; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4">Register New Vehicle</h2>
        <a href="{{ route('owner.vehicles') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Vehicles
        </a>
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

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Vehicle Details</h5>
        </div>
        
        <div class="card-body">
            <form id="vehicleForm" action="{{ route('owner.vehicles.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Vehicle Number * 
                            <span class="material-icons" style="vertical-align: middle; font-size: 18px;">directions_car</span>
                        </label>
                        <input type="text" name="vehicle_number" id="vehicle_number" class="form-control readonly-input" value="{{ session('owner_vehicle_number') }}" readonly required>
                        <small class="form-text text-muted">Your registered vehicle number</small>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Model *</label>
                        <input type="text" name="model" id="model" class="form-control" placeholder="e.g., Swift Dzire" required>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label">Manufacturer *</label>
                        <input type="text" name="manufacturer" id="manufacturer" class="form-control" placeholder="e.g., Maruti Suzuki" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Year *</label>
                        <input type="number" name="year" id="year" class="form-control" placeholder="e.g., 2022" required min="1990" max="{{ date('Y') + 1 }}">
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label">Customer Name *</label>
                        <input type="text" name="customer_name" id="customer_name" class="form-control readonly-input" value="{{ session('owner_name') }}" readonly required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" id="email" class="form-control readonly-input" value="{{ $owner->email ?? '' }}" readonly required>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label">Mobile Number *</label>
                        <div class="input-group">
                            <span class="input-group-text">+91</span>
                            <input type="text" name="mobile_number" id="mobile_number" class="form-control readonly-input" value="{{ $owner->phone ?? '' }}" readonly required>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Register Vehicle
                    </button>
                    <a href="{{ route('owner.vehicles') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(function () {
    const Toast = Swal.mixin({ 
        toast: true, 
        position: 'top-end', 
        showConfirmButton: false, 
        timer: 3000 
    });
    $('#model, #manufacturer').on('input', function () {
        this.value = this.value.toUpperCase();
    });
    $('#year').on('input', function () {
        const currentYear = new Date().getFullYear();
        const year = parseInt(this.value);
        if (year < 1990 || year > currentYear + 1) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    function checkExistingVehicle() {
        $.ajax({
            url: '{{ route("owner.vehicles.check-existing") }}',
            method: 'GET',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.has_vehicle) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Vehicle Already Registered',
                        html: 'You already have a registered vehicle.<br><br>Each owner can register only one vehicle.',
                        confirmButtonText: 'View My Vehicle'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route("owner.vehicles") }}';
                        }
                    });
                    $('#vehicleForm').find('button[type="submit"]').prop('disabled', true);
                }
            },
            error: function(xhr) {
                console.log('Error checking existing vehicle:', xhr.responseText);
            }
        });
    }
    checkExistingVehicle();
    $('#vehicleForm').on('submit', function (e) {
        e.preventDefault();
        
        const model = $('#model').val().trim();
        const manufacturer = $('#manufacturer').val().trim();
        const year = $('#year').val().trim();
        if (!model || !manufacturer || !year) {
            Swal.fire('Error', 'Please fill all required fields.', 'error');
            return false;
        }
        const currentYear = new Date().getFullYear();
        if (parseInt(year) < 1990 || parseInt(year) > currentYear + 1) {
            Swal.fire('Error', 'Please enter a valid year (1990 - ' + (currentYear + 1) + ').', 'error');
            return false;
        }

        Swal.fire({
            title: 'Registering Vehicle...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        this.submit();
    });
});
</script>
@endsection