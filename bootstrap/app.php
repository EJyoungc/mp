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
        //
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'doctor'=> \App\Http\Middleware\DoctorAuth::class,
            'mother'=> \App\Http\Middleware\MotherAuth::class,
            'pract'=> \App\Http\Middleware\PractitionerAuth::class,
            'isOrginizationVerified'=> \App\Http\Middleware\IsOrginizationVerified::class,
            'admin'=> \App\Http\Middleware\AdminAuth::class,
            'System'=> \App\Http\Middleware\SystemAdminAuth::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
