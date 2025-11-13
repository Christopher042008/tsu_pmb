<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Session;

class CheckUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!Session::has('user')){
            // Session::flash('alert', ['title' => 'Information', 'message' => 'Silahkan Login Kembali', 'status' => 'warning']);
            return redirect(route('LoginPMB'))->with('alert', ['title' => 'Information', 'message' => 'Silahkan Login Kembali', 'status' => 'warning']);
        }

        return $next($request);
    }
}
