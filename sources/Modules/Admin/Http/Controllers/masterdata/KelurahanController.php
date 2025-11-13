<?php

namespace Modules\Admin\Http\Controllers\masterdata;

use App\Models\MasterData\Master_Kelurahan;
use App\Models\MasterData\Master_Provinsi;

use App\Models\MasterData\Master_Beasiswa;
use App\Models\MasterData\Master_JenisBerkas;
use App\Models\MasterData\Master_JenisPendaftaran;
use App\Models\MasterData\Master_Tingkat;
use App\Models\User\Pendaftaran;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Session, Crypt, DB;
use Yajra\DataTables\DataTables;
use Symfony\Component\HttpFoundation\Response;

class KelurahanController extends Controller
{
    public function index()
    {
        $data = array(
            'title'    => 'Master Kelurahan',
            'menu'     => 'Kelurahan',
        );
        return view('admin::masterdata.kelurahan.index', $data);
    }

    public function TabelKelurahan()
    {
        $data = Master_Kelurahan::join('pmb_master_kecamatan as kec', function($join){
                $join->on('pmb_master_kelurahan.idprov','=','kec.idprov')
                    ->on('pmb_master_kelurahan.idkab','=','kec.idkab')
                    ->on('pmb_master_kelurahan.idkec','=','kec.idkec');
            })
            ->join('pmb_master_kabupaten as kab', function($join){
                $join->on('pmb_master_kelurahan.idprov','=','kab.idprov')
                    ->on('pmb_master_kelurahan.idkab','=','kab.idkab');
            })
            ->join('pmb_master_provinsi as prov', 'pmb_master_kelurahan.idprov','=','prov.idprov')
            ->where('pmb_master_kelurahan.isactive',1)
            ->where('kec.isactive',1)
            ->where('kab.isactive',1)
            ->where('prov.isactive',1)
            ->select(
                'prov.nama_provinsi',
                'kab.nama_kabupaten',
                'kec.nama_kecamatan',
                'pmb_master_kelurahan.nama_kelurahan',
                'pmb_master_kelurahan.isactive'
            )
            ->limit(1000)
            ->get();
        return DataTables::of($data)
        ->addIndexColumn()
         ->addColumn('provinsi', function ($d) {
            return $d->nama_provinsi;
        })
        ->addColumn('kabupaten', function ($d) {
            return $d->nama_kabupaten;
        })
        ->addColumn('kecamatan', function ($d) {
            return $d->nama_kecamatan;
        })
        ->addColumn('kelurahan', function ($d) {
            return $d->nama_kelurahan;
        })
        ->addColumn('status', function ($d) {
            $role = '-';
            $warna = '';
            if($d->isactive==1){
                $role = 'Aktif';
                $warna = 'success';
            }else{
                $role = 'Tidak Aktif';
                $warna = 'danger';
            }
            $show = '<span class="badge bg-'.$warna.'">'.$role.'</span>';
            return $show;
        })
        // ->addColumn('action', function ($d) {
        //     $id = encrypt($d->id);
        //     $edit   = '';
        //     // $edit   = '<a href="#" data-id="'.$id.'" class="btn_edit"><i title="Edit" class="fa fa-edit text-orange"></i></a>';
        //     // $aktif = '<a href="#" class="btn_delete" data-id="'.$id.'"><i title="Hapus" class="fa fa-trash text-red"></i></a>';
        //     $aktif = '';

        //     // $detail = '<a href="#" data-id="'.$id.'" class="btn_detail"><i title="Detail" class="fa fa-info-circle"></i></a>';

        //     return $edit.' '.$aktif;
        // })
        ->rawColumns(['action','status'])
        ->make(true);
    }
}
