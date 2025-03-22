@extends('layouts.default')
@section('content-header')
<li class="breadcrumb-item green"><a href="{{route("Dashboard")}}">Dashboard</a></li>
<li class="breadcrumb-item active">Information</li>
@endsection
@section('content')
<div class="col-12 content-card">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title text-white">Users List</h3>
            <div class="card-tools">
                <a href="" id="add" data-toggle="tooltip" data-placement="bottom" title="Add User"><i class="fas fa-plus text-white"></i></a>
            </div>
        </div>
        <div class="card-body">
            <table id="info-table" class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        
                        <th>ID number</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Department</th>
                        <th>Birth Date</th>
                        <th>Address</th>
                        <th>Gender</th>
                        <th>Exam Year</th>
                        <th>Raw Score</th>
                        <th>Test IP</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        $('#info-table').DataTable({
            "responsive": true,
            "autoWidth": false,
            "destroy": true,
            "ajax": {
                "url": 'EnrolledStudents/getStudentData'
            }, // Pass in the data from your JSON
        columns: [
         
            { data: 'students.id_number' }, // Student ID
            { data: 'students.name' }, // Student Name
          
            { data: 'students.course' }, // Course
            { data: 'students.department' }, // Department
            { data: 'students.birthday',
            render: function(data, type, row) {
                    // Format the date
                    if (data) {
                        const date = new Date(data);
                        const options = { year: 'numeric', month: 'long', day: 'numeric' };
                        return date.toLocaleDateString('en-US', options);
                    }
                    return '';
                }
             },
            { data: 'address' }, // Address
            { data: 'students.gender' }, // Gender
            { data: 'students.exam_year' }, // Exam Year
          
            { data: 'students.results[0].test_ip' } 
        ]
    });
    });
</script>
@endsection