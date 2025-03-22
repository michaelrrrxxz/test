@extends('layouts.auth')
@section('auth')
<div class="login-box">
    <div class="login-logo">
        <img src="{{asset('img/image.png')}}" id="login-logo" alt="nc logo" width="120">
        <h2><b>Northeastern College</b><br>
            <b>Ability Test</b><br>
            <hr>
            <b>Student</b>
        </h2>
    </div>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

    <div class="card">
        
        <div class="card-body login-card-body">
        
            <p class="login-box-msg">Sign in</p>
            
            <form method="POST" action="{{ route('send.otp') }}">
                @csrf <!-- CSRF protection token for Laravel -->
            
                <div class="input-group mb-3">
                    <input type="text" name="id_number" class="form-control" placeholder="ID Number" required="required" id="id_number">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-id-card"></span>
                        </div>
                    </div>
                </div>
            
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required="required" id="email">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
            
                <div class="row">
                    <div class="col-8"></div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-success btn-block">Sign In</button>
                    </div>
                </div>
            </form>
            
        </div>
    </div>
</div>
@endsection