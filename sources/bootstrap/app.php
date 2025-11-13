<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->alias([
        'checkuser' => \App\Http\Middleware\CheckUser::class,
        'checkadmin' => \App\Http\Middleware\CheckAdmin::class,
    ]);
    $middleware->append(HandleCors::class);
    $middleware->append(\App\Http\Middleware\AllowPrivateNetwork::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
