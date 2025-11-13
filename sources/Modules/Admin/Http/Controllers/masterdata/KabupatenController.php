<?php

namespace Modules\Admin\Http\Controllers\masterdata;

use App\Models\MasterData\Master_Kabupaten;
use App\Models\MasterData\Master_Provinsi;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Session, Crypt, DB;
use Yajra\DataTables\DataTables;
use Symfony\Component\HttpFoundation\Response;

class KabupatenController extends Controller
{
    public function index()
    {
        $data = array(
            'title'    => 'Master Kabupaten',
            'menu'     => 'Kabupaten',
        );
        return view('admin::masterdata.kabupaten.index', $data);
    }

    public function TabelKabupaten()
    {
        $data = Master_Kabupaten::with('provinsi')->where('isactive',1)->get();
                // dd($data);
        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('provinsi', function ($d) {
            return $d->provinsi->nama_provinsi;
        })
        ->addColumn('kabupaten', function ($d) {
            return $d->nama_kabupaten;
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
