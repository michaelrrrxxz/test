@extends('layouts.default')
@section('title', 'Enrolled Students')
@section('content-header')
<li class="breadcrumb-item green"><a href="{{route("Dashboard")}}">Dashboard</a></li>
<li class="breadcrumb-item active">Enrolled Students</li>
@endsection
@section('content')


<div class="col-12 content-card">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title text-white">Enrolled Students List</h3>
            <div class="card-tools">
                
                <a href="" id="add" data-toggle="tooltip" data-placement="bottom" title="Add User"><i class="fas fa-plus text-white"></i></a> <span class="text-white">|</span>
                <a href="" id="upload" data-toggle="tooltip" data-placement="bottom" title="Upload User"><i class="fas fa-upload text-white"></i></a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    
                    <label for="course-filter-enrolled" class="form-label">Filter by Course:</label>
                    <select id="course-filter-enrolled" class="form-select">
                        <option value="">All Courses</option>
                        @foreach ($course as $courseName)
                            <option value="{{ $courseName->name }}">{{ $courseName->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="year-filter" class="form-label">Filter by School Year:</label>
                    <select id="year-filter" class="form-select">
                        <option value="">All</option>
                        @php
                            $currentYear = date('Y');
                            $startYear = 2024;
                        @endphp
                        @for ($year = $startYear; $year <= $currentYear; $year++)
                            <option value="{{ $year }}" 
                                @if ($year == $currentYear) selected @endif>
                                {{ $year }}
                            </option>
                        @endfor
                       
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="department-filter" class="form-label">Filter by Department:</label>
                    <select  id="department-filter">
                        <option value="">All Departments</option>
                        @foreach ($departments as $department)
                        <option value="{{ $department->name }}">{{ $department->name }}</option>
                    @endforeach
                    </select>
                </div>
                
            </div>
            <table id="enrolledstudents-table" class="table table-bordered table-hover table-striped">
                <thead>  
                <tr>
                    <th>ID number</th>
                    <th>Course</th> 
                    <th>Department</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Exam Year</th>
                    <th>Options</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>







<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="upload-modal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="enrolledstudents-form" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Excel File</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input name="csv_file" type="file" class="custom-file-input" id="csv_file" accept=".csv">
                                <label class="custom-file-label" for="customFile">Upload Excel File</label>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.getElementById('csv_file').addEventListener('change', function(event) {
        var input = event.target;
        var label = input.nextElementSibling;
        var fileName = input.files[0].name;
        label.innerText = fileName;
    });
</script>


<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="enrolledstudents-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="add-enrolledstudents-form"">
  
                @csrf
                <div class="modal-body">
                    <div class="form-group"> 
                        <label>ID Number</label>
                        <input class="form-control" name="id_number" id="id_number" required>
                        <label>Name</label>
                        <input class="form-control" name="name" id="name" required>
                        <label>Course</label>
                        <select class="form-control" name="course" id="course" required>
                            <option value=""></option>
                            @foreach ($course as $courseName)
                            <option value="{{ $courseName->name }}">{{ $courseName->name }}</option>
                            @endforeach
                        </select>
                        <label>Department</label>
                        <select class="form-control" name="department" id="department" required>
                            <option value=""></option>
                            @foreach ($departments as $department)
                        <option value="{{ $department->name }}">{{ $department->name }}</option>
                    @endforeach
                        </select>
                        {{-- <label>Address</label>
                        <input class="form-control" name="address" id="address" required> --}}
                        <label>Gender</label>
                        <select class="form-control" name="gender" id="gender" required>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                        </select>
                        {{-- <label> Birthday </label>
                        <input name="birthday" type="date" class="form-control" max="<?php echo date('Y-m-d'); ?>">
                        <input class="form-control" name="exam_year" id="exam_year" required hidden>     --}}
  
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id" id="id">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" name="save">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{asset('js/EnrolledStudents.js')}}"></script>
@endsection