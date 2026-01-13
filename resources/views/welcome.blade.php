{{-- resources/views/welcome.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row text-center">
        <div class="col-lg-8 mx-auto">
            <h1 class="display-4 mb-4">Стоматология нового уровня</h1>
            <p class="lead mb-5">Запишитесь к лучшим врачам онлайн за 30 секунд</p>

            <div class="d-grid gap-3 d-md-flex justify-content-center">
                @auth
                    <a href="{{ route('doctors.index') }}" class="btn btn-primary btn-lg px-5">Найти врача</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5">Регистрация</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg px-5">Войти</a>
                @endauth
            </div>

            <div class="mt-5">
                <a href="{{ route('doctors.index') }}" class="text-decoration-none">
                    <h5>Посмотреть всех врачей →</h5>
                </a>
            </div>
        </div>
    </div>

    <!-- Doctors Section -->
    <div class="row mt-5">
        <div class="col-12">
            <h2 class="text-center mb-4 page-title">Наши специалисты</h2>
            <div class="row g-4">
                @foreach($doctors as $doctor)
                <div class="col-lg-4 col-md-6 border-1 gap-3">
                    <div class="app-card p-4 h-100">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="fas fa-user-md fa-lg"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1">{{ $doctor->user->name }}</h5>
                                <span class="badge-soft-primary badge-soft">{{ $doctor->specialization->name }}</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <p class="text-muted small mb-2">{{ $doctor->bio }}</p>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">Стаж: {{ $doctor->experience_years }} лет</small>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <span class="fw-bold">{{ $doctor->rating }}</span>
                            </div>
                        </div>

                        @auth
                            @if(auth()->user()->isPatient())
                            <div class="mt-3">
                                <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-brand btn-sm w-100">
                                    Записаться
                                </a>
                            </div>
                            @endif
                        @endauth
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </div>
</div>
@endsection
