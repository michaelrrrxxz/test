<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NCSAT</title>
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
    <div class="alert alert-danger">
        {{ session('error') }}
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
            <!-- Tab navigation -->
            <ul class="nav nav-tabs justify-content-center" id="loginTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="email-tab" data-toggle="tab" href="#emailPane" role="tab" aria-controls="emailPane" aria-selected="true">Email Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="accessKey-tab" data-toggle="tab" href="#accessKeyPane" role="tab" aria-controls="accessKeyPane" aria-selected="false">Access Key Login</a>
                </li>
            </ul>  
            <!-- Tab content -->
            <div class="tab-content mt-4" id="loginTabContent">
                <!-- Email Form Tab -->
                <div class="tab-pane fade show active" id="emailPane" role="tabpanel" aria-labelledby="email-tab">
                    <form method="POST" action="{{ route('send.otp') }}" id="emailForm">
                        @csrf <!-- CSRF protection token for Laravel -->
                        
                        <!-- ID Number Input -->
                        <div class="input-group mb-3">
                            <input type="text" name="id_number" class="form-control" placeholder="ID Number" required="required" id="id_number">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-id-card"></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Email Input -->
                        <div class="input-group mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email" required="required" id="email">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>
                    
                        <!-- Agreement Checkbox -->
                        <div class="row justify-content-center mb-3">
                            <div class="form-check text-center">
                                <input type="checkbox" class="form-check-input" id="userAgreement">
                                <label class="form-check-label ml-2" for="userAgreement">
                                    I agree to the <a id="agreement-showw" class="text-primary">User Agreement</a>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Take Exam Button -->
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-success btn-block" id="usingemail" disabled>Take Exam</button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Admin Modal (triggered when results exist) -->
                    @if(session('show_admin_modal'))
                    <div id="adminModal" class="modal">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <h2>Admin Login Required</h2>
                            <form action="{{ route('authenticateAdmin') }}" method="POST">
                                @csrf
                                <div>
                                    <label for="username">Username:</label>
                                    <input type="text" id="username" name="username" required>
                                </div>
                    
                                <div>
                                    <label for="password">Password:</label>
                                    <input type="password" id="password" name="password" required>
                                </div>
                    
                                <button type="submit">Login</button>
                            </form>
                        </div>
                    </div>
                    @endif
                    
                    <script>

                        
                    // Enable the "Take Exam" button only if the user checks the agreement
                    document.getElementById('userAgreement').addEventListener('change', function() {
                        document.getElementById('usingemail').disabled = !this.checked;
                    });
                    
                    // Modal handling script (for admin login)
                    @if(session('show_admin_modal'))
                    var modal = document.getElementById("adminModal");
                    var span = document.getElementsByClassName("close")[0];
                    
                    // Show modal if the session has 'show_admin_modal'
                    modal.style.display = "block";
                    
                    // Close the modal
                    span.onclick = function() {
                      modal.style.display = "none";
                    }
                    
                    window.onclick = function(event) {
                      if (event.target == modal) {
                        modal.style.display = "none";
                      }
                    }
                    @endif
                    </script>
                    
                    
                </div>
    
                <!-- Access Key Form Tab -->
                <div class="tab-pane fade" id="accessKeyPane" role="tabpanel" aria-labelledby="accessKey-tab">
                    <form method="POST" action="{{ route('EnrolledStudents/verifyAccessKey') }}" id="accessKeyForm">
                        @csrf <!-- CSRF protection token for Laravel -->
                        <div class="input-group mb-3">
                            <input type="text" name="id_number" class="form-control" placeholder="ID Number" required="required" id="id_number_access">
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

                        <div class="row justify-content-center mb-3">
                            <div class="form-check text-center">
                                <input type="checkbox" class="form-check-input" id="userAgreementt">
                                <label class="form-check-label ml-2" for="userAgreement">
                                    I agree to the <a id="agreement-show" class="text-primary">User Agreement</a>
                                </label>
                            </div>
                        </div>
    
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-success btn-block" id="usingkey" disabled>Take Exam</button>
                            </div>
                        </div>
                    </form>
                </div>
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
                <h4 class="modal-title">Login</h4>
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

