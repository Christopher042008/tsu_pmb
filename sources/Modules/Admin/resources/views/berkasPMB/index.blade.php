@extends('admin::template/admin/header')
@section('title', $title)
@section('link_href')

@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{$menu}}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
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
                            <h5 class="m-0">
                                {{$menu}}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row" style="display: none;">
                                <div class="col-lg-2">
                                    <select class="form-control select2" id="kategori" name="kategori" required>
                                        <option value="" selected disabled>-- Pilih Jenis Berkas --</option>
                                        <option value="{{encrypt('pendaftaran')}}">Berkas Khusus</option>
                                        <option value="{{encrypt('ukt')}}">Berkas Umum</option>
                                    </select>
                                </div>
                                <!-- /.col-lg-6 -->
                                <div class="btn-group">
                                    <button type="button" id="btn-showpembayaran" class="btn btn-primary btn-sm" style="margin-top: 6px;margin-right: 10px;">Show Data</button>
                                    <button type="button" class="btn btn-success btn-sm" style="margin-top: 6px;display:none;">Export Excel</button>
                                    <!-- /input-group -->
                                </div>
                                <!-- /.col-lg-6 -->
                            </div>
                            <code>* Klik Nama Berkas untuk melihat berkas</code>
                            <div class="table-responsive" style="margin-top: 10px;">
                                <table id="example2" class="table table-bordered table-hover" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>No Registrasi</th>
                                            <th>Batch Daftar</th>
                                            <th>Jalur Daftar</th>
                                            <th>Kategori Beasiswa</th>
                                            <th>Berkas Khusus</th>
                                            <th>Keterangan</th>
                                            <th>Validator</th>
                                            <th>Pindah Jalur</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col-md-6 -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->

    <div class="modal fade" id="modal-approval">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="judul-modal">Approval Berkas</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <code>* Jika Masih Ada Revisi Berkas Silahkan Input Pada Keterangan, dan jangan ubah status berkas</code><br>
                    <code>* Berkas yang Sudah Diverifikasi Tidak Dapat diubah lagi</code>
                    <form id="form-approval" action="#" method="POST">
                        <input type="hidden" name="iddaftar" id="iddaftar" value="">
                        <label for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Tambahkan Keterangan (Jika Ada Revisi)"></textarea>
                        <label for="status">Status</label>
                        <select class="form-control select2" id="status" name="status">
                            <option value="0" selected disabled>-- Pilih Status Berkas --</option>
                            <option value="1">OK</option>
                            <option value="-1">Ditolak</option>
                        </select><br>
                        <input type="checkbox" id="pindahjalur" name="pindahjalur" value="0">
                        <label for="pindahjalur">Arahkan Ke jalur Reguler</label>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" id="btn-closemodalberkas" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" id="btn-saveapproval" class="btn btn-success">Simpan</button>
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
                tabelBerkasKhusus()
                SubmitApprovalBerkas()
                checkPindahJalur()
            }

            function tabelBerkasKhusus()
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
                        url: '{!! route('admin.berkaspmb.tabel') !!}',
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
                            data: 'bkskhusus'
                        },
                        {
                            data: 'keterangan'
                        },
                        {
                            data: 'validator'
                        },
                        {
                            data: 'pindahjalur'
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
                        approvalBerkas()
                    }
                });

                otable.on('draw', function(event) {
                    $('[data-toggle="tooltip"]').tooltip({trigger: "hover"});
                    $('[data-tooltip="tooltip"]').tooltip({trigger: "hover"});

                });
            }

            function approvalBerkas()
            {
                $('.btn_approval').click(function (e) {
                    e.preventDefault();
                    let params = $(this).data('id');
                    $('#iddaftar').val(params)
                    $('#modal-approval').modal('show')
                });
            }

            function SubmitApprovalBerkas()
            {
                $('#btn-saveapproval').click(function (e) {
                    e.preventDefault();
                    let status = $('#status').val()
                    // if(status==''||status==null){
                        // notifalert('Information','Status harus diisi!','warning')
                    // }else{
                        let formData = new FormData();
                        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                        formData.append('kodedaftar', $('#iddaftar').val());
                        formData.append('keterangan', $('#keterangan').val());
                        formData.append('status', status);
                        formData.append('pindahjalur', $('#pindahjalur').val());
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
                                    url: "{{route('admin.berkaspmb.save')}}",
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
                    // }
                });
            }

            function checkPindahJalur()
            {
                $('#pindahjalur').on('change', function () {
                    let aa = $(this).is(':checked')
                    if(aa){
                        $(this).val(1)
                    }else{
                        $(this).val(0)
                    }
                });
            }

            $('#modal-approval').on('hidden.bs.modal', function () {
                $('#iddaftar').val('')
                $('#keterangan').val('')
                $('#status').val(0).trigger('change');
                $('#pindahjalur').val(0)
                $('#pindahjalur').prop('checked',false)
            });
        });
    </script>
@endsection
