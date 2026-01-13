@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Врачи</h1>
        <a href="{{ route('patient.appointments.week') }}" class="btn btn-primary">
            <i class="fas fa-calendar-plus"></i> Записаться на приём
        </a>
    </div>

    <!-- Фильтры -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="spec" class="form-label">Специализация</label>
                    <select name="spec" id="spec" class="form-select">
                        <option value="">Все специализации</option>
                        @foreach ($specializations as $spec)
                            <option value="{{ $spec->id }}" {{ request('spec') == $spec->id ? 'selected' : '' }}>{{ $spec->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sort" class="form-label">Сортировка</label>
                    <select name="sort" id="sort" class="form-select">
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>По рейтингу</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>По имени</option>
                        <option value="experience" {{ request('sort') == 'experience' ? 'selected' : '' }}>По опыту</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="direction" class="form-label">Порядок</label>
                    <select name="direction" id="direction" class="form-select">
                        <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>По убыванию</option>
                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>По возрастанию</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-filter"></i> Применить
                        </button>
                        <a href="{{ route('doctors.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Сбросить
                        </a>
                    </div>
                </div>
            </form>
            @if(request()->hasAny(['spec', 'sort', 'direction']))
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Применены фильтры: 
                        @if(request('spec'))
                            Специализация: {{ $specializations->find(request('spec'))->name ?? 'Не указана' }}
                        @endif
                        @if(request('sort'))
                            @if(request('sort') == 'rating') По рейтингу @elseif(request('sort') == 'name') По имени @elseif(request('sort') == 'experience') По опыту @endif
                        @endif
                        @if(request('direction'))
                            {{ request('direction') == 'asc' ? 'по возрастанию' : 'по убыванию' }}
                        @endif
                    </small>
                </div>
            @endif
        </div>
    </div>

    <!-- Список врачей -->
    @if($doctors->count() > 0)
        <div class="row">
            @foreach ($doctors as $doctor)
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-3">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; font-size: 24px;">
                                    {{ substr($doctor->user->name, 0, 1) }}
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1">{{ $doctor->user->name }}</h5>
                                    <p class="text-muted mb-2">{{ $doctor->specialization->name }}</p>
                                    <div class="d-flex align-items-center">
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
                                        <span class="badge bg-secondary">{{ number_format($doctor->rating, 1) }}</span>
                                    </div>
                                </div>
                            </div>

                            @if($doctor->services->count() > 0)
                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">Услуги:</h6>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($doctor->services->take(3) as $service)
                                            <span class="badge bg-light text-dark">{{ $service->name }}</span>
                                        @endforeach
                                        @if($doctor->services->count() > 3)
                                            <span class="badge bg-secondary">+{{ $doctor->services->count() - 3 }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-user-md"></i> Опыт: {{ $doctor->experience ?? 'Не указан' }}
                                </small>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-info-circle"></i> Подробнее
                                    </a>
                                    <a href="{{ route('appointments.create', $doctor) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-calendar-plus"></i> Записаться
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Пагинация -->
        <div class="d-flex justify-content-center mt-4">
            {{ $doctors->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-user-md fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">Врачи не найдены</h4>
            <p class="text-muted">Попробуйте изменить параметры фильтрации</p>
            <a href="{{ route('doctors.index') }}" class="btn btn-primary">Сбросить фильтры</a>
        </div>
    @endif
@endsection
