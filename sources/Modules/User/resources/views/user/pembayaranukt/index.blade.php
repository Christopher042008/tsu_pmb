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
                    {{-- @for ($i = 0; $i < 10; $i++) --}}
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="m-0">
                                {{$menu}}
                                <button type="button" id="pay-button" class="btn btn-success btn-sm float-right">Test Bayar</button>
                            </h5>
                        </div>
                        <div class="card-body">
                            <code>* Tekan </code> <i title="Bayar Sekarang" class="fas fa-money-bill text-green"></i> <code>  Untuk Pembayaran</code> <br>
                            <code>* Tekan </code> <i title="Detail Pendaftaran" class="fa fa-info-circle text-blue"></i> <code>  Untuk Melihat Detail Pendaftaran dan dan Pembayaran</code><br>
                            <code>* Jika Status sudah </code> <span class="badge bg-success">paid</span> <code> Maka Pembayaran Sudah Lunas</code><br>
                            <table id="example2" class="table table-bordered table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Nomer Registrasi</th>
                                        <th>Jenis Pembayaran</th>
                                        <th>Jurusan Diterima</th>
                                        <th>Nominal</th>
                                        <th>keterangan</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
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
                                <th colspan="4" class="text-center" style="background-color: rgb(0, 204, 255);">Data Pendaftaran Calon Mahasiswa Baru</th>
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
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            loadEvent()

            function loadEvent()
            {
                tabelPayment()
            }

            function tabelPayment()
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
                        url: '{!! route('PembayaranUKT.TabelBayar') !!}',
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
                            data: 'jenis'
                        },
                        {
                            data: 'jurusanditerima'
                        },
                        {
                            data: 'nominal'
                        },
                        {
                            data: 'keterangan'
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
                        payment()
                        ShowDetail()
                    }
                });

                otable.on('draw', function(event) {
                    $('[data-toggle="tooltip"]').tooltip({trigger: "hover"});
                    $('[data-tooltip="tooltip"]').tooltip({trigger: "hover"});

                });
            }

            function payment()
            {
                $('.btn_bayar').click(function (e) {
                    e.preventDefault();
                    let params = $(this).data('id')
                    Swal.fire({
                        title: "Information",
                        text: "Bayar Biaya Pendaftaran ??",
                        icon: "question",
                        showConfirmButton: true,
                        showCancelButton: true,
                    }).then((result) => {
                        if(result.value){
                            $.ajax({
                                type: "GET",
                                url: '{!! url('PembayaranUKT/PaymentUKT') !!}' + '/' + params,
                                dataType: "JSON",
                                beforeSend: function(response) {
                                    $('#loading').show()
                                },
                                success: function(data) {
                                    $('#loading').hide()
                                    if(data.status==false){
                                        notifalert('Information',data.message,'warning');
                                    }else{
                                        notifalert('Information',data.message,'success');
                                        $('#example2').DataTable().ajax.reload();
                                        // snap.pay(data.snap_token, {
                                        //     onSuccess: function(result) {
                                        //         notifalert('Information','Pembayaran sukses!','success');
                                        //         console.log(result);
                                        //     },
                                        //     onPending: function(result) {
                                        //         notifalert('Information','Menunggu pembayaran...','warning');
                                        //         console.log(result);
                                        //     },
                                        //     onError: function(result) {
                                        //         notifalert('Information','Pembayaran gagal!','error');
                                        //         console.log(result);
                                        //     },
                                        //     onClose: function() {
                                        //         notifalert('Information','Popup ditutup tanpa membayar','warning');
                                        //     }
                                        // });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    $('#loading').hide()
                                    Swal.fire({
                                        title: 'Gagal Bayar Pendaftaran',
                                        text: 'Periksa Data Anda !',
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

            function ShowDetail()
            {
                $('.btn_detail').click(function (e) {
                    e.preventDefault();
                    let param = $(this).data('daftarid')
                    $.ajax({
                        type: "GET",
                        url: '{!! url('PembayaranUKT/ShowPaymentUKT') !!}'+'/'+param,
                        dataType: "JSON",
                        beforeSend: function(response) {
                            $('#loading').show()
                            $('.o-detaildaftar').empty()
                        },
                        success: function(data) {
                            $('#loading').hide()
                            if(data.hasil==0){
                                notifalert('Information', 'Data Pembayaran Tidak Ditemukan','error')
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
                                let statusdaftar = data.daftar.bayar
                                let warna1 = 'warning'
                                let nama1 = 'waiting'
                                let warna2 = 'warning'
                                let nama2 = 'waiting'
                                for(i=0;i<statusdaftar.length;i++){
                                    if(statusdaftar[i].kategori=='pendaftaran'){
                                        nama1 = statusdaftar[i].status
                                        if(statusdaftar[i].status=='pending'){
                                            warna1 = 'warning'
                                        }else if(statusdaftar[i].status=='paid'){
                                            warna1 = 'success'
                                        }else{
                                            warna1 = 'danger'
                                        }
                                    }else{
                                        nama2 = statusdaftar[i].status
                                        if(statusdaftar[i].status=='pending'){
                                            warna2 = 'warning'
                                        }else if(statusdaftar[i].status=='paid'){
                                            warna2 = 'success'
                                        }else{
                                            warna2 = 'danger'
                                        }
                                    }
                                }
                                let showstatusdaftar = '<span class="badge bg-'+warna1+'">'+nama1+'</span>'
                                let showstatusUKT = '<span class="badge bg-'+warna2+'">'+nama2+'</span>'

                                $('#o-biayadaftar').html(biayadaftar+' '+showstatusdaftar)
                                $('#o-tgldaftar').html(data.daftar.tgl_daftar)
                                $('#o-waktukuliah').html(data.daftar.waktukuliah.waktu)
                                let statusUkt = data.daftar.jalur.status_ukt=='0' ? 'Gratis '+showstatusUKT : 'Bayar '+showstatusUKT;
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


                                $('#modal-detail').modal('show')
                            }
                        },
                        error: function(data) {
                            $('#loading').hide()
                            Swal.fire({
                                title: 'Gagal Show Data Pembayaran !',
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

            // let testingku = 0;
            // let intervalId = setInterval(function() {
            //     console.log(testingku++)

            //     if(testingku==5){
            //         clearInterval(intervalId); // Stop reload
            //         console.log("Interval dihentikan");
            //     }
            // }, 5000); // 5000 ms = 5 detik

            // $('#loading').show()
            $('#pay-button').click(function(){
                $.ajax({
                    url: "{{ route('paymentUKT.create') }}",
                    type: "POST",
                    data: {_token: "{{ csrf_token() }}"},
                    beforeSend: function(response) {
                        $('#loading').show()
                    },
                    success: function(data){
                        $('#loading').hide()
                        snap.pay(data.snap_token, {
                            onSuccess: function(result) {
                                notifalert('Information','Pembayaran sukses!','success');
                                console.log(result);
                            },
                            onPending: function(result) {
                                notifalert('Information','Menunggu pembayaran...','warning');
                                console.log(result);
                            },
                            onError: function(result) {
                                notifalert('Information','Pembayaran gagal!','error');
                                console.log(result);
                            },
                            onClose: function() {
                                notifalert('Information','Popup ditutup tanpa membayar','warning');
                            }
                        });
                    },
                    error: function(data) {
                        $('#loading').hide()
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Silahkan Hubungi Admin PMB TSU',
                            icon: 'error'
                        }).then((result) => {
                            // window.isEditing = false;
                        });
                        return;
                    }
                });
            });

        });

    </script>
@endsection
