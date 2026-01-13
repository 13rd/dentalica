<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $doctors = \App\Models\Doctor::with(['user', 'specialization'])
        ->orderBy('rating', 'desc')
        ->take(6)
        ->get();

    return view('welcome', compact('doctors'));
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isDoctor()) {
            return redirect()->route('doctor.dashboard');
        } else {
            return redirect()->route('patient.dashboard');
        }
    })->name('dashboard');

    // Patient routes
    Route::middleware('role:patient')->group(function () {
        Route::get('/patient/dashboard', [PatientController::class, 'dashboard'])->name('patient.dashboard');
        Route::get('/patient/profile', [PatientController::class, 'profile'])->name('patient.profile');
        Route::post('/patient/profile', [PatientController::class, 'updateProfile']);
        Route::get('/patient/appointments/week', [PatientController::class, 'weekAppointments'])->name('patient.appointments.week');
        Route::post('/appointment/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointment.cancel');
        Route::get('/appointment/{appointment}/pay', [AppointmentController::class, 'pay'])->name('appointment.pay');
        Route::post('/appointment/{appointment}/pay', [AppointmentController::class, 'processPayment'])->name('appointment.process-payment');

        // Doctor catalogue (patients only)
        Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
        Route::get('/doctors/{doctor}', [DoctorController::class, 'show'])->name('doctors.show');

        Route::get('/appointments/create/{doctor}', [AppointmentController::class, 'create'])
            ->name('appointments.create');

        Route::post('/appointments/{doctor}', [AppointmentController::class, 'store'])
            ->name('appointments.store');
    });

    // Doctor routes
    Route::middleware('role:doctor')->prefix('doctor')->group(function () {
        Route::get('/dashboard', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');
        Route::get('/schedule', [DoctorController::class, 'schedule'])->name('doctor.schedule');
        Route::post('/appointment/{appointment}/complete', [AppointmentController::class, 'complete'])->name('doctor.appointment.complete');
        Route::post('/appointment/{appointment}/cancel', [DoctorController::class, 'cancel'])->name('doctor.cancel');
        Route::get('/schedule/create', [DoctorController::class, 'getSchedule'])->name('doctor.schedule.create');
        Route::post('/schedule/create', [DoctorController::class, 'createSchedule']);
    });

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

        // Doctor management
        Route::get('/doctors', [AdminController::class, 'listDoctors'])->name('admin.doctors.index');
        Route::get('/doctors/create', [AdminController::class, 'createDoctor'])->name('admin.doctors.create');
        Route::post('/doctors', [AdminController::class, 'storeDoctor'])->name('admin.doctors.store');
        Route::get('/doctors/{doctor}', [AdminController::class, 'showDoctor'])->name('admin.doctors.show');
        Route::get('/doctors/{doctor}/edit', [AdminController::class, 'editDoctor'])->name('admin.doctors.edit');
        Route::patch('/doctors/{doctor}', [AdminController::class, 'updateDoctor'])->name('admin.doctors.update');
        Route::delete('/doctors/{doctor}', [AdminController::class, 'deleteDoctor'])->name('admin.doctors.delete');
        Route::get('/doctors/{doctor}/report', [AdminController::class, 'doctorReport'])->name('admin.doctors.report');

        // Service management
        Route::get('/services', [AdminController::class, 'listServices'])->name('admin.services.index');
        Route::get('/services/create', [AdminController::class, 'createService'])->name('admin.services.create');
        Route::post('/services', [AdminController::class, 'storeService'])->name('admin.services.store');
        Route::get('/services/{service}', [AdminController::class, 'showService'])->name('admin.services.show');
        Route::get('/services/{service}/edit', [AdminController::class, 'editService'])->name('admin.services.edit');
        Route::patch('/services/{service}', [AdminController::class, 'updateService'])->name('admin.services.update');
        Route::delete('/services/{service}', [AdminController::class, 'deleteService'])->name('admin.services.delete');

        // Doctor-Service relationships
        Route::get('/doctor-services', [AdminController::class, 'manageDoctorServices'])->name('admin.doctor-services.index');
        Route::post('/doctor-services', [AdminController::class, 'updateDoctorServices'])->name('admin.doctor-services.update');

        // Patient management
        Route::get('/patients', [AdminController::class, 'listPatients'])->name('admin.patients.index');
        Route::get('/patients/{user}', [AdminController::class, 'showPatient'])->name('admin.patients.show');
        Route::get('/patients/{user}/edit', [AdminController::class, 'editPatient'])->name('admin.patients.edit');
        Route::patch('/patients/{user}', [AdminController::class, 'updatePatient'])->name('admin.patients.update');
        Route::delete('/patients/{user}', [AdminController::class, 'deletePatient'])->name('admin.patients.delete');
    });

    //Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    /* Route::post('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete'); */
    /* Route::post('/reviews/{appointment}', [ReviewController::class, 'store'])->name('reviews.store'); */
});

require __DIR__.'/auth.php';
