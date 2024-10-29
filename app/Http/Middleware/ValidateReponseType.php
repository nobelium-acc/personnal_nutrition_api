<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Question;
use Illuminate\Http\Request;

class ValidateReponseType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Récupérer l'ID de la question et la réponse depuis la requête
        $questionId = $request->input('question_id');
        $reponse = $request->input('reponse');
        
        // Trouver la question associée à cet ID
        $question = Question::find($questionId);

        // Vérifier si la question existe
        if (!$question) {
            return response()->json([
                'success' => false,
                'message' => 'Question non trouvée'
            ], 404);
        }

        // Valider selon le type de question
        switch ($question->type_question) {
            case 'choix_unique': 
                // Pour les questions à choix unique, la réponse doit être l'un des choix disponibles
                $choixDisponibles = explode(',', $question->texte_question); // Assumant que les choix sont séparés par des virgules
                if (!in_array($reponse, $choixDisponibles)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La réponse doit être l\'un des choix suivants : ' . implode(', ', $choixDisponibles),
                    ], 400);
                }
                break;

            case 'texte': 
                // Pour les questions texte, s'assurer que la réponse est une chaîne de caractères
                if (!is_string($reponse) || empty($reponse)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La réponse doit être un texte valide.',
                    ], 400);
                }
                break;

            case 'oui_non': 
                // Pour les questions oui/non, la réponse doit être "oui" ou "non"
                if (!in_array(strtolower($reponse), ['oui', 'non'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La réponse doit être "oui" ou "non".',
                    ], 400);
                }
                break;

            default: 
                return response()->json([
                    'success' => false,
                    'message' => 'Type de question invalide.',
                ], 400);
        }

        // Passer au prochain middleware ou à l'action du contrôleur si tout est valide
        return $next($request);
    }
}
