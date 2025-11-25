@extends('layouts.app')

@section('content')
    <h1>Doctor Dashboard</h1>
    <table class="table">
        <!-- Аналогично patient, но для доктора -->
        @foreach ($appointments as $appointment)
            <tr>
                <!-- ... -->
                @if ($appointment->status == 'confirmed')
                    <form action="{{ route('appointments.complete', $appointment) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success">Complete</button>
                    </form>
                @endif
            </tr>
        @endforeach
    </table>
@endsection
