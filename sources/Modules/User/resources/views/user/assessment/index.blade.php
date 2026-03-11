@extends('user::layouts/halamanbelakang/header')
@section('title', $title)

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($active_attempt) ? 'Sesi Ujian Berlangsung' : 'Selamat Datang di PMB Universitas Tiga Serangkai' }}
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('Dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">{{ $menu }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    {{-- ========================================================== --}}
                    {{-- STATE 1: RUANG UJIAN (JIKA ADA SESI AKTIF)               --}}
                    {{-- ========================================================== --}}
                    @if (isset($active_attempt))
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h5 class="m-0">
                                    {{ $test_info->nama_test }}
                                    <span class="text-danger text-bold float-right" id="countdown-timer">
                                        Sisa Waktu: <span id="time-display">Menghitung...</span>
                                    </span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <form id="form-ujian" action="{{ route('assessment.finish') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="attempt_id" id="attempt_id"
                                        value="{{ $active_attempt->id }}">

                                    {{-- 1. BAGIAN SOAL --}}
                                    @foreach ($questions as $index => $q)
                                        <div class="question-container" id="question-{{ $index }}"
                                            data-qid="{{ $q->id }}"
                                            style="display: {{ $index == 0 ? 'block' : 'none' }};">

                                            @if ($test_info->tipe_engine == 'disc' || $test_info->tipe_engine == '1')
                                                {{-- TAMPILAN KHUSUS DISC (Teks Pertanyaan Tidak Ditampilkan) --}}
                                                <div class="table-responsive mt-3">
                                                    <table class="table table-bordered text-center">
                                                        <thead class="text-white" style="background-color: #ff0000;">
                                                            {{-- Warna Header Merah seperti referensi --}}
                                                            <tr>
                                                                <th width="5%" class="align-middle"
                                                                    style="background-color: #d3d3d3; color: black;">No.
                                                                </th>
                                                                <th width="5%" class="align-middle"
                                                                    style="background-color: #ffff00; color: black;">P</th>
                                                                <th width="5%" class="align-middle"
                                                                    style="background-color: #90ee90; color: black;">K</th>
                                                                <th class="text-left align-middle">Gambaran Diri</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {{-- Hitung jumlah opsi untuk menentukan nilai rowspan --}}
                                                            @php
                                                                $jmlOpsi = count($q->options);
                                                            @endphp

                                                            @foreach ($q->options as $idx => $opt)
                                                                <tr>
                                                                    {{-- Tampilkan TD Nomor hanya pada iterasi opsi pertama --}}
                                                                    @if ($idx == 0)
                                                                        <td rowspan="{{ $jmlOpsi }}"
                                                                            class="align-middle font-weight-bold"
                                                                            style="font-size: 1.2rem; background-color: #d3d3d3;">
                                                                            {{-- Mengambil nomor urut soal dari parent loop --}}
                                                                            {{ $loop->parent->iteration ?? $index + 1 }}
                                                                        </td>
                                                                    @endif

                                                                    <td class="align-middle"
                                                                        style="background-color: #ffff00;">
                                                                        <input type="radio" style="transform: scale(1.5);"
                                                                            class="answer-option-disc"
                                                                            name="p_{{ $q->id }}"
                                                                            value="{{ $opt->id }}"
                                                                            data-question="{{ $q->id }}"
                                                                            data-type="most"
                                                                            {{ isset($saved_answers[$q->id]) && $saved_answers[$q->id]['most_option_id'] == $opt->id ? 'checked' : '' }}>
                                                                    </td>
                                                                    <td class="align-middle"
                                                                        style="background-color: #90ee90;">
                                                                        <input type="radio" style="transform: scale(1.5);"
                                                                            class="answer-option-disc"
                                                                            name="k_{{ $q->id }}"
                                                                            value="{{ $opt->id }}"
                                                                            data-question="{{ $q->id }}"
                                                                            data-type="least"
                                                                            {{ isset($saved_answers[$q->id]) && $saved_answers[$q->id]['least_option_id'] == $opt->id ? 'checked' : '' }}>
                                                                    </td>
                                                                    <td class="text-left align-middle">{{ $opt->label }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                {{-- TAMPILAN TPA / PILIHAN GANDA BIASA --}}
                                                {{-- TEKS PERTANYAAN PINDAH KE SINI AGAR HANYA MUNCUL DI NON-DISC --}}
                                                <p class="text-bold">{{ $index + 1 }}. {{ $q->pertanyaan }}</p>

                                                @foreach ($q->options as $opt)
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input answer-option" type="radio"
                                                            name="q_{{ $q->id }}" id="opt_{{ $opt->id }}"
                                                            value="{{ $opt->id }}"
                                                            data-question="{{ $q->id }}"
                                                            {{ isset($saved_answers[$q->id]) && $saved_answers[$q->id]['option_id'] == $opt->id ? 'checked' : '' }}>

                                                        <label class="form-check-label" for="opt_{{ $opt->id }}"
                                                            style="cursor: pointer;">
                                                            @if (!empty($opt->kode))
                                                                <span class="text-bold mr-1">{{ $opt->kode }}.</span>
                                                            @endif
                                                            {{ $opt->label }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            @endif

                                            {{-- INDIKATOR TERSIMPAN & TOMBOL SELESAI --}}
                                            <div class="mt-4">
                                                <span class="text-success text-sm saved-indicator font-weight-bold"
                                                    id="indicator-{{ $q->id }}" style="display:none;">
                                                    <i class="fas fa-check"></i> Jawaban Tersimpan!
                                                </span>

                                                @if ($index == count($questions) - 1)
                                                    <br><br>
                                                    <button type="submit" class="btn btn-success" id="btn-submit-ujian">
                                                        <i class="fas fa-paper-plane"></i> Selesai Ujian
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </form>

                                {{-- 2. NAVIGASI SOAL (DIPINDAH KE LUAR LOOP SOAL) --}}
                                <div class="card mt-4 mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 font-weight-bold">Navigasi Soal</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap" style="gap: 8px;">
                                            @foreach ($questions as $index => $q)
                                                @php
                                                    $isAnswered = false;
                                                    if (isset($saved_answers[$q->id])) {
                                                        if (
                                                            $test_info->tipe_engine == 'disc' ||
                                                            $test_info->tipe_engine == '1'
                                                        ) {
                                                            if (
                                                                !empty($saved_answers[$q->id]['most_option_id']) &&
                                                                !empty($saved_answers[$q->id]['least_option_id'])
                                                            ) {
                                                                $isAnswered = true;
                                                            }
                                                        } else {
                                                            if (!empty($saved_answers[$q->id]['option_id'])) {
                                                                $isAnswered = true;
                                                            }
                                                        }
                                                    }
                                                @endphp

                                                <button type="button"
                                                    class="btn btn-sm btn-nav-question {{ $isAnswered ? 'btn-success' : 'btn-danger' }}"
                                                    data-target="{{ $index }}" id="nav-btn-{{ $q->id }}"
                                                    style="width: 40px; height: 40px; font-weight: bold;">
                                                    {{ $index + 1 }}
                                                </button>
                                            @endforeach
                                        </div>
                                        <div class="mt-3">
                                            <span class="badge bg-success p-2 mr-2"><i class="fas fa-check"></i> Sudah
                                                Dijawab</span>
                                            <span class="badge bg-danger p-2"><i class="fas fa-times"></i> Belum
                                                Dijawab</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>
                </div>

                {{-- ========================================================== --}}
                {{-- STATE 2: DASHBOARD UTAMA (JIKA TIDAK ADA SESI AKTIF)     --}}
                {{-- ========================================================== --}}
            @else
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h5 class="m-0">
                            {{ $menu }} <span
                                class="badge bg-success text-bold">{{ $data->KodePendaftaran ?? '' }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach ($test as $key => $p)
                            @php
                                $attempt = $attempts[$p->id] ?? null;
                                $prevTest = $test->get($key - 1) ?? null;
                                $prevAttempt = $prevTest ? $attempts[$prevTest->id] ?? null : null;
                            @endphp
                            <div>
                                <hr>
                                <span class="text-bold">{{ $key + 1 }}. {{ $p->nama_test }}</span><br>
                                <span class="text-bold">Durasi : {{ $p->durasi_menit }} Menit</span> <br>

                                @if (!$attempt)
                                    <span class="text-bold">Status : Belum dikerjakan</span><br>
                                    @if ($prevTest && (!$prevAttempt || $prevAttempt->status != 'finished'))
                                        <button class="btn btn-secondary btn-sm" disabled>Belum Bisa</button>
                                    @else
                                        <form action="{{ route('assessment.start') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="tipe_test_id" value="{{ $p->id }}">
                                            <button type="submit" class="btn btn-primary btn-sm">Mulai
                                                Test</button>
                                        </form>
                                    @endif
                                @elseif($attempt->status == 'in_progress')
                                    <span class="text-bold">Status : Sedang dikerjakan</span><br>
                                    <a href="{{ route('assessment.index') }}"
                                        class="btn btn-warning btn-sm">Lanjutkan</a>
                                @elseif($attempt->status == 'finished')
                                    <span class="text-bold">Status : Selesai</span><br>
                                    <button class="btn btn-success btn-sm" disabled>Selesai</button>
                                @endif
                                <hr>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            // ==========================================
            // 1. SETUP & VARIABEL GLOBAL
            // ==========================================
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            @if (isset($active_attempt))
                const engineType = "{{ $test_info->tipe_engine }}";
                const totalQuestions = {{ count($questions) }};
                let isTimeOut = false;
                let isSubmitting = false;

                // AMAN DARI MANIPULASI JAM LAPTOP: 
                // Kita hitung sisa detik menggunakan PHP (Carbon) berdasarkan waktu server saat halaman dimuat
                let sisaDetik =
                    {{ \Carbon\Carbon::parse($selesai_at)->isPast() ? 0 : \Carbon\Carbon::now()->diffInSeconds(\Carbon\Carbon::parse($selesai_at)) }};

                // ==========================================
                // 2. INISIALISASI SEMUA FUNGSI
                // ==========================================
                initPreventExit();
                initNavigation();
                initAutoSaveUmum();
                initAutoSaveDisc();
                initCountdownTimer();
                initSubmitValidation();
            @endif


            // ==========================================
            // 3. DEFINISI FUNGSI-FUNGSI
            // ==========================================

            // --- Fungsi Proteksi Keluar Halaman ---
            function initPreventExit() {
                window.addEventListener('beforeunload', function(e) {
                    if (!isSubmitting) {
                        e.preventDefault();
                        e.returnValue =
                            'Ujian sedang berlangsung. Jika Anda keluar, waktu akan terus berjalan.';
                    }
                });
            }

            // --- Fungsi Navigasi Soal ---
            function initNavigation() {
                $('.btn-next, .btn-prev, .btn-nav-question').click(function() {
                    let targetId = $(this).data('target');
                    $('.question-container').hide();
                    $('#question-' + targetId).show();
                });
            }

            // --- Fungsi Auto Save TPA/Umum ---
            function initAutoSaveUmum() {
                $('.answer-option').change(function() {
                    let questionId = $(this).data('question');
                    let optionId = $(this).val();
                    let indicator = $('#indicator-' + questionId);

                    let currentContainer = $(this).closest('.question-container');
                    let currentIndex = parseInt(currentContainer.attr('id').replace('question-', ''));
                    let nextIndex = currentIndex + 1;

                    showSavingIndicator(indicator, questionId);

                    $.ajax({
                        url: "{{ route('assessment.save_answer') }}",
                        type: "POST",
                        data: {
                            attempt_id: $('#attempt_id').val(),
                            question_id: questionId,
                            option_id: optionId
                        },
                        success: function() {
                            showSuccessIndicator(indicator);
                            autoNextQuestion(nextIndex);
                        },
                        error: function() {
                            showErrorIndicator(indicator);
                        }
                    });
                });
            }

            // --- Fungsi Auto Save DISC ---
            function initAutoSaveDisc() {
                $('.answer-option-disc').change(function() {
                    let questionId = $(this).data('question');
                    let type = $(this).data('type');
                    let val = $(this).val();
                    let indicator = $('#indicator-' + questionId);

                    let currentContainer = $(this).closest('.question-container');
                    let currentIndex = parseInt(currentContainer.attr('id').replace('question-', ''));
                    let nextIndex = currentIndex + 1;

                    // Cegah baris yang sama dipilih untuk P dan K
                    if (type === 'most') {
                        $(`input[name="k_${questionId}"][value="${val}"]`).prop('checked', false);
                    } else {
                        $(`input[name="p_${questionId}"][value="${val}"]`).prop('checked', false);
                    }

                    let mostVal = $(`input[name="p_${questionId}"]:checked`).val();
                    let leastVal = $(`input[name="k_${questionId}"]:checked`).val();

                    let payload = {
                        attempt_id: $('#attempt_id').val(),
                        question_id: questionId
                    };
                    if (mostVal !== undefined) payload.most_option_id = mostVal;
                    if (leastVal !== undefined) payload.least_option_id = leastVal;

                    // Update warna grid navigasi
                    if (mostVal !== undefined && leastVal !== undefined) {
                        $('#nav-btn-' + questionId).removeClass('btn-danger').addClass('btn-success');
                        autoNextQuestion(nextIndex);
                    } else {
                        $('#nav-btn-' + questionId).removeClass('btn-success').addClass('btn-danger');
                    }

                    showSavingIndicator(indicator, questionId);

                    $.ajax({
                        url: "{{ route('assessment.save_answer') }}",
                        type: "POST",
                        data: payload,
                        success: function() {
                            showSuccessIndicator(indicator);
                        },
                        error: function() {
                            showErrorIndicator(indicator);
                        }
                    });
                });
            }

            // --- Fungsi Keamanan Waktu & Timer Layar ---
            function initCountdownTimer() {
                if (sisaDetik <= 0) {
                    prosesWaktuHabis();
                    return;
                }

                // Jalankan timer pertama kali
                updateTampilanWaktu();

                let timerInterval = setInterval(function() {
                    sisaDetik--; // Kurangi murni 1 detik, aman dari perubahan jam laptop

                    if (sisaDetik <= 0) {
                        clearInterval(timerInterval);
                        prosesWaktuHabis();
                    } else {
                        updateTampilanWaktu();
                    }
                }, 1000);
            }

            // --- Fungsi Update Teks Timer ---
            function updateTampilanWaktu() {
                let hours = Math.floor(sisaDetik / 3600);
                let minutes = Math.floor((sisaDetik % 3600) / 60);
                let seconds = Math.floor(sisaDetik % 60);

                // Tambahkan angka 0 di depan jika angkanya kurang dari 10
                let strMinutes = minutes < 10 ? "0" + minutes : minutes;
                let strSeconds = seconds < 10 ? "0" + seconds : seconds;

                let displayString = "";
                if (hours > 0) {
                    let strHours = hours < 10 ? "0" + hours : hours;
                    displayString += strHours + "j ";
                }

                displayString += strMinutes + "m " + strSeconds + "d";

                $('#time-display').text(displayString);
            }

            // --- Fungsi Aksi Waktu Habis ---
            function prosesWaktuHabis() {
                $('#time-display').text("Waktu Habis!");
                isTimeOut = true;
                isSubmitting = true; // Matikan proteksi window close

                Swal.fire({
                    title: 'Waktu Habis!',
                    text: 'Sistem sedang menyimpan jawaban Anda secara otomatis.',
                    icon: 'info',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                document.getElementById('form-ujian').submit();
            }

            // --- Fungsi Validasi Tombol Selesai Ujian ---
            function initSubmitValidation() {
                $('#form-ujian').on('submit', function(e) {
                    if (isTimeOut) return true; // Biarkan jika auto-submit karena waktu habis

                    e.preventDefault();
                    let allAnswered = checkAllAnswered();

                    if (!allAnswered) {
                        tampilkanPeringatanBelumLengkap();
                        return false;
                    }

                    tampilkanKonfirmasiSelesai();
                });
            }


            // ==========================================
            // 4. HELPER FUNCTIONS (Fungsi Bantuan Kecil)
            // ==========================================

            function showSavingIndicator(indicator, questionId) {
                indicator.text('Menyimpan...').removeClass('text-success text-danger').addClass('text-warning')
                    .show();
                if (engineType !== 'disc' && engineType !== '1') {
                    $('#nav-btn-' + questionId).removeClass('btn-danger').addClass('btn-success');
                }
            }

            function showSuccessIndicator(indicator) {
                indicator.html('<i class="fas fa-check"></i> Tersimpan').removeClass('text-warning').addClass(
                    'text-success');
                setTimeout(() => indicator.fadeOut(), 2000);
            }

            function showErrorIndicator(indicator) {
                indicator.html('<i class="fas fa-times"></i> Gagal! Cek koneksi').removeClass('text-warning')
                    .addClass('text-danger');
            }

            function autoNextQuestion(nextIndex) {
                if (nextIndex < totalQuestions) {
                    setTimeout(function() {
                        $('.question-container').hide();
                        $('#question-' + nextIndex).show();
                    }, 500);
                }
            }

            function checkAllAnswered() {
                let answered = true;
                if (engineType === 'disc' || engineType === '1') {
                    $('.question-container').each(function() {
                        let qId = $(this).data('qid');
                        if ($(`input[name="p_${qId}"]:checked`).length === 0 || $(
                                `input[name="k_${qId}"]:checked`).length === 0) {
                            answered = false;
                        }
                    });
                } else {
                    if ($('.answer-option:checked').length < totalQuestions) answered = false;
                }
                return answered;
            }

            function tampilkanPeringatanBelumLengkap() {
                Swal.fire({
                    title: 'Belum Selesai!',
                    text: 'Masih ada soal yang belum Anda jawab. Anda akan diarahkan ke soal tersebut.',
                    icon: 'warning',
                    confirmButtonText: 'Tunjukkan Soal'
                }).then(() => {
                    $('.btn-nav-question.btn-danger').first().click();
                    $('html, body').animate({
                        scrollTop: $("#form-ujian").offset().top - 50
                    }, 500);
                });
            }

            function tampilkanKonfirmasiSelesai() {
                Swal.fire({
                    title: 'Selesai Ujian?',
                    text: "Apakah Anda yakin semua jawaban sudah benar?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Selesai!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        isSubmitting = true;
                        Swal.fire({
                            title: 'Menyimpan Sesi...',
                            html: 'Mohon tunggu sebentar.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        document.getElementById('form-ujian').submit();
                    }
                });
            }

        });
    </script>
@endsection
