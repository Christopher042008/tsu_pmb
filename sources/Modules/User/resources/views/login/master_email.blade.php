
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset('public/assets/admin/plugins/fontawesome-free/css/all.min.css') }}">
        <!-- SweetAlert2 -->
        <link rel="stylesheet" href="{{ asset('public/assets/admin/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ asset('public/assets/admin/dist/css/adminlte.min.css') }}">
    </head>
    <body class="sidebar-mini">
        <div class="wrapper">
            <div class="content">
                <div class="container-fluid">
                    <div class="row" style="margin-top: 100px;">
                        <div class="col-md-6 offset-3">
                            <div class="card card-primary card-outline">
                                <div class="card-body">
                                    @yield('content')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- jQuery -->
        <script src="{{ asset('public/assets/admin/plugins/jquery/jquery.min.js') }}"></script>
        <!-- jQuery UI 1.11.4 -->
        <script src="{{ asset('public/assets/admin/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

        <!-- Bootstrap 4 -->
        <script src="{{ asset('public/assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <!-- AdminLTE App -->
        <script src="{{ asset('public/assets/admin/dist/js/adminlte.js') }}"></script>
    </body>
</html>
