<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',  
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register middleware aliases
        $middleware->alias([
            'auth.check' => \App\Http\Middleware\AuthCheck::class,
            'role' => \App\Http\Middleware\RoleCheck::class,
            'api.admin' => \App\Http\Middleware\ApiAdminAuth::class, // Tambahkan ini
            'api.superadmin' => \App\Http\Middleware\ApiSuperAdminAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();