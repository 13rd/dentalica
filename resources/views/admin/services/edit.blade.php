@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Service</h4>
                    <a href="{{ route('admin.services.show', $service) }}" class="btn btn-outline-primary btn-sm">&larr; Back to Service</a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.services.update', $service) }}">
                        @csrf
                        @method('PATCH')

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="name" class="form-label">Service Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $service->name) }}" required
                                       class="form-control">
                                @error('name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="price" class="form-label">Price ($)</label>
                                <input type="number" name="price" id="price" value="{{ old('price', $service->price) }}" step="0.01" min="0" required
                                       class="form-control">
                                @error('price')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" rows="4" class="form-control">{{ old('description', $service->description) }}</textarea>
                                @error('description')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Assigned Doctors</label>
                            <div class="border rounded p-3 bg-light" style="max-height: 300px; overflow-y: auto;">
                                <div class="row">
                                    @foreach($doctors as $doctor)
                                        <div class="col-12 mb-2">
                                            <div class="form-check">
                                                <input type="checkbox" name="doctors[]" value="{{ $doctor->id }}" id="doctor_{{ $doctor->id }}"
                                                       {{ $service->doctors->contains($doctor->id) ? 'checked' : '' }}
                                                       class="form-check-input">
                                                <label for="doctor_{{ $doctor->id }}" class="form-check-label">
                                                    <strong>{{ $doctor->user->name }}</strong><br>
                                                    <small class="text-muted">{{ $doctor->specialization->name }} • {{ $doctor->experience_years }} years experience</small>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('doctors')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Select the doctors who can provide this service.</div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.services.show', $service) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Update Service
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
