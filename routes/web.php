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
    return view('welcome');
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
        Route::post('/appointment/{appointment}/cancel', [PatientController::class, 'cancel'])->name('appointment.cancel');
        Route::get('/appointment/{appointment}/pay', [AppointmentController::class, 'pay'])->name('appointment.pay');
        Route::post('/appointment/{appointment}/pay', [AppointmentController::class, 'processPayment'])->name('appointment.process-payment');
    });

    // Doctor routes
    Route::middleware('role:doctor')->prefix('doctor')->group(function () {
        Route::get('/dashboard', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');
        Route::get('/schedule', [DoctorController::class, 'schedule'])->name('doctor.schedule');
        Route::post('/appointment/{appointment}/complete', [DoctorController::class, 'complete'])->name('doctor.appointment.complete');
        Route::post('/appointment/{appointment}/cancel', [DoctorController::class, 'cancel'])->name('doctor.cancel');
        Route::get('/schedule/create', [DoctorController::class, 'createSchedule'])->name('doctor.schedule.create');
        Route::post('/schedule/create', [DoctorController::class, 'createSchedule']);
    });

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/doctors/create', [AdminController::class, 'createDoctor'])->name('admin.doctors.create');
        Route::post('/doctors', [AdminController::class, 'storeDoctor'])->name('admin.doctors.store');
        Route::get('/services/create', [AdminController::class, 'createService'])->name('admin.services.create');
        Route::post('/services', [AdminController::class, 'storeService'])->name('admin.services.store');
        Route::patch('/services/{service}', [AdminController::class, 'updateService'])->name('admin.services.update');
    });

    // General routes
    Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
    Route::get('/doctors/{doctor}', [DoctorController::class, 'show'])->name('doctors.show');

    Route::middleware(['auth', 'role:patient'])->group(function () {
        Route::get('/appointments/create/{doctor}', [AppointmentController::class, 'create'])
            ->name('appointments.create');

        Route::post('/appointments/{doctor}', [AppointmentController::class, 'store'])
            ->name('appointments.store');
    });
    //Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::post('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
    Route::post('/reviews/{appointment}', [ReviewController::class, 'store'])->name('reviews.store');
});

require __DIR__.'/auth.php';
