@extends('user::layouts/halamandepan/master')
@section('title', $title)

@section('link_href')
@endsection
@section('content')

    <main id="main">
        
        <div class="page-header">
            <div class="container position-relative">
                <h2>Program Studi</h2>
                <p>Temukan pilihan Program Studi terbaik di Universitas Tiga Serangkai</p>
            </div>
        </div>

        <section class="prodi-wrapper">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-15">
                        <div class="card prodi-card">
                            
                            <a href="https://tsu.ac.id" target="_blank" class="prodi-item">
                                <span class="prodi-name">Sistem Informasi Akuntansi</span>
                                <span class="badge-d3">D3 - Diploma 3</span>
                            </a>

                            <h3 class="faculty-title">Fakultas Teknik</h3>
                            <a href="https://tsu.ac.id/s1-informatika/" target="_blank" class="prodi-item">
                                <span class="prodi-name">Informatika</span>
                                <span class="badge-s1">S1 - Strata 1</span>
                            </a>
                            <a href="https://tsu.ac.id" target="_blank" class="prodi-item">
                                <span class="prodi-name">Sistem Informasi</span>
                                <span class="badge-s1">S1 - Strata 1</span>
                            </a>
                            <a href="https://tsu.ac.id" target="_blank" class="prodi-item">
                                <span class="prodi-name">Rekayasa Komputer</span>
                                <span class="badge-s1">S1 - Strata 1</span>
                            </a>

                            <h3 class="faculty-title">Fakultas Sains dan Humaniora</h3>
                            <a href="https://tsu.ac.id" target="_blank" class="prodi-item">
                                <span class="prodi-name">Manajemen</span>
                                <span class="badge-s1">S1 - Strata 1</span>
                            </a>
                            <a href="https://tsu.ac.id" target="_blank" class="prodi-item">
                                <span class="prodi-name">Psikologi</span>
                                <span class="badge-s1">S1 - Strata 1</span>
                            </a>
                            <a href="https://tsu.ac.id" target="_blank" class="prodi-item">
                                <span class="prodi-name">Pendidikan Guru Sekolah Dasar</span>
                                <span class="badge-s1">S1 - Strata 1</span>
                            </a>

                            <h3 class="faculty-title">Sekolah Vokasi</h3>
                            <a href="https://tsu.ac.id" target="_blank" class="prodi-item">
                                <span class="prodi-name">Teknologi Informasi</span>
                                <span class="badge-d3">D3 - Diploma 3</span>
                            </a>
                            <a href="https://tsu.ac.id" target="_blank" class="prodi-item">
                                <span class="prodi-name">Sistem Informasi</span>
                                <span class="badge-d3">D3 - Diploma 3</span>
                            </a>
                            <a href="https://tsu.ac.id" target="_blank" class="prodi-item">
                                <span class="prodi-name">Desain Produk Tekstil</span>
                                <span class="badge-d3">D3 - Diploma 3</span>
                            </a>
                            <a href="https://tsu.ac.id" target="_blank" class="prodi-item">
                                <span class="prodi-name">Desain Komunikasi Visual</span>
                                <span class="badge-d3">D3 - Diploma 3</span>
                            </a>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection