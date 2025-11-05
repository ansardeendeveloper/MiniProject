<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffRequest;
use App\Models\ServiceRecord;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class StaffController extends Controller
{
    public function showLoginForm()
    {
        return view('welcome');
    }
    public function showRegisterForm()
    {
        return view('welcome');
    }
    public function register(StaffRequest $request)
    {
        $validated = $request->all();
        $age = Carbon::parse($validated['date_of_birth'])->age;
        $staff = new Staff();
        $staff->name = $validated['name'];
        $staff->email = $validated['email'];
        $staff->address = $validated['address'];
        $staff->date_of_birth = $validated['date_of_birth'];
        $staff->age = $age;
        $staff->phone = $validated['phone'];
        $staff->role = 'staff';
        $staff->password = Hash::make($validated['password']);
        $staff->save();
        return redirect()->route('welcome')->with('success', 'Registration successful! You can now log in.');
    }
    public function login(Request $request)
    {
        $staff = Staff::where('email', $request->email)->first();
        if (!$staff || !Hash::check($request->password, $staff->password)) {
            return back()->with('error', 'Invalid credentials.');
        }
        Session::put('staff_id', $staff->id);
        Session::put('staff_name', $staff->name);
        return redirect()->route('staff.dashboard')->with('success', 'Login successful!');
    }
    public function dashboard()
    {
        $staffId = Session::get('staff_id');
        if (!$staffId) {
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
        $pendingServices = ServiceRecord::where('staff_id', $staffId)->where('status', 'pending')->count();
        $assignedServices = ServiceRecord::where('staff_id', $staffId)->where('status', 'assigned')->count();
        $completedServices = ServiceRecord::where('staff_id', $staffId)->where('status', 'completed')->count();
        $cancelledServices = ServiceRecord::where('staff_id', $staffId)->where('status', 'cancelled')->count();
        $recentServices = ServiceRecord::where('staff_id', $staffId)
            ->with('vehicle')
            ->latest()
            ->take(10)
            ->get();
        return view('staff.dashboard', compact(
            'pendingServices',
            'assignedServices',
            'completedServices',
            'cancelledServices',
            'recentServices'
        ));
    }
    public function servicesIndex(Request $request)
    {
        $staffId = Session::get('staff_id');
        if (!$staffId) {
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
        $query = ServiceRecord::where('staff_id', $staffId)->with('vehicle');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('job_id', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%")
                    ->orWhereHas('vehicle', function ($q) use ($search) {
                        $q->where('registration_no', 'LIKE', "%{$search}%");
                    });
            });
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        $services = $query->latest()->paginate(15);
        $stats = [
            'total' => ServiceRecord::where('staff_id', $staffId)->count(),
            'pending' => ServiceRecord::where('staff_id', $staffId)->where('status', 'pending')->count(),
            'assigned' => ServiceRecord::where('staff_id', $staffId)->where('status', 'assigned')->count(),
            'completed' => ServiceRecord::where('staff_id', $staffId)->where('status', 'completed')->count(),
            'cancelled' => ServiceRecord::where('staff_id', $staffId)->where('status', 'cancelled')->count(),
        ];
        return view('staff.services.index', compact('services', 'stats'));
    }
    public function servicesShow($id)
    {
        $staffId = Session::get('staff_id');
        if (!$staffId) {
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
        try {
            $service = ServiceRecord::where('staff_id', $staffId)
                ->with('vehicle.customer')
                ->findOrFail($id);
            return view('staff.services.show', compact('service'));
        } catch (\Exception $e) {
            return redirect()->route('staff.services.index')->with('error', 'Service not found.');
        }
    }
    public function servicesCreate()
    {
        $staffId = Session::get('staff_id');
        if (!$staffId) {
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
        return view('staff.services.create');
    }
    public function servicesStore(Request $request)
    {
        $staffId = Session::get('staff_id');
        if (!$staffId) {
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
        $pendingService = ServiceRecord::where('vehicle_id', $request->vehicle_id)
            ->whereIn('status', ['pending', 'assigned'])
            ->exists();
        if ($pendingService) {
            return back()->with('error', 'This vehicle already has a pending or assigned service.');
        }
        ServiceRecord::create([
            'vehicle_id' => $request->vehicle_id,
            'staff_id' => $staffId,
            'service_types' => json_encode($request->service_types),
            'amount' => $request->amount,
            'status' => 'pending',
            'service_start_datetime' => $request->service_start_datetime,
            'job_id' => 'JOB' . time() . rand(100, 999),
        ]);
        return redirect()->route('staff.services.index')->with('success', 'Service created successfully!');
    }
    public function servicesUpdateStatus(Request $request, $id)
    {
        $staffId = Session::get('staff_id');
        if (!$staffId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        try {
            $service = ServiceRecord::where('staff_id', $staffId)->findOrFail($id);
            $service->status = $request->status;
            if ($request->status === 'completed') {
                $service->service_end_datetime = now();
            }
            $service->save();
            return response()->json(['success' => 'Status updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Service not found.'], 404);
        }
    }
    public function logout()
    {
        Session::forget(['staff_id', 'staff_name']);
        return redirect()->route('welcome')->with('success', 'Logged out successfully.');
    }
    public function profile()
    {
        $staffId = Session::get('staff_id');
        if (!$staffId) {
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
        $staff = Staff::findOrFail($staffId);
        return view('staff.profile', compact('staff'));
    }
    public function profileUpdate(Request $request)
    {
        $staffId = Session::get('staff_id');
        if (!$staffId) {
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
        $staff = Staff::findOrFail($staffId);
        $staff->update($request->only(['name', 'email', 'phone', 'address']));
        if ($staff->name !== Session::get('staff_name')) {
            Session::put('staff_name', $staff->name);
        }
        return back()->with('success', 'Profile updated successfully!');
    }
}
