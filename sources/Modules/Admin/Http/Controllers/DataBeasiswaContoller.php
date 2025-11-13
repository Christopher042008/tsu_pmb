<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\User\Pendaftaran;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Session, Crypt, DB;
use Yajra\DataTables\DataTables;
use Symfony\Component\HttpFoundation\Response;
use App\Models\MasterData\Master_TarifUKT;


class DataBeasiswaContoller extends Controller
{
    public function index()
    {
        $data = array(
            'title' => 'Data Pendaftar Beasiswa',
            'menu'  => 'Data Pendaftar Beasiswa',
        );
        return view('admin::pendaftaran.beasiswa.index', $data);
    }

    public function tabelBeasiswa()
    {
        $data = Pendaftaran::where('beasiswa','!=',null)->with(['biodata','batch','jalur','jenisbeasiswa','jurusansekolah',
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
            return $d->batch->nama_batch.' '.$d->batch->tahun_akademik;
        })
        ->addColumn('jalur', function ($d) {
            $nama = $d->jalur->jenis_pendaftaran;
            return $nama;
        })
        ->addColumn('beasiswa', function ($d) {
            $nama = $d->jenisbeasiswa->jenis_beasiswa;
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
            // if($d->isactive==1){
            //     if($d->konfirm_pendaftaran==0){
            //         $aktif = '<a href="#" class="btn_delete" data-id="'.$id.'"><i title="Hapus Pendaftaran" class="fa fa-trash text-red"></i></a>';
            //         $konfirm = '<a href="#" data-id="'.$id.'" class="btn_konfirm"><i title="Konfirmasi Pendaftaran" class="fas fa-check-circle text-green"></i></a>';
            //         $edit   = '<a href="#" data-id="'.$id.'" class="btn_edit"><i title="Edit" class="fa fa-edit text-orange"></i></a>';
            //     }
            // }
            // else{
            //     $aktif  = '<a href="#" class="btn_delete" data-id="'.$id.'" data-status="'.encrypt('1').'"><i title="Aktifkan" class="fas fa-check-circle text-green"></i></a>';
            // }
            $detail = '<a href="#" data-id="'.$id.'" class="btn_detail"><i title="Detail" class="fa fa-info-circle"></i></a>';

            return $detail.' '.$edit.' '.$aktif.' '.$konfirm;
        })
        ->rawColumns(['action','status'])
        ->make(true);
    }

    public function showBeasiswa($params)
    {
        $id = decrypt($params);

        $cek1 = Pendaftaran::where('KodePendaftaran',$id)->where('isactive',1)->with(['biodata','batch',
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
            $data['ukt1'] = $prodi1;
            $data['ukt2'] = $prodi2;
        }else{
            $data['hasil'] = 0;
            $data['daftar'] = $cek1;
            $data['ukt1'] = $prodi1;
            $data['ukt2'] = $prodi2;
        }
        return response()->json($data, Response::HTTP_OK);
    }
}
