<?php

namespace App\Http\Controllers;

use App\Models\PlanNutritionnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlanNutritionnelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plansNutritionnels = PlanNutritionnel::all();
        return response()->json([
            'success' => true,
            'message' => 'Liste des plans nutritionnels récupérée avec succès',
            'data' => $plansNutritionnels,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
            //'utilisateur_id' => 'required|exists:utilisateurs,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors(),
            ], 400);
        }

        $planNutritionnel = PlanNutritionnel::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Plan nutritionnel créé avec succès',
            'data' => $planNutritionnel,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $planNutritionnel = PlanNutritionnel::find($id);

        if (!$planNutritionnel) {
            return response()->json([
                'success' => false,
                'message' => 'Plan nutritionnel non trouvé',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Plan nutritionnel récupéré avec succès',
            'data' => $planNutritionnel,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $planNutritionnel = PlanNutritionnel::find($id);

        if (!$planNutritionnel) {
            return response()->json([
                'success' => false,
                'message' => 'Plan nutritionnel non trouvé',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'description' => 'sometimes|string',
           // 'utilisateur_id' => 'sometimes|exists:utilisateurs,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors(),
            ], 400);
        }

        $planNutritionnel->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Plan nutritionnel mis à jour avec succès',
            'data' => $planNutritionnel,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $planNutritionnel = PlanNutritionnel::find($id);

        if (!$planNutritionnel) {
            return response()->json([
                'success' => false,
                'message' => 'Plan nutritionnel non trouvé',
            ], 404);
        }

        $planNutritionnel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Plan nutritionnel supprimé avec succès',
        ], 200);
    }
}
