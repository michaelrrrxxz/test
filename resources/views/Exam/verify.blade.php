@extends('layouts.auth')
@section('auth')
<div class="login-box">
    <div class="login-logo">
        <img src="{{asset('img/image.png')}}" id="login-logo" alt="nc logo" width="120">
        <h2 class="text-white"><b>Northeastern College</b><br>
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
            
            <form action="{{ route('check.otp') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label for="otp">Enter OTP:</label>
                    <input type="text" name="otp" id="otp" class="form-control" required>
                </div>

                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary">Verify OTP</button>
                </div>
            </form>
            
        </div>
    </div>
</div>

<script src="{{asset('js/no-right-click.js')}}"></script>
@endsection