<?php

namespace App\Http\Controllers;

use App\Models\Reponse;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Réponses",
 *     description="API Endpoints pour la gestion des réponses utilisateurs"
 * )
 */
class ReponseController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/reponses",
     *     summary="Lister les réponses de l'utilisateur connecté",
     *     description="Récupère les réponses de l'utilisateur actuellement authentifié.",
     *     tags={"Réponses"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des réponses récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Liste des réponses récupérée avec succès"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Reponse"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $utilisateurId = auth()->id();

        if (!$utilisateurId) {
             return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }

        $reponses = Reponse::with('question')->where('utilisateur_id', $utilisateurId)->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des réponses récupérée avec succès',
            'data' => $reponses,
        ], 200);
    }

    
    /**
     * @OA\Post(
     *     path="/api/reponses",
     *     summary="Enregistrer de nouvelles réponses",
     *     description="Enregistre une ou plusieurs réponses pour l'utilisateur connecté.",
     *     tags={"Réponses"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"reponses"},
     *             @OA\Property(
     *                 property="reponses",
     *                 type="array",
     *                 description="Liste des réponses à enregistrer",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"question_id"},
     *                     @OA\Property(
     *                         property="question_id",
     *                         type="integer",
     *                         example=5,
     *                         description="ID de la question"
     *                     ),
     *                     @OA\Property(
     *                         property="reponse",
     *                         type="string",
     *                         nullable=true,
     *                         example="Ma réponse textuelle",
     *                         description="Obligatoire si la question n'a pas de choix possibles"
     *                     ),
     *                     @OA\Property(
     *                         property="question_possible_answer_id",
     *                         type="integer",
     *                         nullable=true,
     *                         example=10,
     *                         description="Obligatoire si la question possède des choix possibles"
     *                     )
     *                 )
     *             ),
     *
     *             example={
     *                 "reponses": {
     *                     {
     *                         "question_id": 5,
     *                         "reponse": "Ma réponse textuelle pour une question sans choix"
     *                     },
     *                     {
     *                         "question_id": 10,
     *                         "question_possible_answer_id": 3
     *                     },
     *                     {
     *                         "question_id": 12,
     *                         "reponse": "Une autre réponse texte"
     *                     }
     *                 }
     *             }
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Réponses enregistrées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Réponses enregistrées avec succès")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erreur de validation des réponses"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $utilisateurId = auth()->id();

        if (!$utilisateurId) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'reponses' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation initiale',
                'errors' => $validator->errors(),
            ], 400);
        }

        $reponsesData = $request->input('reponses');
        $errors = [];
        $validReponses = [];

        foreach ($reponsesData as $index => $responseData) {
            if (!isset($responseData['question_id'])) {
                $errors["reponses.{$index}.question_id"] = ["Le champ question_id est obligatoire pour l'élément $index."];
                continue;
            }

            $question = Question::find($responseData['question_id']);

            if (!$question) {
                $errors["reponses.{$index}.question_id"] = ["La question avec l'ID {$responseData['question_id']} n'existe pas."];
                continue;
            }

            if ($question->has_possible_answers) {
                if (empty($responseData['question_possible_answer_id'])) {
                    $errors["reponses.{$index}.question_possible_answer_id"] = ["Veuillez sélectionner une option pour la question '{$question->texte_question}'."];
                } else {
                    $validReponses[] = [
                        'question_id' => $question->id,
                        'utilisateur_id' => $utilisateurId,
                        'question_possible_answer_id' => $responseData['question_possible_answer_id'],
                        'description' => null, // Description can be null here
                    ];
                }
            } else {
                if (empty($responseData['reponse'])) {
                    $errors["reponses.{$index}.reponse"] = ["Veuillez fournir une réponse textuelle pour la question '{$question->texte_question}'."];
                } else {
                    $validReponses[] = [
                        'question_id' => $question->id,
                        'utilisateur_id' => $utilisateurId,
                        'question_possible_answer_id' => null,
                        'description' => $responseData['reponse'], // Map 'reponse' input to 'description' column
                    ];
                }
            }
        }

        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation des réponses',
                'errors' => $errors,
            ], 400);
        }

        foreach ($validReponses as $reponseAttributes) {
            Reponse::create($reponseAttributes);
        }

        return response()->json([
            'success' => true,
            'message' => 'Réponses enregistrées avec succès',
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/reponses/{id}",
     *     summary="Afficher une réponse spécifique",
     *     tags={"Réponses"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la réponse",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Réponse récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Réponse récupérée avec succès"),
     *             @OA\Property(property="data", ref="#/components/schemas/Reponse")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Réponse non trouvée"
     *     )
     * )
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
     * @OA\Put(
     *     path="/api/reponses/{id}",
     *     summary="Mettre à jour une réponse",
     *     description="Met à jour une réponse en respectant la logique de validation (texte vs choix).",
     *     tags={"Réponses"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la réponse",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="question_id", type="integer", description="Optionnel. ID de la question pour réassocier.", example=5),
     *             @OA\Property(property="reponse", type="string", description="Nouveau texte (si question ouverte)", example="Réponse modifiée"),
     *             @OA\Property(property="question_possible_answer_id", type="integer", description="Nouvel ID de choix (si question à choix)", example=12)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Réponse mise à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Réponse mise à jour avec succès"),
     *             @OA\Property(property="data", ref="#/components/schemas/Reponse")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur de validation"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Réponse non trouvée"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $reponseModel = Reponse::find($id);

        if (!$reponseModel) {
            return response()->json([
                'success' => false,
                'message' => 'Réponse non trouvée',
            ], 404);
        }

        $questionId = $request->input('question_id') ?? $reponseModel->question_id;
        $question = Question::find($questionId);

        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => 'Question non trouvée',
            ], 404);
        }
        

        $updates = [];
        $errors = [];

        if($request->has('question_id')) {
            $updates['question_id'] = $questionId;
        }

        if ($question->has_possible_answers) {
            $possibleAnswerId = $request->input('question_possible_answer_id');
            
            if ($request->has('question_possible_answer_id')) {
                if (empty($possibleAnswerId)) {
                    $errors['question_possible_answer_id'] = ["Veuillez sélectionner une option valide."];
                } else {
                    $updates['question_possible_answer_id'] = $possibleAnswerId;
                    $updates['description'] = null; // Reset description if switching or ensuring consistency
                }
            } elseif ($request->has('reponse')) {
                // User trying to send text for a choice question
                $errors['question_possible_answer_id'] = ["Cette question nécessite une option, pas du texte."];
            } else {
                
                if ($reponseModel->question_id != $questionId && is_null($reponseModel->question_possible_answer_id)){
                    $errors['question_possible_answer_id'] = ["Veuillez fournir une option pour la nouvelle question sélectionnée."];
                }
            }

        } else {
            $fieldText = $request->input('reponse');

            if ($request->has('reponse')) {
                if (empty($fieldText)) {
                    $errors['reponse'] = ["Veuillez fournir une réponse textuelle."];
                } else {
                    $updates['description'] = $fieldText;
                    $updates['question_possible_answer_id'] = null; // Reset choice
                }
            } elseif ($request->has('question_possible_answer_id')) {
                $errors['reponse'] = ["Cette question nécessite une réponse textuelle."];
            } else {
                if ($reponseModel->question_id != $questionId && is_null($reponseModel->description)){
                    $errors['reponse'] = ["Veuillez fournir une réponse textuelle pour la nouvelle question sélectionnée."];
                }
            }
        }

        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $errors,
            ], 400);
        }

        $reponseModel->update($updates);

        return response()->json([
            'success' => true,
            'message' => 'Réponse mise à jour avec succès',
            'data' => $reponseModel,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/reponses/{id}",
     *     summary="Supprimer une réponse",
     *     tags={"Réponses"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la réponse",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Réponse supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Réponse supprimée avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Réponse non trouvée"
     *     )
     * )
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
