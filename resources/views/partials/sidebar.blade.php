<aside class="main-sidebar elevation-4 sidebar-light-olive" style="position: fixed; height: 100vh; overflow-y: auto;">
    <a href="#" class="brand-link" style="background-color: #048e2b;">
        <img src="{{ asset('img/image.png') }}" class="brand-image img-circle elevation-3" style="opacity:0.8;" alt="">
        <span class="brand-text font-weight-light text-white">NCSAT</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                @if(Auth::check())
                    <img src="{{ asset(Auth::user()->profile ? Auth::user()->profile : 'storage/profile_pictures/default.png') }}" class="img-circle elevation-2" alt="User Image" style="width: 50px; height: 50px;">
                @else
                    <img src="{{ asset('storage/profile_pictures/default.png') }}" class="img-circle elevation-2" alt="User Image" style="width: 50px; height: 50px;">
                @endif
            </div>
            <div class="info">



             
                <a href="#" class="d-block">
                    @if(Auth::check())
                        Welcome,<br> {{ Auth::user()->username }}
                        <hr style="margin: 0; padding: 0;">
                        Role: {{ Auth::user()->role }}
                        @if(Auth::user()->role === 'instructor')
                            <br>Department: {{ Auth::user()->department }}
                        @endif
                    @else
                        Please log in to view your account.
                    @endif
                    <hr style="margin: 0; padding: 0;">
                </a>
            </div>
        </div>

        <nav class="mt-2 mb-0">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @if(Auth::check())
                    @if(Auth::user()->role === 'instructor')
                        <li class="nav-item">
                            <a href="{{ route('Instructors/dashboard') }}" class="nav-link {{ request()->is('Instructors/dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                    @elseif(Auth::user()->role === 'administrator' || Auth::user()->role === 'user')
                        <li class="nav-item">
                            <a href="{{ route('Dashboard') }}" class="nav-link {{ request()->is('Dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                    @endif

                    @if(Auth::user()->role === 'administrator')
                        <li class="nav-item">
                            <a href="{{ route('Batch') }}" class="nav-link {{ request()->is('Batch') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-layer-group"></i>
                                <p>Batch for Examination</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('Results') }}" class="nav-link {{ request()->is('Results') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>Exam Results</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('No-Exam') }}"
                               class="nav-link {{ request()->is('No-Exam') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user"></i>
                                <p>Non Evaluated Students</p>
                            </a>
                        </li>


                        <li class="nav-item">
                            <a href="{{ route('Questions') }}" class="nav-link {{ request()->is('Questions') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-question"></i>
                                <p>Questions List</p>
                            </a>
                        </li>

                        <li class="nav-header">SETTINGS</li>
                        <li class="nav-item">
                            <a href="{{ route('Courses') }}" class="nav-link {{ request()->is('Courses') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>Courses List</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('Departments') }}" class="nav-link {{ request()->is('Departments') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-building"></i>
                                <p>Departments List</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('EnrolledStudents') }}" class="nav-link {{ request()->is('EnrolledStudents') ? 'active' : '' }}">
                                <i class="nav-icon fas  fa-graduation-cap"></i>
                                <p>Enrolled Students list</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('Users') }}" class="nav-link {{ request()->is('Users') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user-cog"></i>
                                <p>Users Lists</p>
                            </a>
                        </li>
                    @endif
                @endif
            </ul>
        </nav>
    </div>
</aside>
