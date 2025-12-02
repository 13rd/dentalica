@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Моё расписание</h1>

    @foreach($schedules as $date => $slots)
        <h4 class="mt-4">{{ \Carbon\Carbon::parse($date)->format('d.m.Y (l)') }}</h4>
        <div class="row">
            @foreach($slots as $slot)
                <div class="col-md-3 mb-3">
                    <div class="card {{ $slot->is_available ? 'border-success' : 'border-danger' }}">
                        <div class="card-body text-center">
                            <strong>{{ $slot->time_slot->format('H:i') }}</strong><br>
                            <small>
                                @if(!$slot->is_available && $slot->appointment)
                                    {{ $slot->appointment->patient->name }}
                                @else
                                    Свободно
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
@endsection
