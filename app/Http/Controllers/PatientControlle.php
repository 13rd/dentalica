<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:patient');
    }

    public function dashboard()
    {
        $appointments = auth()->user()->appointments()->with('doctor.user', 'schedule', 'services')->get();
        return view('patient.dashboard', compact('appointments'));
    }

    public function profile()
    {
        return view('patient.profile');
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'nullable',
            'address' => 'nullable',
        ]);

        auth()->user()->update($validated);
        return back()->with('success', 'Profile updated');
    }
};
