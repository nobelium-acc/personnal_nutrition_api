<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifie si l'utilisateur est authentifié et est un administrateur
        $user = Auth::user();

        if ($user && $user->isAdmin()) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Accès refusé. Vous devez être administrateur pour effectuer cette action.',
        ], 403);
    }
}
