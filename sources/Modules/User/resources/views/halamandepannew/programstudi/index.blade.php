@extends('user::layouts/halamandepan/master')
@section('title', 'Program Studi - Universitas Tiga Serangkai')

@section('link_href')
@endsection

@section('content')
<style>
    /* --- Warna Utama --- */
    :root {
        --teal-color: #1192a8;
        --border-color: #e5e7eb;
        --text-dark: #1f2937;
        --text-muted: #6b7280;
    }

    /* --- Hero Section (Area Gambar) --- */
    .hero-section {
        position: relative;
        /* BACKGROUND GAMBAR SUDAH DIGANTI SESUAI PERMINTAAN */
        background-image:linear-gradient(rgba(18, 108, 132, 0.8), rgba(29, 36, 43, 0.9)), url('https://images.unsplash.com/photo-1434030216411-0b793f4b4173?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); 
        background-size: cover;
        background-position: center;
        padding: 160px 0 140px; 
    }

    .hero-content {
        position: relative;
        z-index: 2; 
        color: #ffffff;
    }

    .hero-content h1 {
        font-family: var(--heading-font);
        font-weight: 800;
        font-size: 2.5rem;
        margin-bottom: 10px;
        color: #fff;
    }

    .hero-content p {
        font-size: 1.1rem;
        opacity: 0.9;
        max-width: 600px;
    }

    /* --- Page Container --- */
    .ps-page-container {
        background-color: #f5faff;
        padding-bottom: 80px;
    }

    /* --- Area Putih / Card Utama (OVERLAP EFFECT) --- */
    .ps-card-wrapper {
        background: #ffffff;
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); 
        margin-top: -80px; /* Efek melayang */
        position: relative;
        z-index: 10;
    }

    .ps-header h3 {
        font-family: var(--heading-font);
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 5px;
        font-size: 1.4rem;
    }

    .ps-header p {
        color: var(--text-muted);
        font-size: 0.95rem;
        margin-bottom: 25px;
    }

    /* --- Tab Buttons --- */
    .btn-tab {
        background-color: transparent;
        border: 1px solid var(--border-color);
        color: var(--text-muted);
        padding: 8px 24px;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.95rem;
        margin-right: 8px;
        transition: all 0.2s ease;
        outline: none;
    }

    .btn-tab:focus { outline: none; box-shadow: none; }
    
    .btn-tab.active {
        border-color: var(--teal-color);
        color: var(--teal-color);
    }

    .btn-tab:hover:not(.active) {
        border-color: #cbd5e1;
        color: var(--text-dark);
    }

    /* --- Search Input --- */
    .search-wrapper {
        position: relative;
        width: 100%;
        max-width: 320px;
    }

    .search-wrapper i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 0.9rem;
    }

    .search-input {
        width: 100%;
        padding: 10px 16px 10px 38px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        font-size: 0.95rem;
        color: var(--text-dark);
        outline: none;
        transition: border-color 0.2s;
    }

    .search-input:focus { border-color: var(--teal-color); }

    /* --- List Program Studi --- */
    .prodi-list-container { margin-top: 30px; }

    .prodi-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        margin-bottom: 15px;
        background-color: #fff;
    }

    .prodi-info h5 {
        font-weight: 700;
        font-size: 1.05rem;
        color: var(--text-dark);
        margin-bottom: 4px;
    }

    .prodi-info p {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin: 0;
    }

    .btn-lihat-detail {
        display: inline-block;
        border: 1px solid var(--teal-color);
        color: var(--teal-color);
        background: transparent;
        padding: 8px 20px;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .btn-lihat-detail:hover {
        background-color: var(--teal-color);
        color: #ffffff;
        text-decoration: none;
    }

    @media (max-width: 768px) {
        .controls-area {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 15px;
        }
        .search-wrapper { max-width: 100%; }
        .prodi-item { flex-direction: column; align-items: flex-start; gap: 15px; }
        .btn-lihat-detail { width: 100%; text-align: center; }
    }
</style>

<section class="hero-section">
    <div class="container hero-content">
        <h1>Program Studi</h1>
        <p>Temukan informasi lengkap mengenai program studi pilihan di Universitas Tiga Serangkai.</p>
    </div>
</section>

<main id="main" class="ps-page-container">
    <div class="container">
        
        <div class="ps-card-wrapper">
            <div class="ps-header">
                <h3>Informasi Program Studi</h3>
                <p>Informasi program studi Universitas Tiga Serangkai</p>
            </div>
            
            <div class="d-flex justify-content-between align-items-center controls-area border-top pt-4">
                <div class="tabs-area">
                    <button class="btn-tab active" onclick="filterData('D3', this)">D3 - Diploma 3</button>
                    <button class="btn-tab" onclick="filterData('S1', this)">S1 - Strata 1</button>
                </div>
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="inputPencarian" class="search-input" placeholder="Cari program studi..." onkeyup="filterData()">
                </div>
            </div>

            <div class="prodi-list-container" id="daftarProdi">
                
                @foreach ($jurusans as $prodi)
                    @php
                        $jenjang = strtoupper($prodi->jenjang->jenjang ?? 'S1');
                        $namaProdiUtuh = $jenjang . ' - ' . $prodi->jurusan;
                        
                        // --- CARA AGAR TEKS INI MENJADI DINAMIS ---
                        // Ganti "1" dengan relasi database Anda. 
                        // Contoh jika menggunakan relasi Eloquent: $jumlahJalur = $prodi->jalur_pendaftarans()->count();
                        // Untuk saat ini saya pancing dengan logic random agar Anda bisa lihat perbedaannya.
                        $jumlahJalur = rand(0, 3); // <-- INI HARUS DIGANTI DENGAN DATA DARI CONTROLLER/MODEL NANTINYA
                        
                        if ($jumlahJalur > 0) {
                            $infoJalur = 'Tersedia ' . $jumlahJalur . ' Jalur Pendaftaran';
                        } else {
                            $infoJalur = 'Belum ada jalur yang buka';
                        }
                    @endphp

                    <div class="prodi-item item-kartu" data-jenjang="{{ $jenjang }}" data-nama="{{ strtolower($namaProdiUtuh) }}">
                        <div class="prodi-info">
                            <h5>{{ $namaProdiUtuh }}</h5>
                            <p>{{ $infoJalur }}</p>
                        </div>
                        <div>
                            <a href="#" class="btn-lihat-detail">Lihat Detail</a>
                        </div>
                    </div>
                @endforeach

            </div>

            <div id="pesanKosong" style="display: none; text-align: center; padding: 40px; color: var(--text-muted);">
                Program studi yang dicari tidak ditemukan.
            </div>

            </div> </div> @include('user::layouts.halamandepan.cta-bantuan')

        </div>

    </div>
</main>

<script>
    let jenjangAktif = 'D3';

    function filterData(pilihanJenjang = null, elementBtn = null) {
        if(pilihanJenjang !== null) {
            jenjangAktif = pilihanJenjang;
            document.querySelectorAll('.btn-tab').forEach(btn => btn.classList.remove('active'));
            if(elementBtn) elementBtn.classList.add('active');
        }

        let keyword = document.getElementById('inputPencarian').value.toLowerCase();
        let daftarKartu = document.querySelectorAll('.item-kartu');
        let jumlahTerlihat = 0;

        daftarKartu.forEach(kartu => {
            let dataJenjang = kartu.getAttribute('data-jenjang');
            let dataNama = kartu.getAttribute('data-nama');
            
            let cocokTab = (dataJenjang === jenjangAktif);
            let cocokPencarian = dataNama.includes(keyword);

            if (cocokTab && cocokPencarian) {
                kartu.style.display = 'flex';
                jumlahTerlihat++;
            } else {
                kartu.style.display = 'none';
            }
        });

        document.getElementById('pesanKosong').style.display = (jumlahTerlihat === 0) ? 'block' : 'none';
    }

    document.addEventListener('DOMContentLoaded', () => {
        filterData('D3');
    });
</script>
@endsection