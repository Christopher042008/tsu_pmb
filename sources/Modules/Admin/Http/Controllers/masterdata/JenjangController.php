<?php

namespace Modules\Admin\Http\Controllers\masterdata;

use App\Models\MasterData\Master_Jenjang;
use App\Models\MasterData\Master_JurusanKuliah;
use App\Models\User\Pendaftaran;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Session, Crypt, DB;
use Yajra\DataTables\DataTables;
use Symfony\Component\HttpFoundation\Response;

class JenjangController extends Controller
{
    public function index()
    {
        $data = array(
            'title' => 'Master Data Jenjang',
            'menu'  => 'Jenjang Pendidikan',
        );
        return view('admin::masterdata.jenjang.index', $data);
    }

    public function table_Pendaftaran()
    {
        $data = Master_Jenjang::where('isactive',1)->get();
        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('jenjang', function ($d) {
            return $d->jenjang;
        })
        ->addColumn('nama', function ($d) {
            $nama = $d->nama_jenjang;
            return $nama;
        })
        ->addColumn('aktif', function ($d) {
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

            $url = '#';
            $edit   = '<a href="#" data-id="'.$id.'" class="btn_edit"><i title="Edit" class="fa fa-edit text-orange"></i></a>';
            $aktif = '';
            if($d->isactive==1){
                $url = route('admin.Jenjang.delete',[$id,encrypt('0')]);
                $aktif = '<a href="'.$url.'" class="btn_delete"><i title="Hapus" class="fa fa-trash text-red"></i></a>';
            }else{
                $url = route('admin.Jenjang.delete',[$id,encrypt('1')]);
                $aktif  = '<a href="'.$url.'" class="btn_delete"><i title="Aktifkan" class="fas fa-check-circle text-green"></i></a>';
            }
            return $edit.' '.$aktif;
        })
        ->rawColumns(['action','aktif'])
        ->make(true);
    }

    public function StoreJurusan(Request $post)
    {
        // DD($post);
        $cek = Master_Jenjang::where('isactive',1)->where('jenjang',$post->jenjang)
        ->where('nama_jenjang',$post->namajenjang)
        ->first();
        $alert = null;
        if($cek){
            $alert = array(
                'title' => 'Gagal!',
                'message' => 'Nama atau Singkatan Jenis Pendaftaran Tidak Boleh Sama !',
                'status' => 'warning'
            );
        }else{
            if($post->IdJenjang == null){
                $alert = $this->Save($post);
            }else{
                $alert = $this->Update($post);
            }
        }


        return redirect()->route('admin.Jenjang.show')->with('alert',$alert);

    }

    public function Save($post)
    {
        $up = array(
            'jenjang' => $post->jenjang,
            'nama_jenjang' => $post->namajenjang,
            'created_at'   => date('Y-m-d H:i:s'),
            'created_by'   => session('session')->nip,
        );
        DB::beginTransaction();
        $save = Master_Jenjang::insert($up);
        if($save){
            DB::commit();
            $alert = array(
                'title' => 'Berhasil!',
                'message' => 'Data Jenjang Pendidikan Tersimpan !',
                'status' => 'success'
            );
        }else{
            DB::rollback();
            $alert = array(
                'title' => 'Gagal!',
                'message' => 'Data Jenjang Pendidikan Gagal Disimpan !',
                'status' => 'error'
            );
        }
        return $alert;
    }

    public function ShowJurusan($params)
    {
        $id = decrypt($params);
        // dd($id);
        $check = Master_Jenjang::where('isactive',1)->where('id',$id)->first();

        if($check){
            $data['hasil'] = 1;
            $data['jenjang'] = $check;
            $data['IdJenjang'] = $params;
        }else{
            $data['hasil'] = 0;
            $data['jenjang'] = $check;
            $data['IdJenjang'] = $params;
        }
        return response()->json($data, Response::HTTP_OK);
    }

    public function Update($post)
    {
        $id = decrypt($post->IdJenjang);

        $up = array(
            'jenjang' => $post->jenjang,
            'nama_jenjang' => $post->namajenjang,
            'updated_at'   => date('Y-m-d H:i:s'),
            'updated_by'   => session('session')->nip,
        );
        DB::beginTransaction();
        $update = Master_Jenjang::where('isactive',1)->where('id',$id)->update($up);
        if($update){
            DB::commit();
            $alert = array(
                'title' => 'Berhasil!',
                'message' => 'Data Jenjang Pendidikan Diperbarui !',
                'status' => 'success'
            );
        }else{
            DB::rollback();
            $alert = array(
                'title' => 'Gagal!',
                'message' => 'Data Jenjang Pendidikan Gagal Diperbarui !',
                'status' => 'error'
            );
        }
        return $alert;
    }

    public function delete($params1,$params2)
    {
        $id = decrypt($params1);
        $aktif = decrypt($params2);
        $cek = Master_JurusanKuliah::where('idjenjang',$id)->first();
        if($cek){
            $alert = ['title' => 'Gagal','message' => 'Jenjang Pendidikan Sudah Dipakai !','status' => 'error'];
            return redirect()->route('admin.Jenjang.show')->with('alert',$alert);
        }
        DB::beginTransaction();
        $up = array(
            'isactive' => $aktif,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => session('session')->nip
        );

        $update = Master_Jenjang::where('id',$id)->update($up);

        if($update){
            DB::commit();
            $alert = ['title' => 'Berhasil','message' => 'Data Jenjang Pendidikan Berhasil Diperbarui','status' => 'success'];
        }else{
            DB::rollback();
            $alert = ['title' => 'Gagal','message' => 'Data Jenjang Pendidikan Gagal Diperbarui','status' => 'error'];
        }
        return redirect()->route('admin.Jenjang.show')->with('alert',$alert);
    }
}
