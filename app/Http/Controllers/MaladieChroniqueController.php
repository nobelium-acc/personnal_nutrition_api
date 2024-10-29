<?php

namespace App\Http\Controllers;

use App\Models\MaladieChronique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MaladieChroniqueController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function store(Request $request)
    {
        $utilisateur = Auth::user();
        
        if (!$utilisateur) {
            Log::warning('Tentative d\'association de maladie chronique sans utilisateur authentifié');
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }

        $request->validate([
            'type_maladie_chronique' => 'required|string|in:Obésité modérée,Obésité sévère,Obésité morbide',
        ]);

        $maladieChronique = MaladieChronique::firstOrCreate([
            'type' => $request->input('type_maladie_chronique'),
        ]);

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

        return response()->json([
            'success' => true,
            'message' => 'Type de maladie chronique enregistré et associé à l\'utilisateur avec succès',
            'data' => [
                'maladie_chronique' => $maladieChronique,
                'utilisateur' => $utilisateur->only(['id', 'maladie_chronique_id']),
            ],
        ], 201);
    }
}