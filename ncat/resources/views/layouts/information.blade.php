 <!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Northeastern College School Ability Test</title>

<style>
    .hi {
     background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('img/NC NEW BUILDING.jpg') }}');
 background-size: cover; /* Makes the image cover the entire background */
 background-position: center; /* Centers the background image */
 background-repeat: no-repeat; /* Prevents the image from repeating */
}

 </style>

{{-- <script>
    window.addEventListener("beforeunload", function (event) {
        event.preventDefault();  
        event.returnValue = '';  /
    });
</script>
<script>
    document.addEventListener("keydown", function (e) {
        // Disable F5 key
        if (e.key === "F5" || (e.ctrlKey && e.key === "r") || (e.metaKey && e.key === "r")) {
            e.preventDefault();
            alert("Page refresh is disabled!");
        }
    });
</script> --}}
    <!-- jQuery -->
<link rel="stylesheet" href="{{asset('plugin/select2/select2.min.css')}}">
    <script src="{{asset('js/jquery.min.js')}}"></script>

    <!-- jQuery UI Datepicker CSS -->
    <link rel="stylesheet" href="{{asset('plugin/select2/jquery-ui.css')}}">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">

    <!-- AdminLTE -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="{{ asset('plugin/toastr/toastr.min.css') }}">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="{{ asset('plugin/sweetalert2/sweetalert2.min.css') }}">

    <!-- Bootstrap Datepicker CSS -->
    <link rel="stylesheet" href="{{ asset('plugin/datepicker/bootstrap-datepicker.min.css') }}">

    <!-- Favicon -->
    <link href="{{ asset('img/image.png') }}" type="image/x-icon" rel="icon">

    <script>
        const base_url = "{{ url('/') }}"; // Corrected to properly reference Laravel's base URL
    </script>
</head>

<body> 
{{-- @php
    $studentName= "michael";
    $studentId= "michael";
@endphp --}}

