<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class DoctorAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // $roles = ['admin']; // Define allowed roles
        // dd(Auth::check() , Auth::user()->role->name == "doctor");
        if (Auth::check() && Auth::user()->role->name == 'doctor') {
            // dd(Auth::check() && Auth::user()->role->name, 'passed');

            // dd($request->route()->getName());
            if ($request->route()->getName() === 'users.create') {
                // Perform specific logic for this route
                $roles = ['admin']; // Disallowed roles
                if (in_array($request->route('role'), $roles)) {
                    // dd($request->route('role'));
                    return redirect()->route('access-denied');
                }
            }

            // Check if the current route name is 'users.edit'
            if ($request->route()->getName() === 'users.edit') {
                // Perform specific logic for this route
                $roles = ['admin']; // Disallowed roles
                if (in_array($request->route('role'), $roles)) {
                    // dd($request->route('role'));
                    return redirect()->route('access-denied');
                }
            }

            return $next($request);
        } else {

            return redirect()->route('access-denied');
        }
    }
}
