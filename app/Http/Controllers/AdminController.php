<?php
namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Staff;
use App\Models\Vehicle;
use App\Models\ServiceRecord;
use App\Models\Customer;
use App\Models\Owner;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    private $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }
    public function showLoginForm()
    {
        return view('welcome');
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $admin = Admin::where('email', $request->email)->first();
        if ($admin && Hash::check($request->password, $admin->password)) {
            Session::put('admin_id', $admin->id);
            Session::put('admin_name', $admin->name);
            Log::info("Admin logged in: {$admin->email}");
            return redirect()->route('admin.dashboard')->with('success', 'Welcome back, ' . $admin->name . '!');
        }
        return back()->withErrors(['login' => 'Invalid credentials'])->withInput();
    }
    public function logout()
    {
        $adminName = Session::get('admin_name');
        Session::forget(['admin_id', 'admin_name']);
        
        Log::info("Admin logged out: {$adminName}");
        return redirect()->route('welcome')->with('success', 'Logged out successfully');
    }
    public function dashboard()
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        try {
            $totalStaff = Staff::count();
            $totalVehicles = Vehicle::count();
            $totalServices = ServiceRecord::count();
            $totalCompletedServices = ServiceRecord::where('status', 'completed')->count();
            $totalPendingServices = ServiceRecord::where('status', 'pending')->count();
            $totalCancelledServices = ServiceRecord::where('status', 'cancelled')->count();
            $totalAmount = ServiceRecord::where('status', 'completed')->sum('amount');
            $recentServices = ServiceRecord::with('vehicle.customer')->latest()->limit(10)->get();
            $totalOwners = Owner::count();
            return view('admin.dashboard', compact(//compact use to create associate array
                'totalStaff',
                'totalVehicles',
                'totalServices',
                'totalCompletedServices',
                'totalPendingServices',
                'totalCancelledServices',
                'recentServices',
                'totalAmount',
                'totalOwners'
            ));
        } catch (\Exception $e) {
            Log::error('Admin dashboard error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to load dashboard']);
        }
    }
    public function vehicles(Request $request)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        try {
            $query = Vehicle::with('customer');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('registration_no', 'like', '%' . $search . '%')
                      ->orWhereHas('customer', function ($q) use ($search) {
                          $q->where('name', 'like', '%' . $search . '%');
                      });
                });
            }
            $vehicles = $query->orderBy('created_at', 'desc')->get();
            $totalVehicles = Vehicle::count();
            return view('admin.vehicles', compact('vehicles', 'totalVehicles'));
        } catch (\Exception $e) {
            Log::error('Admin vehicles error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to load vehicles']);
        }
    }
    public function viewVehicle($id)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        try {
            $vehicle = Vehicle::with('customer')->findOrFail($id);
            return view('admin.vehicle_view', compact('vehicle'));
        } catch (\Exception $e) {
            Log::error('Admin view vehicle error: ' . $e->getMessage());
            return redirect()->route('admin.vehicles')->withErrors(['error' => 'Vehicle not found']);
        }
    }
    public function staff(Request $request)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        try {
            $query = Staff::query();

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
            }
            $staff = $query->orderBy('created_at', 'desc')->get();
            $totalStaff = Staff::count();
            return view('admin.staff', compact('staff', 'totalStaff'));
        } catch (\Exception $e) {
            Log::error('Admin staff error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to load staff']);
        }
    }
    public function viewStaff($id)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        try {
            $staff = Staff::findOrFail($id);
            return view('admin.staff_view', compact('staff'));
        } catch (\Exception $e) {
            Log::error('Admin view staff error: ' . $e->getMessage());
            return redirect()->route('admin.staff')->withErrors(['error' => 'Staff not found']);
        }
    }
    public function showStaffRegister()
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        return view('admin.staff-register');
    }
    public function storeStaff(Request $request)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:500',
            'date_of_birth' => 'required|date|before:today',
            'role' => 'required|in:staff,senior_staff,manager',
            'password' => 'required|string|min:6|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        try {
            $age = Carbon::parse($request->date_of_birth)->age;
            $filename = null;
            if ($request->hasFile('image')) {
                $filename = time() . '_' . $request->file('image')->getClientOriginalName();
                $request->file('image')->storeAs('staff_images', $filename, 'public');
            }
            Staff::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'date_of_birth' => $request->date_of_birth,
                'age' => $age,
                'role' => $request->role,
                'password' => Hash::make($request->password),
                'image' => $filename,
            ]);
            Log::info("New staff created: {$request->name} by admin ID: " . Session::get('admin_id'));
            return redirect()->route('admin.staff')->with('success', 'Staff member registered successfully.');
        } catch (\Exception $e) {
            Log::error('Staff creation error: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to create staff: ' . $e->getMessage()]);
        }
    }
    public function editStaff($id)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        try {
            $staff = Staff::findOrFail($id);
            return view('admin.staff_edit', compact('staff'));
        } catch (\Exception $e) {
            Log::error('Staff edit error: ' . $e->getMessage());
            return redirect()->route('admin.staff')->withErrors(['error' => 'Staff not found']);
        }
    }
    public function updateStaff(Request $request, $id)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email,' . $id,
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:500',
            'date_of_birth' => 'required|date|before:today',
            'role' => 'required|in:staff,senior_staff,manager',
            'password' => 'nullable|string|min:6|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        try {
            $staff = Staff::findOrFail($id);
            $staff->name = $request->name;
            $staff->email = $request->email;
            $staff->phone = $request->phone;
            $staff->address = $request->address;
            $staff->date_of_birth = $request->date_of_birth;
            $staff->age = Carbon::parse($request->date_of_birth)->age;
            $staff->role = $request->role;
            if ($request->filled('password')) {
                $staff->password = Hash::make($request->password);
            }
            if ($request->hasFile('image')) {
                if ($staff->image && Storage::disk('public')->exists('staff_images/' . $staff->image)) {
                    Storage::disk('public')->delete('staff_images/' . $staff->image);
                }
                $filename = time() . '_' . $request->file('image')->getClientOriginalName();
                $request->file('image')->storeAs('staff_images', $filename, 'public');
                $staff->image = $filename;
            }
            $staff->save();
            Log::info("Staff updated: {$staff->name} by admin ID: " . Session::get('admin_id'));
            return redirect()->route('admin.staff.view', $id)->with('success', 'Staff updated successfully.');
        } catch (\Exception $e) {
            Log::error('Staff update error: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to update staff: ' . $e->getMessage()]);
        }
    }
    public function deleteStaff($id)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        try {
            $staff = Staff::findOrFail($id);
            $assignedServices = ServiceRecord::where('staff_id', $id)->count();
            if ($assignedServices > 0) {
                return redirect()->route('admin.staff')->withErrors([
                    'error' => 'Cannot delete staff member. They have ' . $assignedServices . ' assigned services.'
                ]);
            }
            if ($staff->image && Storage::disk('public')->exists('staff_images/' . $staff->image)) {
                Storage::disk('public')->delete('staff_images/' . $staff->image);
            }
            $staffName = $staff->name;
            $staff->delete();
            Log::info("Staff deleted: {$staffName} by admin ID: " . Session::get('admin_id'));
            return redirect()->route('admin.staff')->with('success', 'Staff deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Staff deletion error: ' . $e->getMessage());
            return redirect()->route('admin.staff')->withErrors(['error' => 'Failed to delete staff']);
        }
    }
    public function services(Request $request)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        try {
            $query = ServiceRecord::with(['vehicle.customer', 'staff']);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('job_id', 'like', '%' . $search . '%')
                      ->orWhere('status', 'like', '%' . $search . '%');
            }
            $services = $query->orderBy('created_at', 'desc')->get();
            $totalServices = ServiceRecord::count();
            $totalCompletedServices = ServiceRecord::where('status', 'completed')->count();
            $totalPendingServices = ServiceRecord::where('status', 'pending')->count();
            $totalCancelledServices = ServiceRecord::where('status', 'cancelled')->count();
            $totalRevenue = ServiceRecord::where('status', 'completed')->sum('amount');
            return view('admin.services', compact(
                'services',
                'totalServices',
                'totalCompletedServices',
                'totalPendingServices',
                'totalCancelledServices',
                'totalRevenue'
            ));
        } catch (\Exception $e) {
            Log::error('Admin services error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to load services']);
        }
    }
    public function viewService($id)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }

        try {
            $service = ServiceRecord::with(['vehicle.customer', 'staff'])->findOrFail($id);
            return view('admin.service_view', compact('service'));
        } catch (\Exception $e) {
            Log::error('Admin view service error: ' . $e->getMessage());
            return redirect()->route('admin.services')->withErrors(['error' => 'Service not found']);
        }
    }
    public function createServiceForm()
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        try {
            $vehicles = Vehicle::with('customer')->get();
            $staffMembers = Staff::all(); 
            return view('admin.service_create', compact('vehicles', 'staffMembers'));
        } catch (\Exception $e) {
            Log::error('Admin create service form error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to load service creation form']);
        }
    }
    public function createService(Request $request)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|exists:vehicles,id',
            'service_types' => 'required|array|min:1',
            'service_types.*' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0|max:999999.99',
            'service_start_datetime' => 'required|date|after_or_equal:today',
            'staff_id' => 'nullable|exists:staff,id',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        try {
            $start = Carbon::createFromFormat('Y-m-d\TH:i', $request->service_start_datetime, 'Asia/Kolkata');
            $formattedDate = $start->format('d/m/Y');

            $lastJob = ServiceRecord::whereDate('service_start_datetime', $start->toDateString())
                ->where('job_id', 'LIKE', "JOB NO:%/{$formattedDate}")
                ->orderBy('id', 'desc')
                ->first();
            $nextNumber = 1;
            if ($lastJob && preg_match('/JOB NO:(\d+)/', $lastJob->job_id, $matches)) {
                $nextNumber = (int)$matches[1] + 1;
            }
            $jobId = sprintf('JOB NO:%03d/%s', $nextNumber, $formattedDate);
            $service = ServiceRecord::create([
                'vehicle_id' => $request->vehicle_id,
                'staff_id' => $request->staff_id,
                'service_start_datetime' => $start,
                'service_end_datetime' => null,
                'service_types' => json_encode($request->service_types),
                'amount' => $request->amount,
                'status' => 'pending',
                'job_id' => $jobId,
            ]);

            // Send email notification
            $this->emailService->sendServiceCreatedNotification($service);

            Log::info("Service created by admin: {$jobId}");
            return redirect()->route('admin.services')
                ->with('success', "Service created successfully! Job ID: {$jobId}");

        } catch (\Exception $e) {
            Log::error('Admin create service error: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to create service: ' . $e->getMessage()]);
        }
    }
    public function updateServiceStatus(Request $request, $id)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,assigned,in_progress,completed,cancelled',
            'amount' => 'nullable|numeric|min:0',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        try {
            $service = ServiceRecord::with('vehicle.customer')->findOrFail($id);
            $oldStatus = $service->status;
            $updateData = ['status' => $request->status];  
            if ($request->has('amount')) {
                $updateData['amount'] = $request->amount;
            }
            if ($request->status === 'completed' && !$service->service_end_datetime) {
                $updateData['service_end_datetime'] = Carbon::now('Asia/Kolkata');
            }
            $service->update($updateData);
            if ($service->status === 'completed') {
                $this->emailService->sendServiceInvoice($service);
            } elseif ($service->status !== $oldStatus) {
                $this->emailService->sendServiceStatusUpdate($service, $oldStatus);
            }
            Log::info("Service status updated by admin: {$service->job_id} - {$oldStatus} to {$service->status}");
            return redirect()->route('admin.services.view', $id)
                ->with('success', 'Service status updated successfully!');
        } catch (\Exception $e) {
            Log::error('Admin update service status error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update service: ' . $e->getMessage()]);
        }
    }
    public function editService($id)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        try {
            $service = ServiceRecord::with(['vehicle.customer', 'staff'])->findOrFail($id);
            $staffMembers = Staff::all();
            
            return view('admin.service_edit', compact('service', 'staffMembers'));
        } catch (\Exception $e) {
            Log::error('Admin edit service error: ' . $e->getMessage());
            return redirect()->route('admin.services')->withErrors(['error' => 'Service not found']);
        }
    }
    public function updateService(Request $request, $id)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        $validator = Validator::make($request->all(), [
            'service_types' => 'required|array|min:1',
            'service_types.*' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0|max:999999.99',
            'service_start_datetime' => 'required|date',
            'service_end_datetime' => 'nullable|date|after:service_start_datetime',
            'status' => 'required|in:pending,assigned,in_progress,completed,cancelled',
            'staff_id' => 'nullable|exists:staff,id',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        try {
            $service = ServiceRecord::with('vehicle.customer')->findOrFail($id);
            $oldStatus = $service->status;
            $start = Carbon::createFromFormat('Y-m-d\TH:i', $request->service_start_datetime, 'Asia/Kolkata');
            $end = $request->service_end_datetime ? 
                   Carbon::createFromFormat('Y-m-d\TH:i', $request->service_end_datetime, 'Asia/Kolkata') : 
                   null;
            if ($request->status === 'completed' && !$end) {
                $end = Carbon::now('Asia/Kolkata');
            }
            $service->update([
                'staff_id' => $request->staff_id,
                'service_start_datetime' => $start,
                'service_end_datetime' => $end,
                'service_types' => json_encode($request->service_types),
                'amount' => $request->amount,
                'status' => $request->status,
            ]);
            if ($service->status === 'completed') {
                $this->emailService->sendServiceInvoice($service);
            } elseif ($service->status !== $oldStatus) {
                $this->emailService->sendServiceStatusUpdate($service, $oldStatus);
            }
            Log::info("Service updated by admin: {$service->job_id}");
            return redirect()->route('admin.services.view', $id)
                ->with('success', 'Service updated successfully!');
        } catch (\Exception $e) {
            Log::error('Admin update service error: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to update service: ' . $e->getMessage()]);
        }
    }
    public function deleteService($id)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        try {
            $service = ServiceRecord::findOrFail($id);
            $jobId = $service->job_id;
            $service->delete();
            Log::info("Service deleted by admin: {$jobId}");
            return redirect()->route('admin.services')->with('success', 'Service deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Admin delete service error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete service']);
        }
    }
    public function owners(Request $request)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        try {
            $query = Owner::query();

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%')
                      ->orWhere('phone', 'like', '%' . $search . '%');
            }
            $owners = $query->latest()->get();
            $totalOwners = Owner::count();
            return view('admin.owners', compact('owners', 'totalOwners'));
        } catch (\Exception $e) {
            Log::error('Admin owners error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to load owners']);
        }
    }
    public function viewOwner($id)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        try {
            $owner = Owner::findOrFail($id);
            return view('admin.owner_view', compact('owner'));
        } catch (\Exception $e) {
            Log::error('Admin view owner error: ' . $e->getMessage());
            return redirect()->route('admin.owners')->withErrors(['error' => 'Owner not found']);
        }
    }
    public function deleteOwner($id)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        try {
            $owner = Owner::findOrFail($id);
            $vehicle = Vehicle::where('registration_no', $owner->vehicle_number)->first();
            if ($vehicle) {
                return redirect()->route('admin.owners')->withErrors([
                    'error' => 'Cannot delete owner. Vehicle with registration ' . $owner->vehicle_number . ' exists in the system.'
                ]);
            }
            $ownerName = $owner->name;
            $owner->delete();
            Log::info("Owner deleted by admin: {$ownerName}");
            return redirect()->route('admin.owners')->with('success', 'Owner deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Admin delete owner error: ' . $e->getMessage());
            return redirect()->route('admin.owners')->withErrors(['error' => 'Failed to delete owner']);
        }
    }
    public function reports(Request $request)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        try {
            $validator = Validator::make($request->all(), [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'staff_id' => 'nullable|exists:staff,id',
                'status' => 'nullable|in:pending,assigned,in_progress,completed,cancelled',
                'search' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $staffId = $request->input('staff_id');
            $status = $request->input('status');
            $search = $request->input('search');
            $query = ServiceRecord::with(['vehicle.customer', 'staff']);
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            } else {
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
            }
            if ($staffId && $staffId !== 'all') {
                $query->where('staff_id', $staffId);
            }
            if ($status && $status !== 'all') {
                $query->where('status', $status);
            }
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('job_id', 'like', "%{$search}%")
                      ->orWhereHas('vehicle', function ($q) use ($search) {
                          $q->where('registration_no', 'like', "%{$search}%")
                            ->orWhere('model', 'like', "%{$search}%");
                      })
                      ->orWhereHas('staff', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                });
            }
            $reports = $query->orderBy('created_at', 'desc')->paginate(15);
            $totalReports = (clone $query)->count();
            $completed = (clone $query)->where('status', 'completed')->count();
            $pending = (clone $query)->where('status', 'pending')->count();
            $cancelled = (clone $query)->where('status', 'cancelled')->count();
            $totalRevenue = (clone $query)->where('status', 'completed')->sum('amount');
            $staffList = Staff::select('id', 'name')->orderBy('name')->get();
            return view('admin.reports', compact(
                'reports','totalReports','completed','pending','cancelled','totalRevenue','staffList','startDate','endDate','staffId','status','search'
            ));
        } catch (\Exception $e) {
            Log::error('Admin reports error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to load reports']);
        }
    }
    public function reportsOverview(Request $request)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        try {
            $period = $request->input('period', 'month');
            $staffId = $request->input('staff_id');
            $search = $request->input('search');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $query = ServiceRecord::with(['vehicle.customer', 'staff']);
            if ($period === 'day') {
                $query->whereDate('created_at', Carbon::today('Asia/Kolkata'));
            } elseif ($period === 'week') {
                $query->whereBetween('created_at', [
                    Carbon::now('Asia/Kolkata')->startOfWeek(),
                    Carbon::now('Asia/Kolkata')->endOfWeek()
                ]);
            } elseif ($period === 'month') {
                $query->whereMonth('created_at', Carbon::now('Asia/Kolkata')->month)
                    ->whereYear('created_at', Carbon::now('Asia/Kolkata')->year);
            } elseif ($period === 'year') {
                $query->whereYear('created_at', Carbon::now('Asia/Kolkata')->year);
            }
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [
                    Carbon::parse($startDate, 'Asia/Kolkata')->startOfDay(),
                    Carbon::parse($endDate, 'Asia/Kolkata')->endOfDay()
                ]);
            }
            if ($staffId) {
                $query->where('staff_id', $staffId);
            }
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('job_id', 'LIKE', "%{$search}%")
                      ->orWhereHas('vehicle', function ($q) use ($search) {
                          $q->where('registration_no', 'LIKE', "%{$search}%")
                            ->orWhere('model', 'LIKE', "%{$search}%");
                      })
                      ->orWhereHas('staff', function ($q) use ($search) {
                          $q->where('name', 'LIKE', "%{$search}%");
                      });
                });
            }
            $reports = $query->latest()->paginate(15);
            $staffList = Staff::all();
            return view('admin.reports-overview', compact(
                'reports',
                'staffList',
                'period',
                'staffId',
                'search',
                'startDate',
                'endDate'
            ));
        } catch (\Exception $e) {
            Log::error('Admin reports overview error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to load reports']);
        }
    }
    public function exportReportsPdf(Request $request)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        try {
            $query = ServiceRecord::with(['vehicle.customer', 'staff']);
            $period = $request->input('period', 'month');
            $staffId = $request->input('staff_id');
            $search = $request->input('search');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            if ($period === 'day') {
                $query->whereDate('created_at', Carbon::today('Asia/Kolkata'));
            } elseif ($period === 'week') {
                $query->whereBetween('created_at', [
                    Carbon::now('Asia/Kolkata')->startOfWeek(),
                    Carbon::now('Asia/Kolkata')->endOfWeek()
                ]);
            } elseif ($period === 'month') {
                $query->whereMonth('created_at', Carbon::now('Asia/Kolkata')->month)
                    ->whereYear('created_at', Carbon::now('Asia/Kolkata')->year);
            } elseif ($period === 'year') {
                $query->whereYear('created_at', Carbon::now('Asia/Kolkata')->year);
            }
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [
                    Carbon::parse($startDate, 'Asia/Kolkata')->startOfDay(),
                    Carbon::parse($endDate, 'Asia/Kolkata')->endOfDay()
                ]);
            }
            if ($staffId) {
                $query->where('staff_id', $staffId);
            }
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('job_id', 'LIKE', "%{$search}%")
                      ->orWhereHas('vehicle', function ($q) use ($search) {
                          $q->where('registration_no', 'LIKE', "%{$search}%");
                      });
                });
            }
            $reports = $query->latest()->get();
            $pdf = Pdf::loadView('admin.reports_pdf', [
                'reports' => $reports,
                'period' => $period,
                'staffId' => $staffId,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'totalAmount' => $reports->sum('amount'),
                'totalReports' => $reports->count(),
            ]);
            return $pdf->download('reports_' . now('Asia/Kolkata')->format('Y_m_d_H_i') . '.pdf');
        } catch (\Exception $e) {
            Log::error('Admin export reports PDF error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to export reports']);
        }
    }
    public function exportReportPdf($id)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        try {
            $report = ServiceRecord::with(['vehicle.customer', 'staff'])->findOrFail($id);
            $pdf = Pdf::loadView('admin.single_report_pdf', compact('report'));
            return $pdf->download('report_' . $report->job_id . '_' . now('Asia/Kolkata')->format('Y_m_d') . '.pdf');
        } catch (\Exception $e) {
            Log::error('Admin export report PDF error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Report not found']);
        }
    }
    public function fetchVehicle($reg)
    {
        if (!Session::has('admin_id')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }
        try {
            $reg = strtoupper(trim($reg));
            $vehicle = Vehicle::where('registration_no', $reg)->with('customer')->first();
            if (!$vehicle) {
                return response()->json(['status' => 'error', 'message' => 'Vehicle not found']);
            }
            $service = ServiceRecord::where('vehicle_id', $vehicle->id)->where('status', 'pending')->first();
            if ($service) {
                return response()->json(['status' => 'pending', 'message' => 'Vehicle has a pending service']);
            }
            return response()->json([
                'status' => 'success',
                'vehicle_id' => $vehicle->id,
                'model' => $vehicle->model,
                'manufacturer' => $vehicle->manufacturer,
                'year' => $vehicle->year,
                'customer_name' => $vehicle->customer->name ?? 'Unknown',
                'mobile_number' => $vehicle->customer->mobile_number ?? 'N/A',
            ]);
        } catch (\Exception $e) {
            Log::error('Admin fetch vehicle error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to fetch vehicle'], 500);
        }
    }
    public function viewStaffReport($staffId)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        try {
            $staff = Staff::findOrFail($staffId);
            $services = ServiceRecord::where('staff_id', $staffId)
                ->with('vehicle.customer')
                ->orderBy('created_at', 'desc')
                ->get();

            return view('admin.staff_report', compact('staff', 'services'));
        } catch (\Exception $e) {
            Log::error('Admin view staff report error: ' . $e->getMessage());
            return redirect()->route('admin.reports')->withErrors(['error' => 'Staff not found']);
        }
    }
    public function searchServices(Request $request)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        try {
            $search = $request->input('search');
            $services = ServiceRecord::with('vehicle.customer', 'staff')
                ->where('job_id', 'like', "%{$search}%")
                ->orWhereHas('vehicle', function ($q) use ($search) {
                    $q->where('registration_no', 'like', "%{$search}%");
                })
                ->orWhereHas('staff', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->get();
            $totalServices = ServiceRecord::count();
            $totalCompletedServices = ServiceRecord::where('status', 'completed')->count();
            $totalPendingServices = ServiceRecord::where('status', 'pending')->count();
            $totalCancelledServices = ServiceRecord::where('status', 'cancelled')->count();
            $totalRevenue = ServiceRecord::where('status', 'completed')->sum('amount');
            return view('admin.services', compact(
                'services','totalServices','totalCompletedServices','totalPendingServices','totalCancelledServices','totalRevenue'
            ));
        } catch (\Exception $e) {
            Log::error('Admin search services error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to search services']);
        }
    }
    public function generateServiceInvoice($id)
    {
        if (!Session::has('admin_id')) {
            return redirect()->route('welcome')->withErrors(['login' => 'Please log in first']);
        }
        try {
            $service = ServiceRecord::with('vehicle.customer', 'staff')->findOrFail($id);
            $service->service_types_array = json_decode($service->service_types, true) ?: [];
            $pdf = Pdf::loadView('admin.service_invoice_pdf', compact('service'));
            $pdf->setPaper('A4', 'portrait');
            $filename = 'Invoice_' . ($service->job_id ?? $service->id) . '_' . Carbon::now('Asia/Kolkata')->format('Ymd') . '.pdf';
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Admin generate invoice error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to generate invoice']);
        }
    }
}