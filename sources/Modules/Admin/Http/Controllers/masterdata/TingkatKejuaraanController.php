<?php

namespace Modules\Admin\Http\Controllers\masterdata;

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

class TingkatKejuaraanController extends Controller
{
    public function index()
    {
        $data = array(
            'title'    => 'Master Tingkat Kejuaraan',
            'menu'     => 'Tingkat Kejuaraan',
        );
        return view('admin::masterdata.tingkat_kejuaraan.index', $data);
    }

    public function TabelTingkatKejuaraan()
    {
        $data = Master_Tingkat::where('isactive',1)->get();
        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('nama', function ($d) {
            return $d->tingkat_kejuaraan;
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
        ->addColumn('action', function ($d) {
            $id = encrypt($d->id);
            $edit   = '<a href="#" data-id="'.$id.'" class="btn_edit"><i title="Edit" class="fa fa-edit text-orange"></i></a>';
            $aktif = '<a href="#" class="btn_delete" data-id="'.$id.'"><i title="Hapus" class="fa fa-trash text-red"></i></a>';

            // $detail = '<a href="#" data-id="'.$id.'" class="btn_detail"><i title="Detail" class="fa fa-info-circle"></i></a>';

            return $edit.' '.$aktif;
        })
        ->rawColumns(['action','status'])
        ->make(true);
    }

    public function storeTingkat(Request $post)
    {
        $cek = Master_Tingkat::where('tingkat_kejuaraan',$post->tingkat)->where('isactive',1)->exists();
        if($cek){
            $data['title']  = 'Information';
            $data['status'] = 'warning';
            $data['message'] = 'Nama Tingkat Kejuaraan Sudah Ada !';
        }else{
            DB::beginTransaction();
            if($post->IdTingkat==null){
                $data = $this->save($post);
            }else{
                $data = $this->update($post);
            }
        }
        return response()->json($data, Response::HTTP_OK);
    }

    public function save($post)
    {
        $array = array(
            'tingkat_kejuaraan' => $post->tingkat,
            'created_at' => now(),
            'created_by' => session('session')->nip
        );
        $save = Master_Tingkat::insert($array);
        if($save){
            DB::commit();
            $data['title']  = 'Berhasil';
            $data['status'] = 'success';
            $data['message'] = 'Tingkat Kejuaraan Tersimpan !';
        }else{
            DB::rollback();
            $data['title']  = 'Information';
            $data['status'] = 'error';
            $data['message'] = 'Tingkat Kejuaraan gagal tersimpan !';
        }
        return $data;
    }

    public function ShowTingkat($params)
    {
        $id = decrypt($params);
        $cek = Master_Tingkat::where('id',$id)->first();

        if($cek){
            $data['hasil'] = 1;
            $data['tingkat'] = $cek;
        }else{
            $data['hasil'] = 0;
            $data['tingkat'] = $cek;
        }
        return response()->json($data, Response::HTTP_OK);
    }

    public function update($post)
    {
        $array = array(
            'tingkat_kejuaraan' => $post->tingkat,
            'updated_at' => now(),
            'updated_by' => session('session')->nip
        );
        $save = Master_Tingkat::where('id',$post->IdTingkat)->update($array);
        if($save){
            DB::commit();
            $data['title']  = 'Berhasil';
            $data['status'] = 'success';
            $data['message'] = 'Tingkat Kejuaraan Diperbarui !';
        }else{
            DB::rollback();
            $data['title']  = 'Information';
            $data['status'] = 'error';
            $data['message'] = 'Tingkat Kejuaraan gagal diperbarui !';
        }
        return $data;
    }

    public function delete($params)
    {
        $id = decrypt($params);

        $cek = Master_Beasiswa::where('idtingkat',$id)->exists();
        if($cek){
            $data['title']  = 'Information';
            $data['status'] = 'warning';
            $data['message'] = 'Tingkat Kejuaraan Sudah dipakai !';
        }else{
            DB::beginTransaction();
            $up = Master_Tingkat::where('id',$id)->update([
                'isactive' => '0',
                'updated_at' => now(),
                'updated_by' => session('session')->nip
            ]);

            if($up){
                DB::commit();
                $data['title']  = 'Berhasil';
                $data['status'] = 'success';
                $data['message'] = 'Tingkat Kejuaraan Dihapus !';
            }else{
                DB::rollback();
                $data['title']  = 'Information';
                $data['status'] = 'error';
                $data['message'] = 'Tingkat Kejuaraan gagal dihapus !';
            }
        }
        return response()->json($data, Response::HTTP_OK);
    }
}
