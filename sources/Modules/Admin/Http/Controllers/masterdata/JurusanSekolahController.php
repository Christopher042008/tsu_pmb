<?php

namespace Modules\Admin\Http\Controllers\masterdata;

use App\Models\MasterData\Master_JurusanKuliah;
use App\Models\MasterData\Master_JurusanSekolah;
use App\Models\User\Pendaftaran;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Session, Crypt, DB;
use Yajra\DataTables\DataTables;
use Symfony\Component\HttpFoundation\Response;

class JurusanSekolahController extends Controller
{
    public function index()
    {
        $data = array(
            'title' => 'Master Jurusan Sekolah',
            'menu'  => 'Jurusan Sekolah',
        );
        return view('admin::masterdata.jurusansekolah.index', $data);
    }

    public function table_Pendaftaran()
    {
        $data = Master_JurusanSekolah::where('isactive',1)->get();
        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('sekolah', function ($d) {
            return $d->sekolah;
        })
        ->addColumn('jurusan', function ($d) {
            $nama = $d->jurusan_sekolah;
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
                $url = route('admin.JurusanSekolah.delete',[$id,encrypt('0')]);
                $aktif = '<a href="'.$url.'" class="btn_delete"><i title="Hapus" class="fa fa-trash text-red"></i></a>';
            }else{
                $url = route('admin.JurusanSekolah.delete',[$id,encrypt('1')]);
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
        $cek = Master_JurusanSekolah::where('isactive',1)->where('sekolah',$post->sekolah)
        ->where('jurusan_sekolah',$post->jurusan)
        ->first();
        $alert = null;
        if($cek){
            $alert = array(
                'title' => 'Gagal!',
                'message' => 'Sekolah atau Jurusan Sekolah Sudah Ada !',
                'status' => 'warning'
            );
        }else{
            if($post->IdSekolah == null){
                $alert = $this->Save($post);
            }else{
                $alert = $this->Update($post);
            }
        }


        return redirect()->route('admin.JurusanSekolah.show')->with('alert',$alert);

    }

    public function Save($post)
    {
        $up = array(
            'sekolah' => $post->sekolah,
            'jurusan_sekolah' => $post->jurusan,
            'created_at'   => date('Y-m-d H:i:s'),
            'created_by'   => session('session')->nip,
        );
        DB::beginTransaction();
        $save = Master_JurusanSekolah::insert($up);
        if($save){
            DB::commit();
            $alert = array(
                'title' => 'Berhasil!',
                'message' => 'Data Jurusan Sekolah Tersimpan !',
                'status' => 'success'
            );
        }else{
            DB::rollback();
            $alert = array(
                'title' => 'Gagal!',
                'message' => 'Data Jurusan Sekolah Gagal Disimpan !',
                'status' => 'error'
            );
        }
        return $alert;
    }

    public function ShowJurusan($params)
    {
        $id = decrypt($params);
        // dd($id);
        $check = Master_JurusanSekolah::where('isactive',1)->where('id',$id)->first();

        if($check){
            $data['hasil'] = 1;
            $data['sekolah'] = $check;
            $data['IdSekolah'] = $params;
        }else{
            $data['hasil'] = 0;
            $data['sekolah'] = $check;
            $data['IdSekolah'] = $params;
        }
        return response()->json($data, Response::HTTP_OK);
    }

    public function Update($post)
    {
        $id = decrypt($post->IdSekolah);

        $up = array(
            'sekolah' => $post->sekolah,
            'jurusan_sekolah' => $post->jurusan,
            'updated_at'   => date('Y-m-d H:i:s'),
            'updated_by'   => session('session')->nip,
        );
        DB::beginTransaction();
        $update = Master_JurusanSekolah::where('isactive',1)->where('id',$id)->update($up);
        if($update){
            DB::commit();
            $alert = array(
                'title' => 'Berhasil!',
                'message' => 'Data Jurusan Sekolah Diperbarui !',
                'status' => 'success'
            );
        }else{
            DB::rollback();
            $alert = array(
                'title' => 'Gagal!',
                'message' => 'Data Jurusan Sekolah Gagal Diperbarui !',
                'status' => 'error'
            );
        }
        return $alert;
    }

    public function delete($params1,$params2)
    {
        $id = decrypt($params1);
        $aktif = decrypt($params2);
        $cek = Pendaftaran::where('jurusan_sekolah',$id)->first();
        if($cek){
            $alert = ['title' => 'Gagal','message' => 'Jurusan Sekolah Sudah Dipakai !','status' => 'error'];
            return redirect()->route('admin.JurusanSekolah.show')->with('alert',$alert);
        }
        DB::beginTransaction();
        $up = array(
            'isactive' => $aktif,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => session('session')->nip
        );

        $update = Master_JurusanSekolah::where('id',$id)->update($up);

        if($update){
            DB::commit();
            $alert = ['title' => 'Berhasil','message' => 'Data Jurusan Sekolah Berhasil Diperbarui','status' => 'success'];
        }else{
            DB::rollback();
            $alert = ['title' => 'Gagal','message' => 'Data Jurusan Sekolah Gagal Diperbarui','status' => 'error'];
        }
        return redirect()->route('admin.JurusanSekolah.show')->with('alert',$alert);
    }
}
