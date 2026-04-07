<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, $roles)) {
            abort(403, 'Unauthorized access.');
        }

        if (
            in_array('organizer', $roles, true)
            && $request->user()->role === 'organizer'
            && optional($request->user()->organizerProfile)->status !== 'verified'
        ) {
            return redirect()
                ->route('organizer.pending')
                ->with('error', 'Akun Event Organizer Anda belum diverifikasi admin.');
        }

        return $next($request);
    }
}
