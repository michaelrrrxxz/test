@extends('layouts.default')
@section('title', 'No Exam')
@section('content-header')
<li class="breadcrumb-item green"><a href="{{route("Dashboard")}}">Dashboard</a></li>
<li class="breadcrumb-item active">No Exam</li>
@endsection
@section('content')
@php
    $course = [
        'BSIT', 'BSN', 'BSBA-FM', 'BSBA-MM', 'BSED-EU', 'BSED-FIL',
        'BSED-MATH', 'BSED-SCI', 'BSA', 'BSED-ENGLISH', 'BEED',
        'AB-MASS COM', 'BSCRIM', 'BSHM', 'BSMA', 'AB-POL SCI',
        'BSBA-HRM', 'MIDWIFERY', 'AB-ENG', 'BSGE', 'BSBA-MA'
    ];
@endphp

<div class="col-12 content-card">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title text-white">No Exam List</h3>
            <div class="card-tools">
                <a href="" id="export" data-toggle="tooltip" data-placement="bottom" title="Export"><i class="fas fa-file-download text-white"></i></a>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Course Filter -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="course-filter" class="form-label">Filter by Course:</label>
                    <select id="course-filter" class="form-select">
                        <option value="">All Courses</option>
                        @foreach ($course as $courseName)
                            <option value="{{ $courseName }}">{{ $courseName }}</option>
                        @endforeach
                    </select>
                </div>
            
                <div class="col-md-6">
                    <label for="gender-filter" class="form-label">Filter by Gender:</label>
                    <select id="gender-filter" class="form-select">
                        <option value="">All</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                  
                </div>
                
            </div>
            
            
            <!-- Data Table -->
            <table id="noexam-table" class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>ID Number</th>
                        <th>Course</th>
                        <th>Name</th>
                        <th>Gender</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Table data will be populated here by Students.js -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="export-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Export Options</h4> <!-- Added title for clarity -->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="button_table" class="container">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <button type="button" class="btn btn-primary w-100" id="export-pdf" aria-label="Export to PDF">
                                <i class="fas fa-file-pdf"></i> Export to PDF
                            </button>
                        </div>
                        <div class="col-md-6 mb-2">
                            <button type="button" class="btn btn-success w-100" id="export-csv" aria-label="Export to CSV">
                                <i class="fas fa-file-csv"></i> Export to CSV
                            </button>
                        </div>
                        <div class="col-md-6 mb-2">
                            <button type="button" class="btn btn-info w-100" id="export-excel" aria-label="Export to Excel">
                                <i class="fas fa-file-excel"></i> Export to Excel
                            </button>
                        </div>
                        <div class="col-md-6 mb-2">
                            <button type="button" class="btn btn-secondary w-100" id="export-print" aria-label="Print">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
                
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script src="{{ asset('js/Students.js') }}"></script>


@endsection
