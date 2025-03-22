@php
$departments = [
            'CIT',
            'CON',
            'CABA',
            'COED',
            'COLA',
            'COC',
            'COHM',
            'COLA',
            'SOM',
            'COLA',
            'COGE',
           
           
        ];
        $course = [
    'BSIT',
    'BSN',
    'BSBA-FM',
    'BSBA-MM',
    'BSED-EU',
    'BSED-FIL',
    'BSED-MATH',
    'BSED-SCI',
    'BSA',
    'BSED-ENGLISH',
    'BEED',
    'AB-MASS COM',
    'BSCRIM',
    'BSHM',
    'BSMA',
    'AB-POL SCI',
    'BSBA-HRM',
    'MIDWIFERY',
    'AB-ENG',
    'BSGE',
    'BSBA-MA',
];
@endphp
<nav class="main-header navbar navbar-expand navbar-white navbar-light" style="background-color: #048e2b;">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link text-white" data-widget="fullscreen" href="#" role="button" id="fullscreen-toggle">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                <i class="fas fa-th-large"></i>
            </a>
        </li>
    </ul>
</nav>


<aside class="control-sidebar control-sidebar-dark">
    <div class="p-3">
        <ul class="nav nav-pills nav-sidebar flex-column" role="menu" data-accordion="false">

            <li class="nav-item">
                <a id="edit-profile" class="nav-link text-white">
                    <i class="nav-icon fas fa-solid fa-user-edit"></i>
                    <p>Edit Profile</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('viewLoginHistory') }}"  class="nav-link text-white">
                    <i class="nav-icon fas fa-history"></i>
                    <p>Login History</p>
                </a>
            </li>

            <li class="nav-item">
                <form id="logoutForm" action="{{ route('Logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <a href="#" id="logoutButton" class="nav-link text-white" onclick="confirmLogout(event)">
                    <i class="nav-icon fas fa-power-off"></i>
                    <p>Logout</p>
                </a>
            </li>
            
            <script>
                function confirmLogout(event) {
                    event.preventDefault();  // Prevent the default form submission
                    
                    // Show the SweetAlert confirmation
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Do you want to log out?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, logout!',
                        cancelButtonText: 'No, cancel',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // If confirmed, submit the form
                            document.getElementById('logoutForm').submit();
                        }
                    });
                }
            </script>
            
            
        </ul>
    </div>
</aside>





<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="profile-modal">
    <div class="modal-dialog modal-lg"> <!-- Enlarged modal for better spacing -->
        <div class="modal-content">
            <div class="modal-header text-white">
                <h4 class="modal-title text-dark" id="modal-title"></h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="profile-form" enctype="multipart/form-data" autocomplete="off">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="username" class="font-weight-bold">Username</label>
                        <input class="form-control" name="username" id="username-edit" placeholder="Enter username">

                        <label for="password" class="font-weight-bold mt-3">Password</label>
                        <input type="password" class="form-control" name="password" id="password-edit" placeholder="Enter password">

                        <label for="role" class="font-weight-bold mt-3">Role</label>
                        <select name="role" id="role-edit" class="form-control" onchange="toggleDepartmentField()">
                            <option value="administrator">Admin</option>
                            <option value="user">User</option>
                            <option value="instructor">Instructor</option>
                        </select>

                        <!-- Department Field -->
                        <div id="departmentField-edit" class="mt-3" style="display: none;">
                            <label for="department" class="font-weight-bold">Department</label>
                            <select name="department" id="department" class="form-control">
                                <option value="">All</option>
                                @foreach ($departments as $department)
                                <option value="{{ $department }}">{{ $department }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Profile picture input -->
                        <label for="profile" class="font-weight-bold mt-3">Profile Picture</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="profile" id="profile" accept="image/*">
                                <label class="custom-file-label" for="profile">Choose file</label>
                            </div>
                        </div>

                        <!-- Image preview container -->
                        <div id="image-preview-container" class="mt-3 text-center" style="display:none;">
                            <img id="image-preview" src="" alt="Profile Picture" class="img-thumbnail" style="max-width: 200px;">
                            <button type="button" class="btn btn-danger btn-sm mt-2" id="remove-image-btn">Remove</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" id="id">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" name="save">Save</button>
                </div>
            </form>

            <div class="px-3 pb-3">
                <button class="btn btn-danger btn-block" id="delete-profile">Delete Profile</button>
            </div>
        </div>
    </div>
</div>


<script src="{{asset('js/Auth.js')}}"></script>
<script src="{{asset('js/Profile.js')}}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var fullscreenToggle = document.getElementById('fullscreen-toggle');
    
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(console.error);
                localStorage.setItem('fullscreen', 'true');
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
                localStorage.setItem('fullscreen', 'false');
            }
        }
    
        fullscreenToggle.addEventListener('click', function(event) {
            event.preventDefault();
            toggleFullscreen();
        });
    
        // Check if fullscreen mode was enabled on the previous visit
        if (localStorage.getItem('fullscreen') === 'true') {
            document.documentElement.requestFullscreen().catch(console.error);
        }
    });

</script>