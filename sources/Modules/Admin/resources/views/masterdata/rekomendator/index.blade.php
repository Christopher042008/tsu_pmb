@extends('admin::template/admin/header')
@section('title', $title)
@section('link_href')
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $menu }}</h1>
                </div><div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Master Data</li>
                        <li class="breadcrumb-item active">{{ $menu }}</li>
                    </ol>
                </div></div></div></div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="m-0">{{ $menu }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <form id="form-rekomendator" method="POST" action="#">
                                        @csrf
                                        <input type="hidden" id="IdRekomendator" name="IdRekomendator" value="">
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="kode_rekomendator">Kode Rekomendator</label>
                                                    <input type="text" id="kode_rekomendator" name="kode_rekomendator" placeholder="Auto Generated" class="form-control" readonly>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="nama_rekomendator">Nama Rekomendator</label>
                                                    <input type="text" id="nama_rekomendator" name="nama_rekomendator" placeholder="Nama Lengkap" class="form-control" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="kategori">Kategori</label>
                                                    <input type="text" id="kategori" name="kategori" placeholder="Contoh: Guru, Alumni, dll" class="form-control" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="pekerjaan">Pekerjaan</label>
                                                    <input type="text" id="pekerjaan" name="pekerjaan" placeholder="Pekerjaan" class="form-control">
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="alamat">Alamat</label>
                                                    <textarea id="alamat" name="alamat" rows="3" class="form-control" placeholder="Alamat Lengkap"></textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="no_hp">No. Handphone</label>
                                                    <input type="text" id="no_hp" name="no_hp" placeholder="08xxxxxxxxxx" class="form-control">
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="email">Email</label>
                                                    <input type="email" id="email" name="email" placeholder="Email" class="form-control">
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="nama_bank">Nama Bank</label>
                                                    <input type="text" id="nama_bank" name="nama_bank" placeholder="Contoh: BCA, BRI" class="form-control">
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="no_rekening">No. Rekening</label>
                                                    <input type="text" id="no_rekening" name="no_rekening" placeholder="Nomor Rekening" class="form-control">
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="atasnama_rekening">Atas Nama Rekening</label>
                                                    <input type="text" id="atasnama_rekening" name="atasnama_rekening" placeholder="Atas Nama" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="status">Status</label>
                                            <select class="form-control" id="status" name="status" required>
                                                <option value="" selected disabled>-- Pilih Status --</option>
                                                <option value="1">Aktif</option>
                                                <option value="0">Non Aktif</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <button type="button" id="submit-rekomendator" class="btn btn-success float-right" style="margin-left:10px;"> <i class="fas fa-paper-plane"></i> Submit</button>
                                            <button id="btn-reset" class="btn btn-warning float-right">Reset</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive" style="margin-top: 20px;">
                                <table id="tabel-rekomendator" class="table table-bordered table-hover" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode</th>
                                            <th>Nama Rekomendator</th>
                                            <th>Kategori</th>
                                            <th>Pekerjaan</th>
                                            <th>No. HP</th>
                                            <th>Bank</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div></div>
    @endsection

@section('script')
    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            loadEvent();

            function loadEvent() {
                tabelRekomendator();
                submitRekomendator();
                btn_reset();
            }

            function tabelRekomendator() {
                let otable = $('#tabel-rekomendator').DataTable({
                    destroy: true,
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: '{!! route('admin.Rekomendator.Tabel') !!}',
                        type: 'GET',
                    },
                    columns: [
                        { data: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'kode_rekomendator' },
                        { data: 'nama_rekomendator' },
                        { data: 'kategori' },
                        { data: 'pekerjaan' },
                        { data: 'no_hp' },
                        { data: 'nama_bank' },
                        { data: 'aktif' },
                        { data: 'action', orderable: false, searchable: false },
                    ],
                    drawCallback: function(settings) {
                        EditRekomendator();
                        DeleteRekomendator();
                    }
                });
            }

            function submitRekomendator() {
                $('#submit-rekomendator').click(function (e) {
                    e.preventDefault();
                    let validation = validationRekomendator();
                    if(validation != 'success'){
                        Swal.fire('Peringatan', validation, 'warning');
                    } else {
                        let dataku = $('#form-rekomendator').serialize();
                        Swal.fire({
                            title: "Konfirmasi",
                            text: "Apakah data sudah benar?",
                            icon: "question",
                            showCancelButton: true,
                        }).then((result) => {
                            if(result.isConfirmed){
                                $.ajax({
                                    type: "POST",
                                    url: "{{route('admin.Rekomendator.Store')}}",
                                    data: dataku,
                                    dataType: "JSON",
                                    beforeSend: function() {
                                        $('#submit-rekomendator').html('<i class="fas fa-hourglass"></i> Wait').prop('disabled', true);
                                    },
                                    success: function(data) {
                                        Swal.fire({
                                            title: data.title,
                                            text: data.message,
                                            icon: data.status
                                        }).then(() => {
                                            $('#submit-rekomendator').html('<i class="fas fa-paper-plane"></i> Submit').prop('disabled',false);
                                            $('#form-rekomendator').trigger('reset');
                                            $('#IdRekomendator').val('');
                                            $("#tabel-rekomendator").DataTable().ajax.reload();
                                        });
                                    },
                                    error: function() {
                                        Swal.fire('Error', 'Gagal menyimpan data', 'error');
                                        $('#submit-rekomendator').html('<i class="fas fa-paper-plane"></i> Submit').prop('disabled',false);
                                    }
                                });
                            }
                        });
                    }
                });
            }

            function validationRekomendator() {
                // let kode = $('#kode_rekomendator').val();
                let nama = $('#nama_rekomendator').val();
                let status = $('#status').val();

                // if (kode == null || kode == '') return 'Kode rekomendator tidak boleh kosong';
                if (nama == null || nama == '') return 'Nama rekomendator tidak boleh kosong';
                if (status == null || status == '') return 'Status tidak boleh kosong';
                return 'success';
            }

            function EditRekomendator() {
                $('.btn_edit').click(function (e) {
                    e.preventDefault();
                    let params = $(this).data('id');
                    $.ajax({
                        type: "GET",
                        url: '{!! url('admin/MasterData/Rekomendator/Edit') !!}/' + params,
                        dataType: "JSON",
                        success: function(data) {
                            if(data.hasil == 0){
                                Swal.fire('Informasi', 'Data tidak ditemukan', 'error');
                            } else {
                                $('#IdRekomendator').val(data.IdRekomendator);
                                $('#kode_rekomendator').val(data.data.kode_rekomendator);
                                $('#nama_rekomendator').val(data.data.nama_rekomendator);
                                $('#kategori').val(data.data.kategori);
                                $('#pekerjaan').val(data.data.pekerjaan);
                                $('#alamat').val(data.data.alamat);
                                $('#no_hp').val(data.data.no_hp);
                                $('#email').val(data.data.email);
                                $('#nama_bank').val(data.data.nama_bank);
                                $('#no_rekening').val(data.data.no_rekening);
                                $('#atasnama_rekening').val(data.data.atasnama_rekening);
                                $('#status').val(data.data.isactive).trigger('change');
                            }
                        }
                    });
                });
            }

            function DeleteRekomendator() {
                $('.btn_delete').click(function(e) {
                    e.preventDefault();
                    let params = $(this).data('id');
                    let status = $(this).data('status');

                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah anda yakin mengubah status data ini?',
                        icon: 'question',
                        showCancelButton: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                type: "GET",
                                url: '{!! url('admin/MasterData/Rekomendator/Status') !!}/' + params + '/' + status,
                                dataType: "JSON",
                                success: function(data) {
                                    Swal.fire(data.title, data.message, data.type).then(() => {
                                        $("#tabel-rekomendator").DataTable().ajax.reload();
                                    });
                                }
                            });
                        }
                    });
                });
            }

            function btn_reset() {
                $('#btn-reset').click(function (e) {
                    e.preventDefault();
                    $('#IdRekomendator').val('');
                    $('#form-rekomendator').trigger('reset');
                });
            }
        });
    </script>
@endsection