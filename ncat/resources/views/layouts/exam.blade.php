<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Northeastern College Ability Test  </title>
    <script src="{{ asset('dist/js/demo.js') }}"></script>
    <link href="{{asset('img/image.png')}}" type="image/x-icon" rel="icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <script src="{{asset('js/jquery.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('plugin/toastr/toastr.min.css')}}">
    <style>
body {
    background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('img/NC NEW BUILDING.jpg') }}');
    background-size: cover; /* Makes the image cover the entire background */
    background-position: center; /* Centers the background image */
    background-repeat: no-repeat; /* Prevents the image from repeating */
    background-attachment: fixed; /* Makes the background image fixed */
    -webkit-user-select: none; /* For Safari */
            -moz-user-select: none;    /* For Firefox */
            -ms-user-select: none;     /* For IE/Edge */
            user-select: none;  
}

 
     </style>

<script>
    document.addEventListener('keydown', function(event) {
        if (event.ctrlKey && event.key === 'a') {
            event.preventDefault(); // Prevents Ctrl + A
        }
    });
</script>
    

</head>
<body>
    
    <div class="wrapper">
      
            @yield('content')
    </div>




    {{-- <script src="{{asset('js/no-right-click.js')}}"></script> --}}

    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
    <script src="{{asset('plugin/toastr/toastr.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <!-- jQuery -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- DataTables & Plugins -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js?v=3.2.0') }}"></script>
    <!-- Demo script -->
   
</body>
</html>
