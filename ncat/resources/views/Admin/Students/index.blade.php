@extends('layouts.default')
@section('content')
    {{-- <h1>Students</h1>
    <form action="assign-batch/1/1" method="POST">
        @csrf
        <button type="submit">Assign Batch and Move Student</button>
    </form>
    <form action="assign-all-to-batch/1" method="POST">
        @csrf
        <button type="submit">Assign All Students to Batch 1</button>
    </form> --}}


    <div class="col-12 content-card">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title text-white">Students List</h3>
                <div class="card-tools">
                    {{-- <a href="" id="add" data-toggle="tooltip" data-placement="bottom" title="Upload User"><i class="fas fa-upload text-white"></i></a> --}}
                </div>
            </div>
            <div class="card-body">
                <table id="students-table" class="table table-bordered table-hover table-striped">
                    <thead>  
                    <tr>
                        <th>ID number</th>
                        <th>Course</th> 
                        <th>Name</th>
                        <th>Batch</th>
                        <th>Batch Description</th>
                        <th>Exam Year</th>
                        <th>Options</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    

<script src="{{asset('js/Students.js')}}"></script>
    
@endsection