@extends('layouts.default')

@section('title', 'Dashboard')

@section('content-header')
<li class="breadcrumb-item green"><a href="{{route("Dashboard")}}">Dashboard</a></li>
<li class="breadcrumb-item active">Instructor</li>
@endsection

@section('content')
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
<div class="row content-card">

    <!-- Top Row: Dashboard Widget Boxes -->
    <div class="col-12 mb-4">
        <div class="row">
            <!-- Existing Widgets -->
            @foreach ([
                       ['Enrolled Students', $enrolled, 'bg-success', 'fas fa-user-graduate', 'EnrolledStudents'],
                       ['Non Evaluated Students', $countWithoutExam, 'bg-warning', 'fas fa-user-times', 'No-Exam'],
                       ['Evaluated Students', $countExam, 'bg-info', 'fas fa-file-alt', 'Results']] as $widget)
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="small-box {{ $widget[2] }}">
                        <div class="inner">
                            <h3>{{ $widget[1] }}</h3>
                            <p>{{ $widget[0] }}</p>
                        </div>
                        <div class="icon">
                            <i class="{{ $widget[3] }}"></i>
                        </div>
                        <a href="{{ route($widget[4]) }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            @endforeach

            <!-- Total Instructors Widget -->
            

            <!-- Gender Distribution Widget -->
            {{-- <div class="col-lg-6 col-md-4 col-sm-1 mb-4">
                <div class="small-box bg-success text-white">
                    <div class="inner">
                        <p>Gender Distribution</p>
                        <canvas height="60%" id="genderBarChart"></canvas>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
  

    <div class="col-12 mb-4 row">
        <!-- Add Print Button -->
        <div class="col-12 text-end mb-3">
            <button class="btn btn-primary" onclick="printCharts()">Print All Charts</button>
        </div>
    </div>
    
    <!-- Students per Year Chart -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title text-white">Annual Student Count</h3>
            </div>
            <div class="card-body">
                <div class="chart">
                    <div id="examYearChart"></div>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
    </div>
    
    <!-- Performance Category Chart -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header text-white">
                <h3 class="card-title">Performance Category</h3>
            </div>
            <div class="card-body">
                <div class="chart">
                    <select id="examyear" class="form-select">
                        <option value="">All</option>
                        @php
                            $currentYear = date('Y');
                            $startYear = 2024;
                        @endphp
    
                        @for ($year = $startYear; $year <= $currentYear; $year++)
                            <option value="{{ $year }}" 
                                @if ($year == $currentYear) selected @endif>
                                {{ $year }} - {{ substr($year + 1, -2) }}
                            </option>
                        @endfor
                    </select>
                    <label for="department" class="form-label mt-3">Select Department:</label>
                    <input type="text" class="form-select" id="depart" value="{{ Auth::user()->username }}" readonly>
                   
                    <select id="department-select" class="form-select" onchange="updateChart()" hidden>
                        <option value="">All Departments</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                   
                    <div id="categorychart" style="width: 500px;"></div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
 function printCharts() {
    // Create a new window for printing
    const printWindow = window.open('', '_blank');

    // Get chart content
    const examYearChart = document.getElementById('examYearChart').outerHTML;
    const categoryChart = document.getElementById('categorychart').outerHTML;

    // Define the content for the print window
    const content = `
        <html>
            <head>
                <title>Print Charts</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 20px;
                        padding: 0;
                        color: #000;
                    }
                    .chart-container {
                        margin-bottom: 30px;
                        text-align: center;
                    }
                    h2 {
                        margin-bottom: 20px;
                    }
                    img {
                        width: 1000px;
                        height: auto;
                        display: block;
                        margin: 0 auto;
                    }
                    hr {
                        margin: 15px 0;
                        border: 1px solid #ccc;
                    }
                    h3 {
                        margin: 5px 0;
                        font-size: 14pt;
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                <div style="text-align: center; margin-bottom: 10px;">
                   <img src="{{ asset('img/header-logo.png') }}" alt="Header Logo">
                    <hr>
                    <hr>
                    <h3></h3>
                </div>
                <div class="chart-container">
                    <h2>Students per Year</h2>
                    ${examYearChart}
                </div>
                <div class="chart-container">
                    <h2>Performance Category</h2>
                    ${categoryChart}
                </div>
            </body>
        </html>
    `;

    // Write content to the new window
    printWindow.document.write(content);
    printWindow.document.close();

    // Trigger print
    printWindow.print();
}

    </script>
    

    
    
</div>

<!-- Scripts -->
<script>
$(document).ready(function () {
    $("#taking").DataTable({
        "responsive": true,
        "autoWidth": false,
        "destroy": true,
        "paging": false,           
        "info": false,             
        "ordering": false,          
        "searching": false,      
    });
    $("#logs").DataTable({
        "responsive": true,
        "paging": false,           
        "info": false,             
        "ordering": false,          
        "searching": false,    
            "autoWidth": false,
            "destroy": true,
            "ajax": {
                "url": 'Dashboard/getLogs'
            },
            "columns": [
                { 
                data: "action", 
                title: "Action", 
                render: function (data, type, row) {
                    return formatAction(data);
                }
            },
                { data: "description" },     
                
                        { 
                data: "created_at", 
                render: function (data, type, row) {
                    if (data) {
                        const date = new Date(data); 
                        return new Intl.DateTimeFormat('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: '2-digit'
                        }).format(date); 
                    }
                    return '';
                }
            }

        ],
    });


    function formatAction(action) {
    switch (action) {
        case 'added':
            return '<span class="text-success"><i class="fas fa-plus-circle"></i> Added</span>';
        case 'delete':
            return '<span class="text-danger"><i class="fas fa-trash-alt"></i> Deleted</span>';
        case 'updated':
            return '<span class="text-warning"><i class="fas fa-edit"></i> Updated</span>';
        default:
            return '<span class="text-muted"><i class="fas fa-genderless"></i> Unknown</span>';
    }

}
});


      
        
</script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="{{ asset('plugin/chartjs/Chart.js') }}"></script>
<script src="{{ asset('js/instructors-dashboard.js') }}"></script>

@endsection
