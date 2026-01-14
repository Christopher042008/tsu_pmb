<header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

        <a href="{{ route('indexing') }}" class="logo d-flex align-items-center">
            <img src="{{ asset('public/assets/user/img/tsu.png') }}" alt="">
            <h3 class="sitename">Universitas Tiga Serangkai</h3>
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="{{ route('indexing') }}">Beranda</a></li>
                <li><a href="#">Jalur Pendaftaran</a></li>
                <li class="dropdown"><a href="#"><span>Informasi</span> <i
                            class="bi bi-chevron-down toggle-dropdown"></i></a>
                    <ul>
                        <li><a href="#">program Studi</a></li>
                        <li><a href="#">Pengumuman</a></li>
                        <li><a href="#">Informasi Pendaftaran</a></li>
                    </ul>
                </li>
                <li>
                    {{-- <a href="{{route('LoginPMB')}}" class="btn btn-outline-success px-2 text-white" style="display:inline-block;">Masuk | Daftar</a> --}}
                    <a href="{{route('LoginPMB')}}" class="active">Masuk | Daftar</a>
                </li>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

    </div>
</header>
