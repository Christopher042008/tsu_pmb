<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AllowPrivateNetwork
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Tambahkan header khusus untuk Chrome
        $response->headers->set('Access-Control-Allow-Private-Network', 'true');

        return $response;
    }
}
