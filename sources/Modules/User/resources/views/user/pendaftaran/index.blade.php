@extends('user::layouts/halamanbelakang/header')
@section('title', $title)
@section('link_href')

@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Selamat Datang di PMB Universitas Tiga Serangkai</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active"><a href="{{ route('Dashboard') }}" id="btn-dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">{{$menu}}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- /.col-md-6 -->
                <div class="col-md-12">
                    {{-- @for ($i = 0; $i < 10; $i++) --}}
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="m-0">{{$menu}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row justify-content-center">
                                <div class="col-md-6"> <!-- Ubah lebar form di sini -->
                                    <form id="form-fakultas" method="POST" action="#">
                                        @csrf
                                        <input type="hidden" id="IdPendaftaran" name="IdPendaftaran" value="">
                                        <div id="page-1">
                                            <div class="form-group mb-3">
                                                <label for="batch">Batch Pendaftaran <code>*</code></label>
                                                <select class="form-control select2" id="batch" name="batch">
                                                    <option value="" selected disabled>-- Pilih Batch Pendaftaran --</option>
                                                    @foreach ($batch as $i)
                                                    <option value="{{$i->id}}">{{$i->nama_batch}} - {{$i->tahun_akademik}} - {{ tglIndo($i->tglmulai) }}/{{tglIndo($i->tglselesai)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="jalur">Jalur Pendaftaran <code>*</code></label>
                                                <select class="form-control select2" id="jalur" name="jalur" disabled>
                                                    <option value="" selected disabled>-- Pilih Jalur Pendaftaran --</option>
                                                </select>
                                                {{-- <br> --}}
                                                <code>*Abaikan Bila Bukan Jalur Beasiswa</code>
                                                <select class="form-control select2" id="beasiswa" name="beasiswa" disabled>
                                                    <option value="" selected disabled>-- Pilih Jalur Beasiswa --</option>
                                                </select>
                                                <label for="tingkat"></label>
                                                <input type="text" id="tingkat" placeholder="Tingkat Kejuaraan" class="form-control" disabled>
                                                <label for="keterangan"></label>
                                                <textarea class="form-control" id="keterangan" rows="3" placeholder="Keterangan Beasiswa" disabled></textarea>
                                            </div>
                                            <div class="form-group mb-3">
                                                <button type="button" id="btn-next" class="btn btn-secondary btn-sm float-right">Next</button>
                                            </div>
                                            <br><br>
                                        </div>
                                        <div id="page-2" style="display: none;">
                                            <div class="form-group mb-3">
                                                <label for="tahunlulus">Tahun Lulus</label>
                                                <select class="form-control select2" id="tahunlulus" name="tahunlulus">
                                                    <option value="" selected disabled>-- Pilih Tahun --</option>
                                                    @php
                                                        $currentYear = date('Y')+1;
                                                    @endphp
                                                    @for ($i = 0; $i < 10; $i++)
                                                        <option value="{{ $currentYear - $i }}">{{ $currentYear - $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="jurusansekolah">Jurusan Sekolah</label>
                                                <select class="form-control select2" id="jurusansekolah" name="jurusansekolah" disabled>
                                                    <option value="" selected disabled>-- Pilih Jurusan Sekolah --</option>
                                                    @foreach ($sekolah as $p)
                                                    <option value="{{$p->id}}">{{$p->sekolah}} - {{$p->jurusan_sekolah}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="prodi1">Program Studi Pilihan 1</label>
                                                <select class="form-control select2" id="prodi1" name="prodi1" disabled>
                                                    <option value="" selected disabled>-- Pilih Program Studi 1 --</option>
                                                </select>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="prodi2">Program Studi Pilihan 2</label>
                                                <select class="form-control select2" id="prodi2" name="prodi2" disabled>
                                                    <option value="" selected disabled>-- Pilih Program Studi 2 --</option>
                                                </select>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="waktukuliah">Waktu Kuliah</label>
                                                <select class="form-control select2" id="waktukuliah" name="waktukuliah" disabled>
                                                    <option value="" selected disabled>-- Pilih Waktu Kuliah --</option>
                                                    @foreach ($waktu as $q)
                                                    <option value="{{$q->id}}">{{$q ->waktu}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group mb-3">
                                                <button type="button" id="btn-prev" class="btn btn-secondary btn-sm">Prev</button>
                                                <button type="button" id="submit-daftar" class="btn btn-success btn-sm float-right"> <i class="fas fa-paper-plane"></i> Submit</button>
                                                <button type="button" id="btn-reset" style="margin-right: 10px;" class="btn btn-warning btn-sm float-right">Reset</button>
                                            </div>
                                        </div>
                                        <!-- Buttons -->
                                        {{-- <div class="form-group"> --}}
                                            {{-- <button type="button" id="submit-daftar" class="btn btn-success btn-sm float-right" style="margin-left:10px;"> <i class="fas fa-paper-plane"></i> Submit</button> --}}
                                            {{-- <button id="btn-reset" class="btn btn-warning btn-sm float-right">Reset</button> --}}
                                        {{-- </div> --}}
                                    </form>
                                </div>
                            </div>
                            <div class="table-responsive" style="margin-top: 20px;">
                                <code>* Silahkan Konfirmasi Pendaftaran dengan Klik </code> <i title="Konfirmasi Pendaftaran" class="fas fa-check-circle text-green"></i> <code> yang ada di kolom Action</code><br>
                                <code>* Jika Sudah Konfirmasi, Data Pendaftaran Tidak Dapat diubah</code><br>
                                <code>* Detail Pendaftaran Bisa dilihat pada tombol </code> <i title="Detail" class="fa fa-info-circle text-blue"></i> <code> pada kolom Action</code><br>
                                <label for="example2">Riwayat Pendaftaran</label>
                                <table id="example2" class="table table-bordered table-hover" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Nomer Registrasi</th>
                                            <th>Batch Pendaftaran</th>
                                            <th>Jalur Pendaftaran</th>
                                            <th>Program Studi Pilihan 1</th>
                                            <th>Program Studi Pilihan 2</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{-- @endfor --}}
                </div>
                <!-- /.col-md-6 -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->

    <div class="modal fade" id="modal-detail">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="judul-modal">Detail Pendaftaran</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="tabel-detail" class="table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th colspan="4" class="text-center" style="background-color: rgb(0, 255, 42);">Data Pendaftaran Calon Mahasiswa Baru</th>
                            </tr>
                            <tr>
                                <th>No Registrasi</th>
                                <th id="o-noregist" class="o-detaildaftar"></th>
                                <th>Nama Calon Mahasiswa</th>
                                <th id="o-nama" class="o-detaildaftar"></th>
                            </tr>
                            <tr>
                                <th>Batch Daftar</th>
                                <th id="o-batch" class="o-detaildaftar"></th>
                                <th>Tahun Lulus</th>
                                <th id="o-tahunlulus" class="o-detaildaftar"></th>
                            </tr>
                            <tr>
                                <th>Jalur Daftar</th>
                                <th id="o-jalur" class="o-detaildaftar"></th>
                                <th>Jurusan Sekolah</th>
                                <th id="o-jurusansekolah" class="o-detaildaftar"></th>
                            </tr>
                            <tr>
                                <th>Program Studi Pilihan 1</th>
                                <th id="o-prodi1" class="o-detaildaftar"></th>
                                <th>Program Studi Pilihan 2</th>
                                <th id="o-prodi2" class="o-detaildaftar"></th>
                            </tr>
                            <tr>
                                <th>UKT Program Studi 1</th>
                                <th id="o-uktprodi1" class="o-detaildaftar"></th>
                                <th>UKT Program Studi 2</th>
                                <th id="o-uktprodi2" class="o-detaildaftar"></th>
                            </tr>
                            <tr>
                                <th>Konfirmasi Daftar</th>
                                <th id="o-konfirmdaftar" class="o-detaildaftar"></th>
                                <th>Biaya Pendaftaran</th>
                                <th id="o-biayadaftar" class="o-detaildaftar"></th>
                            </tr>
                            <tr>
                                <th>Tanggal Daftar</th>
                                <th id="o-tgldaftar" class="o-detaildaftar"></th>
                                <th>Waktu Kuliah</th>
                                <th id="o-waktukuliah" class="o-detaildaftar"></th>
                            </tr>
                            <tr>
                                <th>Status UKT</th>
                                <th id="o-statusukt" class="o-detaildaftar"></th>
                                <th>
                                    <code>*Khusus Beasiswa</code>
                                    <br>
                                    Kategori Beasiswa
                                </th>
                                <th id="o-beasiswa" class="o-detaildaftar"></th>
                            </tr>
                            <tr>
                                <th>
                                    <code>*Khusus Beasiswa</code>
                                    <br>
                                    Tingkat Kejuaraan
                                </th>
                                <th id="o-juarabea" class="o-detaildaftar"></th>
                                <th>
                                    <code>*Khusus Beasiswa</code>
                                    <br>
                                    Keterangan
                                </th>
                                <th id="o-keteranganbea" class="o-detaildaftar"></th>
                            </tr>
                            <tr>
                                <th>
                                    <code>*Khusus Beasiswa</code>
                                    <br>
                                    Durasi Beasiswa D3
                                </th>
                                <th id="o-durasid3" class="o-detaildaftar"></th>
                                <th>
                                    <code>*Khusus Beasiswa</code>
                                    <br>
                                    Durasi Beasiswa S1
                                </th>
                                <th id="o-durasis1" class="o-detaildaftar"></th>
                            </tr>
                            <tr>
                                <th colspan="4" style="background-color: rgb(251, 255, 0);"><code>*Berkas yang diperlukan</code></th>
                            </tr>
                        </thead>
                        <tbody id="detail-berkas" class="o-detaildaftar">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default float-right" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection
@section('script')
    <script>
        var isEditing = false; // Flag penanda status edit

        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('.select2').select2()

            LoadEvent()

            function LoadEvent()
            {
                ShowJalur()
                ShowBeasiswa()
                ShowDetailBeasiswa()
                openJurusansekolah()
                openProdi()
                openWaktuKuliah()
                Next()
                Prev()
                submitDaftar()
                resetForm()
                tabelPendaftaran()
            }

            function tabelPendaftaran()
            {
                let otable = $('#example2').DataTable({
                    destroy: true,
                    processing: true,
                    paging: false,
                    scrollX: true,
                    scrollY: '500px',
                    scrollCollapse: true,
                    serverSide: true,
                    searchDelay: 500,
                    responsive: true,
                    order: [],
                    ajax: {
                        url: '{!! route('Daftar.TabelDaftar') !!}',
                        type: 'GET',
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nama'
                        },
                        {
                            data: 'noreg'
                        },
                        {
                            data: 'batch'
                        },
                        {
                            data: 'jalur'
                        },
                        {
                            data: 'prodi1'
                        },
                        {
                            data: 'prodi2'
                        },
                        {
                            data: 'status'
                        },
                        {
                            data: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    language: {
                        processing: '<i class="fa fa-spinner fa-lg fa-spin"></i>'
                    },
                    drawCallback: function(settings) {
                        deleteDaftar()
                        editDaftar()
                        ConfirmDaftar()
                        ShowDetail()
                    }
                });

                otable.on('draw', function(event) {
                    $('[data-toggle="tooltip"]').tooltip({trigger: "hover"});
                    $('[data-tooltip="tooltip"]').tooltip({trigger: "hover"});

                });
            }

            function ConfirmDaftar()
            {
                $('.btn_konfirm').off('click').click(function (e) {
                    e.preventDefault();
                    let params = $(this).data('id')
                    Swal.fire({
                        title: "Information",
                        text: "Apakah Data Pendaftaran Anda Sudah Benar ? Jika Sudah silahkan Konfirmasi",
                        icon: "question",
                        showConfirmButton: true,
                        showCancelButton: true,
                    }).then((result) => {
                        if(result.value){
                            $.ajax({
                                type: "GET",
                                url: '{!! url('Pendaftaran/KonfirmasiDaftar') !!}' + '/' + params,
                                dataType: "JSON",
                                beforeSend: function(response) {
                                    $('#loading').show()
                                },
                                success: function(data) {
                                    $('#loading').hide()
                                    Swal.fire({
                                        title: data.title,
                                        text: data.message,
                                        icon: data.status
                                    }).then((result) => {
                                        $('#example2').DataTable().ajax.reload();
                                        $('#btn-dashboard').trigger('click');
                                    });
                                    return;
                                },
                                error: function(xhr, status, error) {
                                    $('#loading').hide()
                                    Swal.fire({
                                        title: 'Unsuccessfully Saved Data',
                                        text: 'Check Your Data',
                                        icon: 'error'
                                    }).then((result) => {
                                        $('#example2').DataTable().ajax.reload();
                                    });
                                    return;
                                }
                            });
                        }else{
                            return false;
                        }
                    });
                });
            }

            // Function baru untuk memuat Jalur dengan Callback
function loadJalur(batchId, selectedJalur = null, callback = null) {
    if (batchId == null) {
        $('#jalur').empty().append('<option value="" selected disabled>-- Pilih Jalur Pendaftaran --</option>');
        $('#jalur').prop('disabled', true);
        return;
    }

    $.ajax({
        type: "GET",
        url: '{!! url("Pendaftaran/ShowJalur") !!}' + '/' + batchId,
        dataType: "JSON",
        beforeSend: function(response) {
            // Kita tetap kosongkan dulu untuk efek visual loading
            if (!window.isEditing) $('#loading').show();
            $('#jalur').prop('disabled', true);
            $('#jalur').empty(); 
        },
        success: function(data) {
            if (!window.isEditing) { 
                $('#loading').hide(); 
            }

            if (data.hasil == 0) {
                notifalert('Information', 'Batch Pendaftaran Sudah Berakhir', 'warning');
            } else if (data.hasil == -1) {
                notifalert('Information', 'Jalur Pendaftaran Belum Tersedia', 'warning');
            } else {
                let dis1 = '<option value="" selected disabled>-- Pilih Jalur Pendaftaran --</option>';
                
                // Gunakan let agar variabel i tidak bocor keluar (safety)
                for (let i = 0; i < data.jalur.length; i++) {
                    let isSelected = (selectedJalur == data.jalur[i].id) ? 'selected' : '';
                    
                    // Kita simpan data-beasiswa agar bisa dibaca oleh loadBeasiswa nanti
                    dis1 += '<option value="' + data.jalur[i].id + '" data-beasiswa="' + data.jalur[i].is_beasiswa + '" ' + isSelected + '>' + data.jalur[i].jenis_pendaftaran + '</option>';
                }
                
                // --- PERBAIKAN UTAMA DISINI ---
                // Ganti .append() menjadi .html() agar isi dropdown di-replace total (tidak double)
                $('#jalur').html(dis1);
                $('#jalur').prop('disabled', false);

                // Trigger change jika ada selectedJalur agar loadBeasiswa otomatis jalan
                if (selectedJalur) {
                    // Gunakan setTimeout kecil untuk memastikan DOM sudah ter-render sempurna sebelum trigger
                    setTimeout(() => {
                        $('#jalur').trigger('change');
                    }, 50);
                }

                // Eksekusi Callback (Penting untuk Edit)
                if (callback && typeof callback === "function") {
                    callback(); 
                }
            }
        },
        error: function(xhr, status, error) {
            if (!window.isEditing) $('#loading').hide();
            Swal.fire({ title: 'Gagal', text: 'Error, Silahkan Hubungi Admin !', icon: 'error' });
        }
    });
}

            function ShowJalur() {
    $('#batch').on('change', function() {
        let params = $(this).val();
        // Panggil function loadJalur biasa tanpa callback khusus
        loadJalur(params); 
    });
}

            function loadBeasiswa(idJalur = null, selectedBea = null) {
    

    // Prioritaskan parameter kiriman, baru ambil dari DOM
    let params = idJalur ? idJalur : $('#jalur').val();
    
    // --- PERBAIKAN LOGIC DISINI ---
    // Kita ambil status REAL dari atribut data-beasiswa di dropdown yang sudah terpilih.
    // Karena loadJalur sudah selesai, value ini PASTI benar (0 untuk Reguler, 1 untuk Beasiswa).
    let isBeasiswa = $('#jalur').find(':selected').data('beasiswa');
    
    // Fallback: Jika undefined (misal DOM belum ready), anggap 0 (Reguler) biar aman dan tidak error
    if (typeof isBeasiswa === 'undefined') isBeasiswa = 0; 

    

    if (!params) {
        console.warn("Params kosong, dropdown dikosongkan.");
        $('#beasiswa').empty().append('<option value="" selected disabled>-- Pilih Jalur Beasiswa --</option>');
        $('#beasiswa').prop('disabled', true);
        return;
    }

    $.ajax({
        type: "GET",
        url: '{!! url("Pendaftaran/ShowBeasiswa") !!}' + '/' + params,
        dataType: "JSON",
        beforeSend: function(response) {
            // Hanya nyalakan loading jika BUKAN mode edit (konsisten dengan function lain)
            if (!window.isEditing) {
                $('#loading').show();
            }
            $('#beasiswa').prop('disabled', true);
            $('#beasiswa').empty();
            $('#tingkat').val('');
            $('#keterangan').val('');
        },
        success: function(data) {
           

            // Matikan loading hanya jika bukan mode edit
            if (!window.isEditing) { 
                $('#loading').hide(); 
            }
            
            $('#beasiswa').empty();
            let dis1 = '<option value="" selected disabled>-- Pilih Jalur Beasiswa --</option>';

            // Cek apakah array 'bea' ada dan isinya lebih dari 0
            if (data.bea && data.bea.length > 0) {
                
                
                for (let i = 0; i < data.bea.length; i++) {
                    // Pakai loose equality (==) biar aman antara string/int
                    let selected = (selectedBea == data.bea[i].id) ? 'selected' : '';
                    dis1 += '<option value="' + data.bea[i].id + '" ' + selected + '>' + data.bea[i].jenis_beasiswa + '</option>';
                }
                $('#beasiswa').prop('disabled', false);
            } else {
                console.warn("4. Data beasiswa kosong dari server.");
                
                // --- PERBAIKAN LOGIC ALERT ---
                // Hanya munculkan alert jika 'isBeasiswa' bernilai 1 (True/Beasiswa)
                // Jadi kalau Reguler (0), dia akan diam saja meski datanya kosong.
                if (isBeasiswa == 1) {
                    notifalert('Information', 'Kategori Beasiswa Belum Ada !', 'warning');
                }
                
                $('#beasiswa').prop('disabled', true);
            }

            $('#beasiswa').append(dis1);
            $('#tingkat').val('');
            $('#keterangan').val('');

            // Trigger change untuk memuat detail (Tingkat/Keterangan)
            if (selectedBea) {
            
                setTimeout(() => {
                    $('#beasiswa').val(selectedBea).trigger('change');
                }, 100);
            }
        },
        error: function(xhr, status, error) {
            console.error("ERROR Ajax:", error);
            if (!window.isEditing) {
                $('#loading').hide();
            }
        }
    });
}

            function ShowBeasiswa() {
    $('#jalur').on('change', function() {
        // HANYA jalan jika BUKAN sedang mode editing
        if (window.isEditing == false) {
            
            loadBeasiswa();
        } else {
            
        }
    });
}

            function ShowDetailBeasiswa()
            {
                $('#beasiswa').on('change', function() {
                    let params = $(this).val()
                    $.ajax({
                        type: "GET",
                        url: '{!! url('Pendaftaran/ShowDetailBeasiswa') !!}' + '/' + params,
                        dataType: "JSON",
                        beforeSend: function(response) {
                            if (!window.isEditing) {
                    $('#loading').show();
                }
                            $('#tingkat').val('')
                            $('#keterangan').val('')
                        },
                        success: function(data) {
                            
                            if (!window.isEditing) {
                    $('#loading').hide();
                }
                            let tingkat = data.bea.idtingkat==null ? '-' : data.bea.tingkat.tingkat_kejuaraan
                            let keter = data.bea==null ? '-' : data.bea.juara_ke
                            $('#tingkat').val(tingkat)
                            $('#keterangan').val(keter)
                        },
                        error: function(xhr, status, error) {
                            $('#loading').hide()
                            Swal.fire({
                                title: 'Gagal',
                                text: 'Error, Silahkan Hubungi Admin !',
                                icon: 'error'
                            }).then((result) => {
                                console.log(0)
                            });
                            return;
                        }
                    });
                });
            }

            function openJurusansekolah()
            {
                $('#tahunlulus').on('change', function() {
                    let tahun = $(this).val()
                    if(tahun!=null||tahun!=''){
                        $('#jurusansekolah').prop('disabled',false)
                    }else{
                        $('#jurusansekolah').prop('disabled',true)
                    }
                });
            }

            function loadProdi(selectedProdi1 = null, selectedProdi2 = null, forceBatch = null, forceJalur = null, forceJurusan = null, callback = null) {
    let batch = forceBatch ? forceBatch : $('#batch').val();
    let jalur = forceJalur ? forceJalur : $('#jalur').val();
    let jurusansekolah = forceJurusan ? forceJurusan : $('#jurusansekolah').val();

    // Jika data tidak lengkap, hentikan tapi tetap jalankan callback agar loading mati
    if (!batch || !jalur || !jurusansekolah) {
        if (callback && typeof callback === "function") callback();
        return;
    }

    $.ajax({
        type: "GET",
        url: '{!! url("Pendaftaran/ShowProdi") !!}' + '/' + batch + '/' + jalur + '/' + jurusansekolah,
        dataType: "JSON",
        beforeSend: function() {
            if (!window.isEditing) $('#loading').show();
        },
        success: function(data) {
            // Hanya matikan loading jika BUKAN mode edit
            if (!window.isEditing) { 
                $('#loading').hide(); 
            }

            if (data.hasil == 0) {
                if (typeof window.isEditing !== 'undefined' && !window.isEditing) {
                    notifalert('Information', 'Data Jurusan Tidak Ditemukan', 'error');
                }
            } else {
                

                // 1. Matikan Select2 Lama
                if ($('#prodi1').hasClass("select2-hidden-accessible")) {
                    $('#prodi1').select2('destroy');
                    $('#prodi2').select2('destroy');
                }

                // 2. Bersihkan & Enable Select Asli
                $('#prodi1, #prodi2').empty().prop('disabled', false);

                // 3. Tambahkan Placeholder
                $('#prodi1').append(new Option('-- Pilih Program Studi 1 --', '', true, true));
                $('#prodi2').append(new Option('-- Pilih Program Studi 2 --', '', true, true));
                $('#prodi1 option:first').prop('disabled', true);
                $('#prodi2 option:first').prop('disabled', true);

                // --- LOGIC EXTRACTION TARGET ---
                let target1 = null;
                let target2 = null;

                if (selectedProdi1) {
                    if (typeof selectedProdi1 === 'object' && selectedProdi1.KodeJurusan) {
                        target1 = $.trim(selectedProdi1.KodeJurusan);
                    } else {
                        target1 = $.trim(selectedProdi1);
                    }
                }

                if (selectedProdi2) {
                    if (typeof selectedProdi2 === 'object' && selectedProdi2.KodeJurusan) {
                        target2 = $.trim(selectedProdi2.KodeJurusan);
                    } else {
                        target2 = $.trim(selectedProdi2);
                    }
                }

                // --- RENDER OPTIONS ---
                $.each(data.jurusan, function(index, item) {
                    let kode = $.trim(item.KodeJurusan);
                    let teks = item.jenjang.jenjang + ' - ' + item.jurusan;

                    let isSelected1 = (target1 && kode == target1); 
                    let isSelected2 = (target2 && kode == target2);

                    let optionFor1 = new Option(teks, kode, isSelected1, isSelected1);
                    let optionFor2 = new Option(teks, kode, isSelected2, isSelected2);

                    $('#prodi1').append(optionFor1);
                    $('#prodi2').append(optionFor2);
                });

                // 4. Hidupkan Kembali Select2
                $('#prodi1').select2({ theme: 'bootstrap4', width: '100%' });
                $('#prodi2').select2({ theme: 'bootstrap4', width: '100%' });
                
                if(target1) $('#prodi1').val(target1).trigger('change.select2');
                if(target2) $('#prodi2').val(target2).trigger('change.select2');
            }

            // --- PENTING: Jalankan Callback (Matikan Loading) Disini ---
            if (callback && typeof callback === "function") {
                callback();
            }
        },
        error: function(xhr, status, error) {
            if (!window.isEditing) $('#loading').hide();
            console.error("Error AJAX Prodi: " + error);
            
            // Tetap jalankan callback agar loading tidak nyangkut selamanya jika error
            if (callback && typeof callback === "function") callback();
        }
    });
}

            function openProdi()
            {
                $('#jurusansekolah').on('change', function() {
                    // if (!window.isEditing) {
                    loadProdi();
                    // }
                });
            }

            function openWaktuKuliah()
            {
                $('#prodi2').on('change', function() {
                    let prodi2 = $(this).val()
                    if(prodi2!=null||prodi2!=''){
                        $('#waktukuliah').prop('disabled',false)
                    }else{
                        $('#waktukuliah').prop('disabled',true)
                    }

                });
            }

            function Next()
            {
                $('#btn-next').click(function (e) {
                    e.preventDefault();
                    let batch = $('#batch').val()
                    let jalur = $('#jalur').val()
                    let selectjalur = $('#jalur').find(':selected');
                    let beasiswa = selectjalur.data('beasiswa')
                    let kategoribeasiswa = $('#beasiswa').val()
                    
                    if(batch==null){
                        notifalert('Information', 'Isi Batch Pendaftaran Terlebih Dahulu','warning')
                    }else if(jalur==null){
                        notifalert('Information', 'Jalur Pendaftaran Tidak Boleh Kosong','warning')
                    }else if(beasiswa==1){
                        if(kategoribeasiswa==null){
                            notifalert('Information', 'Kategori Beasiswa Tidak Boleh kosong','warning')
                        }else{
                            $('#page-1').hide()
                            $('#page-2').show()
                        }
                    }else{
                        $('#page-1').hide()
                        $('#page-2').show()
                    }
                });
            }

            function Prev()
            {
                $('#btn-prev').click(function (e) {
                    e.preventDefault();
                    $('#page-1').show()
                    $('#page-2').hide()
                });
            }

            function validasiDaftar()
            {
                let tahun          = $('#tahunlulus').val()
                let jurusansekolah = $('#jurusansekolah').val()
                let prodi1         = $('#prodi1').val()
                let prodi2         = $('#prodi2').val()
                let waktukuliah    = $('#waktukuliah').val()

                let notif = '';

                if(tahun==null){
                    notif = 'Tahun Lulus Tidak Boleh Kosong';
                }else if(jurusansekolah==null){
                    notif = 'Jurusan Sekolah Tidak Boleh Kosong';
                }else if(prodi1==null){
                    notif = 'Program Studi Pilihan 1 Tidak Boleh Kosong';
                }else if(prodi2==null){
                    notif = 'Program Studi Pilihan 2 Tidak Boleh Kosong';
                }else if(waktukuliah==null){
                    notif = 'Waktu Kuliah Tidak Boleh Kosong';
                }else if(prodi1==prodi2){
                    notif = 'Program Studi 1 dan 2 tidak boleh sama';
                }else{
                    notif = 'success';
                }
                return notif;
            }

            function submitDaftar()
            {
                $('#submit-daftar').click(function (e) {
                    e.preventDefault();
                    let validasiku = validasiDaftar()
                    if(validasiku != 'success'){
                        notifalert('Information',validasiku,'warning')
                    }else{
                        let dataku = $('#form-fakultas').serialize()
                        Swal.fire({
                            title: "Information",
                            text: "Apakah Data Pendaftaran Anda Sudah Benar ?",
                            icon: "question",
                            showConfirmButton: true,
                            showCancelButton: true,
                        }).then((result) => {
                            if(result.value){
                                $.ajax({
                                    type: "POST",
                                    url: "{{route('Daftar.StoreDaftar')}}",
                                    data: dataku,
                                    dataType: "JSON",
                                    beforeSend: function(response) {
                                        $('#submit-daftar').html('<i class="fas fa-hourglass"></i> Please Wait')
                                        $('#submit-daftar').prop('disabled', true)
                                        $('#loading').show()
                                    },
                                    success: function(data) {
                                        $('#loading').hide()
                                        Swal.fire({
                                            title: data.title,
                                            text: data.message,
                                            icon: data.status
                                        }).then((result) => {
                                            $('#submit-daftar').html('<i class="fas fa-paper-plane"></i> Submit')
                                            $('#submit-daftar').prop('disabled',false)
                                            $('#btn-reset').trigger('click');
                                            $('#example2').DataTable().ajax.reload();
                                            // window.isEditing = false;
                                        });
                                        return;
                                    },
                                    error: function(xhr, status, error) {
                                        $('#loading').hide()
                                        Swal.fire({
                                            title: 'Unsuccessfully Saved Data',
                                            text: 'Check Your Data',
                                            icon: 'error'
                                        }).then((result) => {
                                            $('#submit-daftar').html('<i class="fas fa-paper-plane"></i> Submit')
                                            $('#submit-daftar').prop('disabled',false)
                                        });
                                        return;
                                    }
                                });
                            }else{
                                return false;
                            }
                        });
                    }
                });
            }

            function resetForm()
            {
                $('#btn-reset').click(function (e) {
                    // e.preventDefault();
                    $('#IdPendaftaran').val(null)
                    $('#batch').val('').trigger('change')
                    $('#beasiswa').empty().append('<option value="" selected disabled>-- Pilih Jalur Beasiswa --</option>');
                    $('#beasiswa').prop('disabled',true)
                    $('#tingkat').val('')
                    $('#keterangan').val('')
                    $('#tahunlulus').val('').trigger('change')
                    $('#jurusansekolah').val('').trigger('change')
                    $('#jurusansekolah').prop('disabled',true)
                    $('#waktukuliah').prop('disabled',true)
                    $('#waktukuliah').val('').trigger('change')
                    $('#btn-prev').trigger('click')
                    // notifalert('Information','Silahkan Mengulang Input Dari Awal','success')
                });
            }

            function editDaftar() {
    $('.btn_edit').off('click').click(function(e) {
        e.preventDefault();
        
        // 1. AKTIFKAN MODE EDIT & LOADING
        window.isEditing = true; 
        $('#loading').show();
        
        let param = $(this).data('id');

        $.ajax({
            type: "GET",
            url: '{!! url('Pendaftaran/ShowDaftar') !!}' + '/' + param,
            dataType: "JSON",
            beforeSend: function(response) {
                // Loading sudah nyala di atas
                $('#btn-reset').trigger('click');
            },
            success: function(data) {
                if (data.hasil == 0) {
                    $('#loading').hide();
                    notifalert('Information', 'Data Pendaftaran Tidak Ditemukan', 'error');
                    window.isEditing = false;
                } else {
                    $('#IdPendaftaran').val(data.IdDaftar);
                    
                    // Trigger change agar UI Batch muncul
                    $('#batch').val(data.daftar.batch_daftar).trigger('change'); 

                    // Load Jalur dengan Callback
                    loadJalur(data.daftar.batch_daftar, data.daftar.jalur_daftar, function() {
                        
                        // --- LOGIC ID BEASISWA ---
                        let beaId = null;
                        if (data.daftar.beasiswa && typeof data.daftar.beasiswa !== 'object') {
                            beaId = data.daftar.beasiswa;
                        } else if (data.daftar.beasiswa && data.daftar.beasiswa.id) {
                            beaId = data.daftar.beasiswa.id;
                        } else if (data.daftar.jenisbeasiswa && data.daftar.jenisbeasiswa.id) {
                            beaId = data.daftar.jenisbeasiswa.id;
                        }
                        
                        // Load Beasiswa
                        loadBeasiswa(data.daftar.jalur_daftar, beaId);

                        // Isi data lainnya
                        $('#tahunlulus').val(data.daftar.tahun_lulus).trigger('change');
                        
                        // Beri jeda sedikit agar DOM siap
                        setTimeout(() => {
                            $('#jurusansekolah').val(data.daftar.jurusan_sekolah).trigger('change');
                            $('#waktukuliah').val(data.daftar.waktu_kuliah).trigger('change');
                            $('#waktukuliah').prop('disabled', false); 

                            // --- FUNGSI FINAL: MATIKAN LOADING ---
                            // Fungsi ini akan dipanggil OLEH loadProdi setelah dia selesai
                            var onProdiFinished = function() {
                                setTimeout(() => {
                                    window.isEditing = false;
                                    $('#waktukuliah').prop('disabled', false); // Double check enable
                                    $('#loading').fadeOut(); // Matikan loading dengan halus
                                   
                                }, 500); // Jeda pemanis
                            };

                            // Panggil loadProdi dan kirim fungsi 'onProdiFinished'
                            if(typeof loadProdi === 'function') {
                                loadProdi(data.daftar.prodi1, data.daftar.prodi2, null, null, null, onProdiFinished);
                            } else {
                                // Fallback jika loadProdi tidak ada
                                onProdiFinished();
                            }

                        }, 300); // Delay sebelum mulai render prodi
                    });
                }
            },
            error: function(data) {
                $('#loading').hide();
                window.isEditing = false;
                Swal.fire({ title: 'Error', text: 'Gagal', icon: 'error' });
            }
        });
        return false;
    });
}

            function deleteDaftar()
            {
                $('.btn_delete').click(function (e) {
                    e.preventDefault();
                    let params = $(this).data('id')
                    Swal.fire({
                        title: "Information",
                        text: "Apakah Tidak ingin lanjut pada Pendaftaran Batch Ini ?",
                        icon: "question",
                        showConfirmButton: true,
                        showCancelButton: true,
                    }).then((result) => {
                        if(result.value){
                            $.ajax({
                                type: "GET",
                                url: '{!! url('Pendaftaran/DeleteDaftar') !!}'+'/'+params,
                                dataType: "JSON",
                                beforeSend: function(response) {
                                    $('#loading').show()
                                },
                                success: function(data) {
                                    $('#loading').hide()
                                    Swal.fire({
                                        title: data.title,
                                        text: data.message,
                                        icon: data.status
                                    }).then((result) => {
                                        $('#btn-reset').trigger('click');
                                        $('#example2').DataTable().ajax.reload();
                                    });
                                    return;
                                },
                                error: function(xhr, status, error) {
                                    $('#loading').hide()
                                    Swal.fire({
                                        title: 'Unsuccessfully Delete Data',
                                        text: 'Check Your Data',
                                        icon: 'error'
                                    }).then((result) => {
                                        console.log(0)
                                    });
                                    return;
                                }
                            });
                        }else{
                            return false;
                        }
                    });
                });
            }

            function ShowDetail()
            {
                $('.btn_detail').click(function (e) {
                    e.preventDefault();
                    let param = $(this).data('id')
                    // $('#modal-detail').modal('show')
                    $.ajax({
                        type: "GET",
                        url: '{!! url('Pendaftaran/ShowDaftar') !!}'+'/'+param,
                        dataType: "JSON",
                        beforeSend: function(response) {
                            $('#loading').show()
                            $('.o-detaildaftar').empty()
                        },
                        success: function(data) {
                            $('#loading').hide()
                            if(data.hasil==0){
                                notifalert('Information', 'Data Pendaftaran Tidak Ditemukan','error')
                            }else{
                                $('#o-noregist').html(data.daftar.KodePendaftaran)
                                $('#o-nama').html(data.daftar.biodata.nama)
                                $('#o-batch').html(data.daftar.batch.nama_batch+' '+data.daftar.batch.tahun_akademik)
                                $('#o-tahunlulus').html(data.daftar.tahun_lulus)
                                $('#o-jalur').html(data.daftar.jalur.KodeJenis+'-'+data.daftar.jalur.jenis_pendaftaran)
                                $('#o-jurusansekolah').html(data.daftar.jurusansekolah.sekolah+'/'+data.daftar.jurusansekolah.jurusan_sekolah)
                                $('#o-prodi1').html(data.daftar.prodi1.jenjang.jenjang+'-'+data.daftar.prodi1.jurusan)
                                $('#o-prodi2').html(data.daftar.prodi2.jenjang.jenjang+'-'+data.daftar.prodi2.jurusan)
                                // let ukt1 = data.ukt1.biaya_ukt.replace(/\D/g, '')
                                let ukt1 = new Intl.NumberFormat('id-ID').format(data.ukt1.biaya_ukt);
                                $('#o-uktprodi1').html('Rp '+ukt1)
                                let ukt2 = new Intl.NumberFormat('id-ID').format(data.ukt2.biaya_ukt);
                                $('#o-uktprodi2').html('Rp '+ukt2)
                                let konfirmdaftar = data.daftar.konfirm_pendaftaran=='0' ? 'Belum Konfirmasi' : 'Sudah Konfirmasi';
                                $('#o-konfirmdaftar').html(konfirmdaftar)
                                let biayadaftar = data.daftar.jalur.biaya_pendaftaran=='1' ? 'Rp '+new Intl.NumberFormat('id-ID').format(data.daftar.jalur.jml_biaya_pendaftaran) : 'Gratis';
                                $('#o-biayadaftar').html(biayadaftar)
                                $('#o-tgldaftar').html(data.daftar.tgl_daftar)
                                $('#o-waktukuliah').html(data.daftar.waktukuliah.waktu)
                                let statusUkt = data.daftar.jalur.status_ukt=='0' ? 'Gratis' : 'Bayar';
                                $('#o-statusukt').html(statusUkt)
                                let beasiswa = data.daftar.jenisbeasiswa==null ? '-' : data.daftar.jenisbeasiswa.jenis_beasiswa
                                $('#o-beasiswa').html(beasiswa)
                                let tingkat = data.daftar.jenisbeasiswa==null ? '-' : data.daftar.jenisbeasiswa.idtingkat;
                                $('#o-juarabea').html(tingkat==null||tingkat=='-'?'-':data.daftar.jenisbeasiswa.tingkat.tingkat_kejuaraan)
                                let ketbea = data.daftar.jenisbeasiswa==null ? '-' : data.daftar.jenisbeasiswa.juara_ke
                                $('#o-keteranganbea').html(ketbea)
                                let durasid3 = data.daftar.jenisbeasiswa==null ? '-' : data.daftar.jenisbeasiswa.durasi_d3+' Semester'
                                $('#o-durasid3').html(durasid3)
                                let durasis1 = data.daftar.jenisbeasiswa==null ? '-' : data.daftar.jenisbeasiswa.durasi_s1+' Semester'
                                $('#o-durasis1').html(durasis1)

                                let berkas = ''

                                berkas += '<tr style="background-color: rgb(0, 238, 255);">'+
                                            '<th colspan="4" class="text-center">'+data.daftar.jalur.berkasumum.jenis_berkas+'</th>'+
                                          '</tr>'
                                berkas += '<tr style="background-color: rgb(0, 238, 255);">'+
                                            '<th>No</td>'+
                                            '<th>Nama berkas</th>'+
                                            '<th>Keterangan</th>'+
                                            '<th>Format File</th>'+
                                          '</tr>'

                                for(i=0;i<data.daftar.jalur.berkasumum.berkas.length;i++){
                                    let no = i+1;
                                    berkas += '<tr>'+
                                                '<td>'+no+'</td>'+
                                                '<td>'+data.daftar.jalur.berkasumum.berkas[i].nama_berkas+'</td>'+
                                                '<td>'+data.daftar.jalur.berkasumum.berkas[i].keterangan+'</td>'+
                                                '<td>'+data.daftar.jalur.berkasumum.berkas[i].formatfile+'</td>'+
                                              '</tr>'
                                }

                                let berkaskhusus = data.daftar.jalur.berkas_khusus ? data.daftar.jalur.berkaskhusus.jenis_berkas : 'Khusus';
                                berkas += '<tr style="background-color: rgb(0, 238, 255);">'+
                                            '<th colspan="4" class="text-center">'+berkaskhusus+'</th>'+
                                          '</tr>'
                                if(data.daftar.jalur.berkas_khusus){
                                    berkas += '<tr style="background-color: rgb(0, 238, 255);">'+
                                                '<th>No</td>'+
                                                '<th>Nama berkas</th>'+
                                                '<th>Keterangan</th>'+
                                                '<th>Format File</th>'+
                                              '</tr>'
                                    for(i=0;i<data.daftar.jalur.berkaskhusus.berkas.length;i++){
                                        let noo = i+1;
                                        berkas += '<tr>'+
                                                    '<td>'+noo+'</td>'+
                                                    '<td>'+data.daftar.jalur.berkaskhusus.berkas[i].nama_berkas+'</td>'+
                                                    '<td>'+data.daftar.jalur.berkaskhusus.berkas[i].keterangan+'</td>'+
                                                    '<td>'+data.daftar.jalur.berkaskhusus.berkas[i].formatfile+'</td>'+
                                                '</tr>'
                                    }

                                }else{
                                    berkas += '<tr>'+
                                            '<th colspan="4" class="text-center">Tidak Ada Berkas Khusus</th>'+
                                          '</tr>'
                                }

                                $('#detail-berkas').append(berkas)

                                $('#modal-detail').modal('show')
                            }
                        },
                        error: function(data) {
                            $('#loading').hide()
                            Swal.fire({
                                title: 'Gagal Show Data Pendaftaran !',
                                text: 'Silahkan Hubungi Admin PMB TSU',
                                icon: 'error'
                            }).then((result) => {
                                window.isEditing = false;
                            });
                            return;
                        }
                    });
                    return false;
                });
            }

        });

    </script>
@endsection