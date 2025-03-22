@extends('layouts.default')
@section('title', 'Users')
@section('content-header')
<li class="breadcrumb-item green"><a href="{{route("Dashboard")}}">Dashboard</a></li>
<li class="breadcrumb-item active">Users</li>
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
@endphp
<div class="col-12 content-card">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title text-white">Users List</h3>
            <div class="card-tools">
                <a href="" id="add" data-toggle="tooltip" data-placement="bottom" title="Add User"><i class="fas fa-plus text-white"></i></a>
            </div>
        </div>
        <div class="card-body">
            <table id="users-table" class="table table-bordered table-hover table-striped text-capitalize">
                <thead>
                <tr>
                    <th>Username</th>
                    <th >Role</th>
                    <th>Options</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="users-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="users-form" enctype="multipart/form-data" autocomplete="off">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Username</label>
                        <input class="form-control" name="username" id="username" placeholder="Enter username">

                        <label>Password</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Enter password">

                        <label>Role</label>
                        <select name="role" id="role" class="form-control" onchange="toggleDepartmentField()">
                            <option value="administrator">Admin</option>
                            {{-- <option value="user">User</option> --}}
                            {{-- <option value="instructor">Instructor</option> --}}
                        </select>

                        <!-- Department Field -->
                        <div id="departmentField" style="display: none; margin-top: 10px;">
                            <label>Department</label>
                          <select name="department" id="department" >

                            @foreach ($departments as $department)
                            <option value="{{ $department }}">{{ $department }}</option>
                        @endforeach
                          </select>
                        </div>

                        <!-- Profile picture input -->
                        <label for="profile" class="mt-2">Profile Picture</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="profile" id="profile" accept="image/*">
                                <label class="custom-file-label" for="profile">Choose file</label>
                            </div>
                        </div>

                        <!-- Image preview container -->
                        <div id="image-preview-container" class="mt-2" style="display:none;">
                            <div class="text-center">
                                <img id="image-preview" src="" alt="Profile Picture" class="img-fluid rounded" style="max-width: 200px;"/>
                                <button type="button" class="btn btn-danger mt-2" id="remove-image-btn">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- Removed hidden role input here -->
                    <input type="hidden" name="id" id="id">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" name="save">Save</button>
                </div>
            </form>

        </div>
    </div>
</div>


<script>

    // Event listener to handle image selection
    $('#profile').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Create an image preview using FileReader
            const reader = new FileReader();
            reader.onload = function(event) {
                // Set the image source to the file data
                $('#image-preview').attr('src', event.target.result);

                // Show the image preview container
                $('#image-preview-container').show();

                // Update the label with the selected file name
                const fileName = file.name;
                $('#profile').next('.custom-file-label').html(fileName);
            };
            reader.readAsDataURL(file);
        }
    });

    // Event listener to handle image removal
    $('#remove-image-btn').on('click', function() {
        // Clear the file input
        $('#profile').val('');

        // Hide the image preview container
        $('#image-preview-container').hide();

        // Reset the file input label
        $('#profile').next('.custom-file-label').html('Choose file');
    });
</script>


<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="user-modal">
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


<script src="{{asset('js/Users.js')}}"></script>
@endsection
