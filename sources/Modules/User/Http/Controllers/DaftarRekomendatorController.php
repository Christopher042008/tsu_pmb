<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_Rekomendator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Modules\Admin\Http\Controllers\masterdata\RekomendatorController;


class DaftarRekomendatorController extends Controller
{
    public function index()
    {
        $kategori = DB::table('pmb_master_kategori_rekomendator')->where('isactive', '1')->get();
        $data = array(
            'title' => 'Daftar Rekomendator',
            'menu' => 'Daftar Rekomendator',
            'kategori' => $kategori // Kirim ke View
        );
        return view('user::daftar_rekomendator.index',$data);
    }

    private function generateKodeRekomendator($kodeKategori)
    {
        $kategori = DB::table('pmb_master_kategori_rekomendator')->where('kode_kategori', $kodeKategori)->first();

        if (!$kategori) {
            return 'UNKNOWN0001';
        }

        $prefix = $kategori->kode_kategori;

        // PERBAIKAN: Cari murni berdasarkan awalan kode_rekomendator-nya saja (mengabaikan kolom kategori)
        // dan urutkan berdasarkan kodenya secara menurun (Z-A) agar dapat angka terbesar
        $lastData = Master_Rekomendator::where('kode_rekomendator', 'like', $prefix . '%')
                        ->orderBy('kode_rekomendator', 'desc')
                        ->first();

        if (!$lastData || empty($lastData->kode_rekomendator)) {
            $newNumber = 1;
        } else {
            $lastKode = $lastData->kode_rekomendator;
            $lastNumber = (int) substr($lastKode, strlen($prefix));
            $newNumber = $lastNumber + 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function save(Request $post)
    {
        // DB::beginTransaction();
        $kode = $this->generateKodeRekomendator($post->kategori);

        try {
            $arrayIn = array(
                'kode_rekomendator' => $kode,
                'kategori'          => $post->kategori,
                'nama_rekomendator' => $post->nama_rekomendator,
                'alamat'            => $post->alamat,
                'pekerjaan'         => $post->pekerjaan,
                'no_hp'             => $post->no_hp,
                'email'             => $post->email,
                'no_rekening'       => $post->no_rekening,
                'atasnama_rekening' => $post->atasnama_rekening,
                'nama_bank'         => $post->nama_bank,
                'created_at'        => date('Y-m-d H:i:s'),
                'created_by'        => 'System',
                'isactive'          => '0'
            );

            Master_Rekomendator::insert($arrayIn);
            DB::commit();

            $status = ['title' => 'Berhasil', 'status' => 'success', 'message' => 'Data Rekomendator Berhasil Disimpan'];
            return redirect()->route('indexing')->with('alert', $status);

            // return ['title' => 'Information', 'status' => 'success', 'message' => 'Data Berhasil Disimpan'];
        } catch (\Throwable $e) { // Menggunakan Throwable untuk tangkap semua error
            DB::rollback();
            $status = ['title' => 'Gagal', 'status' => 'error', 'message' => 'Data Rekomendator Gagal Disimpan. Error : '.$e];
            return redirect()->back()->with('alert', $status);
            // return ['title' => 'Error', 'status' => 'error', 'message' => 'Gagal menyimpan: ' . $e->getMessage()];
        }
    }
}
