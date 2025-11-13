@extends('user::login/master_email')

@section('content')
<div class="text-center">
    <img src="https://i.imgur.com/zeCXY2b.png" alt="" style="width: 100px;">
    <br>
    <label>
        Universitas Tiga Serangkai
    </label>
</div>

<div>
    <p>
        Hai {{ $biodata->nama }},
        <br>
        Selamat datang di Sistem PMB Universitas Tiga Serangkai
    </p>
    <p>
        Anda, telah mereset password Akun dengan email <code>{{ $akun->email }}</code>.
        <br>
        <br>
        Password baru Anda : <h1><b>{{ $password_plaintext }}</b></h1>
        <br>
        <br>
        Segera ubah password anda, Terimakasih
    </p>
</div>

<div class="text-center" style="font-size: 8pt">
    <br><br><br><hr>
    <span class="apple-link">
        <i>
            Don't reply this e-mail, e-mail auto sent <br>
            Powered by <a href="">IT Team Universitas Tiga Serangkai</a>
        </i>
    </span>
</div>
@endsection
