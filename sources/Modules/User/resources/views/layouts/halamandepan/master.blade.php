<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link rel="icon" href="{{ asset('public/assets/user/img/logotsu.png') }}" type="image/png" />
    <title>{{$title}}</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    {{-- <link href="{{ asset('public/assets/user/img/logotsu.png') }}" rel="icon"> --}}
    {{-- <link href="{{ asset('public/assets/user/img/apple-touch-icon.png') }}" rel="apple-touch-icon"> --}}

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('public/assets/user/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/user/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/user/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/user/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/user/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="{{ asset('public/assets/user/css/main.css') }}" rel="stylesheet">
</head>

<body class="index-page">

    @include('user::layouts/halamandepan/navbar')
    <main class="main">
        @yield('content')
    </main>
    @include('user::layouts/halamandepan/footer')

    <script>
        @if (Session::has('alert'))
            Swal.fire('{{ session('alert')['title'] }}', '{{ session('alert')['message'] }}',
                '{{ session('alert')['status'] }}')
        @endif
        // Swal.fire('halo', 'test alert',
        //         'success')

        function sweetAlert(alert, desc, text) {
            const Alert = Swal.mixin({
                showConfirmButton: true,
                timer: 3000
            });

            Alert.fire({
                type: alert,
                title: desc,
                text: text,
            });
        }
    </script>
    @yield('script')
</body>


</html>
