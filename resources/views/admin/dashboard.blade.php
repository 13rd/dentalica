@extends('layouts.app')

@section('content')
    <h1>Admin Dashboard</h1>
    <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary">Register Doctor</a>
    <a href="{{ route('admin.services.create') }}" class="btn btn-primary">Add Service</a>
    <!-- Список пользователей, записей и т.д. -->
@endsection
