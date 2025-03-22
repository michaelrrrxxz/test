@extends('layouts.default')

@section('content')

<link rel="stylesheet" href="{{asset('plugin/tippy/tippy.css')}}">
<style>
    /* Style for the tooltip content and background */
.tooltip-bg {
    position: relative;
    background-image: url('{{asset('img/nc-logo1.png')}}'); /* Set the background image */
    background-size: cover;       /* Cover the entire tooltip area */
    background-repeat: no-repeat;
    width: 250px;                 /* Adjust the width of the tooltip */
    height: 150px;                /* Adjust the height of the tooltip */
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
            </div>
        </div>
        <div class="card-body">
            <table id="results-table" class="table table-bordered table-responsive table-hover table-striped text-center text-capitalize">
                <thead class="bg-light">
                    <tr>
                        <th rowspan="3" class="align-middle">ID No.</th>
                        <th rowspan="3" class="align-middle">Name</th>
                        <th rowspan="3" class="align-middle">Course</th>
                        <th rowspan="3" class="align-middle" title="Raw Score">Raw Score</th>
                        <th rowspan="3" class="align-middle" title="Student Ability Index">SAI</th>
                        <th colspan="2" title="Performance By Age">Performance By Age</th>
                        <th colspan="2" title="Peformance By Grade">Performance By Grade</th>
                        <th colspan="5">Verbal</th>
                        <th colspan="5">Non-Verbal</th>
                    </tr>
                    <tr>
                        <!-- PBA -->
                        <th rowspan="2" class="align-middle">Percentile Rank</th>
                        <th rowspan="2" class="align-middle">Stanine</th>
                        <!-- PBG -->
                        <th rowspan="2" class="align-middle">Percentile Rank</th>
                        <th rowspan="2" class="align-middle">Stanine</th>
                        <!-- Verbal -->
                        <th colspan="2" title="">Verbal Comprehension</th>
                        <th colspan="2" title="">Verbal Reasoning</th>
                        <th rowspan="2" class="align-middle">Total</th>
                        <!-- NonVerbal -->
                        <th colspan="2" title="Quantitative Reasoning">QR</th>
                        <th colspan="2" title="Figural Reasoning">FR</th>
                        <th rowspan="2" class="align-middle">Total</th>
                    </tr>
                    <tr>
                        <!-- Verbal -->
                        <th class="align-middle">Score</th>
                        <th class="align-middle">Performance Category</th>
                        <th class="align-middle">Score</th>
                        <th class="align-middle">Performance Category</th>
                        <!-- NonVerbal -->
                        <th class="align-middle">Score</th>
                        <th class="align-middle">Performance Category</th>
                        <th class="align-middle">Score</th>
                        <th class="align-middle">Performance Category</th>
                    </tr>
                </thead>
                <tbody>
            
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="{{asset('plugin/popperjs/popper.min.js')}}"></script>
<script src="{{asset('plugin/tippy/tippy.js')}}"></script>
<script src="{{asset('js/Batch.js')}}"></script>
@endsection