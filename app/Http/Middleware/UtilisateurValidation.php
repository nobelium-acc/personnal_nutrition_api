<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Support\Facades\Validator;

class UtilisateurValidation
{
    public function handle(Request $request, Closure $next): JsonResponse
    {
        $route = $request->route()->getName();

        if ($route === 'utilissateur.store') {
            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'age' => 'required|integer|min:16',
                'sexe' => 'required|string|max:10',
                'poids' => 'required|numeric',
                'taille' => 'required|numeric',
                'email' => 'required|string|email|max:255|unique:utilisateurs',
                'mot_de_passe' => 'required|string|min:10',
                'tour_de_taille' => 'required|numeric',
                'tour_de_hanche' => 'required|numeric',
                'tour_du_cou' => 'required|numeric',
                'niveau_d_activite_physique' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur d\'inscription. Veuillez revoir vos champs.',
                    'errors' => $validator->errors(),
                ], 400);
            }

            // Si toutes les validations passent, on continue le traitement
            return $next($request);
        }

        if ($route === 'utilisateur.login') {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
                'mot_de_passe' => 'required|string|min:10',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de connexion. Veuillez revoir vos champs.',
                    'errors' => $validator->errors(),
                ], 400);
            }

            // Si la validation passe, on continue le traitement
            return new JsonResponse($next($request));
        }

        // Pour les autres routes, on continue simplement
        return $next($request);
    }
}
