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
                                {{$menu}} <span class="badge bg-success text-bold">{{$data->KodePendaftaran}}</span>
                                <span class="text-bold float-right" id="timer"></span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach($test as $key => $p)
                                @php
                                    $attempt = $attempts[$p->id] ?? null;
                                    $prevTest = $test->get($key-1) ?? null;
                                    $prevAttempt = $prevTest ? ($attempts[$prevTest->id] ?? null) : null;
                                @endphp
                                <div>
                                    <hr>
                                        <span class="text-bold">{{$key+1}}. {{$p->nama_test}}</span><br>
                                        <span class="text-bold">Durasi : {{$p->durasi_menit}}</span> <br>
                                        @if(!$attempt)
                                            <span class="text-bold">Status : Belum dikerjakan</span><br>
                                            @if($prevTest && (!$prevAttempt || $prevAttempt->status != 'finished'))
                                                <button class="btn btn-secondary btn-sm" disabled>Belum Bisa</button>
                                            @else
                                                <a href="#" class="btn btn-primary btn-sm">Mulai Test</a>
                                            @endif
                                        @elseif($attempt->status == 'in_progress')
                                            <span class="text-bold">Status : Sedang dikerjakan</span><br>
                                            <a href="#" class="btn btn-warning btn-sm">Lanjutkan</a>
                                        @elseif($attempt->status == 'finished')
                                            <span class="text-bold">Status : Selesai</span><br>
                                            <button class="btn btn-success btn-sm" disabled>Selesai</button>
                                        @endif
                                    <hr>
                                </div>
                            @endforeach
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
@endsection
@section('script')
    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

        });

    </script>
@endsection
