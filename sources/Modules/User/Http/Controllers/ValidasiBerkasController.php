<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_JenisPendaftaran;
use App\Models\MasterData\Master_TarifUKT;
use App\Models\Parameter;
use App\Models\User\PindahJalur;
use App\Models\Transaksi;
use App\Models\TransaksiHistory;
use App\Models\User\Biodata;
use App\Models\User\Maba;
use App\Models\User\Pendaftaran;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;

use Symfony\Component\HttpFoundation\Response;
use Session, Crypt, DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use Psy\Command\HistoryCommand;

class ValidasiBerkasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = array(
            'title' => 'Berkas Beasiswa',
            'menu' => 'Berkas Beasiswa',
        );
        return view('user::user.berkas.index',$data);
    }

    public function tabelBerkasBeasiswa()
    {
        $bioId = decrypt(session('user')->_biodata);
        $data = Pendaftaran::where('biodata_id',$bioId)
        ->join('pmb_master_jenispendaftaran as a','pmb_pendaftaran.jalur_daftar','=','a.id')
        ->selectRaw('pmb_pendaftaran.*')
        ->whereRaw('a.berkas_khusus is not null')
        ->with(['biodata','batch','jalur','jenisbeasiswa'])
        ->get();
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
        ->addColumn('beasiswa', function ($d) {
            $nama = $d->jenisbeasiswa ? $d->jenisbeasiswa->jenis_beasiswa : '-';
            return $nama;
        })
        ->addColumn('status', function ($d) {
            $role = '';
            $warna = '';
            if($d->validasi_berkas_khusus=='0'){
                $role = 'Validasi Berkas';
                $warna = 'warning';
            }elseif($d->validasi_berkas_khusus=='1'){
                $role = 'Berkas OK';
                $warna = 'success';
            }elseif($d->validasi_berkas_khusus=='-1'){
                $role = 'Berkas Ditolak';
                $warna = 'danger';
            }else{
                $role = 'Belum Diupload';
                $warna = 'warning';
            }
            $show = '<span class="badge bg-'.$warna.'">'.$role.'</span>';
            return $show;
        })
        ->addColumn('keterangan', function ($d) {
            $id = encrypt($d->KodePendaftaran);
            $nama = $d->keterangan ? $d->keterangan:'-';
            $btn = '';
            if($d->status_pindah_jalur=='0'){
                $btn  = '<br><a href="#" class="tidaksetuju-pindah" data-id="'.$id.'"><i title="Tidak Setuju Pindah Jalur" class="fa fa-window-close fa-lg text-red"></i></a>
                                <a href="#" class="setuju-pindah" data-id="'.$id.'"><i title="Setuju Pindah jalur" class="fa fa-check-square fa-lg text-green"></i></a>';
            }
            return $nama.$btn;
        })
        ->addColumn('action', function ($d) {
            $id = encrypt($d->KodePendaftaran);
            $upload = '';
            $show = '';
            $params1 = Parameter::where('id',1)->first();
            if($d->berkas_khusus!=null){
                $link = asset('sources/storage/app/'.$params1->file_khusus.'/'.$d->berkas_khusus);
                $show = '<a href="'.$link.'" target="_blank" class="btn_show"><i title="Lihat Berkas" class="fas fa-eye text-green"></i></a>';
            }

            if($d->validasi_berkas_khusus=='0'||$d->validasi_berkas_khusus==null){
                $upload = '<a href="#" data-id="'.$id.'" class="btn_upload"><i title="Upload Berkas" class="fas fa-upload text-green"></i></a>';
            }

            $detail = '<a href="#" data-id="'.$id.'" class="btn_detail"><i title="Detail Pendaftaran" class="fa fa-info-circle"></i></a>';

            return $detail.' '.$show.' '.$upload;
        })
        ->rawColumns(['action','status','keterangan'])
        ->make(true);
    }

    public function showBerkasBeasiswa($params)
    {
        $id = decrypt($params);
        $bioId = decrypt(session('user')->_biodata);
        //->where('isactive',1)
        $cek1 = Pendaftaran::where('KodePendaftaran',$id)->where('biodata_id',$bioId)->with(['biodata','batch',
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
        $prodi1 = null;
        $prodi2 = null;
        if($cek1){
            $prodi1 = Master_TarifUKT::where('idbatch',$cek1->batch_daftar)->where('idjalur',$cek1->jalur_daftar)->where('idjurusan',$cek1->prodi1->id)->where('isactive',1)->first();
            $prodi2 = Master_TarifUKT::where('idbatch',$cek1->batch_daftar)->where('idjalur',$cek1->jalur_daftar)->where('idjurusan',$cek1->prodi2->id)->where('isactive',1)->first();
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
        // dd($data);
        return response()->json($data, Response::HTTP_OK);
    }

    public function saveBerkas(Request $post)
    {
        $kddftar = decrypt($post->kodedaftar);
        $cek = Pendaftaran::where('KodePendaftaran',$kddftar)->with(['jalur'])->first();
        $file = $post->file('fileberkas');
        $ext = $file->getClientOriginalExtension();
        $filename = $cek->jalur->KodeJenis.'_'.$kddftar.'_'.date('YmdHis').'.'.$ext;


        DB::beginTransaction();

        $parameter = Parameter::where('id',1)->first();
        $updateberkas = null;
        if($cek->berkas_khusus!=null){
            $path = $parameter->file_khusus.'/'.$cek->berkas_khusus;
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
            $stepku = $cek->current_step;
            $updateberkas = date('Y-m-d H:i:s');
        }else{
            $stepku = $cek->current_step+1;
            $updateberkas = null;
        }

        $update = Pendaftaran::where('KodePendaftaran',$kddftar)->update([
            'current_step' => $stepku,
            'berkas_khusus' => $filename,
            'validasi_berkas_khusus' => '0',
            'update_berkaskhusus' => $updateberkas,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if($update){
            DB::commit();
            $file->storeAs($parameter->file_khusus, $filename);
            $data['title'] = 'Berhasil';
            $data['message'] = 'Upload Berkas Sukses!';
            $data['status'] = 'success';
        }else{
            DB::rollback();
            $data['title'] = 'Gagal';
            $data['message'] = 'Upload Berkas Gagal!';
            $data['status'] = 'error';
        }
        return response()->json($data, Response::HTTP_OK);
    }

    public function PindahJalur($params1,$params2)
    {
        $id = decrypt($params1);
        $pindahjalur = $params2;
        // dd($params1,$params2);
        $cekdaftar = Pendaftaran::where('KodePendaftaran',$id)->where('isactive',1)->first();

        DB::beginTransaction();

        if($pindahjalur=='-1'){
            $updt = Pendaftaran::where('KodePendaftaran',$id)->where('isactive',1)->update([
                'keterangan' => 'Anda dinyatakan mengundurkan diri ! Silahkan Daftar pada batch selanjutnya.',
                'status_pindah_jalur' => $pindahjalur,
                'stop_step' => $cekdaftar->current_step,
                'isactive' => '0',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            if($updt){
                DB::commit();
                $data['title'] = 'Berhasil';
                $data['message'] = 'Anda dinyatakan mengundurkan diri ! Silahkan Daftar pada batch selanjutnya.';
                $data['status'] = 'success';
            }else{
                DB::rollback();
                $data['title'] = 'Gagal';
                $data['message'] = 'Gagal Mengundurkan diri. Silahkan Coba Kembali !';
                $data['status'] = 'error';
            }
        }else{
            $cek = Pendaftaran::where('KodePendaftaran',$id)->where('isactive',1)->first();
            $transaksi = Transaksi::where('user_id',$cek->biodata_id)->where('id_referensi',$id)->first();
            $cekjalur = Master_JenisPendaftaran::wherelike('jenis_pendaftaran','%reguler%')->where('isactive',1)->first();

            // Tracking pindah jalur
            $up1 = PindahJalur::insert([
                'biodata_id' => $cek->biodata_id,
                'KodePendaftaran' => $id,
                'batch' => $cek->batch_daftar,
                'jalur' => $cek->jalur_daftar,
                'beasiswa' => $cek->beasiswa,
                'berkas_khusus' => $cek->berkas_khusus,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            //Pindah Jalur Pendaftaran
            $up2 = Pendaftaran::where('KodePendaftaran',$id)->where('biodata_id',$cek->biodata_id)->where('isactive',1)->update([
                'bayar_pendaftaran'          => $cek->bayar_pendaftaran=='-1' ? '0' : $cek->bayar_pendaftaran,
                'batch_daftar'               => $cek->batch_daftar,
                'jalur_daftar'               => $cekjalur->id,
                'beasiswa'                   => null,
                'bayar_ukt'                  => $cekjalur->status_ukt,
                'berkas_khusus'              => null,
                'validasi_berkas_khusus'     => null,
                'nik_validasi_berkas_khusus' => null,
                'tgl_validasi_berkas_khusus' => null,
                'keterangan'                 => null,
                'status_pindah_jalur'        => '1',
                'updated_at'                 => date('Y-m-d H:i:s')
            ]);

            //Cek Biaya Pendaftaran
            if($cek->bayar_pendaftaran=='-1'){
                Transaksi::where('user_id',$cek->biodata_id)->where('id_referensi',$id)
                ->update([
                    'jumlah' => $cekjalur->jml_biaya_pendaftaran,
                    'status' => 'pending',
                    'metode_bayar' => null,
                    'midtrans_order_id' => null,
                    'midtrans_snap_token' => null,
                    'expired_at' => null
                ]);
                TransaksiHistory::where('transaksi_id',$transaksi->id)->delete();
                TransaksiHistory::insert([
                    'transaksi_id' => $transaksi->id,
                    'status' => 'pending',
                    'keterangan' => 'Menunggu pembayaran',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                Pendaftaran::where('KodePendaftaran',$id)->where('biodata_id',$cek->biodata_id)->where('isactive',1)->update([
                    'current_step'               => 2,
                    'updated_at'                 => now()
                ]);
            }else{
                Pendaftaran::where('KodePendaftaran',$id)->where('biodata_id',$cek->biodata_id)->where('isactive',1)->update([
                    'current_step'               => 6,
                    'updated_at'                 => now()
                ]);
            }

            if($up1&&$up2){
                DB::commit();
                $data['title'] = 'Berhasil';
                $data['message'] = 'Anda Sudah Berpindah ke jalur pendaftaran Reguler. Silahkan Lanjut Ke Proses Selanjutnya!';
                $data['status'] = 'success';
            }else{
                DB::rollback();
                $data['title'] = 'Gagal';
                $data['message'] = 'Gagal Berpindah Jalur. Silahkan Coba Lagi atau hubungi Admin PMB !';
                $data['status'] = 'error';
            }
        }
        return response()->json($data, Response::HTTP_OK);
    }
}
