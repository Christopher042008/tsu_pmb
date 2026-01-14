<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_Batch;
use App\Models\MasterData\Master_Beasiswa;
use App\Models\MasterData\Master_JenisBerkas;
use App\Models\MasterData\Master_JenisPendaftaran;
use App\Models\MasterData\Master_JurusanKuliah;
use App\Models\MasterData\Master_JurusanSekolah;
use App\Models\MasterData\Master_TarifUKT;
use App\Models\MasterData\Master_WaktuKuliah;
use App\Models\Transaksi;
use App\Models\TransaksiHistory;
use App\Models\User\Biodata;
use App\Models\User\Maba;
use App\Models\User\Pendaftaran;
use Symfony\Component\HttpFoundation\Response;
use Session, Crypt, DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $now = date('Y-m-d');
        $batch = Master_Batch::where('isactive',1)->whereRaw('? BETWEEN tglmulai and tglselesai',[$now])->get();
        $sekolah = Master_JurusanSekolah::where('isactive',1)->selectRaw('id,sekolah,jurusan_sekolah')->get();
        $waktu = Master_WaktuKuliah::where('isactive',1)->selectRaw('id,waktu')->get();
        $bks = Master_JenisBerkas::with('berkas')->get();
        $data = array(
            'title' => 'Pendaftaran',
            'menu' => 'Pendaftaran',
            'batch' => $batch,
            'sekolah' => $sekolah,
            'waktu' => $waktu
        );
        return view('user::user.pendaftaran.index',$data);
    }

    public function tabelPendaftaran()
    {
        $bioId = decrypt(session('user')->_biodata);

        $data = Pendaftaran::where('biodata_id',$bioId)->with(['biodata','batch','jalur','jenisbeasiswa','jurusansekolah',
        'prodi1'=>function($q){
            $q->with('jenjang');
        },
        'prodi2'=>function($q){
            $q->with('jenjang');
        },
        'waktukuliah'])->get();
        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('nama', function ($d) {
            return $d->biodata->nama;
        })
        ->addColumn('noreg', function ($d) {
            return $d->KodePendaftaran;
        })
        ->addColumn('batch', function ($d) {
            return $d->batch->nama_batch;
        })
        ->addColumn('jalur', function ($d) {
            $nama = $d->jalur->jenis_pendaftaran;
            return $nama;
        })
        ->addColumn('prodi1', function ($d) {
            $nama = $d->prodi1->jenjang->jenjang.'-'.$d->prodi1->jurusan;
            return $nama;
        })
        ->addColumn('prodi2', function ($d) {
            $nama = $d->prodi2->jenjang->jenjang.'-'.$d->prodi2->jurusan;
            return $nama;
        })
        ->addColumn('status', function ($d) {
            $role = '-';
            $warna = '';
            if($d->isactive==1){
                if($d->konfirm_pendaftaran==1){
                    $role = 'Sudah Konfirmasi';
                    $warna = 'success';
                }else{
                    $role = 'Belum Konfirmasi';
                    $warna = 'warning';
                }
            }else{
                $role = 'Mengundurkan Diri';
                $warna = 'danger';
            }
            $show = '<span class="badge bg-'.$warna.'">'.$role.'</span>';
            return $show;
        })
        ->addColumn('action', function ($d) {
            $id = encrypt($d->KodePendaftaran);

            $aktif = '';
            $detail = '';
            $konfirm = '';
            $edit = '';
            if($d->isactive==1){
                // if($d->konfirm_pendaftaran==0){
                if($d->current_step==1){
                    $aktif = '<a href="#" class="btn_delete" data-id="'.$id.'"><i title="Hapus Pendaftaran" class="fa fa-trash text-red"></i></a>';
                    $konfirm = '<a href="#" data-id="'.$id.'" class="btn_konfirm"><i title="Konfirmasi Pendaftaran" class="fas fa-check-circle text-green"></i></a>';
                    $edit   = '<a href="#" data-id="'.$id.'" class="btn_edit"><i title="Edit" class="fa fa-edit text-orange"></i></a>';
                }
            }
            // else{
            //     $aktif  = '<a href="#" class="btn_delete" data-id="'.$id.'" data-status="'.encrypt('1').'"><i title="Aktifkan" class="fas fa-check-circle text-green"></i></a>';
            // }
            $detail = '<a href="#" data-id="'.$id.'" class="btn_detail"><i title="Detail" class="fa fa-info-circle"></i></a>';

            return $detail.' '.$edit.' '.$aktif.' '.$konfirm;
        })
        ->rawColumns(['action','status'])
        ->make(true);
    }

    public function showJalur($params)
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");

        $id = $params;
        // dd($id);
        $now = date('Y-m-d');
        $cek1 = Master_Batch::where('id',$id)->where('isactive',1)->whereRaw('? BETWEEN tglmulai and tglselesai',[$now])->first();
        if($cek1){
            $jalur = [];
            $cek3 = Master_JenisPendaftaran::where('isactive',1)->get();
            foreach($cek3 as $q){
                $cek2 = Master_TarifUKT::where('idbatch',$id)->where('idjalur',$q->id)->where('isactive',1)->exists();
                if($cek2){
                    $jalur[] = $q;
                }
            }
            // dd($jalur);
            if(count($jalur)>0){
                $data['hasil'] = 1;
                $data['jalur'] = $jalur;
            }else{
                $data['hasil'] = -1;
                $data['jalur'] = null;
            }
        }else{
            $data['hasil'] = 0;
            $data['jalur'] = null;
        }

        return response()->json($data, Response::HTTP_OK);
    }

    public function showBeasiswa($params)
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");

        $cek1 = Master_Beasiswa::where('idjalur',$params)->selectRaw('id,jenis_beasiswa')->get();
        $data['bea'] = $cek1;
        return response()->json($data, Response::HTTP_OK);
    }

    public function detailbeasiswa($params)
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");

        $cek1 = Master_Beasiswa::where('id',$params)->with('tingkat')->selectRaw('id,idtingkat,juara_ke')->first();
        $data['bea'] = $cek1;
        return response()->json($data, Response::HTTP_OK);
    }

    public function showProdi($batch,$jalur,$jurusansekolah)
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");

        $idbatch = $batch;
        $idjalur = $jalur;
        $idjurusansekolah = $jurusansekolah;

        // $cek1 = null;
        // if($idjurusansekolah==1){
        //     $cek1 = Master_JurusanKuliah::selectRaw('id,KodeJurusan,idfakultas,idjenjang,idjurusansekolah,jurusan')->whereIn('idjurusansekolah',[1])->with('jenjang')->where('isactive',1)->get();
        // }else{
        //     $cek1 = Master_JurusanKuliah::selectRaw('id,KodeJurusan,idfakultas,idjenjang,idjurusansekolah,jurusan')->whereNotIn('idjurusansekolah',[1])->with('jenjang')->where('isactive',1)->get();
        // }
	    $cek1 = Master_JurusanKuliah::selectRaw('id,KodeJurusan,idfakultas,idjenjang,idjurusansekolah,jurusan')->with('jenjang')->where('isactive',1)->get();

        $prodi = [];
        foreach($cek1 as $q){
            $cek2 = Master_TarifUKT::where('idbatch',$idbatch)->where('idjalur',$idjalur)->where('idjurusan',$q->id)->exists();
            if($cek2){
                $prodi[] = $q;
            }
        }
        if(count($prodi)>0){
            $data['hasil'] = 1;
            $data['jurusan'] = $prodi;
        }else{
            $data['hasil'] = 0;
            $data['jurusan'] = $prodi;
        }
        return response()->json($data, Response::HTTP_OK);

    }

    public function StoreDaftar(Request $post)
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
        // dd($post);
        if($post->IdPendaftaran==null){
            $data = $this->save($post);
        }else{
            $data = $this->update($post);
        }
        return response()->json($data, Response::HTTP_OK);
    }

    public function showDaftar($params)
    {
        $id = decrypt($params);
        $bioId = decrypt(session('user')->_biodata);

        $cek1 = Pendaftaran::where('KodePendaftaran',$id)->where('biodata_id',$bioId)->where('isactive',1)->with(['biodata','batch',
        'jalur'=>function($q){
            $q->with(['berkasumum'=>function($q){
                    $q->with('berkas');
                },

            'berkaskhusus'=>function($q){
                    $q->with('berkas');
                }
            ]);
        },
        'jenisbeasiswa'=>function($q){
            $q->with('tingkat');
        },
        'jurusansekolah',
        'prodi1'=>function($q){
            $q->with('jenjang');
        },
        'prodi2'=>function($q){
            $q->with('jenjang');
        },
        'waktukuliah'])->first();
        $prodi1 = Master_TarifUKT::where('idbatch',$cek1->batch_daftar)->where('idjalur',$cek1->jalur_daftar)->where('idjurusan',$cek1->prodi1->id)->where('isactive',1)->first();
        $prodi2 = Master_TarifUKT::where('idbatch',$cek1->batch_daftar)->where('idjalur',$cek1->jalur_daftar)->where('idjurusan',$cek1->prodi2->id)->where('isactive',1)->first();
        if($cek1){
            $data['hasil'] = 1;
            $data['daftar'] = $cek1;
            $data['IdDaftar'] = $params;
            $data['ukt1'] = $prodi1;
            $data['ukt2'] = $prodi2;
        }else{
            $data['hasil'] = 0;
            $data['daftar'] = $cek1;
            $data['IdDaftar'] = null;
            $data['ukt1'] = $prodi1;
            $data['ukt2'] = $prodi2;
        }
        return response()->json($data, Response::HTTP_OK);
    }

    public function save($post)
    {
        $batch = $post->batch;
        $jalur = $post->jalur;
        $beasiswa = $post->beasiswa;
        $tahun = $post->tahunlulus;
        $jurusansekolah = $post->jurusansekolah;
        $prodi1 = $post->prodi1;
        $prodi2 = $post->prodi2;
        $waktukuliah = $post->waktukuliah;
        $bioId = decrypt(session('user')->_biodata);

        $cek = Pendaftaran::where('biodata_id',$bioId)->where('batch_daftar',$batch)->exists();
        if($cek){
            $data['title'] = 'Information';
            $data['message'] = 'Anda Sudah Daftar Pada Batch ini ! Silahkan Daftar pada Batch Berikutnya';
            $data['status'] = 'warning';
        }else{
            DB::beginTransaction();
            $cekbiayadaftar = Master_JenisPendaftaran::where('id',$jalur)->where('isactive',1)->first();
            $tahun = date('Y');
            $kd1 = '0'.$batch;
            $kd2 = '0'.$jalur;

            $count = Pendaftaran::whereYear('tgl_daftar', $tahun)->where('isactive',1)->count();
            $urut = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

            $kode = $tahun.$kd1.$kd2.$urut;
            // dd($kode);
            $data_daftar = array(
                'KodePendaftaran'   => $kode,
                'biodata_id'        => $bioId,
                'bayar_pendaftaran' => $cekbiayadaftar->biaya_pendaftaran==1 ? '0' : '-1',
                'tgl_daftar'        => date('Y-m-d H:i:s'),
                'batch_daftar'      => $batch,
                'jalur_daftar'      => $jalur,
                'beasiswa'          => $beasiswa,
                'tahun_lulus'       => $tahun,
                'jurusan_sekolah'   => $jurusansekolah,
                'pilihan1'          => $prodi1,
                'pilihan2'          => $prodi2,
                'waktu_kuliah'      => $waktukuliah,
                'bayar_ukt'         => $cekbiayadaftar->status_ukt==1 ? '0' : '-1',
                'created_at'        => date('Y-m-d H:i:s'),
            );
            $pendaftaran = Pendaftaran::insert($data_daftar);

            if($pendaftaran){
                DB::commit();
                $data['title'] = 'Berhasil';
                $data['message'] = 'Data Pendaftaran Berhasil disimpan ! Silahkan konfirmasi pendaftaran anda';
                $data['status'] = 'success';
            }else{
                DB::rollback();
                $data['title'] = 'Gagal';
                $data['message'] = 'Data Pendaftaran Gagal disimpan !';
                $data['status'] = 'error';
            }
        }

        return $data;

    }

    public function update($post)
    {
        $kdDaftar = decrypt($post->IdPendaftaran);
        $batch = $post->batch;
        $jalur = $post->jalur;
        $beasiswa = $post->beasiswa;
        $tahun = $post->tahunlulus;
        $jurusansekolah = $post->jurusansekolah;
        $prodi1 = $post->prodi1;
        $prodi2 = $post->prodi2;
        $waktukuliah = $post->waktukuliah;
        $bioId = decrypt(session('user')->_biodata);

        DB::beginTransaction();
        $cekbiayadaftar = Master_JenisPendaftaran::where('id',$jalur)->where('isactive',1)->first();
        // dd($kode);
        $data_daftar = array(
            'bayar_pendaftaran' => $cekbiayadaftar->biaya_pendaftaran==1 ? '0' : '-1',
            'batch_daftar'      => $batch,
            'jalur_daftar'      => $jalur,
            'beasiswa'          => $beasiswa,
            'tahun_lulus'       => $tahun,
            'jurusan_sekolah'   => $jurusansekolah,
            'pilihan1'          => $prodi1,
            'pilihan2'          => $prodi2,
            'waktu_kuliah'      => $waktukuliah,
            'bayar_ukt'         => $cekbiayadaftar->status_ukt==1 ? '0' : '-1',
            'updated_at'        => date('Y-m-d H:i:s'),
        );
        $updt = Pendaftaran::where('KodePendaftaran',$kdDaftar)->where('biodata_id',$bioId)->where('isactive',1)->update($data_daftar);
        if($updt){
            DB::commit();
            $data['title'] = 'Berhasil';
            $data['message'] = 'Data Pendaftaran Berhasil diperbarui ! Silahkan konfirmasi pendaftaran anda';
            $data['status'] = 'success';
        }else{
            DB::rollback();
            $data['title'] = 'Gagal';
            $data['message'] = 'Data Pendaftaran Gagal diperbarui !';
            $data['status'] = 'error';
        }
        return $data;
    }

    public function ConfirmDaftar($params)
    {
        $kode = decrypt($params);

        DB::beginTransaction();

        $cek1 = Pendaftaran::where('KodePendaftaran',$kode)->where('isactive',1)->first();
        $jalur = Master_JenisPendaftaran::where('id',$cek1->jalur_daftar)->where('isactive',1)->first();
        $biaya = $jalur->jml_biaya_pendaftaran;

        $updt = Pendaftaran::where('KodePendaftaran',$kode)->where('isactive',1)->update([
            'konfirm_pendaftaran'=>'1',
            'tgl_konfirm' => date('Y-m-d H:i:s'),
            'current_step' => $biaya == 0 ? $cek1->current_step+3 : $cek1->current_step+1,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $kode = 'PMB-'.$cek1->KodePendaftaran.'-'.date('YmdHis');
        $bioId = decrypt(session('user')->_biodata);

        // dd($bioId);
        // Simpan transaksi
        $transaksi = Transaksi::insert([
            'user_id' => $bioId,
            'kategori' => 'pendaftaran',
            'id_referensi' => $cek1->KodePendaftaran,
            'kode_transaksi' => $kode,
            'jumlah' => $biaya,
            'status' => $biaya == 0 ? 'paid' : 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // $cek2 = Transaksi::orderby('id','desc')->latest()->first();
        // Simpan history
        // $historyTransaksi = TransaksiHistory::insert([
            // 'transaksi_id' => $cek2->id,
            // 'status' => $cek2->status,
            // 'keterangan' => $biaya == 0 ? 'Gratis / Beasiswa' : 'Menunggu pembayaran',
            // 'created_at' => date('Y-m-d H:i:s')
        // ]);

        if($updt&&$transaksi){ //&&$historyTransaksi
           DB::commit();
            $data['title'] = 'Berhasil';
            $data['message'] = 'Konfirmasi Pendaftaran Berhasil';
            $data['status'] = 'success';
        }else{
            DB::rollback();
            $data['title'] = 'Gagal';
            $data['message'] = 'Konfirmasi Pendaftaran Gagal';
            $data['status'] = 'error';
        }
        return response()->json($data, Response::HTTP_OK);
    }

    public function delete($params)
    {
        $id = decrypt($params);
        $cek1 = Pendaftaran::where('KodePendaftaran',$id)->where('isactive',1)->exists();

        if($cek1){
            DB::beginTransaction();
            $update = Pendaftaran::where('KodePendaftaran',$id)->where('isactive',1)->update([
                'isactive' => '0',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            if($update){
                DB::commit();
                $data['title'] = 'Berhasil';
                $data['message'] = 'Anda Dinyatakan Mengundurkan Diri Pada Batch ini. Silahkan Mendaftar lagi pada batch selanjutnya !';
                $data['status'] = 'success';
            }else{
                DB::rollback();
                $data['title'] = 'Gagal';
                $data['message'] = 'Gagal Menghapus Data Pendaftaran !';
                $data['status'] = 'error';
            }
        }else{
            $data['title'] = 'Gagal';
            $data['message'] = 'Data Pendaftaran Tidak Ada !';
            $data['status'] = 'error';
        }

        return response()->json($data, Response::HTTP_OK);
    }
}
