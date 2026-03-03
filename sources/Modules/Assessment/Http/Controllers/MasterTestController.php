<?php

namespace Modules\Assessment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assessment\Assessment_EngineTest;
use App\Models\Assessment\Assessment_TipeTest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class MasterTestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $engine = Assessment_EngineTest::where('isactive',1)->select('id','tipe_engine','keterangan')->get();
        $data = array(
            'title' => 'Data Tipe Test',
            'menu'  => 'Data Tipe Test',
            'engine' => $engine
        );
        return view('assessment::masterdata.test.index',$data);
    }

    function tabel_test()
    {
        $query = Assessment_TipeTest::where('isactive',1)->orderBy('created_at', 'desc')->get();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama', function($q){
                return $q->nama_test;
            })
            ->editColumn('kode', function ($q) {
                return $q->kode_test;
            })
            ->editColumn('urutan', function ($q) {
                return $q->urutan;
            })
            ->editColumn('tipe', function ($q) {
                return $q->tipe_engine;
            })
            ->editColumn('durasi', function ($q) {
                return $q->durasi_menit;
            })
            ->editColumn('aktif', function ($q) {
                $aktif = $q->isactive==1 ? '<div class="text-center text-success"><i class="fas fa-check"></i></div>' : '<div class="text-center text-danger"><i class="fas fa-times"></i></div>';
                return $aktif;
            })
            ->addColumn('action', function ($q) {
                $id = encrypt($q->id);
                $btn = '';
                // Tombol Set Aktif (Hijau)
                if($q->aktif==0){
                    $btn .= '<button class="btn btn-success btn-sm btn_aktif" data-aktif="1" data-id="'.$id.'" title="Set Aktif"><i class="fas fa-check"></i></button>';
                }
                // Tombol Set Tidak Aktif (merah)
                if($q->aktif==1){
                    $btn .= '<button class="btn btn-danger btn-sm btn_aktif" data-aktif="0" data-id="'.$id.'" title="Set Non Aktif"><i class="fas fa-times"></i></button>';
                }
                // Tombol Edit/Detail (Biru)
                $btn .= ' <button class="btn btn-warning btn-sm btn_edit" data-id="'.$id.'" title="Edit Data"><i class="fas fa-edit"></i></button>';
                // Tombol Hapus (Merah)
                // $btn .= ' <button type="button" class="btn btn-danger btn-sm btn_hapus" data-id="'.$id.'" title="Hapus"><i class="fas fa-trash"></i></button>';
                return '<div class="text-center">'.$btn.'</div>';
            })
            ->rawColumns(['aktif', 'action'])
            ->make(true);
    }

    public function store(Request $post)
    {
        dd($post);
        DB::beginTransaction();
        $save = null;
        if($post->IdTest){
            $save = $this->update($post);
        }else{
            $save = $this->save($post);
        }
        return response()->json($save, Response::HTTP_OK);
    }

    public function save($post)
    {

    }

    public function update($post)
    {

    }

    public function show($id)
    {
        return view('assessment::show');
    }

    public function destroy($id)
    {
        //
    }
}
