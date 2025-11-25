@extends('layouts.app')

@section('content')
    <h1>Register Doctor</h1>
    <form method="POST" action="{{ route('admin.doctors.store') }}">
        @csrf
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Specialization</label>
            <select name="specialization_id" class="form-control">
                @foreach ($specializations as $spec)
                    <option value="{{ $spec->id }}">{{ $spec->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Bio</label>
            <textarea name="bio" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label>Experience Years</label>
            <input type="number" name="experience_years" class="form-control" min="0">
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
@endsection
