<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AuthController;

class UtilisateurController extends Controller
{
    public function __construct()
    {
        // Appliquer le middleware auth pour garantir que l'utilisateur est connecté
        $this->middleware('auth:api')->except(['login', 'store']); // Seules les méthodes login et store sont accessibles sans authentification
        $this->middleware('update.personal.info')->only('update');
        $this->middleware('utilisateur.validation')->only('store', 'login');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $utilisateurs = Utilisateur::all();
        return response()->json([
            'success' => true,
            'message' => 'Liste des utilisateurs récupérée avec succès',
            'data' => $utilisateurs,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Hacher le mot de passe avant de stocker l'utilisateur
        $hashedPassword = Hash::make($request->mot_de_passe);

        // Créer l'utilisateur avec les données et le mot de passe haché
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

        Auth::login($utilisateur);

        $token = $utilisateur->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur créé et authentifié avec succès',
            'data' => $utilisateur,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $utilisateur = Utilisateur::find($id);

        if (!$utilisateur) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur récupéré avec succès',
            'data' => $utilisateur,
        ], 200);
    }

    /**
     * Login an existing user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // La validation est gérée par le middleware UtilisateurValidation

        // Tentative d'authentification
        if (Auth::attempt(['email' => $request->email, 'password' => $request->mot_de_passe])) {
            $utilisateur = Auth::user();
            $token = $utilisateur->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie',
                'data' => $utilisateur,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Identifiants invalides',
        ], 401);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Vérifier si l'utilisateur est authentifié
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }

        $utilisateur = Utilisateur::find($id);

        if (!$utilisateur) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé',
            ], 404);
        }

        // Valider uniquement les champs soumis pour la mise à jour
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|string|email|max:255|unique:utilisateurs,email,' . $id,
            'mot_de_passe' => 'sometimes|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Mise à jour des informations personnelles
        if ($request->has('mot_de_passe')) {
            $request->merge(['mot_de_passe' => Hash::make($request->mot_de_passe)]);
        }

        $utilisateur->update($request->only(['email', 'mot_de_passe']));

        return response()->json([
            'success' => true,
            'message' => 'Informations personnelles mises à jour avec succès',
            'data' => $utilisateur,
        ], 200);
    }

    /**
     * Update the chronic illness of the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateMaladieChronique(Request $request, $id)
    {
        // Vérifier si l'utilisateur est authentifié
        if (!Auth::check()) {
            \Log::warning('Tentative d\'association de maladie chronique sans utilisateur authentifié');
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }
        

        $utilisateur = Utilisateur::find($id);

        if (!$utilisateur) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé',
            ], 404);
        }

        if (!Auth::check()) {
            \Log::warning('Tentative d\'association de maladie chronique sans utilisateur authentifié');
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }

        // Valider que l'ID de la maladie chronique est présent dans la requête
        $validator = Validator::make($request->all(), [
            'id_maladie_chronique' => 'required|integer|exists:maladies_chroniques,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Mettre à jour l'ID de la maladie chronique
        $utilisateur->id_maladie_chronique = $request->id_maladie_chronique;
        $utilisateur->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Maladie chronique mise à jour avec succès',
            'data' => $utilisateur,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Vérifier si l'utilisateur est authentifié
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }

        $utilisateur = Utilisateur::find($id);

        if (!$utilisateur) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé',
            ], 404);
        }

        $utilisateur->delete();

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur supprimé avec succès',
        ], 200);
    }
}
