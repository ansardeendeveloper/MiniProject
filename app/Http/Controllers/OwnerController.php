<?php

namespace App\Http\Controllers;

use App\Http\Requests\OwnerRequest;
use App\Models\Owner;
use App\Models\ServiceRecord;
use App\Models\Vehicle;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class OwnerController extends Controller
{
    public function showLoginRegister()
    {
        return view('welcome');
    }
    
   public function register(OwnerRequest $request)
    {
        $validated = $request->validated();
        $existingOwner = Owner::where('email', $validated['email'])->first();
        if ($existingOwner) {
            return back()->with('error', 'Email already registered. Please use a different email.');
        }
        $existingPhone = Owner::where('phone', $validated['phone'])->first();
        if ($existingPhone) {
            return back()->with('error', 'Mobile number already registered. Please use a different number.');
        }

        Owner::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'vehicle_number' => strtoupper($validated['vehicle_number']),
            'address' => $validated['address'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('welcome')->with('success', 'Registration successful! You can now log in.');
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        $owner = Owner::where('email', $request->email)->first();
        if (!$owner || !Hash::check($request->password, $owner->password)) {
            return back()->with('error', 'Invalid credentials.');
        }
        Session::put('owner_id', $owner->id);
        Session::put('owner_name', $owner->name);
        Session::put('owner_email', $owner->email);
        Session::put('owner_vehicle_number', $owner->vehicle_number);
        return redirect()->route('owner.dashboard')->with('success', 'Login successful!');
    }
    public function dashboard()
    {
        $ownerId = Session::get('owner_id');
        $ownerVehicleNumber = Session::get('owner_vehicle_number');

        if (!$ownerId || !$ownerVehicleNumber) {
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
        $owner = Owner::findOrFail($ownerId);
        $vehicle = Vehicle::where('registration_no', $ownerVehicleNumber)->first();
        if (!$vehicle) {
            return view('owner.dashboard', [
                'owner' => $owner,
                'totalServices' => 0,
                'completedServices' => 0,
                'cancelledServices' => 0,
                'totalAmountPaid' => 0,
                'recentServices' => collect(),
                'vehicle' => null,
            ]);
        }

        // Get statistics
        $totalServices = ServiceRecord::where('vehicle_id', $vehicle->id)->count();
        $completedServices = ServiceRecord::where('vehicle_id', $vehicle->id)
            ->where('status', 'completed')->count();
        $cancelledServices = ServiceRecord::where('vehicle_id', $vehicle->id)
            ->where('status', 'cancelled')->count();
        $totalAmountPaid = ServiceRecord::where('vehicle_id', $vehicle->id)
            ->where('status', 'completed')->sum('amount');

        // Get recent services
        $recentServices = ServiceRecord::where('vehicle_id', $vehicle->id)
            ->with(['vehicle'])
            ->latest()
            ->take(10)
            ->get();

        return view('owner.dashboard', compact(
            'owner',
            'vehicle',
            'totalServices',
            'completedServices',
            'cancelledServices',
            'totalAmountPaid',
            'recentServices'
        ));
    }
    public function services(Request $request)
    {
        $ownerId = Session::get('owner_id');
        $ownerVehicleNumber = Session::get('owner_vehicle_number');
        if (!$ownerId || !$ownerVehicleNumber) {
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
        $vehicle = Vehicle::where('registration_no', $ownerVehicleNumber)->first();
        if (!$vehicle) {
            $services = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            return view('owner.services', compact('services'));
        }
        $query = ServiceRecord::where('vehicle_id', $vehicle->id)
            ->with(['vehicle'])
            ->latest();
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        $services = $query->paginate(10);
        return view('owner.services', compact('services'));
    }
    public function serviceShow($id)
    {
        $ownerId = Session::get('owner_id');
        $ownerVehicleNumber = Session::get('owner_vehicle_number');
        if (!$ownerId || !$ownerVehicleNumber) {
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
        $vehicle = Vehicle::where('registration_no', $ownerVehicleNumber)->first();
        if (!$vehicle) {
            return redirect()->route('owner.services')->with('error', 'Vehicle not found.');
        }
        $service = ServiceRecord::where('vehicle_id', $vehicle->id)
            ->with(['vehicle'])
            ->findOrFail($id);
        return view('owner.service-show', compact('service'));
    }
    public function invoices(Request $request)
    {
        $ownerId = Session::get('owner_id');
        $ownerVehicleNumber = Session::get('owner_vehicle_number');
        if (!$ownerId || !$ownerVehicleNumber) {
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
        $vehicle = Vehicle::where('registration_no', $ownerVehicleNumber)->first();
        if (!$vehicle) {
            $invoices = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            return view('owner.invoices', compact('invoices'));
        }
        $query = ServiceRecord::where('vehicle_id', $vehicle->id)
            ->with(['vehicle'])
            ->whereIn('status', ['completed', 'cancelled'])
            ->latest();
        if ($request->has('payment_status') && $request->payment_status != '') {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        $invoices = $query->paginate(10);
        return view('owner.invoices', compact('invoices'));
    }
    public function printInvoice($id)
    {
        $ownerId = Session::get('owner_id');
        $ownerVehicleNumber = Session::get('owner_vehicle_number');

        if (!$ownerId || !$ownerVehicleNumber) {
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
        $vehicle = Vehicle::where('registration_no', $ownerVehicleNumber)->first();
        if (!$vehicle) {
            return redirect()->route('owner.invoices')->with('error', 'Vehicle not found.');
        }
        $service = ServiceRecord::where('vehicle_id', $vehicle->id)
            ->with(['vehicle'])
            ->findOrFail($id);
        return view('owner.invoice-print', compact('service'));
    }
    public function downloadInvoice($id)
    {
        $ownerId = Session::get('owner_id');
        $ownerVehicleNumber = Session::get('owner_vehicle_number');
        if (!$ownerId || !$ownerVehicleNumber) {
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
        $vehicle = Vehicle::where('registration_no', $ownerVehicleNumber)->first();
        if (!$vehicle) {
            return redirect()->route('owner.invoices')->with('error', 'Vehicle not found.');
        }
        $service = ServiceRecord::where('vehicle_id', $vehicle->id)
            ->with(['vehicle'])
            ->findOrFail($id);
         Carbon::setTimezone('Asia/Kolkata');
        $pdf = PDF::loadView('owner.invoice-pdf', compact('service'));
        $filename = "invoice-{$service->job_id}-" . Carbon::now()->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }
    public function viewInvoice($id)
    {
        $ownerId = Session::get('owner_id');
        $ownerVehicleNumber = Session::get('owner_vehicle_number');
        if (!$ownerId || !$ownerVehicleNumber) {
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
        $vehicle = Vehicle::where('registration_no', $ownerVehicleNumber)->first();
        if (!$vehicle) {
            return redirect()->route('owner.invoices')->with('error', 'Vehicle not found.');
        }
        $service = ServiceRecord::where('vehicle_id', $vehicle->id)
            ->with(['vehicle'])
            ->findOrFail($id);
        return view('owner.invoice-view', compact('service'));
    }
    public function vehicles()
    {
        $ownerId = Session::get('owner_id');
        $ownerVehicleNumber = Session::get('owner_vehicle_number');
        if (!$ownerId || !$ownerVehicleNumber) {
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
        $owner = Owner::findOrFail($ownerId);
        $vehicle = Vehicle::where('registration_no', $ownerVehicleNumber)->first();
        $vehicles = collect();
        if ($vehicle) {
            $vehicles = collect([$vehicle]);
            $vehicle->services_count = ServiceRecord::where('vehicle_id', $vehicle->id)->count();
            $vehicle->completed_services = ServiceRecord::where('vehicle_id', $vehicle->id)
                ->where('status', 'completed')->count();
            $vehicle->pending_services = ServiceRecord::where('vehicle_id', $vehicle->id)
                ->where('status', 'pending')->count();
        }
        return view('owner.vehicles', compact('vehicles', 'owner'));
    }
    public function createVehicle()
    {
        $ownerId = Session::get('owner_id');
        if (!$ownerId) {
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
        $owner = Owner::findOrFail($ownerId);
        $existingVehicle = Vehicle::where('registration_no', $owner->vehicle_number)->first();
        if ($existingVehicle) {
            return redirect()->route('owner.vehicles')->with('error', 'You already have a registered vehicle.');
        }
        return view('owner.vehicles.create', compact('owner'));
    }
    public function storeVehicle(Request $request)
    {
        $ownerId = Session::get('owner_id');
        $ownerVehicleNumber = Session::get('owner_vehicle_number');
        if (!$ownerId || !$ownerVehicleNumber) {
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
        $owner = Owner::findOrFail($ownerId);
        $existingVehicle = Vehicle::where('registration_no', $ownerVehicleNumber)->first();
        if ($existingVehicle) {
            return redirect()->route('owner.vehicles')->with('error', 'Vehicle already registered.');
        }
        $request->validate([
            'model' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'year' => 'required|integer|min:1990|max:' . date('Y'),
        ]);
        try {
            $customer = Customer::where('mobile_number', $owner->phone)->first(); 
            if (!$customer) {
                $customer = Customer::create([
                    'name' => $owner->name,
                    'email' => $owner->email,
                    'mobile_number' => $owner->phone,
                ]);
            }
            $vehicleData = [
                'registration_no' => $ownerVehicleNumber,
                'model' => $request->model,
                'manufacturer' => $request->manufacturer,
                'year' => $request->year,
                'customer_id' => $customer->id,
            ];
            if (Schema::hasColumn('vehicles', 'owner_id')) {
                $vehicleData['owner_id'] = $ownerId;
            }
            Vehicle::create($vehicleData);
            return redirect()->route('owner.vehicles')->with('success', 'Vehicle registered successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error registering vehicle: ' . $e->getMessage())
                ->withInput();
        }
    }
    public function checkExistingVehicle()
    {
        $ownerId = Session::get('owner_id');
        if (!$ownerId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $ownerVehicleNumber = Session::get('owner_vehicle_number');
        $existingVehicle = Vehicle::where('registration_no', $ownerVehicleNumber)->first();   
        return response()->json([
            'has_vehicle' => $existingVehicle ? true : false
        ]);
    }
    public function checkVehicle(Request $request)
    {
        $ownerId = Session::get('owner_id');
        if (!$ownerId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $vehicleNumber = $request->vehicle_number;
        $existingVehicle = Vehicle::where('registration_no', $vehicleNumber)->first();
        return response()->json([
            'exists' => $existingVehicle ? true : false
        ]);
    }
    public function editVehicle($id)
    {
        $ownerId = Session::get('owner_id');
        $ownerVehicleNumber = Session::get('owner_vehicle_number');
        if (!$ownerId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $vehicle = Vehicle::where('registration_no', $ownerVehicleNumber)->findOrFail($id);
        return response()->json($vehicle);
    }
    public function updateVehicle(Request $request, $id)
    {
        $ownerId = Session::get('owner_id');
        $ownerVehicleNumber = Session::get('owner_vehicle_number');
        if (!$ownerId || !$ownerVehicleNumber) {
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
        $vehicle = Vehicle::where('registration_no', $ownerVehicleNumber)->findOrFail($id);
        $request->validate([
            'model' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'year' => 'required|integer|min:1990|max:' . date('Y'),
        ]);
        $vehicle->update([
            'model' => $request->model,
            'manufacturer' => $request->manufacturer,
            'year' => $request->year,
        ]);
        return redirect()->route('owner.vehicles')->with('success', 'Vehicle updated successfully!');
    }
    public function destroyVehicle($id)
    {
        $ownerId = Session::get('owner_id');
        $ownerVehicleNumber = Session::get('owner_vehicle_number');
        if (!$ownerId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $vehicle = Vehicle::where('registration_no', $ownerVehicleNumber)->findOrFail($id);
        if ($vehicle->services()->exists()) {
            return response()->json(['error' => 'Cannot delete vehicle with existing services.'], 422);
        }
        $vehicle->delete();
        return response()->json(['success' => 'Vehicle deleted successfully!']);
    }
    public function profile()
    {
        $ownerId = Session::get('owner_id');
        if (!$ownerId) {
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
        $owner = Owner::findOrFail($ownerId);
        $vehicle = Vehicle::where('registration_no', $owner->vehicle_number)->first();
        if ($vehicle) {
            $owner->vehicles_count = 1;
            $owner->services_count = ServiceRecord::where('vehicle_id', $vehicle->id)->count();
            $owner->completed_services_count = ServiceRecord::where('vehicle_id', $vehicle->id)
                ->where('status', 'completed')->count();
        } else {
            $owner->vehicles_count = 0;
            $owner->services_count = 0;
            $owner->completed_services_count = 0;
        }
        return view('owner.profile', compact('owner'));
    }
    public function profileUpdate(Request $request)
    {
        $ownerId = Session::get('owner_id');
        if (!$ownerId) {
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
        $owner = Owner::findOrFail($ownerId);
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
        ]);
        $owner->update($request->only([
            'name', 'phone', 'date_of_birth', 'address', 
            'city', 'state', 'zip_code', 'country'
        ]));
        Session::put('owner_name', $owner->name);
        return back()->with('success', 'Profile updated successfully!');
    }
    public function changePassword(Request $request)
    {
        $ownerId = Session::get('owner_id');
        if (!$ownerId) {
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);
        $owner = Owner::findOrFail($ownerId);
        if (!Hash::check($request->current_password, $owner->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }
        $owner->update([
            'password' => Hash::make($request->new_password)
        ]);
        return back()->with('success', 'Password changed successfully!');
    }
    public function logout()
    {
        Session::forget(['owner_id', 'owner_name', 'owner_email', 'owner_vehicle_number']);
        return redirect()->route('welcome')->with('success', 'Logged out successfully.');
    }
}