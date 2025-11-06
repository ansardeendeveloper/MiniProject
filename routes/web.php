<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\Admin\AdminWorkAssignController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\AdminServiceController;

//welcome route
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Admin routes
Route::prefix('admin')->group(function () {
    // authentication
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login.submit');
    Route::get('/logout', [AdminController::class, 'logout'])->name('admin.logout.form');
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');

    // dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // FIXED: OWNERS MANAGEMENT ROUTES - Added admin. prefix
    Route::get('/owners', [AdminController::class, 'owners'])->name('admin.owners');
    Route::get('/owners/{id}', [AdminController::class, 'viewOwner'])->name('admin.owners.view');
    Route::delete('/owners/{id}', [AdminController::class, 'deleteOwner'])->name('admin.owners.delete');

    // vehicles
    Route::get('/vehicles', [AdminController::class, 'vehicles'])->name('admin.vehicles');
    Route::get('/vehicles/{id}', [AdminController::class, 'viewVehicle'])->name('admin.vehicles.view');
    Route::get('/vehicles/fetch/{reg}', [AdminController::class, 'fetchVehicle'])->name('admin.vehicles.fetch');

    // staff management
    Route::get('/staff', [AdminController::class, 'staff'])->name('admin.staff');
    Route::get('/staff/register', [AdminController::class, 'showStaffRegister'])->name('admin.staff.register');
    Route::post('/staff/store', [AdminController::class, 'storeStaff'])->name('admin.staff.store');

    Route::get('/staff/{id}', [AdminController::class, 'viewStaff'])->name('admin.staff.view');
    Route::get('/staff/{id}/edit', [AdminController::class, 'editStaff'])->name('admin.staff.edit');
    Route::put('/staff/{id}/update', [AdminController::class, 'updateStaff'])->name('admin.staff.update');
    Route::delete('/staff/{id}/delete', [AdminController::class, 'deleteStaff'])->name('admin.staff.delete');

    Route::get('/staff/list', [AdminController::class, 'staff'])->name('admin.staff.list'); 
    
    // services
    Route::get('/services', [AdminController::class, 'services'])->name('admin.services');
    Route::get('/services/{id}', [AdminController::class, 'viewService'])->name('admin.services.view');
    Route::get('/services/search', [AdminController::class, 'searchServices'])->name('admin.services.search');

    // reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/reports/export/pdf', [AdminController::class, 'exportReportsPdf'])->name('admin.exportReportsPdf');
    Route::get('/reports/export-pdf/{id}', [AdminController::class, 'exportReportPdf'])->name('admin.exportReportPdf');
    Route::get('/reports/staff/{staffId}', [AdminController::class, 'viewStaffReport'])->name('admin.viewStaffReport');

    // Work assignment routes
    Route::get('/assign', [AdminWorkAssignController::class, 'create'])->name('admin.assign');
    Route::get('/staff-statistics', [AdminWorkAssignController::class, 'getStaffStatistics'])->name('admin.staff.statistics');
    Route::post('/assign-work', [AdminWorkAssignController::class, 'store'])->name('admin.assign.work');


Route::get('/admin/services', [AdminServiceController::class, 'index'])->name('admin.services');
Route::get('/admin/services/{id}', [AdminServiceController::class, 'view'])->name('admin.services.view');
Route::get('/admin/services/print/{id}', [AdminServiceController::class, 'print'])->name('admin.services.print');
Route::get('/admin/services/search', [AdminServiceController::class, 'search'])->name('admin.services.search');

});