<!-- Modal HTML -->
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="agreement-modal">
    <div class="modal-dialog">
        <div class="modal-content modal-custom">
            <div class="modal-header modal-header-custom">
                <h4 class="modal-title">User Agreement</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="users-form">
                <div class="modal-body modal-body-custom">
                    <div class="form-group">
                        <h5 class="section-title">DISCLAIMER</h5>
                        <p class="section-content">The NCAT System, developed in collaboration with Northeastern College Santiago City, is designed for educational purposes and may contain inaccuracy or potential loss of credibility over time. The system and Northeastern College cannot be held responsible for any unwanted events or information inaccuracy. Users are advised to independently verify information and consult professionals for better credibility. The information provided is for educational purposes only.</p>
                        
                        <h5 class="section-title">USER POLICY</h5>
                        <ul class="policy-list">
                            <li>The "user" will provide the information required when they register for the application. This will be for users who take the exam on the scheduled day and only for freshman students.</li>
                            <li>If the user fails to log in, it means they are not a student who will take the exam on that day, or else they do not register students at Northeastern College.</li>
                            <li>When the user logs in successfully, they are a valid student who is going to take the exam within that day, and they are a freshman student at Northeastern College.</li>
                        </ul>

                        <h5 class="section-title">Data Privacy Policy</h5>
                        <ul class="policy-list">
                            <li>The organization (“Northeastern College”) will not ask for any additional information aside from the required information from the user, such as their ID number and Email.</li>
                            <li>The organization ("Northeastern College") shall not be held responsible for the leakage of unwanted information supplied by the "user" to any third party without permission and authorization of the requirement.</li>
                            <li>The 'User' shall not disclose his or her information with any third party without legitimate or prior consent from the organization unless legally required.</li>
                        </ul>

                        <h5 class="section-title">SYSTEM POLICY</h5>
                        <ul class="policy-list">
                            <li>The user will first need to register within the computer for access to the complete functionality of the system.</li>
                            <li>The NCAT system is provided only to those freshman students who are taking the exam, further updates of the student's data will be added by the administrator of the system.</li>
                            <li>A user needs to be connected to the Internet; a medium-to-strong Internet connection is needed for stable functionality of the system.</li>
                        </ul>

                        <h5 class="section-title">DEVICE POLICY</h5>
                        <ul class="policy-list">
                            <li>The "user" needs to log into the computer; it is always advisable that the signal or the reception of the network is strong.</li>
                            <li>The logged-in user should have a phone device to verify the students by sending OTP to access the examination. Once they log in, there is limited time to finish the exam.</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Custom CSS -->
<style>
    /* Modal Container Styling */
    .modal-dialog {
        max-width: 700px;
        margin: 1.75rem auto;
    }
    
    .modal-custom {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.3);
    }
    
    /* Header Styling */
    .modal-header-custom {
        background-color: #048e2b;
        color: #ffffff;
        padding: 15px 20px;
        border-bottom: 1px solid #ddd;
    }
    .modal-header-custom h4 {
        margin: 0;
        font-weight: bold;
    }

    /* Body Styling */
    .modal-body-custom {
        max-height: 60vh;
        overflow-y: auto;
        padding: 20px;
        background-color: #f8f9fa;
    }
    .section-title {
        font-size: 1.2em;
        font-weight: bold;
        color: #048e2b;
        margin-top: 15px;
        margin-bottom: 10px;
    }
    .section-content {
        color: #333;
        margin-bottom: 15px;
        line-height: 1.6;
    }
    
    /* List Styling */
    .policy-list {
        padding-left: 20px;
        margin: 0 0 15px;
        color: #555;
    }
    .policy-list li {
        margin-bottom: 8px;
        line-height: 1.5;
    }

    /* Footer Styling */
    .modal-footer-custom {
        padding: 10px 20px;
        border-top: 1px solid #ddd;
        background-color: #f1f1f1;
        display: flex;
        justify-content: flex-end;
    }
    .btn-default {
        color: #048e2b;
        border-color: #048e2b;
        background-color: #ffffff;
    }
    .btn-default:hover {
        background-color: #048e2b;
        color: #ffffff;
    }
</style>


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
 </script>
<script src="{{asset('js/Auth.js')}}"></script>

</body>
</html>
