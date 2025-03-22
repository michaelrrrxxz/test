<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NCAT</title>
    <!-- Favicon -->
    <link href="{{asset('img/image.png')}}" type="image/x-icon" rel="icon">
    <link href="{{asset('img/image.png')}}" type="image/x-icon" rel="shortcut icon">
    <link rel="stylesheet" href="{{ asset('plugin/toastr/toastr.min.css') }}">
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
   <link rel="stylesheet" href="{{asset('css/root.css')}}">
   <style>
        body {
        background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('img/NC NEW BUILDING.jpg') }}');
        background-size: cover;
        background-position: center; 
        background-repeat: no-repeat; 
    }
   </style>

</head>
<body class="hold-transition login-page">
<div class="login-box">
    <!-- Logo -->
    <div class="login-logo">
        <img src="{{asset('img/image.png')}}" id="login-logo" alt="NCAT Logo" width="120">
        <h2 class="text-white shadow-sm"><b>Northeastern College</b><br>School Ability Test</h2>
        <hr>
    </div>

    @if (session('error'))
    window.onload = function() {
        alert("{{ session('error') }}");
    };
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
            <!-- Warning Message -->
            <div class="alert alert-warning text-center" role="alert">
                <strong>No batch active </strong>or<br> 
                <strong>Question not set</strong><br>
                Please contact your proctor for further assistance.
            </div>
        </div>
    </div>
    <div class="my-4"></div> 
   
    <!-- Login Buttons -->
    <div class="card">
        <div class="card-body login-card-body">
           

            <div class="row">
                <div class="col-12 mb-3">
                    <a id="login-admin" class="btn btn-success btn-block">
                        <i class="fas fa-user-shield"></i> Login
                    </a>
                </div>
                
            </div>
        </div>
    </div>

  
    <!-- Modal Section -->
    <!-- Administrator Login Modal -->
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
        <div class="form-group">
            <label for="admin-username">Username</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                </div>
                <input type="text" class="form-control" id="username" placeholder="Enter username">
            </div>
        </div>
        <div class="form-group">
            <label for="admin-password">Password</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
                <input type="password" class="form-control" id="password" placeholder="Enter password">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" >Login</button>
    </div>
</form>

        </div>
    </div>
</div>

<!-- Instructor Login Modal -->
<div class="modal fade" id="instructor-login-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Instructor Login</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="instructor-login-form">
                @csrf
                <div class="modal-body">
                    <!-- Form Content -->
                    <div class="form-group">
                        <label for="admin-username">Username</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input type="text" class="form-control" id="username-ins" placeholder="Enter username">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="admin-password">Password</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            </div>
                            <input type="password" class="form-control" id="password-ins" placeholder="Enter password">
                        </div>
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

<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="agreement-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">User Agreement</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="users-form">
                <div class="modal-body">
                    <div class="form-group">
                      
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

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

<script src="{{ asset('plugin/toastr/toastr.min.js') }}"></script>
<!-- Bootstrap & AdminLTE Scripts -->
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('dist/js/adminlte.min.js')}}"></script>

<!-- Modal Script -->
<script>
    const login = '{{ route('Login') }}'
    const logout = '{{route('Logout')}}'
    const logininstructor =  '{{route('Login/Instructors')}}'
 </script>
<script src="{{asset('js/Auth.js')}}"></script>

</body>
</html>
