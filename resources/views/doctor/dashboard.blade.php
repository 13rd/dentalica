@extends('layouts.app')

@section('title', 'Кабинет врача')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Добрый день, {{ auth()->user()->name }}!</h2>
            <p class="text-muted">
                Специализация: <strong>{{ $doctor->specialization->name }}</strong>
                • Стаж: {{ $doctor->experience_years }} лет
            </p>
        </div>
        <div>
            <a href="{{ route('doctor.schedule') }}" class="btn btn-outline-primary">Полное расписание →</a>
        </div>
    </div>
    <div class="text-end mb-3">
        <a href="{{ route('doctor.schedule.create') }}" class="btn btn-success">
            Создать расписание
        </a>
    </div>

    <!-- Сегодняшние приёмы -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Сегодня, {{ now()->format('d.m.Y') }} ({{ now()->translatedFormat('l') }})</h5>
        </div>
        <div class="card-body">
            @if($todayAppointments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Время</th>
                                <th>Пациент</th>
                                <th>Телефон</th>
                                <th>Услуги</th>
                                <th>Статус</th>
                                <th>Действие</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todayAppointments as $app)
                                <tr class="{{ $app->status === 'completed' ? 'table-success' : '' }}">
                                    <td><strong>{{ \Carbon\Carbon::parse($app->schedule->time_slot)->format('H:i') }}</strong></td>
                                    <td>{{ $app->patient->name }}</td>
                                    <td>{{ $app->patient->phone ?? '—' }}</td>
                                    <td>
                                        @foreach($app->services as $service)
                                            <span class="badge bg-info me-1">{{ $service->name }}</span>
                                        @endforeach
                                        @if($app->services->isEmpty()) <em>Консультация</em> @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $app->status === 'completed' ? 'bg-success' : 'bg-warning' }}">
                                            {{ $app->status === 'completed' ? 'Завершён' : 'Ожидается' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($app->status !== 'completed')
                                            <form method="POST" action="{{ route('doctor.appointment.complete', $app) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    Завершить приём
                                                </button>
                                            </form>
                                        @else
                                            <em class="text-success">Завершён</em>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">Сегодня пациентов нет. Отличный день для отдыха!</p>
            @endif
        </div>
    </div>

    <!-- Расписание на ближайшие дни -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Расписание на ближайшие дни</h5>
        </div>
        <div class="card-body">
            @if($schedules->count() > 0)
                <div class="row">
                    @foreach($schedules as $date => $slots)
                        <div class="col-md-6 mb-3">
                            <h6 class="text-primary">
                                {{ \Carbon\Carbon::parse($date)->format('d.m (D)') }}
                                @if($date == today()->toDateString()) <small class="text-danger">(сегодня)</small> @endif
                            </h6>
                            <div class="list-group list-group-flush">
                                @foreach($slots as $slot)
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span>
                                            <strong>{{ \Carbon\Carbon::parse($slot->time_slot)->format('H:i') }}</strong>
                                            @if(!$slot->is_available && $slot->appointment)
                                                — {{ $slot->appointment->patient->name }}
                                            @else
                                                <span class="text-success">— Свободно</span>
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted">Нет слотов на ближайшие 14 дней.</p>
            @endif
        </div>
    </div>
</div>
@endsection
