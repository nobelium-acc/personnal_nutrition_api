<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Reponse;

class RetrieveUserResponses
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
        // Récupérer l'utilisateur ID depuis la requête (ou session si l'utilisateur est connecté)
        $utilisateurId = $request->input('utilisateur_id');

        if (!$utilisateurId) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé',
            ], 400);
        }

        // Récupérer les réponses données par l'utilisateur
        $reponses = Reponse::where('utilisateur_id', $utilisateurId)->get();

        if ($reponses->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune réponse trouvée pour cet utilisateur',
            ], 404);
        }

        // Attacher les réponses aux données de la requête pour utilisation ultérieure
        $request->merge(['user_reponses' => $reponses]);

        return $next($request);
    }
}
