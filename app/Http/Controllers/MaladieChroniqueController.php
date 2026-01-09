<?php

namespace App\Http\Controllers;

use App\Models\MaladieChronique;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\NutritionController;

class MaladieChroniqueController extends Controller
{
    protected $nutritionController;

    public function __construct(NutritionController $nutritionController)
    {
        $this->middleware('auth:api');
        $this->nutritionController = $nutritionController;
    }
    
    /**
     * @OA\Post(
     *     path="/api/user_infos/maladie-chronique",
     *     tags={"User Infos"},
     *     security={{"BearerToken":{}}},
     *     summary="Associer maladie chorinique à l'utilisateur",
     *     description="Associate a disease to the user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="type_maladie_chronique", type="string", example="Obésité")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User infos saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     * )
    */
    public function store(Request $request)
    {
        $utilisateur = Utilisateur::find(Auth::id());
        
        if (!$utilisateur) {
            Log::warning('Tentative d\'association de maladie chronique sans utilisateur authentifié');
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }

        $request->validate([
            'type_maladie_chronique' => 'required|string|in:obésité modérée,obésité sévère,obésité morbide',
        ]);

        $maladieChronique = MaladieChronique::where('type', $request->type_maladie_chronique)->first();
        if(!$maladieChronique) {
            return response()->json([
                'success' => false,
                'message' => "Cette maladie n'est pas prise en compte pas le systeme",
            ], 500);
        }

        $utilisateur->maladie_chronique_id = $maladieChronique->id;
        $utilisateur->save();

        $utilisateur = $utilisateur->fresh();

        if ($utilisateur->maladie_chronique_id !== $maladieChronique->id) {
            Log::error('Échec de la mise à jour de maladie_chronique_id pour l\'utilisateur', [
                'utilisateur_id' => $utilisateur->id,
                'maladie_chronique_id' => $maladieChronique->id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'association de la maladie chronique à l\'utilisateur',
            ], 500);
        }

        Log::info('Maladie chronique associée à l\'utilisateur : ', [
            'utilisateur_id' => $utilisateur->id,
            'maladie_chronique_id' => $maladieChronique->id,
            'type' => $maladieChronique->type
        ]);

        // Trigger nutrition calculation and inconsistency checks
        $calcResponse = $this->nutritionController->calculate($request);
        $calcData = $calcResponse->getData(true);

        return response()->json([
            'success' => true,
            'message' => 'Type de maladie chronique enregistré et associé à l\'utilisateur avec succès. Calculs nutritionnels mis à jour.',
            'data' => [
                'maladie_chronique' => $maladieChronique,
                'utilisateur' => $utilisateur->only(['id', 'maladie_chronique_id']),
                'nutrition_metrics' => $calcData
            ],
        ], 201);
    }
}