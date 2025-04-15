<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsOrginizationVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {


        // dd(Auth::user()->role->name == "system-admin");
        if (Auth::user()->role->name == "system-admin") {

            return $next($request);
        }else{

            if (Auth::user()->organization_id == null || Auth::user()->organization_verify == 'pending') {

                return redirect()->route('organizations.user.check');
            }
            return $next($request);

        }


    }
}
