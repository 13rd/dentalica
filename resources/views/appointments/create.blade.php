@extends('layouts.app')

@section('content')
    <h1>Book Appointment with {{ $doctor->user->name }}</h1>
    <form method="POST" action="{{ route('appointments.store') }}">
        @csrf
        <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
        <div class="form-group">
            <label for="schedule_id">Select Slot</label>
            <select name="schedule_id" class="form-control">
                @foreach ($schedules as $schedule)
                    <option value="{{ $schedule->id }}">{{ $schedule->date }} {{ $schedule->time_slot }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Additional Services</label>
            @foreach ($services as $service)
                <div class="form-check">
                    <input type="checkbox" name="services[]" value="{{ $service->id }}" class="form-check-input">
                    <label class="form-check-label">{{ $service->name }} ({{ $service->price }}$)</label>
                </div>
            @endforeach
        </div>
        <button type="submit" class="btn btn-primary">Book and Pay (Mock)</button>
    </form>
@endsection
