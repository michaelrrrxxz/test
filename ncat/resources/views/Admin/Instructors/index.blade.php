@extends('layouts.default')
@section('content-header')
<li class="breadcrumb-item green"><a href="{{route("Dashboard")}}">Dashboard</a></li>
<li class="breadcrumb-item active">Instructors</li>
@endsection
@section('content')
@php
    $departments = ['CIT', 'CON', 'CABA','COGE','COC','COLA','COHM','SOM'];
@endphp
<div class="col-12 content-card">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title text-white">Instructors List</h3>
            <div class="card-tools">
                <a href="" id="add" data-toggle="tooltip" data-placement="bottom" title="Add User"><i class="fas fa-plus text-white"></i></a>
            </div>
        </div>
        <div class="card-body">
            <table id="instructors-table" class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th>ID Number</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Department</th>
                    <th>Options</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="instructors-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="instructors-form">
                <div class="modal-body">
                    <div class="form-group">
                        <label>ID Number</label>
                        <input class="form-control" name="id_number" id="id_number">
                        <label>Full Name</label>
                        <input class="form-control" name="fullname" id="fullname">
                        <label>Username</label>
                        <input class="form-control" name="username" id="username">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" id="password">
                        <label>Department</label>
                        <select name="department" id="department" class="form-control">
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}">{{ $dept }}</option>
                            @endforeach
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

<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="instructor-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body center">
                <div id="qrcode" style="text-align: center;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<script src="{{asset('js/Instructors.js')}}"></script>
@endsection