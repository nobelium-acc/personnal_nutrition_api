<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Assurer que l'utilisateur est authentifié via un token
        if (Auth::check() && $request->user()->identifiant === env('ADMIN_USERNAME')) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Accès non autorisé',
        ], 403);
    }
}
