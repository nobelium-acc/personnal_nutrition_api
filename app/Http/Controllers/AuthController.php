<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Utilisateur;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Authentification"},
     *     summary="Login an user account",
     *     description="Register a new user by providing the required information",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="mot_de_passe", type="string", format="password", example="password123"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     * )
    */
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
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Authentification"},
     *     summary="Create an user account",
     *     description="Register a new user by providing the required information",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="nom", type="string", example="HUGOSS"),
     *             @OA\Property(property="prenom", type="string", example="Henry"),
     *             @OA\Property(property="age", type="integer", example="12"),
     *             @OA\Property(property="sexe", type="string", example="Homme"),
     *             @OA\Property(property="poids", type="float", example="70"),
     *             @OA\Property(property="taille", type="float", example="171"),
     *             @OA\Property(property="tour_de_taille", type="float", example="34"),
     *             @OA\Property(property="tour_de_hanche", type="float", example="25"),
     *             @OA\Property(property="tour_du_cou", type="float", example="20"),
     *             @OA\Property(property="niveau_d_activite_physique", type="string", example="Haut"),
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="mot_de_passe", type="string", format="password", example="password123"),
     *             @OA\Property(property="mot_de_passe_confirmation", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
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