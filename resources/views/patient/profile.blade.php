@extends('layouts.app')

@section('content')
    <h1>Profile</h1>
    <form method="POST" action="{{ route('patient.profile') }}">
        @csrf
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" value="{{ auth()->user()->phone }}" class="form-control">
        </div>
        <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" value="{{ auth()->user()->address }}" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
@endsection
