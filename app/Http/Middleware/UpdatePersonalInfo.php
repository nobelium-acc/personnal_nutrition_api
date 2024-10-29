<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class UpdatePersonalInfo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
        // Récupérer l'utilisateur authentifié
        $user = Auth::user();

        // Vérifier si l'utilisateur essaie de modifier son propre email ou mot de passe
        if ($request->isMethod('put') || $request->isMethod('patch')) {
            // Vérifier si les champs modifiés concernent l'email ou le mot de passe
            if ($request->has('email') || $request->has('mot_de_passe')) {
                // Empêcher les utilisateurs de modifier les informations d'autres utilisateurs
                if ($request->route('id') != $user->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Vous n\'êtes pas autorisé à modifier les informations d\'autres utilisateurs',
                    ], 403);
                }
            }
        }
        return $next($request);
    }
}
