<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\MasterData\Master_TarifUKT;
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

class PembayaranPMBController extends Controller
{
    public function index()
    {

        $data = array(
            'title' => 'Pembayaran PMB',
            'menu'  => 'Data Pembayaran PMB',
        );
        return view('admin::pembayaranPMB.index', $data);
    }

    public function tabelPembayaranPMB($params)
    {
        $jenis = decrypt($params);
        $data = Transaksi::with('biodata')->where('kategori',$jenis)->get();
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
            }elseif($d->status=='pending'){
                $warna = 'warning';
            }else{
                $warna = 'danger';
            }
            $show = '<span class="badge bg-'.$warna.'">'.$d->status.'</span>';
            return $show;
        })
        ->addColumn('action', function ($d) {
            $id = encrypt($d->id_referensi);

            $url = '#';
            // $edit   = '<a href="#" data-id="'.$id.'" class="btn_edit"><i title="Edit" class="fa fa-edit text-orange"></i></a>';
            $edit   = '';
            $aktif = '';
            $detail = '';
            // if($d->isactive==1){
            //     $aktif = '<a href="#" class="btn_delete" data-id="'.$id.'" data-status="'.encrypt('0').'"><i title="Hapus" class="fa fa-trash text-red"></i></a>';
            // }else{
            //     $aktif  = '<a href="#" class="btn_delete" data-id="'.$id.'" data-status="'.encrypt('1').'"><i title="Aktifkan" class="fas fa-check-circle text-green"></i></a>';
            // }
            $detail = '<a href="#" data-id="'.$id.'" class="btn_detail"><i title="Detail" class="fa fa-info-circle"></i></a>';

            return $detail.' '.$edit.' '.$aktif;
        })
        ->rawColumns(['action','status'])
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
        'waktukuliah','bayar'=>function($q){
            $q->with('history_transaksi');
        }
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
}
