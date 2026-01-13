@extends('layouts.app')

@section('title', 'Создать расписание')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4>Создать рабочее расписание</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Дата</label>
                            <input type="date" name="date" class="form-control" required min="{{ today()->format('Y-m-d') }}" 
                                   value="{{ $prefilledDate ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Время приёма (можно выбрать несколько)</label>
                            <div class="row row-cols-4 g-2">
                                @foreach(['09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30',
                                          '14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30',
                                          '18:00','18:30','19:00','19:30'] as $time)
                                    <div class="col">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   name="time_slots[]" value="{{ $time }}" id="time{{ str_replace(':', '', $time) }}">
                                            <label class="form-check-label" for="time{{ str_replace(':', '', $time) }}">
                                                {{ $time }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-lg px-5">
                                Сохранить расписание
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4 text-center">
                <a href="{{ route('doctor.dashboard') }}" class="btn btn-outline-secondary">
                    ← Вернуться в кабинет
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
