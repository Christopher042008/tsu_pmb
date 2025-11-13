<?php

namespace Modules\Admin\Http\Controllers\masterdata;

use App\Models\MasterData\Master_Berkas;
use App\Models\MasterData\Master_JenisBerkas;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Session, Crypt, DB;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class JenisBerkasController extends Controller
{
    public function index()
    {
        $data = array(
            'title'  => 'Master Jenis Berkas',
            'menu'   => 'Jenis Berkas',
        );
        return view('admin::masterdata.jenisberkas.index', $data);
    }

    public function TabelJenisBerkas()
    {
        $data = Master_JenisBerkas::with('berkas')->where('isactive',1)->get();
        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('jenis', function ($d) {
            return $d->jenis_berkas;
        })
        ->addColumn('kategori', function ($d) {
            $role = $d->kategori==1 ? 'Khusus' : 'Umum';
            $show = '<span class="badge bg-warning">'.$role.'</span>';
            return $show;
        })
        ->addColumn('jumlah', function ($d) {
            $id = encrypt($d->id);
            $jumlah = '<a href="#" class="detail_berkas" data-id="'.$id.'" title="Detail Jenis Berkas '.$d->jenis_berkas.'"><span class="badge bg-success">'.count($d->berkas).' Berkas</span></a>';
            return $jumlah;

        })
        ->addColumn('keterangan', function ($d) {
            $nama = $d->keterangan;
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
            $detail = '';
            if($d->isactive==1){
                $aktif = '<a href="#" class="btn_delete" data-id="'.$id.'" data-status="'.encrypt('0').'"><i title="Hapus" class="fa fa-trash text-red"></i></a>';
            }else{
                $aktif  = '<a href="#" class="btn_delete" data-id="'.$id.'" data-status="'.encrypt('1').'"><i title="Aktifkan" class="fas fa-check-circle text-green"></i></a>';
            }
            // $detail = '<a href="#" data-id="'.$id.'" class="btn_detail"><i title="Detail" class="fa fa-info-circle"></i></a>';

            return $detail.' '.$edit.' '.$aktif;
        })
        ->rawColumns(['action','aktif','jumlah','kategori'])
        ->make(true);
    }

    public function StoreJenisBerkas(Request $post)
    {
        if($post->IdJenisBerkas==null){
            $cek = Master_JenisBerkas::where('isactive', 1)
            ->where(function($q) use ($post) {
                $q->where('jenis_berkas', $post->jenisberkas);
                // ->orWhere('jenis_berkas', $post->batch)
            })
            ->exists();
            if($cek){
                $data['title']  = 'Information';
                $data['status'] = 'warning';
                $data['message'] = 'Nama Jenis berkas Sudah Ada !';
            }else{
                $data = $this->save($post);
            }
        }else{
            $cek = Master_JenisBerkas::where('isactive', 1)->where('id','!=',$post->IdJenisBerkas)
            ->where(function($q) use ($post) {
                $q->where('jenis_berkas', $post->jenisberkas);
                // ->orWhere('jenis_berkas', $post->batch)
            })
            ->exists();
            if($cek){
                $data['title']  = 'Information';
                $data['status'] = 'warning';
                $data['message'] = 'Nama Jenis berkas Sudah Ada !';
            }else{
                $data = $this->update($post);
            }
        }
        return response()->json($data, Response::HTTP_OK);
        // return $data;
    }

    public function save($post)
    {
        DB::beginTransaction();

        $arrayIn = array(
            'jenis_berkas' => $post->jenisberkas,
            'kategori' => $post->kategori,
            'keterangan'   => $post->keterangan,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => session('session')->nip

        );

        $batch = Master_JenisBerkas::insert($arrayIn);

        if(!$batch){
            DB::rollback();
            $data['title']  = 'Information';
            $data['status'] = 'error';
            $data['message'] = 'Jenis Berkas Gagal Tersimpan';
        }else{
            DB::commit();
            $data['title']  = 'Information';
            $data['status'] = 'success';
            $data['message'] = 'Jenis Berkas Tersimpan ';
        }
        return $data;
    }

    public function ShowJenisBerkas($params)
    {
        $id = decrypt($params);
        $cek = Master_JenisBerkas::where('isactive',1)->where('id',$id)->with('berkas')->first();

        if($cek){
            $master['hasil']  = 1;
            $master['jenisberkas'] = $cek;
            $master['IdJenisBerkas'] = $params;
        }else{
            $master['hasil'] = 0;
            $master['jenisberkas'] = null;
            $master['IdJenisBerkas'] = $params;
        }
        return response()->json($master, Response::HTTP_OK);
    }

    public function update($post)
    {
        $id = decrypt($post->IdJenisBerkas);
        DB::beginTransaction();

        $arrayIn = array(
            'jenis_berkas' => $post->jenisberkas,
            'kategori' => $post->kategori,
            'keterangan'   => $post->keterangan,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => session('session')->nip

        );

        $batch = Master_JenisBerkas::where('id',$id)->update($arrayIn);

        if(!$batch){
            DB::rollback();
            $data['title']  = 'Information';
            $data['status'] = 'error';
            $data['message'] = 'Update Jenis Berkas Gagal';
        }else{
            DB::commit();
            $data['title']  = 'Information';
            $data['status'] = 'success';
            $data['message'] = 'Update Jenis Berkas Berhasil ';
        }
        return $data;
    }

    public function delete($params1,$params2)
    {
        $id = decrypt($params1);
        $aktif = decrypt($params2);
        DB::beginTransaction();
        $up = array(
            'isactive' => $aktif,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => session('session')->nip
        );

        $update = Master_JenisBerkas::where('id',$id)->update($up);

        $kata = $aktif=='1' ? 'Berhasil Mengaktifkan Data': 'Berhasil Menghapus Data';
        $del = $aktif=='1' ? 'Gagal Mengaktifkan Data': 'Gagal Menghapus Data';

        if($update){
            DB::commit();
            $master['message'] = $kata;
            $master['type'] = 'success';
        }else{
            DB::rollback();
            $master['message'] = $del;
            $master['type'] = 'error';
        }
        return response()->json($master, Response::HTTP_OK);
        // return redirect()->route('admin.Test.show')->with('alert',$alert);
    }
}
