@extends('layouts.app')

@section('content')
    <h1>Профиль</h1>
    <form method="POST" action="{{ route('patient.profile') }}">
        @csrf
        <div class="form-group">
            <label>Телефон</label>
            <input type="text" name="phone" value="{{ auth()->user()->phone }}" class="form-control">
        </div>
        <div class="form-group">
            <label>Адрес</label>
            <input type="text" name="address" value="{{ auth()->user()->address }}" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Обновить</button>
    </form>
@endsection
