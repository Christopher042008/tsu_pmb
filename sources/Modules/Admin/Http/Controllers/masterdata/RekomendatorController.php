<?php

namespace Modules\Admin\Http\Controllers\masterdata;

use App\Models\MasterData\Master_Rekomendator;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Admin\resources\views\masterdata\rekomendator\RekomendatorImport;
use Modules\Admin\resources\views\masterdata\rekomendator\RekomendatorTemplateExport;
use Modules\Admin\resources\views\masterdata\rekomendator\RekomendatorMigrationExport;

class RekomendatorController extends Controller
{
    public function index()
    {
        // Ambil data kategori dari tabel pmb_master_kategori_rekomendator
        $kategori = DB::table('pmb_master_kategori_rekomendator')->where('isactive', '1')->get();

        $data = array(
            'title'    => 'Master Rekomendator',
            'menu'     => 'Data Rekomendator',
            'kategori' => $kategori // Kirim ke View
        );
        return view('admin::masterdata.rekomendator.index', $data);
    }

    public function TabelRekomendator()
    {
        // PERBAIKAN: Join menggunakan kode_kategori
        $data = Master_Rekomendator::leftJoin('pmb_master_kategori_rekomendator', 'pmb_master_rekomendator.kategori', '=', 'pmb_master_kategori_rekomendator.kode_kategori')
            ->select('pmb_master_rekomendator.*', 'pmb_master_kategori_rekomendator.kategori_rekomendator as nama_kategori')
            ->orderBy('pmb_master_rekomendator.id', 'desc')
            ->get();
        return DataTables::of($data)
            ->addIndexColumn()
            // TAMBAHAN: Kolom baru khusus untuk menampilkan Nama Kategori di tabel
            ->addColumn('nama_kategori', function ($d) {
                // Jika data join ditemukan tampilkan namanya, jika tidak tampilkan data mentahnya
                return $d->nama_kategori ?? $d->kategori;
            })
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
                    $aktif = '<a href="#" class="btn_status" data-id="'.$id.'" data-status="'.encrypt('0').'" style="margin-left: 5px;"><i title="Nonaktifkan" class="fa fa-times text-warning"></i></a>';
                } else {
                    $aktif = '<a href="#" class="btn_status" data-id="'.$id.'" data-status="'.encrypt('1').'" style="margin-left: 5px;"><i title="Aktifkan" class="fa fa-check text-green"></i></a>';
                }

                $hapus = '<a href="#" data-id="'.$id.'" class="btn_destroy" style="margin-left: 5px;"><i title="Hapus Permanen" class="fa fa-trash text-red"></i></a>';

                return $edit . ' ' . $aktif . ' ' . $hapus;
            })
            ->rawColumns(['action', 'aktif'])
            ->make(true);
    }

    // PERBAIKAN: Parameter sekarang menerima teks kode kategori, bukan ID angka
    // Ubah parameternya untuk menerima kode_kategori
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

    public function StoreRekomendator(Request $post)
    {
        if ($post->IdRekomendator == null) {
            // Logika Insert
            $post->merge([
                'kode_rekomendator' => $this->generateKodeRekomendator($post->kategori)
            ]);

            return $this->save($post);
        } else {
            // Logika Update
            $id = decrypt($post->IdRekomendator);

            $oldData = Master_Rekomendator::find($id);
            $kodeRekomendator = $post->kode_rekomendator;

            // Jika kategori diubah, buatkan kode baru
            if ($oldData && $oldData->kategori != $post->kategori) {
                $kodeRekomendator = $this->generateKodeRekomendator($post->kategori);
            }

            $post->merge(['kode_rekomendator' => $kodeRekomendator]);

            // PERBAIKAN: Hapus pengecekan 'isactive' agar sistem mendeteksi kode yang kembar
            // baik di data yang aktif maupun yang sudah dinonaktifkan
            $cek = Master_Rekomendator::where('id', '!=', $id)
                ->where('kode_rekomendator', $post->kode_rekomendator)
                ->exists();

            if ($cek) {
                return [
                    'title' => 'Information',
                    'status' => 'warning',
                    'message' => 'Kode Rekomendator ('.$post->kode_rekomendator.') Sudah Digunakan!'
                ];
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
                'created_by'        => session('session')->nip ?? 'System',
                'isactive'          => $post->status
            );

            Master_Rekomendator::insert($arrayIn);
            DB::commit();

            return ['title' => 'Information', 'status' => 'success', 'message' => 'Data Berhasil Disimpan'];
        } catch (\Throwable $e) { // Menggunakan Throwable untuk tangkap semua error
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
        } catch (\Throwable $e) {
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

    public function importExcel(Request $request)
    {
        // Validasi file harus Excel
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        DB::beginTransaction();
        try {
            // Ambil file yang diupload
            $file = $request->file('file_excel');

            // Lakukan proses import menggunakan class yang ada di folder views tadi
            Excel::import(new RekomendatorImport, $file);

            DB::commit();
            return response()->json([
                'title' => 'Berhasil',
                'status' => 'success',
                'message' => 'Data Rekomendator berhasil diimport!'
            ]);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json([
                'title' => 'Gagal',
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy($params)
    {
        $id = decrypt($params);
        DB::beginTransaction();
        try {
            // Menghapus data secara permanen dari database
            Master_Rekomendator::where('id', $id)->delete();

            DB::commit();
            return response()->json([
                'title' => 'Berhasil!',
                'message' => 'Data Rekomendator berhasil dihapus permanen.',
                'status' => 'success'
            ]);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json([
                'title' => 'Gagal!',
                'message' => 'Gagal menghapus data. Pastikan data tidak sedang digunakan.',
                'status' => 'error'
            ]);
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new RekomendatorTemplateExport, 'Template_Import_Rekomendator.xlsx');
    }

    public function downloadMigrationTemplate()
    {
        return Excel::download(new RekomendatorMigrationExport, 'Template_Migrasi_Rekomendator.xlsx');
    }
}
