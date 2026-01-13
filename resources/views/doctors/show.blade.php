@extends('layouts.app')

@section('content')
    <!-- Навигация -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('doctors.index') }}">Врачи</a></li>
            <li class="breadcrumb-item active">{{ $doctor->user->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Информация о враче -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-start mb-4">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-4" style="width: 100px; height: 100px; font-size: 40px;">
                            {{ substr($doctor->user->name, 0, 1) }}
                        </div>
                        <div class="flex-grow-1">
                            <h1 class="mb-2">{{ $doctor->user->name }}</h1>
                            <p class="text-muted mb-2">{{ $doctor->specialization->name }}</p>
                            <div class="d-flex align-items-center mb-3">
                                <div class="text-warning me-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($doctor->rating))
                                            <i class="fas fa-star"></i>
                                        @elseif($i - 0.5 <= $doctor->rating)
                                            <i class="fas fa-star-half-alt"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="badge bg-secondary fs-6">{{ number_format($doctor->rating, 1) }}</span>
                            </div>
                            <div class="d-flex gap-3">
                                <span class="text-muted">
                                    <i class="fas fa-briefcase"></i> Опыт: {{ $doctor->experience ?? 'Не указан' }}
                                </span>
                                <span class="text-muted">
                                    <i class="fas fa-envelope"></i> {{ $doctor->user->email }}
                                </span>
                            </div>
                        </div>
                        <div class="text-end">
                            <a href="{{ route('appointments.create', $doctor) }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-calendar-plus"></i> Записаться
                            </a>
                        </div>
                    </div>

                    @if($doctor->bio)
                        <div class="mb-4">
                            <h5>О враче</h5>
                            <p class="text-muted">{{ $doctor->bio }}</p>
                        </div>
                    @endif

                    @if($doctor->services->count() > 0)
                        <div class="mb-4">
                            <h5>Услуги и цены</h5>
                            <div class="row">
                                @foreach($doctor->services as $service)
                                    <div class="col-md-6 mb-3">
                                        <div class="border rounded p-3">
                                            <h6 class="mb-1">{{ $service->name }}</h6>
                                            <p class="text-muted small mb-2">{{ $service->description ?? 'Описание отсутствует' }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-semibold text-primary">{{ number_format($service->price) }} ₽</span>
                                                <span class="badge bg-light text-dark">{{ $service->duration ?? '30' }} мин</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Боковая панель -->
        <div class="col-lg-4">
            <!-- Статистика -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Статистика</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Рейтинг:</span>
                        <strong>{{ number_format($doctor->rating, 1) }}/5</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Опыт работы:</span>
                        <strong>{{ $doctor->experience ?? 'Не указан' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Специализация:</span>
                        <strong>{{ $doctor->specialization->name }}</strong>
                    </div>
                </div>
            </div>

            <!-- Быстрая запись -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Быстрая запись</h5>
                    <p class="text-muted small">Нажмите кнопку ниже, чтобы выбрать удобное время для записи</p>
                    <a href="{{ route('appointments.create', $doctor) }}" class="btn btn-success w-100">
                        <i class="fas fa-calendar-check"></i> Выбрать время
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Доступное расписание -->
    @if($schedules->count() > 0)
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Доступное время для записи</h5>
            </div>
            <div class="card-body">
                <div class="row" id="scheduleContainer">
                    @foreach($schedules->groupBy('date') as $date => $daySchedules)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="border rounded p-3">
                                <h6 class="text-primary mb-2">{{ \Carbon\Carbon::parse($date)->format('d.m.Y (l)') }}</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($daySchedules as $schedule)
                                        <a href="{{ route('appointments.create', $doctor) }}?date={{ $schedule->date }}&time={{ $schedule->time_slot }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            {{ \Carbon\Carbon::parse($schedule->time_slot)->format('H:i') }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($schedules->count() > 12)
                    <div class="text-center mt-3">
                        <button class="btn btn-outline-primary" onclick="loadMoreSchedule()">
                            Показать ещё
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="card shadow-sm mt-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Свободного времени нет</h5>
                <p class="text-muted">У данного врача сейчас нет доступных слотов для записи</p>
                <a href="{{ route('patient.appointments.week') }}" class="btn btn-primary">
                    Посмотреть других врачей
                </a>
            </div>
        </div>
    @endif

    <script>
        function loadMoreSchedule() {
            // Здесь можно добавить логику подгрузки дополнительного расписания
            console.log('Load more schedule...');
        }
    </script>
@endsection
