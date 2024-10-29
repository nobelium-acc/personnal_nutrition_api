<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Commentaire;
use App\Models\Utilisateur;


class IdentifyCommentAuthor
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
        // Vérifiez si l'identifiant du commentaire est présent dans la requête
        if ($request->has('commentaire_id')) {
            // Récupérer le commentaire
            $commentaire = Commentaire::find($request->input('commentaire_id'));

            if ($commentaire) {
                // Récupérer l'utilisateur qui a écrit le commentaire
                $utilisateur = Utilisateur::find($commentaire->utilisateur_id);

                if ($utilisateur) {
                    // Ajouter les informations de l'utilisateur à la requête pour les utiliser dans le contrôleur
                    $request->merge([
                        'auteur_nom' => $utilisateur->nom,
                        'auteur_prenom' => $utilisateur->prenom,
                    ]);
                }
            }
        }

        return $next($request);
    }
}
