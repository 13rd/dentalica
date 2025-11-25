@extends('layouts.app')

@section('content')
    <h1>My Schedule</h1>
    <ul>
        @foreach ($schedules as $schedule)
            <li>{{ $schedule->date }} {{ $schedule->time_slot }} - {{ $schedule->is_available ? 'Available' : 'Booked' }}</li>
        @endforeach
    </ul>
@endsection
