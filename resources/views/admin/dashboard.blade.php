@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <h1 class="h2 mb-4">Панель администратора</h1>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="p-3 bg-primary rounded me-3">
                        <img src="{{ asset('images/icons/doctors.png') }}" alt="Врачи" width="32" height="32" class="text-white">
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Врачи</h6>
                        <h2 class="mb-0">{{ $doctorsCount }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="p-3 bg-success rounded me-3">
                        <img src="{{ asset('images/icons/services.png') }}" alt="Услуги" width="32" height="32" class="text-white">
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Услуги</h6>
                        <h2 class="mb-0">{{ $servicesCount }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="p-3 bg-warning rounded me-3">
                        <img src="{{ asset('images/icons/appointments.png') }}" alt="Записи" width="32" height="32" class="text-white">
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Записи</h6>
                        <h2 class="mb-0">{{ $appointmentsCount }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="p-3 bg-info rounded me-3">
                        <img src="{{ asset('images/icons/patients.png') }}" alt="Пациенты" width="32" height="32" class="text-white">
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Пациенты</h6>
                        <h2 class="mb-0">{{ $patientsCount }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Управление врачами</h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.doctors.index') }}" class="btn btn-primary">
                            Просмотреть всех врачей
                        </a>
                        <a href="{{ route('admin.doctors.create') }}" class="btn btn-success">
                            Зарегистрировать нового врача
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Управление услугами</h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.services.index') }}" class="btn btn-primary">
                            Просмотреть все услуги
                        </a>
                        <a href="{{ route('admin.services.create') }}" class="btn btn-success">
                            Добавить новую услугу
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Управление пациентами</h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.patients.index') }}" class="btn btn-primary">
                            Просмотреть всех пациентов
                        </a>
                    </div>
                </div>
            </div>
        </div>


</div>
@endsection
