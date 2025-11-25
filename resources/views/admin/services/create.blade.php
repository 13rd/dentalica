@extends('layouts.app')

@section('content')
    <h1>Add Service</h1>
    <form method="POST" action="{{ route('admin.services.store') }}">
        @csrf
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Price</label>
            <input type="number" name="price" step="0.01" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add</button>
    </form>
@endsection
