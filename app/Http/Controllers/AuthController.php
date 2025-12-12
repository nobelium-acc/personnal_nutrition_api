<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Mail\Auth\ResetPasswordMail;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

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

        $utilisateur->last_login_date = now();
        $utilisateur->save();
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
     *             @OA\Property(property="sexe", type="string", example="M"),
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

    /**
     * @OA\Post(
     *     path="/api/auth/forgot-password",
     *     tags={"Authentification"},
     *     summary="Forgot yout password",
     *     description="Reset user password - step 1",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Mail with reset code sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     * )
    */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $utilisateur = Utilisateur::where('email', $request->email)->first();
        if (!$utilisateur) {
            return response()->json([
                'success' => false,
                'message' => 'Mail invalide. Vérifiez et reessayez à nouveau !',
            ], 401);
        }
        $code = $randomNumber = rand(1000000, 9999999);
        $password_reset = PasswordReset::create([
            'email' => $request->email,
            'code' => $code,
        ]);

        Mail::to($request->email)->send(new ResetPasswordMail(['code' => $code]));
        return response()->json([
            'success' => true,
            'message' => 'Mail de réinitialisation envoyé',
            'data' => $utilisateur,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/forgot-password/verify",
     *     tags={"Authentification"},
     *     summary="Forgot yout password",
     *     description="Reset user password - step 2",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code"},
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="code", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Code correct",
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     * )
    */
    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|numeric|digits:7',
        ]);

        $utilisateur = Utilisateur::where('email', $request->email)->first();
        if (!$utilisateur) {
            return response()->json([
                'success' => false,
                'message' => 'Mail invalide. Vérifiez et reessayez à nouveau !',
            ], 401);
        }
        $password_reset = PasswordReset::where('email', $request->email)
                ->where('code', $request->code)
                ->where('created_at', '>=', Carbon::now()->subHour())
                ->orderBy('created_at', 'desc')
                ->first();

        if ($password_reset) {
            return response()->json([
                'success' => true,
                'message' => 'Validation éffectuée !',
                'data' => $utilisateur,
            ], 200);
        }
        return response()->json([
            'success' => true,
            'message' => 'Le code que vous avez saisi est invalide. Veuillez réessayer',
        ], 500);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/reset-password",
     *     tags={"Authentification"},
     *     summary="Forgot yout password",
     *     description="Reset user password - step 3",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code"},
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", example="motP@sst**"),
     *             @OA\Property(property="password_confirmation", type="string", example="motP@sst**")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Mot de passe modifié",
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     * )
    */
    public function resetUserPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => ['required','confirmed', Password::defaults()]
        ]);

        $utilisateur = Utilisateur::where('email', $request->email)->first();
        if (!$utilisateur) {
            return response()->json([
                'success' => false,
                'message' => 'Mail invalide. Vérifiez et reessayez à nouveau !',
            ], 401);
        }
        try {
            $utilisateur->mot_de_passe = Hash::make($request->password);
            $utilisateur->save();
            return response()->json([
                'success' => true,
                'message' => 'Le mot de passe a bien été réinitialisé',
                'data' => $utilisateur,
            ], 200);
        } catch( Exception $e) {
            return response()->json([
                'success' => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }    
}