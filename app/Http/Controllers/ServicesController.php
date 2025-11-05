<?php
namespace App\Http\Controllers;
use App\Models\ServiceRecord;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\Staff;
use App\Models\Owner;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class ServicesController extends Controller
{
    private $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }
private function getStaffId()
    {
        $staffId = session('staff_id');
        if (!$staffId) {
            throw new \Exception('Unauthorized access. Please login.');
        }
        return $staffId;
    }
    public function dashboard()
    {
        try {
            // Get logged-in staff ID from session helper
            $staffId = $this->getStaffId();

            if (!$staffId) {
                Log::warning("Dashboard accessed without valid session.");
                return redirect()->route('welcome')->with('error', 'Please login first.');
            }

            Log::info("Loading dashboard for staff ID: {$staffId}");

            // ✅ Count only services created by this staff
            $pendingServices = ServiceRecord::where('staff_id', $staffId)
                ->where('status', 'pending')
                ->count();

            $completedServices = ServiceRecord::where('staff_id', $staffId)
                ->where('status', 'completed')
                ->count();

            $cancelledServices = ServiceRecord::where('staff_id', $staffId)
                ->where('status', 'cancelled')
                ->count();

            // ✅ Services assigned to this staff
            $assignedToMe = ServiceRecord::where('staff_id', $staffId)->count();

            // ✅ Only this staff's total services
            $totalServices = ServiceRecord::where('staff_id', $staffId)->count();

            Log::info("Assigned to me: {$assignedToMe}, Total services: {$totalServices}");

            // ✅ Fetch only the recent services created by this staff
            $recentServices = ServiceRecord::with(['vehicle.customer', 'staff'])
                ->where('staff_id', $staffId)
                ->latest()
                ->limit(10)
                ->get();

            return view('staff.dashboard', compact(
                'pendingServices',
                'completedServices',
                'cancelledServices',
                'assignedToMe',
                'totalServices',
                'recentServices'
            ));

        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
    }
    public function assignService(Request $request)
    {
        try {
            $currentStaffId = $this->getStaffId();
            
            $request->validate([
                'service_id' => 'required|exists:service_records,id',
                'staff_id' => 'required|exists:staff,id',
                'assignment_notes' => 'nullable|string|max:500',
            ]);

            $service = ServiceRecord::with('vehicle.customer')->findOrFail($request->service_id);
            
            if ($service->staff_id && $service->staff_id != $currentStaffId) {
                return response()->json([
                    'success' => false,
                    'message' => 'This service is already assigned to another staff member.'
                ], 422);
            }

            $service->update([
                'staff_id' => $request->staff_id,
                'status' => 'assigned',
                'assignment_notes' => $request->assignment_notes,
                'assigned_at' => now(),
                'assigned_by' => $currentStaffId,
            ]);

            $assignedStaff = Staff::find($request->staff_id);
            Log::info("Service {$service->job_id} assigned to {$assignedStaff->name} by staff ID {$currentStaffId}");

            return response()->json([
                'success' => true, 
                'message' => 'Service assigned successfully!',
                'job_id' => $service->job_id,
                'assigned_to' => $assignedStaff->name
            ]);
            
        } catch (\Exception $e) {
            Log::error('Service assignment failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Assignment failed: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getStaffStatistics(Request $request)
    {
        try {
            $request->validate([
                'staff_id' => 'required|exists:staff,id'
            ]);

            $today = Carbon::today();
            
            $pending = ServiceRecord::whereDate('created_at', $today)
                ->where('staff_id', $request->staff_id)
                ->where('status', 'pending')
                ->count();

            $completed = ServiceRecord::whereDate('created_at', $today)
                ->where('staff_id', $request->staff_id)
                ->where('status', 'completed')
                ->count();

            $assigned = ServiceRecord::whereDate('created_at', $today)
                ->where('staff_id', $request->staff_id)
                ->where('status', 'assigned')
                ->count();

            return response()->json([
                'pending' => $pending,
                'completed' => $completed,
                'assigned' => $assigned
            ]);
            
        } catch (\Exception $e) {
            Log::error('Staff statistics failed: ' . $e->getMessage());
            return response()->json([
                'pending' => 0,
                'completed' => 0,
                'assigned' => 0
            ], 500);
        }
    }
    public function index()
    {
        try {
            $staffId = $this->getStaffId();
            $services = ServiceRecord::where('staff_id', $staffId)
                ->with('vehicle.customer')
                ->orderBy('id', 'desc')
                ->paginate(10);

            return view('staff.services.index', compact('services', 'staffId'));
        } catch (\Exception $e) {
            return redirect()->route('welcome')->with('error', 'Please login first.');
        }
    }
    public function create()
    {
        try {
            $this->getStaffId();
            return view('staff.services.create');
        } catch (\Exception $e) {
            return redirect()->route('welcome')->with('error', 'Please login.');
        }
    }
    public function fetchVehicle($reg)
    {
        $reg = strtoupper(trim($reg));
        
        // First check in owners table
        $owner = Owner::where('vehicle_number', $reg)->first();

        if ($owner) {
            // Check if vehicle exists in vehicles table
            $vehicle = Vehicle::where('registration_no', $reg)->with('customer')->first();
            
            if (!$vehicle) {
                return response()->json([
                    'status' => 'owner_found',
                    'message' => 'Vehicle found in owners database. Please verify details.',
                    'owner_name' => $owner->name,
                    'email' => $owner->email,
                    'phone' => $owner->phone,
                    'address' => $owner->address
                ]);
            }

            // Check for pending services
            $pending = ServiceRecord::where('vehicle_id', $vehicle->id)
                ->where('status', 'pending')
                ->exists();

            if ($pending) {
                return response()->json([
                    'status' => 'pending',
                    'message' => 'This vehicle already has a pending service.',
                ]);
            }

            return response()->json([
                'status' => 'success',
                'vehicle_id' => $vehicle->id,
                'model' => $vehicle->model,
                'manufacturer' => $vehicle->manufacturer,
                'year' => $vehicle->year,
                'customer_name' => $vehicle->customer->name ?? $owner->name,
                'mobile_number' => $vehicle->customer->mobile_number ?? $owner->phone,
                'email' => $vehicle->customer->email ?? $owner->email,
            ]);
        }

        // If not in owners table, check vehicles table
        $vehicle = Vehicle::where('registration_no', $reg)->with('customer')->first();

        if (!$vehicle) {
            return response()->json([
                'status' => 'new',
                'message' => 'Vehicle not found. Please enter all required details.',
            ]);
        }

        $pending = ServiceRecord::where('vehicle_id', $vehicle->id)
            ->where('status', 'pending')
            ->exists();

        if ($pending) {
            return response()->json([
                'status' => 'pending',
                'message' => 'This vehicle already has a pending service.',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'vehicle_id' => $vehicle->id,
            'model' => $vehicle->model,
            'manufacturer' => $vehicle->manufacturer,
            'year' => $vehicle->year,
            'customer_name' => $vehicle->customer->name ?? '',
            'mobile_number' => $vehicle->customer->mobile_number ?? '',
            'email' => $vehicle->customer->email ?? '',
        ]);
    }
    public function store(Request $request)
    {
        try {
            $staffId = $this->getStaffId();
            $data = $request->all();
            $data['staff_id'] = $staffId;
            $data['service_types'] = json_encode($data['service_types']);

            $start = Carbon::createFromFormat('Y-m-d\TH:i', $data['service_start_datetime'], 'Asia/Kolkata');
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

            if (empty($data['vehicle_id'])) {
                $customer = Customer::firstOrCreate(
                    ['mobile_number' => $data['mobile_number']],
                    [
                        'name' => $data['customer_name'],
                        'email' => $data['email'] ?? null
                    ]
                );

                $vehicle = Vehicle::create([
                    'registration_no' => strtoupper($data['vehicle_number']),
                    'model' => $data['vehicle_name'],
                    'manufacturer' => $data['manufacturer'],
                    'year' => $data['year'],
                    'customer_id' => $customer->id,
                ]);
                $data['vehicle_id'] = $vehicle->id;
            } else {
                $vehicle = Vehicle::with('customer')->findOrFail($data['vehicle_id']);
                
                // Update customer email if provided
                if (!empty($data['email']) && $vehicle->customer) {
                    $vehicle->customer->update(['email' => $data['email']]);
                }
            }

            $service = ServiceRecord::create([
                'staff_id' => $staffId,
                'vehicle_id' => $data['vehicle_id'],
                'service_start_datetime' => $start->copy()->timezone('UTC'),
                'service_end_datetime' => null,
                'service_types' => $data['service_types'],
                'amount' => $data['amount'],
                'status' => 'pending',
                'job_id' => $jobId,
            ]);

            // Send email notification
            $this->emailService->sendServiceCreatedNotification($service);

            return redirect()->route('staff.services.index')
                ->with('success', "Service created! Job ID: {$jobId}");
        } catch (\Exception $e) {
            Log::error('Service store failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed: ' . $e->getMessage()]);
        }
    }
    public function show($id)
    {
        try {
            $staffId = $this->getStaffId();
            $service = ServiceRecord::where('staff_id', $staffId)
                ->with('vehicle.customer')
                ->findOrFail($id);

            return view('staff.services.show', compact('service', 'staffId'));
        } catch (\Exception $e) {
            return redirect()->route('staff.services.index')->with('error', 'Service not found.');
        }
    }
    public function edit($id)
    {
        try {
            $staffId = $this->getStaffId();
            $service = ServiceRecord::where('staff_id', $staffId)
                ->with('vehicle.customer')
                ->findOrFail($id);

            $service->service_start_datetime = Carbon::parse($service->service_start_datetime);
            $service->service_end_datetime = $service->service_end_datetime
                ? Carbon::parse($service->service_end_datetime)
                : null;

            return view('staff.services.edit', compact('service', 'staffId'));
        } catch (\Exception $e) {
            return redirect()->route('welcome')->with('error', 'Please login.');
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $staffId = $this->getStaffId();
            $service = ServiceRecord::where('staff_id', $staffId)
                ->with('vehicle.customer', 'vehicle.owner')
                ->findOrFail($id);
            $oldStatus = $service->status;
            $data = $request->all();
            $start = Carbon::createFromFormat('Y-m-d\TH:i', $data['service_start_datetime'], 'Asia/Kolkata');
            $end = !empty($data['service_end_datetime'])
                ? Carbon::createFromFormat('Y-m-d\TH:i', $data['service_end_datetime'], 'Asia/Kolkata')
                : null;
            if ($data['status'] === 'completed' && !$end) {
                $end = Carbon::now('Asia/Kolkata');
            }
            $service->update([
                'service_start_datetime' => $start->copy()->timezone('UTC'),
                'service_end_datetime' => $end ? $end->copy()->timezone('UTC') : null,
                'service_types' => json_encode($data['service_types']),
                'amount' => $data['amount'],
                'status' => $data['status'],
            ]);
            if ($service->status === 'completed') {
                $this->emailService->sendServiceInvoice($service);
            } elseif ($service->status !== $oldStatus) {
                $this->emailService->sendServiceStatusUpdate($service, $oldStatus);
            }
            return redirect()->route('staff.services.index')
                ->with('success', 'Service updated successfully!');
        } catch (\Exception $e) {
            Log::error('Update failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Update failed: ' . $e->getMessage()]);
        }
    }
    public function delete($id)
    {
        try {
            $staffId = $this->getStaffId();
            $service = ServiceRecord::where('staff_id', $staffId)
                ->with('vehicle.customer')
                ->findOrFail($id);
            return view('staff.services.delete', compact('service', 'staffId'));
        } catch (\Exception $e) {
            return redirect()->route('welcome')->with('error', 'Please login.');
        }
    }
    public function destroy($id)
    {
        try {
            $staffId = $this->getStaffId();
            $service = ServiceRecord::where('staff_id', $staffId)->findOrFail($id);
            $service->delete();
            return redirect()->route('staff.services.index')
                ->with('success', 'Service deleted!');
        } catch (\Exception $e) {
            Log::error('Delete failed: ' . $e->getMessage());
            return back()->with('error', 'Delete failed.');
        }
    }
    public function generatePdf($id)
    {
        try {
            $staffId = $this->getStaffId();
            $service = ServiceRecord::where('staff_id', $staffId)
                ->with('vehicle.customer')
                ->findOrFail($id);
            $service->service_types_array = json_decode($service->service_types, true) ?: [];
            $pdf = Pdf::loadView('staff.services.invoice_pdf', compact('service'));
            $pdf->setPaper('A4', 'portrait');
            $filename = 'Invoice_' . ($service->job_id ?? $service->id) . '_' . Carbon::now()->format('Ymd') . '.pdf';
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('PDF failed: ' . $e->getMessage());
            return redirect()->route('staff.services.index')->with('error', 'PDF generation failed.');
        }
    }
    public function vehicleServices($customerId)
    {
        try {
            $staffId = $this->getStaffId();
            $services = ServiceRecord::whereHas('vehicle', function ($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            })->where('staff_id', $staffId)->paginate(10);
            return view('staff.services.vehicle_services', compact('services', 'staffId', 'customerId'));
        } catch (\Exception $e) {
            return redirect()->route('welcome')->with('error', 'Please login.');
        }
    }
    public function debugAssignments()
    {
        try {
            $staffId = $this->getStaffId();
            
            $allMyServices = ServiceRecord::where('staff_id', $staffId)->get();
            $pendingMyServices = ServiceRecord::where('staff_id', $staffId)->where('status', 'pending')->get();
            $assignedMyServices = ServiceRecord::where('staff_id', $staffId)->where('status', 'assigned')->get();        
            Log::info("Debug - Staff ID: {$staffId}");
            Log::info("Debug - All my services: " . $allMyServices->count());
            Log::info("Debug - Pending my services: " . $pendingMyServices->count());
            Log::info("Debug - Assigned my services: " . $assignedMyServices->count());
            return response()->json([
                'staff_id' => $staffId,
                'all_services' => $allMyServices->count(),
                'pending_services' => $pendingMyServices->count(),
                'assigned_services' => $assignedMyServices->count(),
                'services' => $allMyServices->map(function($service) {
                    return [
                        'id' => $service->id,
                        'job_id' => $service->job_id,
                        'status' => $service->status,
                        'vehicle_id' => $service->vehicle_id
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}