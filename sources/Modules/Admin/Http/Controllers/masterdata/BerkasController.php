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

class BerkasController extends Controller
{
    public function index()
    {
        $jenis = Master_JenisBerkas::where('isactive',1)->get();
        $data = array(
            'title' => 'Master Berkas',
            'menu'  => 'Berkas',
            'jenis' => $jenis
        );
        return view('admin::masterdata.berkas.index', $data);
    }

    public function TabelBerkas()
    {
        $data = Master_Berkas::with('jenis')->where('isactive',1)->get();
        return DataTables::of($data)
        ->addIndexColumn()
        // ->addColumn('kode', function ($d) {
        //     return $d->KodeBerkas;
        // })
        ->addColumn('jenis', function ($d) {
            return $d->jenis->jenis_berkas;
        })
        ->addColumn('nama', function ($d) {
            return $d->nama_berkas;
        })
        ->addColumn('deskripsi', function ($d) {
            $nama = $d->deskripsi;
            return $nama;
        })
        ->addColumn('keterangan', function ($d) {
            $nama = $d->keterangan;
            $warna = '';
            if($nama == 'Wajib'){
                $warna = 'danger';
            }else{
                $warna = 'secondary';
            }
            $show = '<span class="badge bg-'.$warna.'">'.$nama.'</span>';
            return $show;
        })
        ->addColumn('format', function ($d) {
            $nama = $d->formatfile;
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
        ->rawColumns(['action','aktif','keterangan'])
        ->make(true);
    }

    public function StoreBerkas(Request $post)
    {
        // dd($post);

        if($post->IdBerkas==null){
            $cek = Master_Berkas::where('isactive', 1)
            ->where(function($q) use ($post) {
                $q->where('KodeBerkas', $post->kodebatch)
                ->orWhere('nama_berkas', $post->batch);
                // ->orWhere('jenis_berkas', $post->batch)
            })
            ->exists();
            if($cek){
                $data['title']  = 'Information';
                $data['status'] = 'warning';
                $data['message'] = 'Kode atau nama berkas Sudah Ada !';
            }else{
                $data = $this->save($post);
            }
        }else{
            $id = decrypt($post->IdBerkas);
            $cek = Master_Berkas::where('id','!=',$id)->where('isactive', 1)
            ->where(function($q) use ($post) {
                $q->where('KodeBerkas', $post->kodebatch)
                ->orWhere('nama_berkas', $post->batch);
                // ->orWhere('jenis_berkas', $post->batch)
            })
            ->exists();

            if($cek){
                $data['title']  = 'Information';
                $data['status'] = 'warning';
                $data['message'] = 'Kode atau nama berkas Sudah Ada !';
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

        $cek = Master_JenisBerkas::where('id',$post->jenis)->where('isactive',1)->first();

        $umum = '0';

        if(strtolower($cek->jenis_berkas)=='umum'){
            $umum = '1';
        }

        $form = $post->formatfile;
        $nama = '';
        if(count($form)==1){
            $nama = $form[0];
        }else{
            $nama = implode(', ',$form);
        }

        $last = Master_Berkas::latest('id')->first();

        $nextNumber = $last ? $last->id + 1 : 1;

        $kode = 'brks_' . $nextNumber;

        $arrayIn = array(
            'KodeBerkas' => $kode,
            'IdJenis' => $post->jenis,
            'nama_berkas' => $post->nama,
            'deskripsi' => $post->deskripsi,
            'keterangan'   => $post->status,
            'is_standard' => $umum,
            'formatfile' => $nama,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => session('session')->nip

        );

        $batch = Master_Berkas::insert($arrayIn);

        if(!$batch){
            DB::rollback();
            $data['title']  = 'Information';
            $data['status'] = 'error';
            $data['message'] = 'Data Berkas Gagal Tersimpan';
        }else{
            DB::commit();
            $data['title']  = 'Information';
            $data['status'] = 'success';
            $data['message'] = 'Data Berkas Tersimpan ';
        }
        return $data;
    }

    public function ShowBerkas($params)
    {
        $id = decrypt($params);
        $cek = Master_Berkas::where('isactive',1)->where('id',$id)->first();

        if($cek){
            $master['hasil']  = 1;
            $master['berkas'] = $cek;
            $master['IdBerkas'] = $params;
        }else{
            $master['hasil'] = 0;
            $master['berkas'] = null;
            $master['IdBerkas'] = $params;
        }
        return response()->json($master, Response::HTTP_OK);
    }

    public function update($post)
    {
        $id = decrypt($post->IdBerkas);
        DB::beginTransaction();

        $cek = Master_JenisBerkas::where('id',$post->jenis)->where('isactive',1)->first();

        $umum = '0';

        if(strtolower($cek->jenis_berkas)=='umum'){
            $umum = '1';
        }

        $form = $post->formatfile;
        $nama = '';
        if(count($form)==1){
            $nama = $form[0];
        }else{
            $nama = implode(', ',$form);
        }

        $arrayIn = array(
            // 'KodeBerkas' => $post->kodeberkas,
            'IdJenis' => $post->jenis,
            'nama_berkas' => $post->nama,
            'deskripsi' => $post->deskripsi,
            'keterangan'   => $post->status,
            'is_standard' => $umum,
            'formatfile' => $nama,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => session('session')->nip

        );

        $batch = Master_Berkas::where('id',$id)->update($arrayIn);

        if(!$batch){
            DB::rollback();
            $data['title']  = 'Information';
            $data['status'] = 'error';
            $data['message'] = 'Update Data Berkas Gagal';
        }else{
            DB::commit();
            $data['title']  = 'Information';
            $data['status'] = 'success';
            $data['message'] = 'Update Data Berkas Berhasil ';
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

        $update = Master_Berkas::where('id',$id)->update($up);

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
