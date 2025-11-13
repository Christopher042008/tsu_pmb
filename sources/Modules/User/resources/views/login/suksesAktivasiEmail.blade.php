@extends('user::login/masterlogin')
@section('title', $title)
@section('link_href')
@endsection

@section('content')
    <div class="login-box">
        <div class="card card-outline card-success">
            <div class="card-header">
                <i class="fas fa-user"></i><b> Sukses Registrasi</b>
            </div>
            <div class="card-body">
                @if ($status)
                    <div class="alert alert-success text-center">
                        <h5><i class="fas fa-check"></i> Berhasil</h5>
                        Selamat, Akun sudah aktif. Silahkan login
                    </div>
                @else
                    <div class="alert alert-success text-center">
                        <h5><i class="fas fa-ban"></i> Gagal!</h5>
                            Gagal aktivasi Akun
                    </div>
                @endif
                <div class="text-center">
                    <a href="{{ route('LoginPMB') }}" class="btn btn-success">Login</a>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {

        });
    </script>
@endsection
