<?php

namespace App\Http\Controllers;

use App\Models\Reponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReponseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // Les réponses sont maintenant disponibles dans $request->user_reponses
        $reponses = $request->input('user_reponses');

        $reponses = Reponse::all();
        return response()->json([
            'success' => true,
            'message' => 'Liste des réponses récupérée avec succès',
            'data' => $reponses,
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
        // Valider les réponses reçues
        $validator = Validator::make($request->all(), [
            'reponses' => 'required|array',
            'reponses.*.reponse' => 'required|string',  // Assure que chaque réponse est une chaîne valide
            //'reponses.*.question_id' => 'required|exists:questions,id',  // Assure que la question existe
            //'utilisateur_id' => 'required|exists:utilisateurs,id',  // Assure que l'utilisateur existe
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Récupérer l'utilisateur ID
        $utilisateurId = $request->input('utilisateur_id');

        // Boucler sur chaque réponse et enregistrer dans la base de données
        foreach ($request->input('reponses') as $responseData) {
            Reponse::create([
                'reponse' => $responseData['reponse'],
                'question_id' => $responseData['question_id'],
                //'utilisateur_id' => $utilisateurId,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Réponses enregistrées avec succès',
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
        $reponse = Reponse::find($id);

        if (!$reponse) {
            return response()->json([
                'success' => false,
                'message' => 'Réponse non trouvée',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Réponse récupérée avec succès',
            'data' => $reponse,
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
        $reponse = Reponse::find($id);

        if (!$reponse) {
            return response()->json([
                'success' => false,
                'message' => 'Réponse non trouvée',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'reponse' => 'sometimes|string',
            'question_id' => 'sometimes|exists:questions,id',
            //'utilisateur_id' => 'sometimes|exists:utilisateurs,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors(),
            ], 400);
        }

        $reponse->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Réponse mise à jour avec succès',
            'data' => $reponse,
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
        $reponse = Reponse::find($id);

        if (!$reponse) {
            return response()->json([
                'success' => false,
                'message' => 'Réponse non trouvée',
            ], 404);
        }

        $reponse->delete();

        return response()->json([
            'success' => true,
            'message' => 'Réponse supprimée avec succès',
        ], 200);
    }
}
