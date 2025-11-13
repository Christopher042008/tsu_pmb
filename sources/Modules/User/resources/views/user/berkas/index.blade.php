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
                        <li class="breadcrumb-item active"><a href="{{ route('Dashboard') }}">Dashboard</a></li>
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
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="m-0">{{$menu}}</h5>
                        </div>
                        <div class="card-body">
                            <code>* Untuk Keterangan diisi oleh admin saat validasi berkas</code><br>
                            <code>* Jika Status Sudah OK/berwarna hijau maka berkas sudah disetujui dan tidak bisa diganti</code><br>
                            <code>* Tekan </code> <i title="Upload Berkas" class="fas fa-upload text-green"></i> <code>  Untuk Upload Berkas</code> <br>
                            <code>* Tekan </code> <i title="Detail Pendaftaran" class="fa fa-info-circle text-blue"></i> <code>  Untuk Melihat Detail Pendaftaran dan Berkas apa saja yang harus di upload</code><br>
                            <code>* Tombol </code> <i title="Lihat Berkas" class="fas fa-eye text-green"></i> <code>  Akan Muncul Setelah Berkas Diupload, Untuk Melihat Berkas</code>
                            <table id="example2" class="table table-bordered table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>No Registrasi</th>
                                        <th>Batch Daftar</th>
                                        <th>Jalur Daftar</th>
                                        <th>Jenis Beasiswa</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
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
                                <th>Berkas Beasiswa</th>
                                <th colspan="3" class="o-detaildaftar" id="o-berkasbeasiswa"></th>
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

    <div class="modal fade" id="modal-upload">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="judul-modal">Upload Berkas Khusus</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Perhatian <br>
                    <code>1. Periksa Kembali berkas Khusus yang ada di Detail Pendaftarann di tombol </code> <i title="Detail Pendaftaran" class="fa fa-info-circle text-blue"></i><br>
                    <code>2. Semua berkas dijadikan dalam 1 file berbentuk PDF</code><br>
                    <code>3. Ukuran Maksimal berkas adalah 2 MB</code><br>
                    <form id="form-uploadberkas" method="POST" action="#" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="iddaftar" name="iddaftar" value="">
                        <div class="form-group">
                            <label class="col-form-label">Pilih Berkas Khusus</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="berkaskhusus" name="berkaskhusus" accept="application/pdf">
                                    <label class="custom-file-label" for="berkaskhusus">Choose file</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" id="btn-closemodalberkas" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                    <button type="button" id="btn-saveberkas" class="btn btn-success btn-sm">Simpan</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection
