@extends('layouts.default')

@section('content')
<div class="col-12 content-card">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title text-white">Login History</h3>
            <div class="card-tools">
             
            </div>
        </div>
        <div class="card-body">
            <table id="login-history-table" class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th>Username</th>
                    <th>Ip address</th>
                    <th>Login</th>
                    <th>Logout</th>
                    <th>Device</th>
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
            <form id="users-form">
                <div class="modal-body">
                    <div class="form-group">
                       
                        <label>Username</label>
                        <input class="form-control" name="username" id="username" required>
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" id="password" required>
                        <label>Role</label>
                        <input class="form-control" name="role" id="role" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="role" id="role" value="administrator">
                    <input type="hidden" name="id" id="id">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" name="save">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

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


<script src="{{asset('js/loginhistory.js')}}"></script>
@endsection