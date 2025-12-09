<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Service;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {

        return view('admin.dashboard');
    }

    public function createDoctor()
    {
        $specializations = Specialization::all();
        return view('admin.doctors.create', compact('specializations'));
    }

    public function storeDoctor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'specialization_id' => 'required|exists:specializations,id',
            'bio' => 'nullable',
            'experience_years' => 'integer|min:0',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => $user->id,
            'specialization_id' => $validated['specialization_id'],
            'bio' => $validated['bio'],
            'experience_years' => $validated['experience_years'],
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Doctor registered');
    }

    public function createService()
    {
        return view('admin.services.create');
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable',
        ]);

        Service::create($validated);

        return redirect()->route('admin.dashboard')->with('success', 'Service created');
    }


    public function updateService(Request $request, Service $service)
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
        ]);

        $service->update($validated);

        return redirect()->back()->with('success', 'Price updated');
    }
};
