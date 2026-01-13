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
        $doctorsCount = Doctor::count();
        $servicesCount = Service::count();
        $appointmentsCount = \App\Models\Appointment::count();
        $patientsCount = \App\Models\User::where('role', 'patient')->count();

        return view('admin.dashboard', compact('doctorsCount', 'servicesCount', 'appointmentsCount', 'patientsCount'));
    }

    // Doctor management methods
    public function listDoctors()
    {
        $doctors = Doctor::with(['user', 'specialization', 'services'])->paginate(10);
        return view('admin.doctors.index', compact('doctors'));
    }

    public function showDoctor(Doctor $doctor)
    {
        $doctor->load(['user', 'specialization', 'services', 'appointments.patient', 'appointments.schedule', 'appointments.services']);
        return view('admin.doctors.show', compact('doctor'));
    }

    public function editDoctor(Doctor $doctor)
    {
        $specializations = Specialization::all();
        $doctor->load(['user', 'services']);
        $services = Service::all();

        return view('admin.doctors.edit', compact('doctor', 'specializations', 'services'));
    }

    public function updateDoctor(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $doctor->user_id,
            'specialization_id' => 'required|exists:specializations,id',
            'bio' => 'nullable',
            'experience_years' => 'integer|min:0',
            'services' => 'array',
            'services.*' => 'exists:services,id',
        ]);

        $doctor->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $doctor->update([
            'specialization_id' => $validated['specialization_id'],
            'bio' => $validated['bio'],
            'experience_years' => $validated['experience_years'],
        ]);

        if (isset($validated['services'])) {
            $doctor->services()->sync($validated['services']);
        }

        return redirect()->route('admin.doctors.show', $doctor)->with('success', 'Врач успешно обновлен');
    }

    public function deleteDoctor(Doctor $doctor)
    {
        $doctor->user->delete();
        $doctor->delete();

        return redirect()->route('admin.doctors.index')->with('success', 'Врач успешно удален');
    }

    public function doctorReport(Doctor $doctor)
    {
        $doctor->load(['user', 'specialization', 'services', 'appointments.patient', 'appointments.schedule', 'appointments.services']);

        $totalAppointments = $doctor->appointments->count();
        $completedAppointments = $doctor->appointments->where('status', 'completed')->count();
        $totalClients = $doctor->appointments->pluck('patient_id')->unique()->count();

        $servicesProvided = collect();
        foreach ($doctor->appointments as $appointment) {
            foreach ($appointment->services as $service) {
                $servicesProvided->push($service);
            }
        }
        $servicesStats = $servicesProvided->groupBy('id')->map(function ($group) {
            return [
                'service' => $group->first(),
                'count' => $group->count(),
            ];
        });

        return view('admin.doctors.report', compact('doctor', 'totalAppointments', 'completedAppointments', 'totalClients', 'servicesStats'));
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
            'services' => 'array',
            'services.*' => 'exists:services,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'doctor',
        ]);

        $doctor = Doctor::create([
            'user_id' => $user->id,
            'specialization_id' => $validated['specialization_id'],
            'bio' => $validated['bio'],
            'experience_years' => $validated['experience_years'],
        ]);

        if (isset($validated['services'])) {
            $doctor->services()->attach($validated['services']);
        }

        return redirect()->route('admin.doctors.index')->with('success', 'Врач успешно зарегистрирован');
    }

    // Service management methods
    public function listServices()
    {
        $services = Service::with(['doctors.user', 'doctors.specialization'])->paginate(10);
        return view('admin.services.index', compact('services'));
    }

    public function showService(Service $service)
    {
        $service->load(['doctors.user', 'doctors.specialization', 'appointments.doctor.user', 'appointments.patient', 'appointments.schedule']);
        return view('admin.services.show', compact('service'));
    }

    public function editService(Service $service)
    {
        $service->load('doctors');
        $doctors = Doctor::with('user')->get();
        $specializations = Specialization::all();

        return view('admin.services.edit', compact('service', 'doctors', 'specializations'));
    }

    public function updateService(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable',
            'doctors' => 'array',
            'doctors.*' => 'exists:doctors,id',
        ]);

        $service->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'description' => $validated['description'],
        ]);

        if (isset($validated['doctors'])) {
            $service->doctors()->sync($validated['doctors']);
        } else {
            $service->doctors()->detach();
        }

        return redirect()->route('admin.services.show', $service)->with('success', 'Услуга успешно обновлена');
    }

    public function deleteService(Service $service)
    {
        $service->doctors()->detach();
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Услуга успешно удалена');
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
            'doctors' => 'array',
            'doctors.*' => 'exists:doctors,id',
        ]);

        $service = Service::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'description' => $validated['description'],
        ]);

        if (isset($validated['doctors'])) {
            $service->doctors()->attach($validated['doctors']);
        }

        return redirect()->route('admin.services.index')->with('success', 'Услуга успешно создана');
    }

    // Doctor-Service relationship management
    public function manageDoctorServices()
    {
        $doctors = Doctor::with(['user', 'specialization', 'services'])->get();
        $services = Service::with('doctors')->get();

        return view('admin.doctor-services.index', compact('doctors', 'services'));
    }

    public function updateDoctorServices(Request $request)
    {
        $validated = $request->validate([
            'doctor_services' => 'array',
            'doctor_services.*' => 'array',
        ]);

        foreach ($validated['doctor_services'] as $doctorId => $serviceIds) {
            $doctor = Doctor::find($doctorId);
            if ($doctor) {
                $doctor->services()->sync($serviceIds ?: []);
            }
        }

        return redirect()->route('admin.doctor-services.index')->with('success', 'Связи врач-услуга успешно обновлены');
    }

    // Patient management methods
    public function listPatients()
    {
        $patients = User::where('role', 'patient')->with(['appointments.doctor.user', 'appointments.services'])->paginate(10);
        return view('admin.patients.index', compact('patients'));
    }

    public function showPatient(User $user)
    {
        $user->load(['appointments.doctor.user', 'appointments.services', 'appointments.schedule']);
        return view('admin.patients.show', compact('user'));
    }

    public function editPatient(User $user)
    {
        return view('admin.patients.edit', compact('user'));
    }

    public function updatePatient(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable',
            'address' => 'nullable',
        ]);

        $user->update($validated);

        return redirect()->route('admin.patients.show', $user)->with('success', 'Пациент успешно обновлен');
    }

    public function deletePatient(User $user)
    {
        
        $appointmentCount = $user->appointments()->count();
        if ($appointmentCount > 0) {
            return redirect()->route('admin.patients.index')->with('error', 'Нельзя удалить пациента с существующими записями');
        }

        $user->delete();

        return redirect()->route('admin.patients.index')->with('success', 'Пациент успешно удален');
    }
};
