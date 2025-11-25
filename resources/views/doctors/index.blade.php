@extends('layouts.app')

@section('content')
    <h1>Doctors</h1>
    <form method="GET">
        <select name="spec">
            <option value="">All Specializations</option>
            @foreach ($specializations as $spec)
                <option value="{{ $spec->id }}" {{ request('spec') == $spec->id ? 'selected' : '' }}>{{ $spec->name }}</option>
            @endforeach
        </select>
        <button type="submit">Filter</button>
    </form>
    <table class="table">
        <thead>
            <tr>
                <th>Name <a href="?sort=name&direction=asc">↑</a><a href="?sort=name&direction=desc">↓</a></th>
                <th>Specialization</th>
                <th>Rating <a href="?sort=rating&direction=asc">↑</a><a href="?sort=rating&direction=desc">↓</a></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($doctors as $doctor)
                <tr>
                    <td>{{ $doctor->user->name }}</td>
                    <td>{{ $doctor->specialization->name }}</td>
                    <td>{{ $doctor->rating }}</td>
                    <td><a href="{{ route('doctors.show', $doctor) }}">View</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $doctors->links() }}
@endsection
