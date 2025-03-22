<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NCAT</title>
    
    <!-- Favicon -->
    <link href="{{asset('img/image.png')}}" type="image/x-icon" rel="icon">
    <link href="{{asset('img/image.png')}}" type="image/x-icon" rel="shortcut icon">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
    
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{asset('css/style.css')}}">

    <!-- jQuery -->
    <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
    <style>
       body {
        background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('img/NC NEW BUILDING.jpg') }}');
    background-size: cover; /* Makes the image cover the entire background */
    background-position: center; /* Centers the background image */
    background-repeat: no-repeat; /* Prevents the image from repeating */
}

    </style>
</head>
<body class="hold-transition login-page">

<div class="login-box">
    <!-- Logo -->
    <div class="login-logo">
        <img src="{{asset('img/image.png')}}" id="login-logo" alt="NCAT Logo" width="120">
        <h2 class="text-white shadow-sm"><b>Northeastern College</b><br> School Ability Test</h2>
        <hr>
    </div>

    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@endif

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

    <!-- Card Section -->
    <div class="card">
        <div class="card-body login-card-body">
        
            <p class="login-box-msg">No Email Available</p>
            
            <form method="POST" action="{{ route('EnrolledStudents/verifyAccessKey') }}">
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
                    <input type="number" name="access_key" class="form-control" placeholder="Access Key" required="required" id="access_key">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-key"></span>
                        </div>
                    </div>
                </div>
            
                <div class="row">
                    <div class="col-8">
                        <div class="col-4" ><p>
                            <a href="{{route('/')}}">Have Email</a>
                        </p></div>
                    </div>
                    
                    <div class="col-4">
                        
                        <button type="submit" class="btn btn-success btn-block">Take Exam</button>
                    </div>
                </div>
            </form>
            
        </div>
    </div>
    <div class="my-4"></div>

    <!-- Login Buttons -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Select your login type</p>

            <div class="row">
                <div class="col-12 mb-3">
                    <a id="login-admin" class="btn btn-success btn-block">
                        <i class="fas fa-user-shield"></i> Administrator
                    </a>
                </div>
                <div class="col-12">
                    <button onclick="window.location='{{ route('Instructor/login') }}'" class="btn btn-primary btn-block">
                        <i class="fas fa-chalkboard-teacher"></i> Instructor
                    </button>
                </div>
            </div>
        </div>
    </div>

  
    <!-- Modal Section -->
    <div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Administrator Login</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="loginForm">
                    @csrf
                    <div class="modal-body">
                        <!-- Form Content -->
                        <div class="form-group">
                            <label for="admin-username">Username</label>
                            <input type="text" class="form-control" id="username" placeholder="Enter username">
                        </div>
                        <div class="form-group">
                            <label for="admin-password">Password</label>
                            <input type="password" class="form-control" id="password" placeholder="Enter password">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
 
        <!-- Footer -->
    <div class="text-center text-white mt-3">
        <p>© 2024 Northeastern College. All rights reserved.</p>
    </div>
</div>

<!-- Disable Right-Click Script -->
{{-- <script src="{{asset('js/no-right-click.js')}}"></script> --}}


<!-- Bootstrap & AdminLTE Scripts -->
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('dist/js/adminlte.min.js')}}"></script>

<!-- Modal Script -->
<script>
    const login = '{{ route('Login') }}'
    const logout = '{{route('Logout')}}'
 </script>
<script src="{{asset('js/Auth.js')}}"></script>

</body>
</html>