// Staff routes
Route::prefix('staff')->group(function () {
    // Authentication
    Route::get('/login', [StaffController::class, 'showLoginForm'])->name('staff.login');
    Route::post('/login', [StaffController::class, 'login'])->name('staff.login.submit');
    Route::get('/register', [StaffController::class, 'showRegisterForm'])->name('staff.register');
    Route::post('/register', [StaffController::class, 'register'])->name('staff.register.submit');
    Route::get('/logout', [StaffController::class, 'showLogoutForm'])->name('staff.logout.form');
    Route::post('/logout', [StaffController::class, 'logout'])->name('staff.logout');

    Route::get('/dashboard', [ServicesController::class, 'dashboard'])->name('staff.dashboard');

    // Service management
    Route::get('/services', [ServicesController::class, 'index'])->name('staff.services.index');
    Route::get('/services/create', [ServicesController::class, 'create'])->name('staff.services.create');
    Route::post('/services', [ServicesController::class, 'store'])->name('staff.services.store');
    Route::get('/services/{id}', [ServicesController::class, 'show'])->name('staff.services.show');
    Route::get('/services/{id}/edit', [ServicesController::class, 'edit'])->name('staff.services.edit');
    Route::put('/services/{id}', [ServicesController::class, 'update'])->name('staff.services.update');
    Route::get('/services/{id}/delete', [ServicesController::class, 'delete'])->name('staff.services.delete');
    Route::delete('/services/{id}', [ServicesController::class, 'destroy'])->name('staff.services.destroy');

    // PDF and SMS
    Route::get('/services/{id}/pdf', [ServicesController::class, 'generatePdf'])->name('staff.services.generatePdf');
    Route::post('/services/{id}/send-sms', [ServicesController::class, 'sendSms'])->name('staff.services.send-sms');

    // Vehicle services
    Route::get('/vehicles/{customerId}/services', [ServicesController::class, 'vehicleServices'])->name('staff.services.vehicleServices');
    Route::get('/vehicles/fetch/{registration_no}', [ServicesController::class, 'fetchVehicle'])->name('staff.vehicles.fetch');

    // Service assignment 
    Route::post('/services/assign', [ServicesController::class, 'assignService'])->name('staff.services.assign');
    Route::get('/statistics', [ServicesController::class, 'getStaffStatistics'])->name('staff.statistics');
    Route::get('/debug-assignments', [ServicesController::class, 'debugAssignments'])->name('staff.debug.assignments');
});

Route::prefix('owner')->name('owner.')->group(function () {
    // 🔹 Authentication
    Route::get('/login', [OwnerController::class, 'showLoginRegister'])->name('login');
    Route::post('/register', [OwnerController::class, 'register'])->name('register.submit');
    Route::post('/login/submit', [OwnerController::class, 'login'])->name('login.submit');
    
    // Logout
    Route::match(['get', 'post'], '/logout', [OwnerController::class, 'logout'])->name('logout');

    // 🔹 Protected Route
        // 🔹 Dashboard
        Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('dashboard');

        // 🔹 Vehicles (owned vehicles)
        Route::get('/vehicles/create', [OwnerController::class, 'createVehicle'])->name('vehicles.create');
        Route::get('/vehicles', [OwnerController::class, 'vehicles'])->name('vehicles');
        Route::post('/vehicles', [OwnerController::class, 'storeVehicle'])->name('vehicles.store');
        Route::get('/vehicles/{id}/edit', [OwnerController::class, 'editVehicle'])->name('vehicles.edit');
        Route::put('/vehicles/{id}', [OwnerController::class, 'updateVehicle'])->name('vehicles.update');
        Route::delete('/vehicles/{id}', [OwnerController::class, 'destroyVehicle'])->name('vehicles.destroy');
        
        // 🔹 Vehicle check routes
        Route::post('/vehicles/check', [OwnerController::class, 'checkVehicle'])->name('vehicles.check');
        Route::get('/vehicles/check-existing', [OwnerController::class, 'checkExistingVehicle'])->name('vehicles.check-existing');

        // 🔹 Services (view all, details, print invoice)
        Route::get('/services', [OwnerController::class, 'services'])->name('services');
        Route::get('/services/{id}', [OwnerController::class, 'serviceShow'])->name('services.show');
        Route::get('/invoice/{id}/print', [OwnerController::class, 'printInvoice'])->name('invoice.print');

        // 🔹 Invoices List
        Route::get('/invoices', [OwnerController::class, 'invoices'])->name('invoices');

        // 🔹 Profile (view & update)
        Route::get('/profile', [OwnerController::class, 'profile'])->name('profile');
        Route::post('/profile', [OwnerController::class, 'profileUpdate'])->name('profile.update');
        
        // 🔹 Password Update
        Route::post('/password/update', [OwnerController::class, 'changePassword'])->name('password.update');
    });
