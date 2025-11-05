<?php

namespace App\Http\Controllers;

use App\Models\ServiceRecord;  // Changed from Service to ServiceRecord
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;

class AdminServiceController extends Controller
{
    /**
     * Display a listing of the services.
     */
    public function index()
    {
        $services = ServiceRecord::with('vehicle')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalServices = ServiceRecord::count();
        $totalPendingServices = ServiceRecord::where('status', 'pending')->count();
        $totalCompletedServices = ServiceRecord::where('status', 'completed')->count();
        $totalCancelledServices = ServiceRecord::where('status', 'cancelled')->count();

        return view('admin.services.index', compact(
            'services',
            'totalServices',
            'totalPendingServices',
            'totalCompletedServices',
            'totalCancelledServices'
        ));
    }

    /**
     * Display the specified service.
     */
    public function view($id)
    {
        $service = ServiceRecord::with(['vehicle', 'owner'])->findOrFail($id);
        return view('admin.services.view', compact('service'));
    }

    /**
     * Print service details.
     */
    public function print($id)
    {
        $service = ServiceRecord::with(['vehicle', 'owner'])->findOrFail($id);
        return view('admin.services.print', compact('service'));
    }

    /**
     * Search services.
     */
    public function search(Request $request)
    {
        $search = $request->get('search');
        
        $services = ServiceRecord::with('vehicle')
            ->when($search, function($query) use ($search) {
                $query->where('job_id', 'like', "%{$search}%")
                      ->orWhere('status', 'like', "%{$search}%")
                      ->orWhereHas('vehicle', function($q) use ($search) {
                          $q->where('registration_no', 'like', "%{$search}%");
                      });
            })
            ->limit(10)
            ->get();

        return response()->json([
            'services' => $services,
            'total' => ServiceRecord::count(),
            'pending' => ServiceRecord::where('status', 'pending')->count(),
            'completed' => ServiceRecord::where('status', 'completed')->count(),
            'cancelled' => ServiceRecord::where('status', 'cancelled')->count(),
        ]);
    }
}