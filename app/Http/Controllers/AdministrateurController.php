<?php

namespace App\Http\Controllers;

use App\Models\Administrateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class AdministrateurController extends Controller
{
    /**
     * Display the admin details.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $admin = Administrateur::where('identifiant', env('ADMIN_USERNAME'))->first();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Administrateur non trouvé',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Administrateur récupéré avec succès',
            'data' => $admin,
        ], 200);
    }

    /**
     * Handle the admin login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function adminLogin(Request $request)
    {
        // Récupérer les identifiants depuis les variables d'environnement
        $adminUsername = env('ADMIN_USERNAME');
        $adminPassword = env('ADMIN_PASSWORD');

        // Vérification des informations d'identification
        if ($request->input('username') === $adminUsername && Hash::check($request->input('password'), $adminPassword)) {
            // Récupération de l'administrateur
            $admin = Administrateur::where('identifiant', $adminUsername)->first();

            // Création d'un token d'accès pour l'administrateur
            $token = $admin->createToken('AdminAccessToken')->plainTextToken;

            // Réponse en cas de connexion réussie
            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie',
                'access_token' => $token, // Le token d'accès est inclus ici
                'token_type' => 'Bearer',
                'data' => $admin,
            ], 200);
        } else {
            // Logique de connexion échouée
            return response()->json([
                'success' => false,
                'message' => 'Identifiant ou mot de passe incorrect',
            ], 401);
        }
    }
}
