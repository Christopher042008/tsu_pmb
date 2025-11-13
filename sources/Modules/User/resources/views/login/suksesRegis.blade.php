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
                <div class="alert alert-warning text-center">
                    <b>Silahkan aktivasi Akun pada tautan yang tercantum di Email</b>
                </div>
                <div class="text-center" style="display: none;">
                    <a href="{{route('LoginPMB')}}" class="btn btn-success">Login</a>
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
