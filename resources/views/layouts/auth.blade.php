
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NCAT</title>
    <link href="{{asset('img/image.png')}}" type="image/x-icon" rel="icon"><link href="{{asset('img/image.png')}}" type="image/x-icon" rel="shortcut icon" defer>    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" defer>
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" defer>

    <link rel="stylesheet" href="{{ asset('plugin/toastr/toastr.min.css') }}" defer>
    <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}" defer>
    <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}" defer>
    <link rel="stylesheet" href="{{asset('css/style.css')}}" defer>

    <style>
        body {
         background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('img/NC NEW BUILDING.jpg') }}');
     background-size: cover; /* Makes the image cover the entire background */
     background-position: center; /* Centers the background image */
     background-repeat: no-repeat; /* Prevents the image from repeating */
 }
 
     </style>
    <script src="{{asset('plugins/jquery/jquery.min.js')}} defer"></script>

</head>
<body class="hold-transition login-page">

    @yield('auth')


</body>
<script src="{{ asset('plugin/toastr/toastr.min.js') }}" defer></script>
{{-- <script src="{{asset('js/no-right-click.js')}}"></script> --}}
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}} defer"></script>
<script src="{{asset('dist/js/adminlte.min.js')}}" defer></script>
</html>
