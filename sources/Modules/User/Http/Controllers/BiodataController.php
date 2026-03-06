<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_Batch;
use App\Models\MasterData\Master_JenisBerkas;
use App\Models\MasterData\Master_Kabupaten;
use App\Models\MasterData\Master_Kecamatan;
use App\Models\MasterData\Master_Kelurahan;
use App\Models\MasterData\Master_Provinsi;
use App\Models\Parameter;
use App\Models\User\Biodata;
use App\Models\User\Pendaftaran;
use App\Models\User\Saudara;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use Session, Crypt, DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use Modules\Admin\Http\Controllers\masterdata\JenisBerkasController;

class BiodataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bioId = decrypt(session('user')->_biodata);
        $bio = Biodata::where('biodata_id',$bioId)->first();
        $provinsi = Master_Provinsi::where('isactive',1)->get();
        $kabupaten = Master_Kabupaten::where('idprov',$bio->provinsi)->where('isactive',1)->get();
        $kecamatan = Master_Kecamatan::where('idprov',$bio->provinsi)->where('idkab',$bio->kabupaten)->where('isactive',1)->get();
        $now = date('Y-m-d');
        // $batch = Master_Batch::where('isactive',1)->whereRaw('? BETWEEN tglmulai and tglselesai',[$now])->first();
        // $datadaftar = Pendaftaran::where('biodata_id',$bioId)->where('batch_daftar',$batch->id)->first();
        $datadaftar = Pendaftaran::where('biodata_id',$bioId)->orderby('created_at','desc')->first();
        $berkas = Master_JenisBerkas::where('jenis_berkas','Umum')->where('kategori','0')->with('berkas')->first();
        $data = array(
            'title' => 'Biodata',
            'menu' => 'Biodata',
            'bio'  => $bio,
            'provinsi' => $provinsi,
            'kabupaten' => $kabupaten,
            'kecamatan' => $kecamatan,
            'datadaftar' => $datadaftar,
            'berkas' => $berkas
        );
        return view('user::user.biodata.index',$data);
    }

    public function ChangeKabupaten($prov)
    {
        $kabupaten = Master_Kabupaten::where('idprov',$prov)->where('isactive',1)->get();

        if(count($kabupaten)>0){
            $data['hasil'] = 1;
            $data['kabupaten'] = $kabupaten;
        }else{
            $data['hasil'] = 0;
            $data['kabupaten'] = $kabupaten;
        }
        return response()->json($data, Response::HTTP_OK);
    }

    public function ChangeKecamatan($prov,$kab)
    {
        $kecamatan = Master_Kecamatan::where('idprov',$prov)->where('idkab',$kab)->where('isactive',1)->get();

        if(count($kecamatan)>0){
            $data['hasil'] = 1;
            $data['kecamatan'] = $kecamatan;
        }else{
            $data['hasil'] = 0;
            $data['kecamatan'] = $kecamatan;
        }
        return response()->json($data, Response::HTTP_OK);
    }

    public function ChangeKelurahan($prov,$kab,$kec)
    {
        $kelurahan = Master_Kelurahan::where('idprov',$prov)->where('idkab',$kab)->where('idkec',$kec)->where('isactive',1)->get();

        if(count($kelurahan)>0){
            $data['hasil'] = 1;
            $data['kelurahan'] = $kelurahan;
        }else{
            $data['hasil'] = 0;
            $data['kelurahan'] = $kelurahan;
        }
        return response()->json($data, Response::HTTP_OK);
    }

    public function save_biodata(Request $post)
    {
        $jmlsaudara = $post->jumlah_saudara;
        if($jmlsaudara!=count($post->nama_saudara)){
            return redirect()->back()->with('alert',['title' => 'Information', 'message' => 'Jumlah saudara harus sama dengan data saudara !', 'status' => 'warning']);
        }

        $cek = Pendaftaran::where('KodePendaftaran',$post->kodedaftar)->first();
        $file = $post->file('berkasumum');
        $ext = $file->getClientOriginalExtension();
        $filename = 'BERKAS_UMUM_'.$cek->KodePendaftaran.'_'.date('YmdHis').'.'.$ext;

        $bioId = decrypt(session('user')->_biodata);
        $cek1 = Biodata::where('biodata_id',$bioId)->where('isactive',1)->first();
        // dd($post->pekerjaan_saudara[0],$post,$file,$jmlsaudara>0);

        $parameter = Parameter::where('id',1)->first();
        if($cek1->berkas_umum!=null){
            $path = $parameter->file_umum.'/'.$cek1->berkas_umum;
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        }

        DB::beginTransaction();

        $databio = array(
            'nik' => $post->nik,
            'nokk' => $post->nokk,
            'nama' => $post->nama,
            'nohp' => $post->nohp,
            'jenkel' => $post->jenkel,
            'tempat_lahir' => $post->tempat_lahir,
            'tgl_lahir' => $post->tgl_lahir,
            // 'tinggi_badan' => $post->tinggi_badan,
            // 'berat_badan' => $post->berat_badan,
            'agama' => $post->agama,
            'ukuran_jas' => $post->ukuran_jas,
            'provinsi' => $post->provinsi,
            'kabupaten' => $post->kabupatenkota,
            'kecamatan' => $post->kecamatan,
            'kelurahan' => $post->kelurahan,
            'alamat_lengkap' => $post->alamat_lengkap,
            'rt' => $post->rt,
            'rw' => $post->rw,
            'kodepos' => $post->kodepos,
            'nama_ayah' => $post->nama_ayah,
            'tempat_lahir_ayah' => $post->tempat_lahir_ayah,
            'tgl_lahir_ayah' => $post->tgl_lahir_ayah,
            'status_ayah' => $post->status_ayah,
            'statushidup_ayah' => $post->statushidup_ayah,
            'nohp_ayah' => $post->nohp_ayah,
            'pekerjaan_ayah' => $post->pekerjaan_ayah,
            'penghasilan_ayah' => $post->penghasilan_ayah,
            'alamat_ayah' => $post->alamat_ayah,
            'nama_ibu' => $post->nama_ibu,
            'tempat_lahir_ibu' => $post->tempat_lahir_ibu,
            'tgl_lahir_ibu' => $post->tgl_lahir_ibu,
            'status_ibu' => $post->status_ibu,
            'statushidup_ibu' => $post->statushidup_ibu,
            'nohp_ibu' => $post->nohp_ibu,
            'pekerjaan_ibu' => $post->pekerjaan_ibu,
            'penghasilan_ibu' => $post->penghasilan_Ibu,
            'alamat_ibu' => $post->alamat_ibu,
            'jumlah_saudara' => $jmlsaudara,
            'nama_sekolah' => $post->nama_sekolah,
            'jenis_sekolah' => $post->jenis_sekolah,
            'provinsi_sekolah' => $post->provinsi_sekolah,
            'kabupaten_sekolah' => $post->kabupatenkota_sekolah,
            'npsn' => $post->npsn,
            'nisn' => $post->nisn,
            'nilai_akhir' => $post->nilai_akhir,
            'berkas_umum' => $filename,
            'updated_at' => now()
        );

        $upbio = Biodata::where('biodata_id',$bioId)->where('isactive',1)->update($databio);

        $updaftar = Pendaftaran::where('KodePendaftaran',$post->kodedaftar)->update([
            'current_step' => $cek->current_step+1,
            'tahun_lulus' => $post->tahun_lulus,
            'updated_at' => now()
        ]);

        $count = 0;
        if($jmlsaudara>0){
            $cek2 = Saudara::where('bio_id',$bioId);
            if($cek2->exists()){
                $cek2->delete();
            }
            foreach ($post->nama_saudara as $key => $p) {
                $saudara = array(
                    'bio_id' => $bioId,
                    'nama' => $p,
                    'pekerjaan' => $post->pekerjaan_saudara[$key],
                    'status_hidup' => $post->statushidup_saudara[$key],
                    'status_kekerabatan' => $post->statuskekerabatan_saudara[$key],
                    'created_at' => now()
                );
                $ups = Saudara::insert($saudara);
                if($ups){
                    $count++;
                }
            }
        }

        if($upbio&&$updaftar&&$count==$jmlsaudara){
            $file->storeAs($parameter->file_umum, $filename);
            DB::commit();
            return redirect()->route('Dashboard')->with('alert',['title' => 'Berhasil', 'message' => 'Update Biodata Berhasil !', 'status' => 'success']);
        }else{
            DB::rollback();
            return redirect()->back()->with('alert',['title' => 'Error', 'message' => 'Update Biodata Gagal ! Silahkan Input Kembali', 'status' => 'error']);
        }
    }
}
