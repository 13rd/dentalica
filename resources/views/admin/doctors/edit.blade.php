@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Doctor</h4>
                    <a href="{{ route('admin.doctors.show', $doctor) }}" class="btn btn-outline-primary btn-sm">&larr; Back to Doctor</a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.doctors.update', $doctor) }}">
                        @csrf
                        @method('PATCH')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $doctor->user->name) }}" required
                                       class="form-control">
                                @error('name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $doctor->user->email) }}" required
                                       class="form-control">
                                @error('email')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="specialization_id" class="form-label">Specialization</label>
                                <select name="specialization_id" id="specialization_id" required class="form-select">
                                    <option value="">Select Specialization</option>
                                    @foreach($specializations as $specialization)
                                        <option value="{{ $specialization->id }}" {{ old('specialization_id', $doctor->specialization_id) == $specialization->id ? 'selected' : '' }}>
                                            {{ $specialization->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('specialization_id')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="experience_years" class="form-label">Experience (years)</label>
                                <input type="number" name="experience_years" id="experience_years" value="{{ old('experience_years', $doctor->experience_years) }}" min="0" required
                                       class="form-control">
                                @error('experience_years')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="bio" class="form-label">Biography</label>
                            <textarea name="bio" id="bio" rows="4" class="form-control">{{ old('bio', $doctor->bio) }}</textarea>
                            @error('bio')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Services</label>
                            <div class="row">
                                @foreach($services as $service)
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" name="services[]" value="{{ $service->id }}" id="service_{{ $service->id }}"
                                                   {{ $doctor->services->contains($service->id) ? 'checked' : '' }}
                                                   class="form-check-input">
                                            <label for="service_{{ $service->id }}" class="form-check-label">
                                                {{ $service->name }} - ${{ number_format($service->price, 2) }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('services')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.doctors.show', $doctor) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Update Doctor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
