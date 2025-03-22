@extends('layouts.auth')
@section('auth')
<div class="login-box">
    <div class="login-logo">
        <img src="{{asset('img/image.png')}}" id="login-logo" alt="nc logo" width="120">
        <h2 class="text-white"><b>Northeastern College</b><br>
            <b>Ability Test</b><br>
            <hr>
            <b>Instructor</b>
        </h2>
    </div>
    <div class="card">
        
        <div class="card-body login-card-body">
        
            <p class="login-box-msg">Sign in</p>
            
            <form method="post" accept-charset="utf-8" id="loginForminstructor">
                @csrf
                <div class="input-group mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username" required="required" id="username" aria-label="Username">                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required="required" id="password" aria-label="Password">                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
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
<script>
    const logininstructors = '{{ route('Login/Instructors') }}'
    
 </script>
<script src="{{asset('js/Auth.js')}}"></script>
@endsection