<?php

namespace Modules\Admin\Http\Controllers\masterdata;

use App\Models\MasterData\Master_Provinsi;

use App\Models\MasterData\Master_Beasiswa;
use App\Models\MasterData\Master_JenisBerkas;
use App\Models\MasterData\Master_JenisPendaftaran;
use App\Models\MasterData\Master_Kecamatan;
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

class KecamatanController extends Controller
{
    public function index()
    {
        $data = array(
            'title'    => 'Master Kecamatan',
            'menu'     => 'Kecamatan',
        );
        return view('admin::masterdata.kecamatan.index', $data);
    }

    public function TabelKecamatan()
    {
        $data = Master_Kecamatan::join('pmb_master_kabupaten as kab', function($join){
                    $join->on('pmb_master_kecamatan.idprov','=','kab.idprov')
                        ->on('pmb_master_kecamatan.idkab','=','kab.idkab');
                })
                ->join('pmb_master_provinsi as prov', 'pmb_master_kecamatan.idprov','=','prov.idprov')
                ->where('pmb_master_kecamatan.isactive',1)
                ->where('kab.isactive',1)
                ->where('prov.isactive',1)
                ->select(
                    'prov.nama_provinsi',
                    'kab.nama_kabupaten',
                    'pmb_master_kecamatan.nama_kecamatan',
                    'pmb_master_kecamatan.isactive'
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
