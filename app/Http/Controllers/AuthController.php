<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Utilisateur;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'mot_de_passe' => 'required|min:10',
        ]);

        $utilisateur = Utilisateur::where('email', $request->email)->first();

        if (!$utilisateur || !Hash::check($request->mot_de_passe, $utilisateur->mot_de_passe)) {
            return response()->json([
                'success' => false,
                'message' => 'Identifiants invalides',
            ], 401);
        }

        $token = $utilisateur->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'token' => $token,
            'data' => $utilisateur,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/posts",
     *     tags={"Posts"},
     *     summary="Get list of posts",
     *     description="Returns all posts",
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
    */
    public function register(Request $request)
    {
        $request->validate([
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

        $utilisateur = Utilisateur::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'age' => $request->age,
            'sexe' => $request->sexe,
            'poids' => $request->poids,
            'taille' => $request->taille,
            'email' => $request->email,
            'mot_de_passe' => Hash::make($request->mot_de_passe),
            'tour_de_taille' => $request->tour_de_taille,
            'tour_de_hanche' => $request->tour_de_hanche,
            'tour_du_cou' => $request->tour_du_cou,
            'niveau_d_activite_physique' => $request->niveau_d_activite_physique,
        ]);

        $token = $utilisateur->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur enregistré avec succès',
            'token' => $token,
            'data' => $utilisateur,
        ], 201);
    }

    // Autres méthodes...
}