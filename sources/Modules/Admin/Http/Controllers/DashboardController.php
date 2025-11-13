<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\MasterData\Master_Akun;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Session, Crypt, DB;

class DashboardController extends Controller
{
    public function index(){

        if(Session::has('tmp')){
            Session::forget('tmp');
        }
        $total = Master_Akun::where('isactive',1)->count();
        $validasi = Master_Akun::where('isactive',1)->where('verifikasi_email',1)->count();
        $aktif = Master_Akun::where('isactive',1)->where('verifikasi_email',1)->count();
        $blmvalidasi = Master_Akun::where('isactive',1)->where('verifikasi_email',0)->count();
        $data = array(
            'title' => 'Dashboard',
            'menu'  => 'dashboard',
            'total' => $total,
            'validasi' => $validasi,
            'aktif' => $aktif,
            'blmvalidasi' => $blmvalidasi
        );
        return view('admin::dashboard/dashboard', $data);
    }
}
