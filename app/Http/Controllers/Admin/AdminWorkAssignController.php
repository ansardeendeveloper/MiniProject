<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceRecord;
use App\Models\Staff;

class AdminWorkAssignController extends Controller
{
    public function create()
    {
        $services = ServiceRecord::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $staff = Staff::all();

        return view('admin.work_assign', compact('services', 'staff'));
    }


    
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:service_records,id',
            'staff_id'   => 'required|exists:staff,id',
        ]);

        $service = ServiceRecord::findOrFail($request->service_id);
        $service->staff_id = $request->staff_id;
        $service->status = 'assigned';
        $service->save();

        return redirect()->back()->with('success', 'Work assigned successfully!');
    }
}