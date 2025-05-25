@extends('layouts.default')

@section('title', 'Dashboard')

@section('content-header')
<li class="breadcrumb-item green"><a href="{{route("Dashboard")}}">Home</a></li>
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')



<style>
    #categorychart {
      width: 100%;
      height: 400px;
      border: 1px solid #ccc;
      margin-bottom: 20px;
    }
    @media print {
      body * {
        visibility: hidden;
      }
      #categorychart, #categorychart * {
        visibility: visible;
      }
      #categorychart {
        position: absolute;
        top: 0;
        left: 0;
      }
    }
  </style>
<div class="row content-card">

    <!-- Top Row: Dashboard Widget Boxes -->
    <div class="col-12 mb-4">

        <div class="row">
            <!-- Existing Widgets -->
            @foreach ([['Batches', $batch, 'bg-danger', 'fas fa-layer-group', 'Batch'],
                       ['Enrolled Students', $enrolled, 'bg-success', 'fas fa-user-graduate', 'EnrolledStudents'],
                       ['Non Evaluated Students', $countWithoutExam, 'bg-warning', 'fas fa-user-times', 'No-Exam'],
                       ['Evaluated Students', $countExam, 'bg-info', 'fas fa-file-alt', 'Results']] as $widget)
                <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
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

        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title text-white">Annual Student Count</h3>
                <div class="card-tools">
                    <a href="{{route('EnrolledStudents')}}" type="button" class="btn btn-tool">
                        <i class="fas fa-arrow-right text-white"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="chart">
                    <div id="examYearChart"></div>
                    {{-- <canvas id="residentChart" style="min-height: 250px; height: 500px; max-height: 500px; max-width: 100%;"></canvas> --}}
                </div>
            </div>
            <!-- /.card-body -->
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header text-white">
                <h3 class="card-title"></h3>
                <div class="card-tools">
                    <a href="{{ route('EnrolledStudents') }}" type="button" class="btn btn-tool">
                        <i class="fas fa-arrow-right text-white"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="chart">
                    <!-- Exam Year Select Dropdown -->
                    {{-- <label for="examYear" class="form-label">Select Exam Year:</label>
                    <select id="examYear" class="form-select">
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

                    <!-- Department Select Dropdown -->


                    <!-- Course Select Dropdown -->
                    {{-- <label for="course" class="form-label mt-3">Select Course:</label>
                    <select id="course" class="form-select">
                        <option value="">All Courses</option>
                        <!-- Example courses; replace with dynamic options based on selected department if needed -->
                        @foreach ($course as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select> --}}

                    <!-- Chart Container -->

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
                    <select id="department-select" class="form-select" onchange="updateChart()">
                        <option value="">All Departments</option>
                        @foreach ($departments as $department)
                        <option value="{{ $department->name }}">{{ $department->name }}</option>
                        @endforeach
                    </select>

                    <div id="categorychart" style="width: 100%; height: 350px; display: flex; justify-content: center; align-items: center;">
                    </div>


                </div>
            </div>
        </div>

    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header text-white">
                <h3 class="card-title">Annual Exam Participation</h3>
                <div class="card-tools">
                    <a href="{{route('EnrolledStudents')}}" type="button" class="btn btn-tool">
                        <i class="fas fa-arrow-right text-white"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">

                <div id="examParticipationChart"></div>
            </div>
        </div>
    </div>
    <!-- Main Row: Ongoing Exams and Logs Table -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header text-white">
                <h3 class="card-title"></h3>
                <div class="card-tools">
                    <a href="" type="button" class="btn btn-tool">
                        <i class="fas fa-arrow-right text-white"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div id="countstudentbycourse"></div>
            </div>
        </div>
    </div>

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

<script src="{{ asset('plugin/chartjs/Chart.js') }}"></script>
<script src="{{ asset('js/dashboard.js') }}"></script>

@endsection
