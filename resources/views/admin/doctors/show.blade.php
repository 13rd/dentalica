@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Врач: {{ $doctor->user->name }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.doctors.edit', $doctor) }}" class="btn btn-primary">
                Редактировать врача
            </a>
            <a href="{{ route('admin.doctors.report', $doctor) }}" class="btn btn-success">
                Просмотреть отчет
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Основная информация</h5>
                                </div>
                                <div class="card-body">
                                    <dl class="row">
                                        <dt class="col-sm-4">Имя:</dt>
                                        <dd class="col-sm-8">{{ $doctor->user->name }}</dd>

                                        <dt class="col-sm-4">Email:</dt>
                                        <dd class="col-sm-8">{{ $doctor->user->email }}</dd>

                                        <dt class="col-sm-4">Специализация:</dt>
                                        <dd class="col-sm-8">{{ $doctor->specialization->name }}</dd>

                                        <dt class="col-sm-4">Стаж:</dt>
                                        <dd class="col-sm-8">{{ $doctor->experience_years }} лет</dd>

                                        <dt class="col-sm-4">Рейтинг:</dt>
                                        <dd class="col-sm-8">{{ $doctor->rating ?? 'Еще не оценен' }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Биография</h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">{{ $doctor->bio ?? 'Биография не предоставлена.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Предоставляемые услуги</h5>
                                </div>
                                <div class="card-body">
                                    @if($doctor->services->count() > 0)
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            @foreach($doctor->services as $service)
                                                <span class="badge bg-primary">
                                                    {{ $service->name }} - {{ number_format($service->price) }} ₽
                                                </span>
                                            @endforeach
                                        </div>
                                        <p class="text-muted small mb-0">Всего услуг: {{ $doctor->services->count() }}</p>
                                    @else
                                        <p class="text-muted mb-0">Услуги этому врачу не назначены.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Недавние записи</h5>
                                </div>
                                <div class="card-body p-0">
                                    @if($doctor->appointments->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Пациент</th>
                                                        <th>Дата и время</th>
                                                        <th>Статус</th>
                                                        <th>Услуги</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($doctor->appointments->take(10) as $appointment)
                                                    <tr>
                                                        <td>{{ $appointment->patient ? $appointment->patient->name : 'Неизвестный пациент' }}</td>
                                                        <td>{{ $appointment->schedule && $appointment->schedule->date ? \Carbon\Carbon::parse($appointment->schedule->date)->format('d.m.Y') . ' ' . \Carbon\Carbon::parse($appointment->schedule->time_slot)->format('H:i') : 'Расписание не указано' }}</td>
                                                        <td>
                                                            <span class="badge bg-{{
                                                                $appointment->status == 'confirmed' ? 'success' :
                                                                ($appointment->status == 'completed' ? 'primary' :
                                                                ($appointment->status == 'pending' ? 'warning' :
                                                                ($appointment->status == 'cancelled' ? 'danger' : 'secondary')))
                                                            }}">
                                                                {{
                                                                    $appointment->status == 'confirmed' ? 'Подтверждено' :
                                                                    ($appointment->status == 'completed' ? 'Завершено' :
                                                                    ($appointment->status == 'pending' ? 'Ожидает' :
                                                                    ($appointment->status == 'cancelled' ? 'Отменено' : $appointment->status)))
                                                                }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            {{ $appointment->services && $appointment->services->count() ? $appointment->services->pluck('name')->join(', ') : 'Услуги не указаны' }}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @if($doctor->appointments->count() > 10)
                                            <div class="p-3">
                                                <p class="text-muted small mb-0">Показано 10 последних записей. <a href="{{ route('admin.doctors.report', $doctor) }}" class="text-primary">Посмотреть полный отчет</a></p>
                                            </div>
                                        @endif
                                    @else
                                        <div class="p-3">
                                            <p class="text-muted mb-0">Записи для этого врача не найдены.</p>
                                        </div>
                                    @endif
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
