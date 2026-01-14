<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\MasterData\Master_JenisPendaftaran;
use App\Models\MasterData\Master_TarifUKT;
use App\Models\Parameter;
use App\Models\Transaksi;
use App\Models\TransaksiHistory;
use App\Models\User\Pendaftaran;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Yajra\DataTables\DataTables;
use Session, Crypt, DB;
use Symfony\Component\HttpFoundation\Response;

class PembayaranUKTController extends Controller
{
    public function index()
    {
        $data = array(
            'title' => 'Pembayaran UKT',
            'menu'  => 'Data Pembayaran UKT',
        );
        return view('admin::pembayaranUKT.index', $data);
    }

    public function tabelPembayaranUKT()
    {
        $data = Transaksi::with('biodata','pendaftaran')->where('kategori','ukt')->get();
        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('nama', function ($d) {
            return $d->biodata->nama;
        })
        ->addColumn('noreg', function ($d) {
            return $d->id_referensi;
        })
        ->addColumn('kodetx', function ($d) {
            $nama = $d->kode_transaksi;
            return $nama;
        })
        ->addColumn('jenis', function ($d) {
            $nama = $d->kategori;
            return $nama;
        })
        ->addColumn('nominal', function ($d) {
            $nama = rupiah($d->jumlah);
            return $nama;
        })
        ->addColumn('status', function ($d) {
            $warna = 'warning';
            if($d->status=='paid'){
                $warna = 'success';
            }elseif($d->status=='pending'||$d->status=='waiting'){
                $warna = 'warning';
            }else{
                $warna = 'danger';
            }
            $show = '<span class="badge bg-'.$warna.'">'.$d->status.'</span>';
            return $show;
        })
        ->addColumn('keterangan', function ($d) {
            $ket = '-';
            if($d->keterangan){
                $ket = $d->keterangan;
            }
            return $ket;
        })
        ->addColumn('approve', function ($d) {
            $id = encrypt($d->id_referensi);
            $approval = '';
            if($d->pendaftaran->bayar_ukt==0){
                $approval = '<a href="#" class="revisi-bayar" data-id="'.$id.'"><i title="Revisi Bukti Pembayaran" class="fa fa-window-close fa-lg text-red"></i></a>
                                    <a href="#" class="approve-bayar" data-id="'.$id.'"><i title="Approve" class="fa fa-check-square fa-lg text-green"></i></a>';
            }else{
                $approval = '<span class="badge bg-success">'.namaku($d->validator_pembayaran).'</span>' ;
            }
            return $approval;
        })
        ->addColumn('action', function ($d) {
            $id = encrypt($d->id_referensi);

            $detail = '<a href="#" data-id="'.$id.'" class="btn_detail"><i title="Detail" class="fa fa-info-circle"></i></a>';
            $show = '';
            if($d->bukti_pembayaran){
                $params1 = Parameter::where('id',1)->first();
                $linkkhusus = asset('sources/storage/app/'.$params1->bukti_bayar_ukt.'/'.$d->bukti_pembayaran);
                $show = '<a href="'.$linkkhusus.'" target="_blank"><i title="Lihat Bukti Pendaftaran" class="fa fa-eye"></i></a>';
            }

            return $detail.' '.$show;
        })

        ->rawColumns(['action','status','approve'])
        ->make(true);
    }

    public function showPayment($params)
    {
        $id = decrypt($params);
        $cek = Pendaftaran::where('KodePendaftaran',$id)
        // ->where('isactive',1)
        ->select('biodata_id','KodePendaftaran')->first();
        // dd($cek);
        $cek1 = Pendaftaran::where('KodePendaftaran',$id)->where('biodata_id',$cek->biodata_id)
        // ->where('isactive',1)
        ->with(['biodata','batch','jalur'
        // =>function($q){
        //     $q->with(['berkasumum'=>function($q){
        //             $q->with('berkas');
        //         },

        //     'berkaskhusus'=>function($q){
        //             $q->with('berkas');
        //         }
        //     ]);
        // }
        ,
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
        'waktukuliah','bayar'
        // =>function($q){
        //     $q->with('history_transaksi');
        // }
        ])->first();
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

    public function approve($params)
    {
        $id = decrypt($params);
        $cek = Pendaftaran::where('KodePendaftaran',$id)->select('current_step','jalur_daftar')->first();

        $step = $cek->current_step+1;

        DB::beginTransaction();

        $update1 = Pendaftaran::where('KodePendaftaran',$id)->update([
            'bayar_ukt' => '1',
            'current_step' => $step,
            'keterangan' => null,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $update2 = Transaksi::where('id_referensi',$id)->update([
            'status' => 'paid',
            'validator_pembayaran' => session('session')->nip,
            'keterangan' => null,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if($update1&&$update2){
            DB::commit();
            $data['status']  = true;
            $data['message'] = 'Bukti Pembayaran Berhasil di Validasi';
        }else{
            DB::rollback();
            $data['status']  = false;
            $data['message'] = 'Bukti Pembayaran Gagal di Validasi';
        }
        return response()->json($data, Response::HTTP_OK);
    }

    public function revisi(Request $post)
    {
        $id = decrypt($post->iddaftar);
        $ket = $post->keterangan;

        DB::beginTransaction();

        $update1 = Pendaftaran::where('KodePendaftaran',$id)->update([
            'keterangan' => $ket,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $update2 = Transaksi::where('id_referensi',$id)->update([
            'keterangan' => $ket,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if($update1&&$update2){
            DB::commit();
            $alert = ['title' => 'Information', 'message' => 'Berhasil Menambahkan Keterangan', 'status' => 'success'];
        }else{
            DB::rollback();
            $alert = ['title' => 'Information', 'message' => 'Gagal Menambahkan Keterangan', 'status' => 'error'];
        }
        return redirect()->back()->with('alert',$alert);

    }
}
