@extends('layouts.auth')

@section('auth')
<div class="login-box">
    <div class="login-logo">
        <!-- Logo Section -->
        <img src="{{ asset('img/image.png') }}" id="login-logo" alt="nc logo" width="120">
        <h2>
            <b>Northeastern College</b><br>
            <b>Ability Test</b><br>
            <hr>
            <b>Student</b>
        </h2>
    </div>

    <!-- Card Section -->
    <div class="card">
        <div class="card-body login-card-body">
            <!-- Warning Message -->
            <div class="alert alert-warning text-center" role="alert">
                <strong>No batch active</strong><br>
                Please contact your proctor for further assistance.
            </div>
        </div>
    </div>
</div>
@endsection
