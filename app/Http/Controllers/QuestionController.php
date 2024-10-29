<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questions = Question::all();
        return response()->json([
            'success' => true,
            'message' => 'Liste des questions récupérée avec succès',
            'data' => $questions,
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
            'type_question' => 'required|string',
            'texte_question' => 'sometimes|string',
            //'maladie_chronique_id' => 'required|integer|exists:maladie_chroniques,id', // Assurez-vous que la table maladie_chroniques existe
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors(),
            ], 400);
        }

        $question = Question::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Question créée avec succès',
            'data' => $question,
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
        $question = Question::find($id);

        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => 'Question non trouvée',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Question récupérée avec succès',
            'data' => $question,
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
        $question = Question::find($id);

        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => 'Question non trouvée',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'type_question' => 'sometimes|string',
            'texte_question' => 'sometimes|string',
            //'maladie_chronique_id' => 'sometimes|integer|exists:maladie_chroniques,id', // Assurez-vous que la table maladie_chroniques existe
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors(),
            ], 400);
        }

        $question->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Question mise à jour avec succès',
            'data' => $question,
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
        $question = Question::find($id);

        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => 'Question non trouvée',
            ], 404);
        }

        $question->delete();

        return response()->json([
            'success' => true,
            'message' => 'Question supprimée avec succès',
        ], 200);
    }
}
