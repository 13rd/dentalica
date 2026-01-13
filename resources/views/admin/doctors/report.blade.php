@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Отчет по врачу: {{ $doctor->user->name }}</h1>
        <a href="{{ route('admin.doctors.show', $doctor) }}" class="btn btn-outline-primary">&larr; Назад к врачу</a>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">

                    <!-- Statistics Overview -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="card border-primary">
                                <div class="card-body d-flex align-items-center">
                                    <div class="p-3 bg-primary rounded me-3">
                                        <img src="{{ asset('images/icons/appointments.png') }}" alt="Записи" width="32" height="32" class="text-white">
                                    </div>
                                    <div>
                                        <h6 class="card-title text-muted mb-1">Всего записей</h6>
                                        <h2 class="mb-0">{{ $totalAppointments }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-body d-flex align-items-center">
                                    <div class="p-3 bg-success rounded me-3">
                                        <img src="{{ asset('images/icons/doctors.png') }}" alt="Завершено" width="32" height="32" class="text-white">
                                    </div>
                                    <div>
                                        <h6 class="card-title text-muted mb-1">Завершенных записей</h6>
                                        <h2 class="mb-0">{{ $completedAppointments }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border-info">
                                <div class="card-body d-flex align-items-center">
                                    <div class="p-3 bg-info rounded me-3">
                                        <img src="{{ asset('images/icons/patients.png') }}" alt="Клиенты" width="32" height="32" class="text-white">
                                    </div>
                                    <div>
                                        <h6 class="card-title text-muted mb-1">Уникальных клиентов</h6>
                                        <h2 class="mb-0">{{ $totalClients }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Services Statistics -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Предоставленные услуги</h5>
                                </div>
                                <div class="card-body">
                                    @if($servicesStats->count() > 0)
                                        <div class="list-group list-group-flush">
                                            @foreach($servicesStats->sortByDesc('count') as $stat)
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $stat['service']->name }}</h6>
                                                    <p class="text-muted small mb-0">{{ $stat['service']->description }}</p>
                                                </div>
                                                <div class="text-end">
                                                    <div class="h4 mb-0 text-primary">{{ $stat['count'] }}</div>
                                                    <small class="text-muted">раз предоставлено</small>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <p class="text-muted mb-0">Этот врач еще не предоставлял услуги.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Doctor Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Информация о враче</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <dl class="row">
                                                <dt class="col-sm-4">Имя:</dt>
                                                <dd class="col-sm-8">{{ $doctor->user->name }}</dd>

                                                <dt class="col-sm-4">Email:</dt>
                                                <dd class="col-sm-8">{{ $doctor->user->email }}</dd>

                                                <dt class="col-sm-4">Специализация:</dt>
                                                <dd class="col-sm-8">{{ $doctor->specialization->name }}</dd>
                                            </dl>
                                        </div>
                                        <div class="col-md-6">
                                            <dl class="row">
                                                <dt class="col-sm-4">Стаж:</dt>
                                                <dd class="col-sm-8">{{ $doctor->experience_years }} лет</dd>

                                                <dt class="col-sm-4">Рейтинг:</dt>
                                                <dd class="col-sm-8">{{ $doctor->rating ?? 'Еще не оценен' }}</dd>

                                                <dt class="col-sm-4">Услуг:</dt>
                                                <dd class="col-sm-8">{{ $doctor->services->count() }} назначено</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-primary">&larr; Назад к врачам</a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary ms-2">Назад к панели</a>
    </div>
</div>
@endsection
