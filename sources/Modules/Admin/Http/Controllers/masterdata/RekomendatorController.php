<?php

namespace Modules\Admin\Http\Controllers\masterdata;

use App\Models\MasterData\Master_Rekomendator;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;

class RekomendatorController extends Controller
{
    public function index()
    {
        $data = array(
            'title' => 'Master Rekomendator',
            'menu'  => 'Data Rekomendator',
        );
        return view('admin::masterdata.rekomendator.index', $data); // Sesuaikan path view-nya jika perlu
    }

    public function TabelRekomendator()
    {
        $data = Master_Rekomendator::orderBy('id', 'desc')->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('aktif', function ($d) {
                if ($d->isactive == 1) {
                    return '<span class="badge bg-success">Aktif</span>';
                } else {
                    return '<span class="badge bg-danger">Tidak Aktif</span>';
                }
            })
            ->addColumn('action', function ($d) {
                $id = encrypt($d->id);
                $edit = '<a href="#" data-id="'.$id.'" class="btn_edit"><i title="Edit" class="fa fa-edit text-orange"></i></a>';
                
                if ($d->isactive == 1) {
                    $aktif = '<a href="#" class="btn_delete" data-id="'.$id.'" data-status="'.encrypt('0').'"><i title="Nonaktifkan" class="fa fa-trash text-red"></i></a>';
                } else {
                    $aktif = '<a href="#" class="btn_delete" data-id="'.$id.'" data-status="'.encrypt('1').'"><i title="Aktifkan" class="fas fa-check-circle text-green"></i></a>';
                }
                
                return $edit . ' ' . $aktif;
            })
            ->rawColumns(['action', 'aktif'])
            ->make(true);
    }

    private function generateKodeRekomendator()
    {
        // Cari data terakhir berdasarkan ID
        $lastData = Master_Rekomendator::orderBy('id', 'desc')->first();

        // Jika belum ada data sama sekali, mulai dari 0001
        if (!$lastData || empty($lastData->kode_rekomendator)) {
            return 'REKOMTSU0001';
        }

        // Ambil string kode terakhir (contoh: REKOMTSU0005)
        $lastKode = $lastData->kode_rekomendator;

        // Ambil angka saja (memotong 8 karakter pertama 'REKOMTSU')
        $lastNumber = (int) substr($lastKode, 8);

        // Tambah 1
        $newNumber = $lastNumber + 1;

        // Gabungkan lagi dengan format REKOMTSU + 4 digit angka (isi angka 0 di depan jika kurang dari 4 digit)
        $newKode = 'REKOMTSU' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        return $newKode;
    }

    public function StoreRekomendator(Request $post)
    {
        if ($post->IdRekomendator == null) {
            // Logika Insert: Generate Kode Otomatis dan sisipkan ke dalam $post
            $post->merge([
                'kode_rekomendator' => $this->generateKodeRekomendator()
            ]);

            // Tidak perlu cek duplikasi kode lagi karena sudah di-generate otomatis
            return $this->save($post);
        } else {
            // Logika Update
            $id = decrypt($post->IdRekomendator);
            $cek = Master_Rekomendator::where('id', '!=', $id)
                ->where('isactive', 1)
                ->where('kode_rekomendator', $post->kode_rekomendator)
                ->exists();

            if ($cek) {
                return ['title' => 'Information', 'status' => 'warning', 'message' => 'Kode Rekomendator Sudah Digunakan!'];
            }

            return $this->update($post, $id);
        }
    }

    public function save($post)
    {
        DB::beginTransaction();
        try {
            $arrayIn = array(
                'kode_rekomendator' => $post->kode_rekomendator,
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
                'created_by'        => session('session')->nip ?? 'System', // Sesuaikan auth/session kamu
                'isactive'          => $post->status
            );

            Master_Rekomendator::insert($arrayIn);
            DB::commit();

            return ['title' => 'Information', 'status' => 'success', 'message' => 'Data Berhasil Disimpan'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['title' => 'Error', 'status' => 'error', 'message' => 'Gagal menyimpan: ' . $e->getMessage()];
        }
    }

    public function update($post, $id)
    {
        DB::beginTransaction();
        try {
            $arrayIn = array(
                'kode_rekomendator' => $post->kode_rekomendator,
                'kategori'          => $post->kategori,
                'nama_rekomendator' => $post->nama_rekomendator,
                'alamat'            => $post->alamat,
                'pekerjaan'         => $post->pekerjaan,
                'no_hp'             => $post->no_hp,
                'email'             => $post->email,
                'no_rekening'       => $post->no_rekening,
                'atasnama_rekening' => $post->atasnama_rekening,
                'nama_bank'         => $post->nama_bank,
                'updated_at'        => date('Y-m-d H:i:s'),
                'updated_by'        => session('session')->nip ?? 'System',
                'isactive'          => $post->status
            );

            Master_Rekomendator::where('id', $id)->update($arrayIn);
            DB::commit();

            return ['title' => 'Information', 'status' => 'success', 'message' => 'Data Berhasil Diupdate'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['title' => 'Error', 'status' => 'error', 'message' => 'Gagal update: ' . $e->getMessage()];
        }
    }

    public function ShowRekomendator($params)
    {
        $id = decrypt($params);
        $cek = Master_Rekomendator::find($id);

        if ($cek) {
            $master['hasil'] = 1;
            $master['data'] = $cek;
            $master['IdRekomendator'] = $params;
        } else {
            $master['hasil'] = 0;
            $master['data'] = null;
            $master['IdRekomendator'] = $params;
        }
        return response()->json($master, Response::HTTP_OK);
    }

    public function delete($params1, $params2)
    {
        $id = decrypt($params1);
        $aktif = decrypt($params2);
        DB::beginTransaction();
        try {
            Master_Rekomendator::where('id', $id)->update([
                'isactive' => $aktif,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => session('session')->nip ?? 'System'
            ]);
            
            DB::commit();
            $pesan = $aktif == '1' ? 'Berhasil Mengaktifkan Data' : 'Berhasil Menonaktifkan Data';
            return response()->json(['title' => 'Information', 'message' => $pesan, 'type' => 'success'], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['title' => 'Error', 'message' => 'Gagal mengubah status data', 'type' => 'error'], Response::HTTP_OK);
        }
    }
}