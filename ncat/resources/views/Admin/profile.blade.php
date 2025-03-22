@extends('layouts.default')

@section('content')
<div class="col-12 content-card">
    <div class="card">
        <!-- Card Header -->
        <div class="card-header">
            <h3 class="card-title text-white">Edit Profile</h3>
            <div class="card-tools">
                <!-- Optionally, you can add tools or actions like upload, delete, etc. -->
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body">
            <div class="container-fluid">
                <h1 class="mb-4">Edit Profile</h1>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf

                    <!-- Name Input -->
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Username</label>
                        <div class="col-sm-10">
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $user->username) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <!-- Password Input -->
                    <div class="form-group row">
                        <label for="password" class="col-sm-2 col-form-label">Password</label>
                        <div class="col-sm-10">
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            <small class="form-text text-muted">Leave blank to keep current password</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Password Confirmation Input -->
                    <div class="form-group row">
                        <label for="password_confirmation" class="col-sm-2 col-form-label">Confirm Password</label>
                        <div class="col-sm-10">
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password_confirmation" class="col-sm-2 col-form-label">Change Photo</label>
                        <div class="col-sm-10">
                        <input type="file" class="form-control">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group row">
                        <div class="col-sm-10 offset-sm-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Card Body -->
    </div>
</div>
@endsection
