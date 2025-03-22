@php
$course = [
    'BSIT',
    'BSN',
    'BSBA-FM',
    'BSBA-MM',
    'BSED-EU',
    'BSED-FIL',
    'BSED-MATH',
    'BSED-SCI',
    'BSA',
    'BSED-ENGLISH',
    'BEED',
    'AB-MASS COM',
    'BSCRIM',
    'BSHM',
    'BSMA',
    'AB-POL SCI',
    'BSBA-HRM',
    'MIDWIFERY',
    'AB-ENG',
    'BSGE',
    'BSBA-MA',
];

@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> NCSAT |
        @yield('title')
    </title>
    <script>
        const base_url = {{ url('/') }};
    </script>

    <script src="{{ asset('plugin/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('plugin/datepicker/bootstrap-datepicker.min.css') }}"></script>
    <script src="{{ asset('dist/js/demo.js') }}"></script>
    <link href="{{ asset('img/image.png') }}" type="image/x-icon" rel="icon">
    <link rel="stylesheet" href="{{ asset('plugin/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugin/sweetalert2/sweetalert2.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body class="hold-transition sidebar-mini">

    <div class="wrapper">

        <!-- Navbar -->
        @include('partials.navbar')

        <!-- Main Sidebar Container -->
        @include('partials.sidebar')
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">

                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                           @yield('content-header')
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">

            @yield('content')
        </div>

        <!-- Main Footer -->
        @include('partials.footer')
    </div>
    {{-- <script src="{{asset('js/no-right-click.js')}}"></script> --}}
    <script src="plugin/qrcode/qrcode.min.js"></script>
    <script>

        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-bottom-left", // Changed from "toast-top-right" to "toast-bottom-left"
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "3000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    </script>
 <script src="{{asset('plugin/tippy/tippy.js')}}"></script>
    <script src="{{ asset('plugin/apex/apexcharts.js') }}"></script>
    <script src="{{ asset('plugin/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('plugin/datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('plugin/sweetalert2/sweetalert2.min.js') }}"></script>
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
