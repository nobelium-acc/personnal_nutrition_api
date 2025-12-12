<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionPossibleAnswer;
use App\Models\Reponse;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user-infos/get-user-questions",
     *     tags={"User Infos"},
     *     security={{"BearerToken":{}}},
     *     summary="Récupérer la liste des questions",
     *     description="Liste des questions",
     *     @OA\Response(
     *         response=200,
     *         description="User infos saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     * )
    */
    public function index()
    {
        $utilisateur = Utilisateur::find(Auth::id());
        if (!$utilisateur) {
            Log::warning('Utilisateur non authentifié');
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }   
        if (!$utilisateur->maladie_chronique_id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez ajouter une maladie chronique',
            ], 500);
        }
        $questions = Question::with('possibleAnswers')->where('maladie_chronique_id', $utilisateur->maladie_chronique_id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des questions récupérée avec succès',
            'data' => $questions,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/user-infos/set-user-answer",
     *     tags={"User Infos"},
     *     security={{"BearerToken":{}}},
     *     summary="Ajouter une réponse de l'utilisateur",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"question_id", "answer", "description"},
     *             @OA\Property(property="question_id", type="string", example="1"),
     *             @OA\Property(property="answer_id", type="string", example="2"),
     *             @OA\Property(property="description", type="string", example="Description de la réponse de l'utilisateur"),
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
            Log::warning('Utilisateur non authentifié');
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }  
        $validator = Validator::make($request->all(), [
            'question_id' => 'required|integer|exists:questions,id',
            'answer_id' => 'sometimes|string|exists:question_possible_answers,id',
            'description' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors(),
            ], 400);
        }

        $previous_answer = Reponse::where('question_id', $request->question_id)->where('utilisateur_id', Auth::id())->first();

        if ($previous_answer) {
            return response()->json([
                'success' => false,
                'message' => "L'utilisateur a déja répondu à cette question",
            ], 500);
        }  

        $question = Question::find($request->question_id);
        if ($question->maladie_chronique_id !== $utilisateur->maladie_chronique_id) {
            return response()->json([
                'success' => false,
                'message' => "Cette question n'est pas relative à la maladie associée à l'utilisateur",
            ], 500);
        }

        $selected_answer = QuestionPossibleAnswer::find($request->answer_id);
        if ($selected_answer && $selected_answer->question_id != $request->question_id) {
            return response()->json([
                'success' => false,
                'message' => "La réponse choisie n'est pas relative à la question",
            ], 500);
        }


        $user_answer = Reponse::create([
            'question_id' => $request->question_id,
            'question_possible_answer_id' => $request->answer_id ?? null,
            'utilisateur_id' => $utilisateur->id,
            'description' => $request->description,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Réponse enregistrée avec succès',
            'data' => $user_answer,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/user-infos/get-user-answers",
     *     tags={"User Infos"},
     *     security={{"BearerToken":{}}},
     *     summary="Récupérer la liste des réponses",
     *     description="Liste des questions",
     *     @OA\Response(
     *         response=200,
     *         description="User infos saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     * )
    */
    public function list_answers()
    {
        $utilisateur = Utilisateur::find(Auth::id());
        if (!$utilisateur) {
            Log::warning('Utilisateur non authentifié');
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }   
        if (!$utilisateur->maladie_chronique_id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez d\'abord ajouter une maladie chronique',
            ], 500);
        }

        $answers = Reponse::where('utilisateur_id', $utilisateur->id)->get();
        $questions = Question::where('maladie_chronique_id', $utilisateur->maladie_chronique_id)->get();
        // foreach ($questions as $question) {
        //     $question->possible_answers = QuestionPossibleAnswer::where('question_id', $question->id)->get();
        // }
        return response()->json([
            'success' => true,
            'message' => 'Liste des reponses récupérée avec succès',
            'data' => [
                'answers' => $answers,
                'status' =>  $questions->count() == $answers->count() ? 'Complete' : 'Pending'
            ],
        ], 200);
    }
    
}
