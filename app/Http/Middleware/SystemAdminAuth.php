<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SystemAdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // dd(Auth::user()->role->name);
        if (Auth::check() && Auth::user()->role->name == 'system-admin') {
            return $next($request);
        } else {
            return redirect()->route('access-denied');
        }
    }
}
