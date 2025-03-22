@extends('layouts.default')
@section('title', 'Batch')
@section('content-header')
<li class="breadcrumb-item green"><a href="{{route("Dashboard")}}">Dashboard</a></li>
<li class="breadcrumb-item active">Batch</li>
@endsection
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
            <h3 class="card-title text-white">Batch List</h3>
            <div class="card-tools">
                <a href="" id="printall" data-toggle="tooltip" data-placement="bottom" title="Print all Batches"><i class="fas fa-print text-white"></i></a> <span class="text-white">|</span>
                <a href="" id="add" data-toggle="tooltip" data-placement="bottom" title="Add Batch"><i class="fas fa-plus text-white"></i></a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="year-filter" class="form-label">Filter by School Year:</label>
                    <select id="year-filter" class="form-select">
                        <option value="">All</option>
                        @php
                            $currentYear = date('Y');
                            $startYear = 2024;
                        @endphp
                
                        {{-- Loop through the years starting from the start year --}}
                        @for ($year = $startYear; $year <= $currentYear; $year++)
                            <option value="{{ $year }}" 
                                @if ($year == $currentYear) selected @endif>
                                {{ $year }} - {{ substr($year + 1, -2) }}
                            </option>
                        @endfor
                    </select>
                </div>
                
            
              
                
            </div>
            <table id="batch-table" class="table table-bordered table-hover table-striped text-capitalize">
                <thead>
                <tr>
                    <th>Batch Name</th>
                    <th>Description</th>
                    <th>Access Key</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Date Created</th>
                    <th>Options</th>
                </tr>
                </thead>

            </table>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="batch-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="batch-form">
                @csrf
                <div class="modal-body">
                  <div class="form-group">
                    <label>Batch Name</label>
                    <input class="form-control" name="name" id="name">
                    
                    <label>Description</label>
                    <input class="form-control" name="description" id="description">
              
                    {{-- <label>Duration</label>
                    <div class="input-group">
                        <input type="number" class="form-control" name="duration" id="duration" required placeholder="Enter duration">
                        <div class="input-group-append">
                        <select name="unit" id="unit" class="form-control" style="width: 100px;">
                            <option value="minutes">Minutes</option>
                            <option value="hours">Hours</option>
                        </select>
                        </div>
                    </div> --}}
                    <input class="form-control" name="duration" id="duration" value="2700" required hidden>         
                    <label id="statusLabel" hidden>Status</label>
                    <select name="status" id="status" class="form-control" value="active" hidden> 
                      <option value="active">Active</option>
                      <option value="locked">Locked</option>
                    </select>
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

<style>
    .modal-dialog.modal-xl-custom {
    width: 100%;
    max-width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
}

.modal-content {
    height: 100%;
    border-radius: 0;
}

</style>
<!-- Modal -->
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="getStudentbyBatch-modal">
    <div class="modal-dialog modal-xl-custom">
        <div class="modal-content">
            <div class="modal-header">
                <div class="card-tools">
                    <a href="" id="add" data-toggle="tooltip" data-placement="bottom" title="Add User"><i class="fas fa-plus text-white"></i></a>
                </div>
                
                <h4 class="modal-title">View Batch</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-12 content-card">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title text-white text-capitalize"></h3>
                            <div class="card-tools">
                                <a href="" id="print" data-toggle="tooltip" data-placement="bottom" title="Export"><i class="fas fa-print text-white"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="students-table" class="table table-bordered small table-hover table-striped text-center text-capitalize">
                                <thead class="bg-light">
                                    <tr>
                                        <th rowspan="3" class="align-middle">ID No.</th>
                                        <th rowspan="3" class="align-middle">Name</th>
                                        <th rowspan="3" class="align-middle">Course</th>
                                        <th rowspan="3" class="align-middle" title="Raw Score">RS</th>
                                        <th rowspan="3" class="align-middle" title="Student Ability Index">SAI</th>
                                        <th colspan="2" title="Performance By Age">PBA</th>
                                        <th colspan="2" title="Peformance By Grade">PBG</th>
                                        <th colspan="5">V</th>
                                        <th colspan="5">NV</th>
                                    </tr>
                                    <tr>
                                        <!-- PBA -->
                                        <th rowspan="2" class="align-middle">PR</th>
                                        <th rowspan="2" class="align-middle">S</th>
                                        <!-- PBG -->
                                        <th rowspan="2" class="align-middle">PR</th>
                                        <th rowspan="2" class="align-middle">S</th>
                                        <!-- Verbal -->
                                        <th colspan="2" title="">VC</th>
                                        <th colspan="2" title="">VR</th>
                                        <th rowspan="2" class="align-middle">T</th>
                                        <!-- NonVerbal -->
                                        <th colspan="2" title="Quantitative Reasoning">QR</th>
                                        <th colspan="2" title="Figural Reasoning">FR</th>
                                        <th rowspan="2" class="align-middle">T</th>
                                    </tr>
                                    <tr>
                                        <!-- Verbal -->
                                        <th class="align-middle">S</th>
                                        <th class="align-middle">PC</th>
                                        <th class="align-middle">S</th>
                                        <th class="align-middle">PC</th>
                                        <!-- NonVerbal -->
                                        <th class="align-middle">S</th>
                                        <th class="align-middle">PC</th>
                                        <th class="align-middle">S</th>
                                        <th class="align-middle">PC</th>
                                    </tr>
                                </thead>
                                <tbody>
                            
                                </tbody>
                            </table>
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

<script>
      var viewUrl = "{{ route('Batch/viewgetBatch') }}";
</script>
<script src="{{asset('js/Batch.js')}}"></script>
@endsection