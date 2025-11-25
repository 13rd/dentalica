@extends('layouts.app')

@section('content')
    <h1>Doctor: {{ $doctor->user->name }}</h1>
    <p>Specialization: {{ $doctor->specialization->name }}</p>
    <p>Rating: {{ $doctor->rating }}</p>
    <p>Experience: {{ $doctor->experience_years }} years</p>
    <p>Bio: {{ $doctor->bio }}</p>

    <h2>Available Slots</h2>
    <ul>
        @foreach ($schedules as $schedule)
            <li>{{ $schedule->date }} at {{ $schedule->time_slot }}</li>
        @endforeach
    </ul>

    <a href="{{ route('appointments.create', $doctor) }}" class="btn btn-primary">Book Appointment</a>
@endsection
