<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_TarifUKT;
use App\Models\Parameter;
use App\Models\Transaksi;
use App\Models\TransaksiHistory;
use App\Models\User\Pendaftaran;
use App\Models\MasterData\Master_JenisPendaftaran;
use Symfony\Component\HttpFoundation\Response;
use Session, Crypt, DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Midtrans\Snap;
use Illuminate\Support\Facades\Storage;

use Midtrans\Config;
use Midtrans\Transaction;
use Yajra\DataTables\DataTables;


use Illuminate\Http\Request;

class PembayaranUKTController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = array(
            'title' => 'Pembayaran UKT',
            'menu' => 'Pembayaran UKT'
        );
        return view('user::user.pembayaranukt.index',$data);
    }

    public function tabelPembayaran()
    {
        $bioId = decrypt(session('user')->_biodata);
        $data = Transaksi::where('user_id',$bioId)->where('kategori','ukt')
        // ->with(['biodata','batch','jalur','jenisbeasiswa','jurusansekolah',
        // 'prodi1'=>function($q){
        //     $q->with('jenjang');
        // },
        // 'prodi2'=>function($q){
        //     $q->with('jenjang');
        // },
        // 'waktukuliah'])
        ->with(['pendaftaran'=>function($q){
            $q->with(['jurusan_acc'=>function($w){
                $w->with('jenjang');
            }]);
        }
        ])
        ->get();
        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('nama', function ($d) {
            return session('user')->nama;
        })
        ->addColumn('noreg', function ($d) {
            return $d->id_referensi;
        })
        ->addColumn('jenis', function ($d) {
            return $d->kategori;
        })
        ->addColumn('jurusanditerima', function ($d) {
            return $d->pendaftaran->jurusan_acc->jenjang->jenjang.' - '.$d->pendaftaran->jurusan_acc->jurusan;
        })
        ->addColumn('nominal', function ($d) {
            $nama = rupiah($d->jumlah) ;
            return $nama;
        })
        ->addColumn('status', function ($d) {
            $role = $d->status;
            $warna = 'warning';
            if($role=='pending'||$role=='waiting'){
                $warna = 'warning';
            }elseif($role=='paid'){
                $warna = 'success';
            }else{
                $warna = 'danger';
            }
            $show = '<span class="badge bg-'.$warna.'">'.$role.'</span>';
            return $show;
        })
        ->addColumn('keterangan', function ($d) {
            $nama = '-';
            if($d->keterangan){
                $nama = $d->keterangan;
            }
            return $nama;
        })
        ->addColumn('action', function ($d) {
            $id = encrypt($d->id);
            $daftarId = encrypt($d->id_referensi);

            $show = '';
            $detail = '';
            $bayar = '';
            $show = '';
            if($d->status!='paid'){
                $bayar = '<a href="#" data-id="'.$id.'" class="btn_bayar"><i title="Bayar Sekarang" class="fas fa-money-bill text-green"></i></a>';
            }
            $detail = '<a href="#" data-id="'.$id.'" data-daftarid="'.$daftarId.'" class="btn_detail"><i title="Detail" class="fa fa-info-circle"></i></a>';
            if($d->bukti_pembayaran){
                $params1 = Parameter::where('id',1)->first();
                $linkkhusus = asset('sources/storage/app/'.$params1->bukti_bayar_ukt.'/'.$d->bukti_pembayaran);
                $show = '<a href="'.$linkkhusus.'" target="_blank"><i title="Lihat Bukti Pembayaran UKT" class="fa fa-eye"></i></a>';
            }

            return $detail.' '.$bayar.' '.$show;
        })
        ->rawColumns(['action','status'])
        ->make(true);
    }

    public function showPayment($params)
    {
        $id = decrypt($params);
        $bioId = decrypt(session('user')->_biodata);
        //->where('isactive',1)
        $cek1 = Pendaftaran::where('KodePendaftaran',$id)->where('biodata_id',$bioId)->with(['biodata','batch',
        'jalur'
        // =>function($q){
        //     $q->with(['berkasumum'=>function($q){
        //             $q->with('berkas');
        //         },

        //     'berkaskhusus'=>function($q){
        //             $q->with('berkas');
        //         }
        //     ]);
        // }
        ,
        'jenisbeasiswa'=>function($q){
            $q->with('tingkat');
        },
        'jurusansekolah',
        'prodi1'=>function($q){
            $q->with('jenjang');
        },
        'prodi2'=>function($q){
            $q->with('jenjang');
        },
        'waktukuliah','bayar'])->first();
        $prodi1 = Master_TarifUKT::where('idbatch',$cek1->batch_daftar)->where('idjalur',$cek1->jalur_daftar)->where('idjurusan',$cek1->prodi1->id)->where('isactive',1)->first();
        $prodi2 = Master_TarifUKT::where('idbatch',$cek1->batch_daftar)->where('idjalur',$cek1->jalur_daftar)->where('idjurusan',$cek1->prodi2->id)->where('isactive',1)->first();
        if($cek1){
            $data['hasil'] = 1;
            $data['daftar'] = $cek1;
            $data['IdDaftar'] = $params;
            $data['ukt1'] = $prodi1;
            $data['ukt2'] = $prodi2;
        }else{
            $data['hasil'] = 0;
            $data['daftar'] = $cek1;
            $data['IdDaftar'] = null;
            $data['ukt1'] = $prodi1;
            $data['ukt2'] = $prodi2;
        }
        return response()->json($data, Response::HTTP_OK);
    }

    public function upload_bayar(Request $post)
    {
        $id = decrypt($post->idtransaksi);
        $transaksi = Transaksi::findOrFail($id);
        // dd($transaksi);
        $file = $post->file('bukti_ukt');
        $ext = $file->getClientOriginalExtension();
        $filename = 'BUKTI_UKT_'.$transaksi->id_referensi.'_'.date('YmdHis').'.'.$ext;

        $parameter = Parameter::where('id',1)->first();

        $cek = Pendaftaran::where('KodePendaftaran',$transaksi->id_referensi)->where('biodata_id',$transaksi->user_id)->select('current_step','jalur_daftar')->first();

        if($transaksi->bukti_pembayaran==null){
            $transaksi->bukti_pembayaran = $filename;
            $transaksi->status = 'waiting';
            $transaksi->save();

            $step = $cek->current_step+1;
            $file->storeAs($parameter->bukti_bayar_ukt, $filename);
        }else{
            // if($transaksi->bukti_pembayaran!=null){
            $path = $parameter->bukti_bayar_ukt.'/'.$transaksi->bukti_pembayaran;
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
            // }
            $transaksi->bukti_pembayaran = $filename;
            $transaksi->save();

            $step = $cek->current_step;
            $file->storeAs($parameter->bukti_bayar_ukt, $filename);
        }

        Pendaftaran::where('KodePendaftaran',$transaksi->id_referensi)->where('biodata_id',$transaksi->user_id)->update([
            'current_step' => $step,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // dd($file);
        $alert = ['title' => 'Information', 'message' => 'Bukti Pembayaran Sudah diupload ! Silahkan Tunggu Konfirmasi Dari Admin PMB TSU.', 'status' => 'success'];
        return redirect()->back()->with('alert',$alert);

    }

    //tdk dipake
    public function PaymentUKT($params)
    {
        $id = decrypt($params);
        $transaksi = Transaksi::findOrFail($id);
        $orderID = generateOrderId($transaksi->id);
        if ($transaksi->status == 'paid') {
            $data['status'] = false;
            $data['message'] = 'Sudah dibayar';
        }else{

            $transaksi->status = 'paid';
            $transaksi->metode_bayar = 'bank_transfer';
            $transaksi->midtrans_order_id = $orderID;
            $transaksi->midtrans_snap_token = uniqid();
            $transaksi->expired_at = date('Y-m-d H:i:s',strtotime('+1 days'));
            $transaksi->save();

            TransaksiHistory::insert([
                'transaksi_id' => $transaksi->id,
                'status' => 'paid',
                'keterangan' => 'Pembayaran Lunas',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $cek = Pendaftaran::where('KodePendaftaran',$transaksi->id_referensi)->where('biodata_id',$transaksi->user_id)->select('current_step')->first();

            Pendaftaran::where('KodePendaftaran',$transaksi->id_referensi)->where('biodata_id',$transaksi->user_id)->update([
                'current_step' => $cek->current_step+1,
                'bayar_ukt' => '1',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $data['status'] = true;
            $data['message'] = 'Pembayaran Berhasil';
        }

        return response()->json($data, Response::HTTP_OK);

    }

    //tdk dipake
    public function PaymentPMBnext($params)
    {
        $id = decrypt($params);
        $transaksi = Transaksi::findOrFail($id);

        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = false; // true jika live
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $orderID = generateOrderId($transaksi->id);

        if ($transaksi->status == 'paid') {
            $data['status'] = false;
            $data['message'] = 'Sudah dibayar';
        }else{
            $params = [
                'transaction_details' => [
                    'order_id' => $orderID,
                    'gross_amount' => 1000, //$transaksi->nominal,
                ],
                'customer_details' => [
                    'first_name' => session('user')->nama,
                    'email' => session('user')->email,
                ],
                'enabled_payments' => ['bank_transfer','other_qris'] // ✅ VA + QRIS
            ];

            $snapToken = Snap::getSnapToken($params);
            // Simpan token & order_id
            $transaksi->midtrans_order_id = $params['transaction_details']['order_id'];
            $transaksi->midtrans_snap_token = $snapToken;
            $transaksi->save();

            $data['status'] = true;
            $data['snap_token'] = $snapToken;
        }

        return response()->json($data, Response::HTTP_OK);

    }

    //tdk dipake
    public function test_bayar(Request $post)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Contoh order untuk biaya pendaftaran
        $params = [
            'transaction_details' => [
                'order_id' => 'ORDER-' . uniqid(),
                'gross_amount' => 2500, // Rp250.000
            ],
            'customer_details' => [
                'first_name' => "Budi",
                'email' => "budi@example.com",
                'phone' => "08123456789",
            ],
            // Hanya tampilkan Virtual Account
            'enabled_payments' => ['bank_transfer','other_qris']
        ];

        $snapToken = Snap::getSnapToken($params);
        // dd($snapToken);
        return response()->json(['snap_token' => $snapToken]);
    }
}
