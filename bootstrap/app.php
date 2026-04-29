<?php

use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\DoctorAuth;
use App\Http\Middleware\EnsureUserIsApproved;
use App\Http\Middleware\IsOrginizationVerified;
use App\Http\Middleware\MotherAuth;
use App\Http\Middleware\PractitionerAuth;
use App\Http\Middleware\RestrictPharmacyAdminMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SystemAdminAuth;
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
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'doctor' => DoctorAuth::class,
            'mother' => MotherAuth::class,
            'pract' => PractitionerAuth::class,
            'isOrginizationVerified' => IsOrginizationVerified::class,
            'admin' => AdminAuth::class,
            'System' => SystemAdminAuth::class,
            'approved' => EnsureUserIsApproved::class,
            'notPharmacyAdmin' => RestrictPharmacyAdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
