@extends('layouts.information')
@section('default')
    

<div class="col-12 content-card">


    <form action="" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label for="address">Address</label>
            
                    <input type="hidden" name="student_id" value=" $studentId">

                <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" placeholder="Enter address">
                @error('address')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
 
</div>



<script src="{{asset('js/Results.js')}}"></script>
@endsection