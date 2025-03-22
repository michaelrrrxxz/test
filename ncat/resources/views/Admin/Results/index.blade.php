@extends('layouts.default')
@section('title', 'Result')
@section('content-header')
<li class="breadcrumb-item green"><a href="{{route("Dashboard")}}">Dashboard</a></li>
<li class="breadcrumb-item active">Results</li>
@endsection
@section('content')
@php
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
<link rel="stylesheet" href="{{asset('plugin/tippy/tippy.css')}}">
<style>
    /* Style for the tooltip content and background */
.tooltip-bg {
    position: relative;
    background-image: url('{{asset('img/nc-logo1.png')}}'); /* Set the background image */
    background-size: cover;       /* Cover the entire tooltip area */
    background-repeat: no-repeat;
    width: 250px;                 /* Adjust the width of the tooltip */
    height: 200px;                /* Adjust the height of the tooltip */
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;                 /* Text color */
    border-radius: 8px;           /* Optional rounded corners */
    padding: 10px;
    /* Darker overlay */
    background-color: rgba(255, 255, 255, 0.5); /* Black overlay with 50% opacity */
}

/* Adding a fade effect overlay */
.tooltip-bg::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(0deg, rgba(0, 0, 0, 0.5), #048e2b); /* Fade effect from dark to green */
    border-radius: 8px; /* Match the parent border radius */
    z-index: 0; /* Ensure it's behind the content */
}

/* Style for the text inside the tooltip */
.tooltip-content {
    position: relative; /* Ensure it's on top of the overlay */
    z-index: 1; /* Above the overlay */
    font-size: 14px;
    font-weight: bold;
    text-shadow: 1px 1px 2px black; /* Optional text shadow for visibility */
}


</style>

<div class="col-12 content-card">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title text-white">Exam Results List</h3>
            <div class="card-tools">
                <a href="#" id="print" data-toggle="tooltip" data-placement="bottom" title="Print"> 
                    <i class="fas fa-download text-white"></i> 
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive p-0">
                <label for="course-filter" class="form-label">Filter by Course:</label>
                    <select id="course-filter" class="form-select">
                        <option value="">All Courses</option>
                        @foreach ($course as $courseName)
                            <option value="{{ $courseName }}">{{ $courseName }}</option>
                        @endforeach
                    </select>
                
                <table id="results-table" class="table table-bordered table-hover small table-striped text-center text-capitalize smallr">
                    <thead class="bg-light">
                        <tr>
                            <th rowspan="3" class="align-middle">ID No.</th>
                            <th rowspan="3" style="width: 200px;" class="align-middle">Name</th>
                            <th rowspan="3" class="align-middle">Course</th>
                            <th rowspan="3" class="align-middle" title="Raw Score">Raw Score</th>
                            <th rowspan="3" class="align-middle" title="Student Ability Index">SAI</th>
                            <th colspan="2" title="Performance By Age">PBA</th>
                            <th colspan="2" title="Peformance By Grade">PBG</th>
                            <th colspan="5">Verbal</th>
                            <th colspan="5">Non-Verbal</th>
                            <th rowspan="3" class="align-middle">Options</th>
                        </tr>
                        <tr>
                            <!-- PBA -->
                            <th rowspan="2" class="align-middle" title="Percentile Rank">PR</th>
                            <th rowspan="2" class="align-middle" title="Stanine">S</th>
                            <!-- PBG -->
                            <th rowspan="2" class="align-middle" title="Percentile Rank">PR</th>
                            <th rowspan="2" class="align-middle" title="Stanine">S</th>
                            <!-- Verbal -->
                            <th colspan="2" title="Verbal Comprehension">VC</th>
                            <th colspan="2"title="Verbal Reasoning">VR</th>
                            <th rowspan="2" class="align-middle">Total</th>
                            <!-- NonVerbal -->
                            <th colspan="2" title="Quantitative Reasoning">QR</th>
                            <th colspan="2" title="Figural Reasoning">FR</th>
                            <th rowspan="2" class="align-middle">Total</th>
                        </tr>
                        <tr>
                            <!-- Verbal -->
                            <th class="align-middle" title="Score">S</th>
                            <th class="align-middle" title="Performance Category">PC</th>
                            <th class="align-middle" title="Score">S</th>
                            <th class="align-middle" title="Performance Category">PC</th>
                            <!-- NonVerbal -->
                            <th class="align-middle" title="Score">S</th>
                            <th class="align-middle" title="Performance Category">PC</th>
                            <th class="align-middle" title="Score">S</th>
                            <th class="align-middle" title="Performance Category">PC</th>
                        </tr>
                    </thead>
                </table>
            </div>
            
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
                <div id="button_table">
                    <button type="button" class="btn btn-primary" id="export-pdf" aria-label="Export to PDF">Export to PDF</button>
                    <button type="button" class="btn btn-success" id="export-csv" aria-label="Export to CSV">Export to CSV</button>
                    <button type="button" class="btn btn-info" id="export-excel" aria-label="Export to Excel">Export to Excel</button>
                    <button type="button" class="btn btn-secondary" id="export-print" aria-label="Print">Print</button>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('plugin/popperjs/popper.min.js')}}"></script>
{{-- <script src="{{asset('plugin/tippy/tippy.js')}}"></script> --}}
<script src="{{asset('js/Results.js')}}"></script>
@endsection