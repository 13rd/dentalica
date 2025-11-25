@extends('layouts.app')

@section('content')
    <h1>My Appointments</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Doctor</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Total</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($appointments as $appointment)
                <tr>
                    <td>{{ $appointment->schedule->date }}</td>
                    <td>{{ $appointment->schedule->time_slot }}</td>
                    <td>{{ $appointment->doctor->user->name }}</td>
                    <td>{{ $appointment->status }}</td>
                    <td>{{ $appointment->payment_status }}</td>
                    <td>{{ $appointment->total_price }}$</td>
                    <td>
                        @if ($appointment->status == 'pending' || $appointment->status == 'confirmed')
                            <form action="{{ route('appointments.cancel', $appointment) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger">Cancel</button>
                            </form>
                        @endif
                        @if ($appointment->status == 'completed' && !$appointment->review)
                            <form action="{{ route('reviews.store', $appointment) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Rating</label>
                                    <select name="rating">
                                        <option>1</option><option>2</option><option>3</option><option>4</option><option>5</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Comment</label>
                                    <textarea name="comment"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Leave Review</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
