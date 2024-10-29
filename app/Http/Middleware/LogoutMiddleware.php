<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifiez si l'utilisateur est connecté
        if (Auth::check()) {
            // Déconnecter l'utilisateur
            Auth::logout();

            // Supprimer tous les tokens d'accès de l'utilisateur
            $user = Auth::user();
            $user->tokens()->delete();
        }

        // Rediriger ou répondre avec une confirmation de déconnexion
        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie',
        ], 200);
    }
}