@section('script')
    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.select2').select2()

            loadEvent()

            function loadEvent()
            {
                tabelBerkasBeasiswa()
                saveBerkas()
                validasiberkas()
            }

            function tabelBerkasBeasiswa()
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
                        url: '{!! route('BksBeasiswa.Tabel') !!}',
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
                            data: 'beasiswa'
                        },
                        {
                            data: 'status'
                        },
                        {
                            data: 'keterangan'
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
                        ShowBerkasBeasiswa()
                        UploadBerkas()
                        setuju()
                        tidaksetuju()
                    }
                });

                otable.on('draw', function(event) {
                    $('[data-toggle="tooltip"]').tooltip({trigger: "hover"});
                    $('[data-tooltip="tooltip"]').tooltip({trigger: "hover"});

                });
            }

            function ShowBerkasBeasiswa()
            {
                $('.btn_detail').click(function (e) {
                    e.preventDefault();
                    let param = $(this).data('id')
                    // $('#modal-detail').modal('show')
                    $.ajax({
                        type: "GET",
                        url: '{!! url('BerkasBeasiswa/ShowBerkasBeasiswa') !!}'+'/'+param,
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
                                let namaberkaskhusus = data.daftar.berkas_khusus ? data.daftar.berkas_khusus : '-'
                                $('#o-berkasbeasiswa').html(namaberkaskhusus)

                                let berkas = ''

                                // berkas += '<tr style="background-color: rgb(0, 238, 255);">'+
                                //             '<th colspan="4" class="text-center">'+data.daftar.jalur.berkasumum.jenis_berkas+'</th>'+
                                //           '</tr>'
                                // berkas += '<tr style="background-color: rgb(0, 238, 255);">'+
                                //             '<th>No</td>'+
                                //             '<th>Nama berkas</th>'+
                                //             '<th>Keterangan</th>'+
                                //             '<th>Format File</th>'+
                                //           '</tr>'

                                // for(i=0;i<data.daftar.jalur.berkasumum.berkas.length;i++){
                                //     let no = i+1;
                                //     berkas += '<tr>'+
                                //                 '<td>'+no+'</td>'+
                                //                 '<td>'+data.daftar.jalur.berkasumum.berkas[i].nama_berkas+'</td>'+
                                //                 '<td>'+data.daftar.jalur.berkasumum.berkas[i].keterangan+'</td>'+
                                //                 '<td>'+data.daftar.jalur.berkasumum.berkas[i].formatfile+'</td>'+
                                //               '</tr>'
                                // }

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

            function UploadBerkas()
            {
                $('.btn_upload').click(function (e) {
                    e.preventDefault();
                    let param = $(this).data('id')
                    $('#iddaftar').val(param)
                    $('#modal-upload').modal('show')
                });
            }

            function validasiberkas(){
                $('#berkaskhusus').on('change', function() {
                    let file = this.files[0];

                    if (file) {
                        let fileType = file.type;
                        let fileSize = file.size; // dalam byte

                        // ✅ Validasi tipe file (harus PDF)
                        if (fileType != 'application/pdf') {
                            notifalert('Information','Hanya file PDF yang diperbolehkan!','warning');
                            $(this).val(''); // reset input
                            return false;
                        }

                        // ✅ Validasi ukuran file (maks 2 MB)
                        if (fileSize > 2 * 1024 * 1024) { // 2 MB = 2 * 1024 * 1024 bytes
                            notifalert('Information','Ukuran file maksimal 2 MB!','warning');
                            $(this).val(''); // reset input
                            return false;
                        }
                    }
                });
            }

            function saveBerkas()
            {
                $('#btn-saveberkas').click(function (e) {
                    e.preventDefault();
                    let berkaskhusus = $('#berkaskhusus').val()
                    let fileInput = $('#berkaskhusus').prop('files')[0]
                    if(berkaskhusus==''||berkaskhusus==null){
                        notifalert('Information','Berkas Tidak Boleh Kosong!','warning');
                    }else{
                        // let dataku = $('#form-uploadberkas').serialize()

                        let formData = new FormData();
                        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                        formData.append('kodedaftar', $('#iddaftar').val());
                        formData.append('fileberkas', fileInput);
                        Swal.fire({
                            title: "Information",
                            text: "Apakah Berkas Anda Sudah Benar ?",
                            icon: "question",
                            showConfirmButton: true,
                            showCancelButton: true,
                        }).then((result) => {
                            if(result.value){
                                $.ajax({
                                    type: "POST",
                                    url: "{{route('BksBeasiswa.save')}}",
                                    processData: false,
                                    contentType: false,
                                    data: formData,
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
                                            $('#btn-closemodalberkas').trigger('click')
                                            $('#example2').DataTable().ajax.reload();
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
                                            // $('#submit-daftar').html('<i class="fas fa-paper-plane"></i> Submit')
                                            // $('#submit-daftar').prop('disabled',false)
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

            function setuju()
            {
                $('.setuju-pindah').click(function (e) {
                    e.preventDefault();
                    let param = $(this).data('id')
                    Swal.fire({
                        title: "Information",
                        text: "Apakah Anda Yakin Akan Pindah Jalur Reguler ?",
                        icon: "question",
                        showConfirmButton: true,
                        showCancelButton: true,
                    }).then((result) => {
                        if(result.value){
                            $.ajax({
                                type: "GET",
                                url: '{!! url('BerkasBeasiswa/ApprovalPindahJalur') !!}'+'/'+param+'/'+'1',
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
                                    });
                                    return;
                                },
                                error: function(data) {
                                    $('#loading').hide()
                                    Swal.fire({
                                        title: 'Gagal Pindah Jalur !',
                                        text: 'Silahkan Hubungi Admin PMB TSU',
                                        icon: 'error'
                                    }).then((result) => {
                                        window.isEditing = false;
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

            function tidaksetuju()
            {
                $('.tidaksetuju-pindah').click(function (e) {
                    e.preventDefault();
                    let param = $(this).data('id')
                    Swal.fire({
                        title: "Information",
                        text: "Jika Anda tidak setuju, anda dianggap mengundurkan diri. Apakah Anda Yakin ?",
                        icon: "question",
                        showConfirmButton: true,
                        showCancelButton: true,
                    }).then((result) => {
                        if(result.value){
                            $.ajax({
                                type: "GET",
                                url: '{!! url('BerkasBeasiswa/ApprovalPindahJalur') !!}'+'/'+param+'/'+'-1',
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
                                    });
                                    return;
                                },
                                error: function(data) {
                                    $('#loading').hide()
                                    Swal.fire({
                                        title: 'Gagal Pindah Jalur !',
                                        text: 'Silahkan Hubungi Admin PMB TSU',
                                        icon: 'error'
                                    }).then((result) => {
                                        window.isEditing = false;
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

            $('#modal-upload').on('hidden.bs.modal', function () {
                // Reset semua input di dalam form
                $('#form-uploadberkas')[0].reset();
            });

        });

    </script>
@endsection