<div  class="hi content-wrapper d-flex justify-content-center align-items-center" style="min-height: 100vh; margin-left: 0;">
    <div class="col-md-6 col-lg-4">
        <div class="login-logo">
            <img src="{{asset('img/image.png')}}" id="login-logo" alt="NCAT Logo" width="120">
            <h2 class="text-white shadow-sm"><b>Northeastern College</b><br> School Ability Test</h2>
            <hr>
        </div>
        <div class="card shadow">
            <div class="card-header text-center bg-success">
                <h3 class="card-title text-white">Welcome, <span class="text-capitalize">{{$studentName}}</span></h3>
            </div>

            <div class="card-body">
                @if (session('course'))
                    <div class="alert alert-info text-center">
                        Course: {{ session('course') }}
                    </div>
                @endif

                <form action="{{route('Information/add')}}" method="post">
                    @csrf

                    <!-- Birth Date -->
                    <div class="form-group">
                        <input type="hidden"  name="student_id" value="{{$studentId}}" required>
                        <label for="birth_date">Birth Date</label>
                        <input type="text" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" placeholder="Select Birth Date" value="{{ old('birth_date') }} " required>
                        @error('birth_date')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <!-- Region/Province/City Select -->
                    <div class="form-group">
                        <label for="regionSelect">Region</label>
                        <select class="form-control" name="region_id" id="regionSelect" required>
                            <option value="">Select a region</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="provinceSelect">Province</label>
                        <select class="form-control" name="province_id" id="provinceSelect" disabled required>
                            <option value="">Select a province</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="citySelect">City</label>
                        <select class="form-control" name="city_id" id="citySelect" disabled required>
                            <option value="">Select a city</option>
                        </select>
                    </div>

                    <!-- School Select -->
                    <div class="form-group">
                        <label for="schoolSelect">School Name</label>
                        <select class="form-control" name="school_id" id="schoolSelect" required>
                            <option value="">Select a school</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Group</label>
                        <select name="group_abc" id="group_abc" class="form-control" required>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


    <script>
        $(document).ready(function() {
            $('#schoolSelect').select2({
        placeholder: "Select a school", // Placeholder text
        allowClear: true // Allows clearing the selection
    });

    // Fetch school data
    $.ajax({
        url: 'Exam/getSchoolsname', // Your endpoint
        method: 'GET',
        success: function(data) {
            // Clear existing options before appending new ones
            $('#schoolSelect').empty().append('<option value="">Select a school</option>');

            // Append schools to the select2 dropdown
            data.forEach(function(school) {
                $('#schoolSelect').append(
                    $('<option></option>')
                        .attr('value', school.id) // Set the value as the school ID
                        .text(school.school_name + ', ' + school.city + ', ' + school.province + ', ' + school.region) // Set the displayed text
                );
            });

            // Refresh select2 to show new options
            $('#schoolSelect').select2('close').select2();
        },
        error: function(xhr, status, error) {
            console.error('Error fetching schools:', error);
            // Handle error as needed
        }
    });


            // Fetch data and populate the regions dropdown
            $.getJSON('Exam/regions', function(data) {
                var $regionSelect = $('#regionSelect');
                $.each(data, function(index, region) {
                    $regionSelect.append($('<option>', {
                        value: region.id,
                        text: region.name
                    }));
                });
 
 
    
                // Handle region change
                $regionSelect.change(function() {
                    var selectedRegionId = $(this).val();
                    var $provinceSelect = $('#provinceSelect');
                    var $citySelect = $('#citySelect');
    
                    $provinceSelect.empty().append('<option value="">Select a province</option>');
                    $citySelect.empty().append('<option value="">Select a city</option>').prop('disabled', true);
    
                    if (selectedRegionId) {
                        var selectedRegion = data.find(function(region) {
                            return region.id == selectedRegionId;
                        });
    
                        $.each(selectedRegion.provinces, function(index, province) {
                            $provinceSelect.append($('<option>', {
                                value: province.id,
                                text: province.name
                            }));
                        });
    
                        $provinceSelect.prop('disabled', false);
                    } else {
                        $provinceSelect.prop('disabled', true);
                    }
                });
    
                // Handle province change
                $('#provinceSelect').change(function() {
                    var selectedRegionId = $('#regionSelect').val();
                    var selectedProvinceId = $(this).val();
                    var $citySelect = $('#citySelect');
    
                    $citySelect.empty().append('<option value="">Select a city</option>');
    
                    if (selectedRegionId && selectedProvinceId) {
                        var selectedRegion = data.find(function(region) {
                            return region.id == selectedRegionId;
                        });
                        var selectedProvince = selectedRegion.provinces.find(function(province) {
                            return province.id == selectedProvinceId;
                        });
    
                        $.each(selectedProvince.cities, function(index, city) {
                            $citySelect.append($('<option>', {
                                value: city.id,
                                text: city.name
                            }));
                        });
    
                        $citySelect.prop('disabled', false);
                    } else {
                        $citySelect.prop('disabled', true);
                    }
                });
            }).fail(function() {
                console.error('Error fetching regions data.');
            });
        });
    </script>
    <script src="{{asset('plugin/select2/select2.min.js')}}"></script>

    <!-- jQuery UI Datepicker JS -->
    <script src="{{asset('plugin/select2/jquery-ui.js')}}"></script>

    <!-- Toastr JS -->
    <script src="{{ asset('plugin/toastr/toastr.min.js') }}"></script>

    <!-- SweetAlert2 JS -->
    <script src="{{ asset('plugin/sweetalert2/sweetalert2.min.js') }}"></script>

    <!-- Bootstrap 4 JS -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Bootstrap Datepicker JS -->
    <script src="{{ asset('plugin/datepicker/bootstrap-datepicker.min.js') }}"></script>

    <!-- AdminLTE JS -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>

    <!-- Datepicker Initialization -->
    <script>
        $(document).ready(function() {
            // Initialize Datepicker for Birth Date
            $("#birth_date").datepicker({
                dateFormat: 'yy-mm-dd', // Date format
                changeMonth: true, // Allow month selection
                changeYear: true, // Allow year selection
                yearRange: "1900:+0", // Year range from 1900 to current year
                autoclose: true, // Close datepicker after selection
                todayHighlight: true // Highlight today's date
            });
        });
    </script>
</body>

</html>
