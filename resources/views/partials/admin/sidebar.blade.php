<aside class="main-sidebar elevation-4 sidebar-light-olive">
    <a href="" class="brand-link" style="background-color: #048e2b;"><img src="{{ asset('img/image.png') }}"
            class="brand-image img-circle elevation-3" style="opacity:0.8;" alt=""><span
            class="brand-text font-weight-light text-white">NCAT</span></a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#" class="d-block">
                    @if(Auth::guard('instructor')->check())  
                        Welcome, {{ Auth::guard('instructor')->user()->username }} 
                        <hr style="margin: 0; padding: 0;">
                        Instructor
                    @elseif(Auth::check())  
                        Welcome, {{ Auth::user()->username }}  
                        <hr style="margin: 0; padding: 0;">
                        {{ Auth::user()->role }}  
                    @else
                        Please log in to view your account. 
                    @endif
                    <hr style="margin: 0; padding: 0;">
                </a>
                
            </div>
            
        </div>
        <nav class="mt-2 mb-0">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('Dashboard') }}" class="nav-link {{ request()->is('Dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                
                @if(auth()->check() && auth()->user()->role == 'administrator') 
                <li class="nav-item">
                    <a href="{{ route('Batch') }}" class="nav-link {{ request()->is('Batch') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-layer-group"></i>
                        <p>Batch</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('Instructors') }}"
                        class="nav-link {{ request()->is('Instructors') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Instructors</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{ route('Results') }}"
                        class="nav-link {{ request()->is('Results') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-alt "></i>
                        <p>Results</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('NoExam') }}"
                        class="nav-link {{ request()->is('NoExam') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>No Exam</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('Questions') }}"
                        class="nav-link {{ request()->is('Questions') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-question"></i>
                        <p>Questions</p>
                    </a>
                </li>


                
                <li class="nav-header">SETTINGS</li>

                <li class="nav-item {{ request()->is('EnrolledStudents') || request()->is('Students') ? 'menu-open' : 'menu-close' }}">
                    <a href="#" class="nav-link {{ request()->is('EnrolledStudents') || request()->is('Students') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-graduation-cap"></i>
                        <p> Students <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('EnrolledStudents') }}"
                                class="nav-link {{ request()->is('EnrolledStudents') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-graduation-cap"></i>
                                <p>Enrolled Students</p>
                            </a>
                        </li>
                        



                        {{-- <li class="nav-item">
                            <a href="{{ route('Students') }}"
                                class="nav-link {{ request()->is('Students') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-school"></i>
                                <p>Students</p>
                            </a>
                        </li> --}}


                    </ul>
                </li>


                <li class="nav-item">
                    <a href="{{ route('Users') }}"
                        class="nav-link {{ request()->is('Users') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>Users</p>
                    </a>
                </li>
            @endif


                <!-- Additional nav items can be added here -->
            </ul>
        </nav>
    </div>
</aside>
<style>

</style>
