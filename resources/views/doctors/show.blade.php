@extends('layouts.app')

@section('content')
    <h1>Врач: {{ $doctor->user->name }}</h1>
    <p>Специализация: {{ $doctor->specialization->name }}</p>
    <p>Рейтинг: {{ $doctor->rating }}</p>
    <p>Стаж: {{ $doctor->experience_years }} лет</p>
    <p>Биография: {{ $doctor->bio }}</p>

    <h2>Доступные слоты</h2>
    <ul>
        @foreach ($schedules as $schedule)
            <li>{{ $schedule->date }} at {{ $schedule->time_slot }}</li>
        @endforeach
    </ul>

    <a href="{{ route('appointments.create', $doctor) }}" class="btn btn-primary">Записаться на прием</a>
@endsection
