<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class StaffProfileController extends Controller
{
    public function show()
    {
        $staff = Auth::guard('staff')->user();
        return view('staff.profile', compact('staff'));
    }
    public function edit()
    {
        $staff = Auth::guard('staff')->user();
        return view('staff.profile-edit', compact('staff'));
    }
    public function update(Request $request)
    {
        $staff = Auth::guard('staff')->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'phone' => 'required|string|regex:/^[0-9]{10,15}$/',
            'address' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date|before:today',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ], [
            'photo.image' => 'The file must be an image.',
            'photo.mimes' => 'Only JPG, JPEG, and PNG files are allowed.',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $data = $request->only(['name', 'email', 'phone', 'address', 'date_of_birth']);
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $staff->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            if ($request->filled('new_password')) {
                $data['password'] = Hash::make($request->new_password);
            }
        }
         if ($request->hasFile('photo')) {
            if ($staff->photo && Storage::exists('staff_photos/' . $staff->photo)) {
                Storage::delete('staff_photos/' . $staff->photo);
            }
            $photo = $request->file('photo');
            $filename = 'staff_' . $staff->id . '_' . time() . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('staff_photos', $filename);
            $data['photo'] = $filename;
        }
        if ($request->date_of_birth) {
            $data['age'] = Carbon::parse($request->date_of_birth)->age;
        }
        $staff->update($data);
        return redirect()->route('staff.profile')->with('success', 'Profile updated successfully!');
    }
}
