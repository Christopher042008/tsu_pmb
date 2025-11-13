<?php

namespace App\Http\Middleware;

use App\Models\User\Pendaftaran;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Session;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!Session::has('session')){
            // Session::flash('alert', ['title' => 'Information', 'message' => 'Silahkan Login Kembali', 'status' => 'warning']);
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect(route('loginadmin'))->with('alert', ['title' => 'Information', 'message' => 'Silahkan Login Kembali', 'status' => 'warning']);
        }
        //Berkas Khusus
        $berkaskhusus = Pendaftaran::where('current_step',4)->where('isactive',1)->count(); //where('validasi_berkas_khusus','0')
        session(['notifapprovalberkaskhusus'=>$berkaskhusus]);
        //Test
        $test = Pendaftaran::where('validasi_test','0')->where('isactive',1)->count();
        session(['notifapprovaltest'=>$test]);
        return $next($request);
    }
}
