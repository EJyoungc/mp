<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->organization_id) {
            if ($user->organization_verify === 'pending') {
                if (! $request->is('waiting-approval') && ! $request->is('logout')) {
                    return redirect()->route('waiting-approval');
                }
            } elseif ($user->organization_verify === 'declined') {
                if (! $request->is('access-denied') && ! $request->is('logout')) {
                    return redirect()->route('access-denied');
                }
            }
        }

        return $next($request);
    }
}
