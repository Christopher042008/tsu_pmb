<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\Parameter;
use App\Models\User\Pendaftaran;
use Yajra\DataTables\DataTables;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Session, Crypt, DB;
use Symfony\Component\HttpFoundation\Response;

class BerkasPMBController extends Controller
{
    public function index()
    {
        $data = array(
            'title' => 'Berkas PMB',
            'menu'  => 'Data Berkas PMB',
        );
        return view('admin::berkasPMB.index', $data);
    }

    public function tabelBerkasPMB()
    {
        $data = Pendaftaran::join('pmb_master_jenispendaftaran as a','pmb_pendaftaran.jalur_daftar','=','a.id')
        ->selectRaw('pmb_pendaftaran.*')
        ->whereRaw('a.berkas_khusus is not null') // AND pmb_pendaftaran.current_step=4
        ->with(['biodata','batch','jalur','jenisbeasiswa'])
        ->get();
        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('nama', function ($d) {
            return $d->biodata->nama;
        })
        ->addColumn('noreg', function ($d) {
            return $d->KodePendaftaran;
        })
        ->addColumn('batch', function ($d) {
            $nama = $d->batch->nama_batch;
            return $nama;
        })
        ->addColumn('jalur', function ($d) {
            $nama = $d->jalur->jenis_pendaftaran;
            return $nama;
        })
        ->addColumn('beasiswa', function ($d) {
            $nama = $d->jenisbeasiswa ? $d->jenisbeasiswa->jenis_beasiswa : '-';
            return $nama;
        })
        ->addColumn('bkskhusus', function ($d) {
            $params1 = Parameter::where('id',1)->first();
            if($d->berkas_khusus){
                $link = asset('sources/storage/app/'.$params1->file_khusus.'/'.$d->berkas_khusus);
                $warna = 'success';
                $status = $d->berkas_khusus;
                $target = 'target="_blank"';
            }else{
                $link = '#';
                $warna = 'warning';
                $status = 'Belum Upload';
                $target = '';
            }
            $show = '<a href="'.$link.'" '.$target.'><span class="badge bg-'.$warna.'">'.$status.'</span></a>';
            $update = '';
            if($d->update_berkaskhusus){
                $tgljm = explode(' ',$d->update_berkaskhusus);
                $tgl = tglIndo($tgljm[0]);
                $jam = explode(':',$tgljm[1]);
                $waktu = $jam[0].':'.$jam[1];
                $update = '<span class="badge bg-warning">Diperbarui pada : '.$tgl.' '.$waktu.'</span>';
            }
            return $show.$update;
        })
        ->addColumn('keterangan', function ($d) {

            $show = $d->keterangan ? $d->keterangan : '-';
            return $show;
        })
        ->addColumn('validator', function ($d) {
            if($d->validasi_berkas_khusus=='1'||$d->validasi_berkas_khusus=='-1'){
                $nik = '<span class="badge bg-success">'.$d->nik_validasi_berkas_khusus.'</span>';
                $nama = '<span class="badge bg-warning">'.namaku($d->nik_validasi_berkas_khusus).'</span>';
                $show = $nik.''.$nama;
            }else{
                $show = '-';
            }
            return $show;
        })
        ->addColumn('pindahjalur', function ($d) {
            if($d->status_pindah_jalur=='0'){
                $show = 'Menunggu Persetujuan';
            }elseif($d->status_pindah_jalur=='1'){
                $show = 'Pindah Jalur : ';
            }elseif($d->status_pindah_jalur=='-1'){
                $show = 'Ditolak Pendaftar';
            }else{
                $show = '-';
            }
            return $show;
        })
        ->addColumn('status', function ($d) {

            if($d->validasi_berkas_khusus=='0'){
                $status = 'Tahap Validasi';
                $warna = 'warning';
            }elseif($d->validasi_berkas_khusus=='1'){
                $status = 'Berkas OK';
                $warna = 'success';
            }elseif($d->validasi_berkas_khusus=='-1'){
                $status = 'Berkas Ditolak';
                $warna = 'danger';
            }else{
                $warna = 'warning';
                $status = 'Belum Upload Berkas';
            }
            $show = '<span class="badge bg-'.$warna.'">'.$status.'</span>';
            return $show;
        })
        ->addColumn('action', function ($d) {
            $id = encrypt($d->KodePendaftaran);

            $url = '#';
            $edit   = '';
            $aktif = '';
            $detail = '';
            if($d->validasi_berkas_khusus=='0'||$d->validasi_berkas_khusus=='-1'){
                $edit   = '<a href="#" data-id="'.$id.'" class="btn_approval"><i title="Approval Berkas" class="fa fa-edit text-orange"></i></a>';
            }
            // if($d->isactive==1){
            //     $aktif = '<a href="#" class="btn_delete" data-id="'.$id.'" data-status="'.encrypt('0').'"><i title="Hapus" class="fa fa-trash text-red"></i></a>';
            // }else{
            //     $aktif  = '<a href="#" class="btn_delete" data-id="'.$id.'" data-status="'.encrypt('1').'"><i title="Aktifkan" class="fas fa-check-circle text-green"></i></a>';
            // }
            // $detail = '<a href="#" data-id="'.$id.'" class="btn_detail"><i title="Detail" class="fa fa-info-circle"></i></a>';
            // $detail = '<a href="#" data-id="'.$id.'" class="btn_detail"><i class="fas fa-pencil"></i></a>';

            return $detail.' '.$edit.' '.$aktif;
        })
        ->rawColumns(['action','status','bkskhusus','validator'])
        ->make(true);
    }

    public function saveApprovalBerkas(Request $post)
    {
        // dd($post);
        $id = decrypt($post->kodedaftar);
        $cek = Pendaftaran::where('KodePendaftaran',$id)->where('current_step',5)->first(); //->where('validasi_berkas_khusus','0')
        $status = $post->status=='null' ? '0' : $post->status;
        $stepku = $cek->current_step;
        $keterangan = $post->keterangan;
        $pindahjalur = $post->pindahjalur=='0' ? null : '0';
        if($status=='1'){
            $stepku = $cek->current_step+1;
        }
        if(!$cek){
            $data['title'] = 'Gagal !';
            $data['message'] = 'Berkas Sudah divalidasi !';
            $data['status'] = 'error';
        }else{
            DB::beginTransaction();

            $updt = Pendaftaran::where('KodePendaftaran',$id)
            // ->where('isactive',1)
            // ->where('validasi_berkas_khusus','0')
            ->update([
                'validasi_berkas_khusus' => $status,
                'update_berkaskhusus' => null,
                'nik_validasi_berkas_khusus' => session('session')->nip,
                'tgl_validasi_berkas_khusus' => date('Y-m-d H:i:s'),
                'status_pindah_jalur' => $pindahjalur,
                'keterangan' => $keterangan,
                'current_step' => $stepku,
                'stop_step' => null,
                'updated_at' => date('Y-m-d H:i:s'),
                'isactive' => '1'
            ]);

            if($updt){
                DB::commit();
                $data['title'] = 'Berhasil !';
                $data['message'] = $status=='0' ? 'Keterangan Revisi Berkas Sudah Ditambahkan !' : 'Berhasil Validasi Berkas';
                $data['status'] = 'success';
            }else{
                DB::rollback();
                $data['title'] = 'Gagal !';
                $data['message'] = 'Gagal Validasi Berkas';
                $data['status'] = 'error';
            }
        }
        return response()->json($data, Response::HTTP_OK);
    }
}
