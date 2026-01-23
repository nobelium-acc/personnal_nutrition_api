<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\ObesityInconsistencyMail;
use App\Mail\RthWarningMail;
use App\Mail\ImgInconsistencyMail;

use Illuminate\Support\Facades\DB;
use App\Services\NutritionDataService;

class NutritionController extends Controller
{
    protected $nutritionDataService;

    public function __construct(NutritionDataService $nutritionDataService)
    {
        $this->nutritionDataService = $nutritionDataService;
    }
    /**
     * @OA\Post(
     *     path="/api/nutrition/calculate",
     *     summary="Calculer les indicateurs nutritionnels (IMC, RTH, IMG, BMR, TDEE)",
     *     description="Effectue les calculs nutritionnels basés sur les données stockées de l'utilisateur (poids, taille, tours, etc.). Compare également le résultat avec le type d'obésité déclaré par l'utilisateur (via sa maladie chronique) et envoie un email en cas d'incohérence.",
     *     tags={"Nutrition"},
     *     security={{"BearerToken":{}}},
     *     @OA\RequestBody(
     *         description="Exemple de corps de requête pour tester (user_id est optionnel si authentifié)",
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="user_id", 
     *                 type="integer", 
     *                 example=1, 
     *                 description="Id de l'utilisateur si la requête est faite par un admin, sinon l'utilisateur authentifié est utilisé"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Calculs réussis",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="imc", type="number", format="float", example=31.02),
     *             @OA\Property(property="rth", type="number", format="float", example=0.77),
     *             @OA\Property(property="img", type="number", format="float", example=28.5),
     *             @OA\Property(property="bmr", type="integer", example=1850),
     *             @OA\Property(property="tdee", type="integer", example=2500),
     *             @OA\Property(property="status", type="string", example="Obese", description="Statut calculé par l'algorithme"),
     *             @OA\Property(property="grade_imc", type="string", example="Obésité modérée (Grade 1)", description="Grade selon l'IMC"),
     *             @OA\Property(property="declared_type", type="string", example="Obésité modérée", description="Type déclaré dans Maladie Chronique"),
     *             @OA\Property(property="message", type="string", example="D’après vos mesures..."),
     *             @OA\Property(property="consistent", type="boolean", example=true, description="Indique si le type déclaré correspond au calcul")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données manquantes ou ID requis"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur non trouvé"
     *     )
     * )
     */
    public function calculate(Request $request)
    {
        $userId = $request->user_id ?? auth()->id();

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'User ID required'], 400);
        }

        $user = \App\Models\Utilisateur::with('maladieChronique')->find($userId);

        if (!$user) {
             return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        return $this->performCalculation($user);
    }

    /**
     * @OA\Post(
     *     path="/api/nutrition/recommendation",
     *     summary="Obtenir des recommandations nutritionnelles personnalisées",
     *     description="Calcule l'apport calorique quotidien et la répartition des macronutriments en fonction de l'objectif de l'utilisateur, de ses pathologies et de son niveau d'activité.",
     *     tags={"Nutrition"},
     *     security={{"BearerToken":{}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Recommandations générées",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="tdee", type="number", example=2500),
     *             @OA\Property(property="apport_calorique", type="number", example=2000),
     *             @OA\Property(property="deficit_calorique", type="integer", example=500),
     *             @OA\Property(
     *                 property="macronutriments",
     *                 type="object",
     *                 @OA\Property(property="distribution", type="object"),
     *                 @OA\Property(property="grammes", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function recommendation(Request $request)
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Non authentifié'], 401);
        }

        $user = \App\Models\Utilisateur::with(['maladieChronique', 'reponse.question'])->find($userId);

        if (!$user) {
             return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Restriction to ID 1 (Obésité modérée) as requested by the user
        // The advanced logic for questionnaires (Q66-Q91) is specific to this category.
        if ($user->maladie_chronique_id != 1) {
            return response()->json([
                'success' => false, 
                'message' => 'La logique de recommandation avancée est actuellement limitée aux profils avec obésité modérée (ID 1).'
            ], 403);
        }

        // BMR/TDEE Calculation
        $metrics = $this->calculateBasicMetrics($user);
        if ($metrics instanceof \Illuminate\Http\JsonResponse) return $metrics;

        $tdee = $metrics['tdee'];
        $isMale = $metrics['is_male'];

        // Extract Advanced Info from Reponses
        $objective = 'Perte de poids';
        $deficitValue = 0;
        $weightLossRange = 'Inconnu';
        $pathologies = ['diabetes' => false, 'hypertension' => false, 'cardio' => false];
        $responsesMap = [];
        $questionnaireObesite = [];

        foreach ($user->reponse as $rep) {
            $qid = $rep->question_id;
            $question = DB::table('questions')->where('id', $qid)->first();
            $questionText = $question ? $question->texte_question : 'Question ' . $qid;
            
            $text = $rep->description ?: '';
            $answerId = $rep->question_possible_answer_id;
            
            if ($answerId) {
                $possibleAnswer = DB::table('question_possible_answers')->where('id', $answerId)->first();
                if ($possibleAnswer) $text = $possibleAnswer->value;
            }

            $responsesMap[$qid] = [
                'text' => $text,
                'id' => $answerId,
                'question' => $questionText
            ];

            // All responses for the requested data section
            $questionnaireObesite[] = [
                'question' => $questionText,
                'reponse' => $text
            ];

            switch ($qid) {
                case 89: // Objectif principal
                    $objective = $text;
                    break;
                case 90: // Kg à perdre
                    $weightLossRange = $text;
                    break;
                case 91: // Niveau de changement (Déficit)
                    if (preg_match('/(\d+)\s*kcal/i', $text, $matches)) {
                        $deficitValue = intval($matches[1]);
                    }
                    break;
                case 67: // Si oui, lequel ?
                    if (stripos($text, 'diabète') !== false) $pathologies['diabetes'] = true;
                    if (stripos($text, 'hypertension') !== false || stripos($text, 'tension') !== false) $pathologies['hypertension'] = true;
                    if (stripos($text, 'cardio') !== false || stripos($text, 'coeur') !== false) $pathologies['cardio'] = true;
                    break;
                case 70: // Médicaments
                    if (stripos($text, 'Insuline') !== false || stripos($text, 'glycémie') !== false) $pathologies['diabetes'] = true;
                    if (stripos($text, 'tension artérielle') !== false) $pathologies['hypertension'] = true;
                    if (stripos($text, 'cholestérol') !== false || stripos($text, 'cardiaque') !== false) $pathologies['cardio'] = true;
                    break;
            }
        }

        $isWeightLoss = (stripos($objective, 'Perte') !== false || stripos($objective, 'Perdre') !== false);
        $isFitness = (stripos($objective, 'forme physique') !== false);

        // Apport Calorique Calculation
        $apportCalorique = $tdee - $deficitValue;
        
        if ($isFitness) {
            $pct = ($tdee < 2000) ? 0.05 : (($tdee <= 3000) ? 0.04 : 0.03);
            $calcDeficit = round($tdee * $pct);
            $deficitValue = min(300, $calcDeficit);
            $apportCalorique = $tdee - $deficitValue;
        }

        // Safety Check
        $minThreshold = $isMale ? 1500 : 1200;
        $lowCalNotification = false;
        
        if ($apportCalorique < $minThreshold) {
            $warningData = [
                'gender' => $isMale ? 'Homme' : 'Femme',
                'objectif' => $objective,
                'tdee' => round($tdee),
                'deficit' => $deficitValue,
                'apport' => round($apportCalorique)
            ];
            Mail::to($user->email)->send(new \App\Mail\LowCalorieWarningMail($user->nom . ' ' . $user->prenom, $warningData, $minThreshold));
            $lowCalNotification = true;
        }

        // Macronutrients Distribution
        $macros = $this->calculateMacrosEnhanced($user, $user->niveau_d_activite_physique, $deficitValue, $isWeightLoss, $isFitness, $apportCalorique, $weightLossRange, $pathologies);

        // NEW: Generate Intervention Plan and Food Guide
        $planIntervention = $this->generateNutritionInterventionPlan($user, $metrics, $responsesMap, $pathologies);
        $guideData = $this->generateDynamicFoodGuide($user, $macros['grammes'], $pathologies);

        return response()->json([
            'success' => true,
            'user_profile' => [
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'sexe' => $isMale ? 'Homme' : 'Femme',
                'objectif' => $objective,
                'pathologies_detectees' => array_keys(array_filter($pathologies)),
                'donnees_obesite' => $questionnaireObesite
            ],
            'tdee' => round($tdee, 2),
            'apport_calorique' => round($apportCalorique, 2),
            'deficit_calorique' => $deficitValue,
            'unite_calorique' => 'kcal',
            'macronutriments' => $macros,
            'low_calorie_notification' => $lowCalNotification,
            'suivi_hebdomadaire' => [
                'colonnes' => ['Semaine', 'Poids (kg)', 'Tour de taille (cm)', 'Energie (1-5)', 'Faim (1-5)', 'Activité physique (heure)', 'Humeur', 'Remarque/Ajustements'],
                'legendes' => [
                    'Énergie' => '1 = épuisé·e, 5 = en pleine forme',
                    'Faim' => '1 = jamais faim, 5 = toujours faim',
                    'Activité physique' => 'total en heures (ou nombre de séances)',
                    'Humeur' => 'bonne humeur, neutre ou fatigué·e/irritable'
                ],
                'utilisation' => 'À remplir chaque fin de semaine (par ex. dimanche matin) pour surveiller les tendances : stagnation, fatigue, besoin d’ajustement.'
            ],
            'plan_intervention' => $planIntervention,
            'menu_journalier' => $guideData['menu_journalier'],
            'facteurs_ajustement' => $guideData['facteurs'],
            'conseils_personnalises' => $guideData['conseils']
        ]);
    }

    private function calculateBasicMetrics(\App\Models\Utilisateur $user)
    {
        $weight = $user->poids;
        $height = $user->taille;
        $gender = $user->sexe;
        $age = $user->age;
        $niveauPhysique = $user->niveau_d_activite_physique;

        if (!$weight || !$height || !$gender) {
             return response()->json(['success' => false, 'message' => 'Données incomplètes.'], 400);
        }

        $isMale = (strtoupper($gender) === 'M' || strtoupper($gender) === 'HOMME');
        $bmr = $isMale ? (10 * $weight) + (6.25 * $height) - (5 * $age) + 5 
                       : (10 * $weight) + (6.25 * $height) - (5 * $age) - 161;

        $multipliers = [
            "Sédentaire" => 1.2,
            "Légèrement actif" => 1.4,
            "Modérément actif" => 1.6,
            "Très actif" => 1.7,
            "Extrêmement actif" => 1.9,
        ];
        $tdee = $bmr * ($multipliers[$niveauPhysique] ?? 1.2);

        // Calculate RTH for the intervention plan logic
        $rth = ($user->tour_de_hanche > 0) ? round($user->tour_de_taille / $user->tour_de_hanche, 2) : 0;
        $rthThreshold = $isMale ? 0.90 : 0.85;

        // Calculate IMC
        $heightM = $height / 100;
        $imc = round($weight / ($heightM * $heightM), 2);

        return [
            'bmr' => $bmr, 
            'tdee' => $tdee, 
            'is_male' => $isMale, 
            'height' => $height, 
            'weight' => $weight,
            'rth' => $rth,
            'rth_threshold' => $rthThreshold,
            'imc' => $imc
        ];
    }

    private function performCalculation(\App\Models\Utilisateur $user)
    {        
        $metrics = $this->calculateBasicMetrics($user);
        if ($metrics instanceof \Illuminate\Http\JsonResponse) return $metrics;

        $weight = $metrics['weight'];
        $height = $metrics['height'];
        $isMale = $metrics['is_male'];
        $tdee = $metrics['tdee'];

        // Save TDEE
        $user->tdee = $tdee;
        $user->save();

        // IMC
        $heightM = $height / 100;
        $imc = round($weight / ($heightM * $heightM), 2);

        // RTH
        $rth = ($user->tour_de_hanche > 0) ? round($user->tour_de_taille / $user->tour_de_hanche, 2) : 0;

        // IMG
        $waist = $user->tour_de_taille;
        $neck = $user->tour_du_cou;
        $hip = $user->tour_de_hanche;
        $img = 0;
        if ($isMale) {
            if (($waist - $neck) > 0) $img = 86.010 * log10($waist - $neck) - 70.041 * log10($height) + 36.76;
        } else {
            if (($waist + $hip - $neck) > 0) $img = 163.205 * log10($waist + $hip - $neck) - 97.684 * log10($height) - 78.387;
        }
        $img = round($img, 2);

        // Obesity logic
        $imgThreshold = $isMale ? 25 : 32;
        $isObeseIMG = $img < $imgThreshold;
        $rthThreshold = $isMale ? 0.90 : 0.85;
        $isHighRTH = $rth > $rthThreshold;
        $isObeseIMC = $imc >= 30;

        $calculatedStatus = 'Normal';
        if ($isObeseIMG || ($isObeseIMC && $isHighRTH)) $calculatedStatus = 'Obese';
        elseif ($isObeseIMC) $calculatedStatus = 'Normal';

        $obesityGrade = ($imc >= 40) ? 'Obésité morbide (Grade 3)' : (($imc >= 35) ? 'Obésité sévère (Grade 2)' : (($imc >= 30) ? 'Obésité modérée (Grade 1)' : 'Normal/Surpoids'));
        $declaredType = $user->maladieChronique ? $user->maladieChronique->type : 'Inconnu';
        
        $isConsistent = ($calculatedStatus === 'Obese') ? (stripos($declaredType, 'Obésité') !== false || stripos($declaredType, 'Obesite') !== false) 
                                                        : (stripos($declaredType, 'Obésité') === false && stripos($declaredType, 'Obesite') === false);

        $imgNotification = false; $imcNotification = false; $rthNotification = true;
        
        if ($isObeseIMG) {
             Mail::to($user->email)->send(new ImgInconsistencyMail($user->nom . ' ' . $user->prenom, ['gender' => $user->sexe, 'height' => $height, 'waist' => $waist, 'neck' => $neck, 'hip' => $hip], $img, $imgThreshold));
             $imgNotification = true;
        }
        if (!$isConsistent) {
             Mail::to($user->email)->send(new ObesityInconsistencyMail($user->nom . ' ' . $user->prenom, ['weight' => $weight, 'height' => $height, 'declared_type' => $declaredType], $imc, $obesityGrade));
             $imcNotification = true;
        }
        Mail::to($user->email)->send(new RthWarningMail($user->nom . ' ' . $user->prenom, $rth, $isMale ? 'M' : 'F', $rthThreshold));

        $user->update(['img_notification' => $imgNotification, 'imc_notification' => $imcNotification, 'rth_notification' => $rthNotification]);

        return response()->json([
            'success' => true,
            'user_id' => $user->id,
            'imc' => $imc,
            'rth' => $rth,
            'img' => $img,
            'bmr' => round($metrics['bmr']),
            'tdee' => round($tdee),
            'status' => $calculatedStatus,
            'grade_imc' => $obesityGrade,
            'consistent' => $isConsistent,
            'notifications' => ['img' => $imgNotification, 'imc' => $imcNotification, 'rth' => $rthNotification]
        ]);
    }

    private function calculateMacrosEnhanced($user, $activity, $deficit, $isWeightLoss, $isFitness, $apportCalorique, $weightLossRange, $pathologies)
    {
        // Default Distribution (General Case / Fallback)
        $pProt = 30; $pGlu = 40; $pLip = 30;

        $hasDiabetes = $pathologies['diabetes'];
        $hasHypertension = $pathologies['hypertension'];
        $hasCardio = $pathologies['cardio'];

        if ($isWeightLoss) {
            // Determine Range Category
            $range = 'low'; // < 5kg
            if (stripos($weightLossRange, 'Plus de 10 kg') !== false) {
                $range = 'high';
            } elseif (stripos($weightLossRange, '5 - 10 kg') !== false) {
                $range = 'mid';
            } elseif (stripos($weightLossRange, 'Moins de 5 kg') !== false) {
                $range = 'low';
            } else {
                if ($deficit >= 700) $range = 'high';
                elseif ($deficit >= 500) $range = 'mid';
            }

            if ($hasDiabetes) {
                // TABLES 9, 12, 15
                if ($range === 'low') { // Table 9
                    if ($activity === "Sédentaire") { $pProt=30; $pGlu=35; $pLip=35; }
                    elseif ($activity === "Légèrement actif") { $pProt=30; $pGlu=40; $pLip=30; }
                    elseif ($activity === "Modérément actif") { $pProt=25; $pGlu=45; $pLip=30; }
                    elseif ($activity === "Très actif") { $pProt=25; $pGlu=48; $pLip=27; }
                    else { $pProt=25; $pGlu=50; $pLip=25; }
                } elseif ($range === 'mid') { // Table 12
                    if ($activity === "Sédentaire") { $pProt=30; $pGlu=35; $pLip=35; } 
                    elseif ($activity === "Légèrement actif") { $pProt=30; $pGlu=40; $pLip=30; }
                    elseif ($activity === "Modérément actif") { $pProt=25; $pGlu=45; $pLip=30; }
                    elseif ($activity === "Très actif") { $pProt=25; $pGlu=48; $pLip=27; }
                    else { $pProt=25; $pGlu=50; $pLip=25; }
                } else { // Table 15
                    if ($activity === "Sédentaire") { $pProt=30; $pGlu=35; $pLip=35; }
                    elseif ($activity === "Légèrement actif") { $pProt=28; $pGlu=40; $pLip=32; }
                    elseif ($activity === "Modérément actif") { $pProt=25; $pGlu=43; $pLip=32; }
                    elseif ($activity === "Très actif") { $pProt=25; $pGlu=48; $pLip=27; }
                    else { $pProt=25; $pGlu=50; $pLip=25; }
                }
            } elseif ($hasHypertension) {
                // TABLES 10, 13, 16
                if ($range === 'low') { // Table 10
                    if ($activity === "Sédentaire") { $pProt=30; $pGlu=40; $pLip=30; }
                    elseif ($activity === "Légèrement actif") { $pProt=28; $pGlu=44; $pLip=28; }
                    elseif ($activity === "Modérément actif") { $pProt=25; $pGlu=45; $pLip=30; }
                    elseif ($activity === "Très actif") { $pProt=25; $pGlu=48; $pLip=27; }
                    else { $pProt=25; $pGlu=50; $pLip=25; }
                } elseif ($range === 'mid') { // Table 13
                    if ($activity === "Sédentaire") { $pProt=29; $pGlu=41; $pLip=30; }
                    elseif ($activity === "Légèrement actif") { $pProt=29; $pGlu=44; $pLip=30; }
                    elseif ($activity === "Modérément actif") { $pProt=25; $pGlu=45; $pLip=30; }
                    elseif ($activity === "Très actif") { $pProt=25; $pGlu=48; $pLip=27; }
                    else { $pProt=25; $pGlu=50; $pLip=25; }
                } else { // Table 16
                    if ($activity === "Sédentaire") { $pProt=30; $pGlu=40; $pLip=30; }
                    elseif ($activity === "Légèrement actif") { $pProt=28; $pGlu=44; $pLip=28; }
                    elseif ($activity === "Modérément actif") { $pProt=25; $pGlu=45; $pLip=30; }
                    elseif ($activity === "Très actif") { $pProt=25; $pGlu=48; $pLip=27; }
                    else { $pProt=25; $pGlu=50; $pLip=25; }
                }
            } elseif ($hasCardio) {
                // TABLES 11, 14, 17
                if ($range === 'low') { // Table 11
                    if ($activity === "Sédentaire") { $pProt=30; $pGlu=40; $pLip=30; }
                    elseif ($activity === "Légèrement actif") { $pProt=30; $pGlu=44; $pLip=26; }
                    elseif ($activity === "Modérément actif") { $pProt=25; $pGlu=45; $pLip=30; }
                    elseif ($activity === "Très actif") { $pProt=25; $pGlu=48; $pLip=27; }
                    else { $pProt=25; $pGlu=50; $pLip=25; }
                } elseif ($range === 'mid') { // Table 14
                    if ($activity === "Sédentaire") { $pProt=30; $pGlu=41; $pLip=29; }
                    elseif ($activity === "Légèrement actif") { $pProt=28; $pGlu=45; $pLip=27; }
                    elseif ($activity === "Modérément actif") { $pProt=25; $pGlu=45; $pLip=30; }
                    elseif ($activity === "Très actif") { $pProt=25; $pGlu=48; $pLip=27; }
                    else { $pProt=25; $pGlu=50; $pLip=25; }
                } else { // Table 17
                    if ($activity === "Sédentaire") { $pProt=30; $pGlu=40; $pLip=30; }
                    elseif ($activity === "Légèrement actif") { $pProt=28; $pGlu=45; $pLip=27; }
                    elseif ($activity === "Modérément actif") { $pProt=25; $pGlu=45; $pLip=30; }
                    elseif ($activity === "Très actif") { $pProt=25; $pGlu=48; $pLip=27; }
                    else { $pProt=25; $pGlu=50; $pLip=25; }
                }
            } else {
                // NO PATHOLOGY (Tables 21, 22, 23)
                if ($range === 'low') { // Table 21
                    if ($activity === "Sédentaire") { $pProt=30; $pGlu=35; $pLip=35; }
                    elseif ($activity === "Légèrement actif") { $pProt=30; $pGlu=40; $pLip=30; }
                    elseif ($activity === "Modérément actif") { $pProt=27; $pGlu=45; $pLip=28; }
                    elseif ($activity === "Très actif") { $pProt=25; $pGlu=50; $pLip=25; }
                    else { $pProt=25; $pGlu=55; $pLip=20; }
                } elseif ($range === 'mid') { // Table 22
                    if ($activity === "Sédentaire") { $pProt=30; $pGlu=35; $pLip=35; }
                    elseif ($activity === "Légèrement actif") { $pProt=30; $pGlu=40; $pLip=30; }
                    elseif ($activity === "Modérément actif") { $pProt=26; $pGlu=45; $pLip=29; }
                    elseif ($activity === "Très actif") { $pProt=25; $pGlu=50; $pLip=25; }
                    else { $pProt=25; $pGlu=55; $pLip=20; }
                } else { // Table 23
                    if ($activity === "Sédentaire") { $pProt=30; $pGlu=35; $pLip=35; }
                    elseif ($activity === "Légèrement actif") { $pProt=30; $pGlu=40; $pLip=30; }
                    elseif ($activity === "Modérément actif") { $pProt=27; $pGlu=45; $pLip=28; }
                    elseif ($activity === "Très actif") { $pProt=25; $pGlu=50; $pLip=25; }
                    else { $pProt=25; $pGlu=55; $pLip=20; }
                }
            }
        } elseif ($isFitness) {
            // FITNESS LOGIC
            if ($hasDiabetes) { // Tableau 28
                if ($activity === "Sédentaire") { $pProt=27; $pGlu=35; $pLip=38; }
                elseif ($activity === "Légèrement actif") { $pProt=27; $pGlu=38; $pLip=35; }
                elseif ($activity === "Modérément actif") { $pProt=27; $pGlu=40; $pLip=33; }
                elseif ($activity === "Très actif") { $pProt=27; $pGlu=43; $pLip=30; }
                else { $pProt=27; $pGlu=45; $pLip=28; }
            } elseif ($hasHypertension) { // Tableau 29
                if ($activity === "Sédentaire") { $pProt=28; $pGlu=37; $pLip=30; }
                elseif ($activity === "Légèrement actif") { $pProt=27; $pGlu=44; $pLip=29; }
                elseif ($activity === "Modérément actif") { $pProt=25; $pGlu=45; $pLip=25; }
                elseif ($activity === "Très actif") { $pProt=25; $pGlu=50; $pLip=22; }
                else { $pProt=25; $pGlu=45; $pLip=20; }
            } elseif ($hasCardio) { // Cardio (Tableau sans numéro)
                if ($activity === "Sédentaire") { $pProt=27; $pGlu=37; $pLip=30; }
                elseif ($activity === "Légèrement actif") { $pProt=28; $pGlu=42; $pLip=28; }
                elseif ($activity === "Modérément actif") { $pProt=25; $pGlu=45; $pLip=25; }
                elseif ($activity === "Très actif") { $pProt=25; $pGlu=45; $pLip=25; }
                else { $pProt=25; $pGlu=45; $pLip=25; }
            } else { // No pathology (Tableau 33)
                if ($activity === "Sédentaire") { $pProt=30; $pGlu=35; $pLip=35; }
                elseif ($activity === "Légèrement actif") { $pProt=30; $pGlu=42; $pLip=28; }
                elseif ($activity === "Modérément actif") { $pProt=30; $pGlu=48; $pLip=22; }
                elseif ($activity === "Très actif") { $pProt=30; $pGlu=53; $pLip=17; }
                else { $pProt=30; $pGlu=58; $pLip=12; }
            }
        }

        // Final conversion to Grams
        $gProt = round(($apportCalorique * ($pProt/100)) / 4);
        $gGlu = round(($apportCalorique * ($pGlu/100)) / 4);
        $gLip = round(($apportCalorique * ($pLip/100)) / 9);

        return [
            'distribution' => [
                'proteines_percent' => $pProt,
                'glucides_percent' => $pGlu,
                'lipides_percent' => $pLip
            ],
            'grammes' => [
                'proteines' => $gProt,
                'glucides' => $gGlu,
                'lipides' => $gLip
            ]
        ];
    }

    private function generateNutritionInterventionPlan($user, $metrics, $responses, $pathologies)
    {
        $sections = [];

        // 1. Antécédents & Diabète + RTH Constat
        $sections[] = $this->getAntecedentsAdvice($metrics, $responses, $pathologies);

        // 2. Médicaments
        $sections[] = $this->getMedicationAdvice($responses);

        // 3. Habitudes Alimentaires (Fruits/Légumes, Grignotage, Boissons)
        $sections[] = $this->getDietaryHabitsAdvice($responses);

        // 4. Mode de vie (Sommeil, Apnée, Sédentarité)
        $sections[] = $this->getLifestyleAdvice($responses);

        // 5. Comportement (Stress, Satiété)
        $sections[] = $this->getBehavioralAdvicePlan($responses);

        // 6. Activité Physique & Postures
        $sections[] = $this->getPhysicalActivityAdvice($user, $responses);

        // 7. Aide et Gestion du Poids (Historique)
        $sections[] = $this->getWeightManagementHistoryAdvice($responses);

        // 8. Psychologique et Soutien
        $sections[] = $this->getPsychologicalAdvice($responses);

        // 9. Surveillance Santé
        $sections[] = $this->getHealthMonitoringAdvice($responses);

        // 10. Principes Transversaux et Encouragements
        $sections[] = $this->getTransversalAdvice();

        return array_filter($sections);
    }

    private function getAntecedentsAdvice($metrics, $responses, $pathologies)
    {
        $rth = $metrics['rth'] ?? 0;
        $threshold = $metrics['rth_threshold'] ?? 0.85;
        $gender = ($metrics['is_male'] ?? false) ? 'homme' : 'femme';

        $title = "Antécédents et Risques Métaboliques";
        $content = "";

        if ($pathologies['diabetes']) {
            $content .= "✅ SI OUI - Diabète type 2 présent :\n";
            $content .= "• Privilégier les glucides à index glycémique bas.\n";
            $content .= "• Fractionner les repas (éviter les gros volumes).\n";
            $content .= "• Surveiller les portions de féculents.\n";
            $content .= "• Augmenter les fibres (légumes verts).\n\n";

            $content .= "📢 CONSTAT CAPITAL : D'après le calcul de votre RTH ($rth) sur la base de vos données personnelles par notre système, nous remarquons une évidence par rapport à votre profil (seuil $threshold pour un(e) $gender), ce qui confirme nos affirmations sur les risques accourus au diabète de type 2. De ce fait, nous vous exhortons à prendre en compte nos conseils et notre guide alimentaire.\n\n";
        }

        if ($pathologies['hypertension']) {
            $content .= "❤️ HYPERTENSION ARTÉRIELLE :\n";
            $content .= "• Réduire drastiquement le sel < 5g/jour.\n";
            $content .= "• Privilégier les aliments riches en potassium (banane, avocat, épinards).\n";
            $content .= "• Éviter les graisses saturées et la friture (sauce graine, huile de palme).\n\n";
        }

        if ($pathologies['cardio']) {
            $content .= "💙 MALADIES CARDIOVASCULAIRES :\n";
            $content .= "• Réduire les graisses saturées et trans.\n";
            $content .= "• Privilégier les oméga-3 (poissons gras locaux : maquereau, sardines fraîches).\n\n";
        }

        if (!$pathologies['diabetes'] && !$pathologies['hypertension'] && !$pathologies['cardio']) {
            $content .= "❌ SI NON - Aucun antécédent :\n";
            $content .= "• Maintenir une alimentation équilibrée actuelle.\n";
            $content .= "• Surveiller les portions pour une perte de poids progressive.\n";
            $content .= "• Privilégier les aliments peu transformés et rester actif physiquement.\n";
        }

        return ['titre' => $title, 'contenu' => $content];
    }

    private function getMedicationAdvice($responses)
    {
        $q70 = $responses[70]['text'] ?? '';
        $q69 = $responses[69]['text'] ?? '';
        
        if (empty($q70) && empty($q69)) {
            $content = "❌ SI NON - Aucun médicament :\n";
            $content .= "• Prévention pure par l'alimentation et surveillance régulière de la glycémie.\n";
            $content .= "• Favoriser les glucides à IG bas et répartir les apports sur la journée.\n";
            $content .= "• Éviter les sucres rapides et augmenter les fibres.\n";
            return ['titre' => "Adaptations liées aux Médicaments", 'contenu' => $content];
        }

        $content = "💊 ADAPTATIONS LIÉES À VOS MÉDICAMENTS (WWW.txt) :\n\n";
        $combinedText = $q70 . ' ' . $q69;

        // TYPE 1 : Glycémie
        if (preg_match('/Metformine|Glimepiride|Gliclazide|Sitagliptine|Empagliflozine|Dapagliflozine|Liraglutide|Semaglutide/i', $combinedText)) {
            $content .= "🟦 MÉDICAMENTS POUR BAISSER LA GLYCÉMIE :\n";
            $content .= "• Metformine : prendre pendant/après repas (éviter nausées).\n";
            $content .= "• Éviter repas trop riches en graisses.\n";
            $content .= "• Soutien SGLT2 : Boire beaucoup d'eau (surtout si Empagliflozine).\n\n";
        }

        // TYPE 2 : Insuline
        if (preg_match('/Insuline|Humalog|Lantus|Tresiba/i', $combinedText)) {
            $content .= "🟨 INSULINE :\n";
            $content .= "• Respecter les horaires fixes des repas et compter précisément les glucides.\n";
            $content .= "🎒 KIT URGENCE HYGOGLYCÉMIE : Toujours avoir sur soi : 3 morceaux de sucre, 150ml de jus d'orange, ou 2-3 biscuits secs locaux.\n\n";
        }

        // TYPE 3 : Perte de poids
        if (preg_match('/Orlistat|Liraglutide|Semaglutide|Saxenda|Wegovy/i', $combinedText)) {
            $content .= "🟧 MÉDICAMENTS POUR PERDRE DU POIDS :\n";
            if (stripos($combinedText, 'Orlistat') !== false) {
                $content .= "• Orlistat : Limiter graisses à 15g/repas. Prendre des multivitamines.\n";
            }
            if (preg_match('/Liraglutide|Semaglutide/i', $combinedText)) {
                $content .= "• Liraglutide/Semaglutide : Manger lentement. Si nausées : éviter gras et épices fortes.\n";
            }
            $content .= "\n";
        }

        // TYPE 4 : Tension
        if (preg_match('/IEC|Ramipril|ARA2|Losartan|Bêta-bloquants|Bisoprolol|Diurétiques/i', $combinedText)) {
            $content .= "🟥 MÉDICAMENTS POUR LA TENSION ARTÉRIELLE :\n";
            $content .= "• Limiter le sel. Boire suffisamment d'eau.\n";
            $content .= "• Favoriser le potassium (banane, épinards, avocat).\n";
            if (stripos($combinedText, 'Bêta-bloquants') !== false) {
                $content .= "• Attention Bêta-bloquants : peuvent masquer les signes d'hypoglycémie.\n";
            }
            $content .= "\n";
        }

        // TYPE 5 : Cholestérol
        if (preg_match('/Statines|Atorvastatine|Simvastatine|cholestérol/i', $combinedText)) {
            $content .= "🟩 MÉDICAMENTS POUR LE CHOLESTÉROL :\n";
            $content .= "• Prendre le soir. ÉVITER LE PAMPLEMOUSSE.\n";
            $content .= "• Favoriser oméga-3 et fibres solubles.\n\n";
        }

        return ['titre' => "Gestion des Médicaments", 'contenu' => $content];
    }

    private function getDietaryHabitsAdvice($responses)
    {
        $q71 = $responses[71]['text'] ?? '';
        $q72 = $responses[72]['text'] ?? '';
        $q73 = $responses[73]['text'] ?? '';
        $q75 = $responses[75]['text'] ?? '';

        $content = "";

        // Typical Day (Q71)
        if (!empty($q71) && stripos($q71, 'Aucun') === false) {
            $content .= "🍽️ ANALYSE DE VOTRE JOURNÉE : Vos habitudes ($q71) permettent de cerner vos réalités quotidiennes pour mieux vous orienter.\n\n";
        }

        // Fruits & Légumes (Q72)
        if (stripos($q72, '1-2') !== false) {
            $content .= "🥦 FRUITS & LÉGUMES (INSUFFISANT) : Objectif 5 portions min. Commencez par papaye + concombre en collation, et carotte + mangue verte le soir.\n";
        } elseif (stripos($q72, '3-4') !== false) {
            $content .= "🥦 FRUITS & LÉGUMES (BON DÉBUT) : Optimisez vers 5-7 portions. Intégrez des légumes dans toutes les sauces (concombre, tomate) et un fruit à chaque collation.\n";
        } elseif (stripos($q72, '5') !== false) {
            $content .= "🥦 FRUITS & LÉGUMES (EXCELLENT) : Focus sur la variété. Privilégiez les légumes verts (épinards, gboma dessi) et crucifères (chou). Limitez les fruits à 2-3 max/jour.\n";
        }
        $content .= "\n";

        // Grignotage (Q73/Q74)
        if (stripos($q73, 'Oui') !== false) {
            $q74 = $responses[74]['text'] ?? '';
            $content .= "🍿 STRATÉGIE GRIGNOTAGE (PLAN 4 SEMAINES) :\n";
            if (preg_match('/sucré|gras|beignets|galettes|sodas|bonbons/i', $q74)) {
                $content .= "• Transition : Remplacer beignets (akara frit) par akara au four, et sodas par bissap maison non sucré.\n";
                $content .= "• Semaine 1 : Remplacer 1 grignotage/jour. Semaine 3 : Tous remplacés. Semaine 4 : Consolidation.\n";
            } else {
                $content .= "• Vos choix (fruits, noix) sont bons. Attention aux portions : 30g max pour les noix (creux de la main).\n";
            }
            $content .= "\n";
        }

        // Boissons (Q75)
        if (stripos($q75, 'Tous les jours') !== false) {
            $content .= "🥤 SEVRAGE BOISSONS (PLAN 4 SEMAINES) :\n";
            $content .= "• Semaine 1 : Alterner 1 jour sucré / 1 jour substitut (Eau de coco, Jus gingembre-citron).\n";
            $content .= "• Semaine 2-3 : Diluer vos jus à 50% avec de l'eau.\n";
            $content .= "• Semaine 4+ : Élimination complète. Boissons sucrées = occasionnelles uniquement.\n";
        } elseif (stripos($q75, 'Une fois par semaine') !== false) {
            $content .= "🥤 BOISSONS : Acceptable si modéré. Privilégiez le jus de baobab (vitamines, fibres) ou de bissap peu sucré en fin de repas.\n";
        }
        $content .= "\n";

        return $content ? ['titre' => "Habitudes Alimentaires", 'contenu' => $content] : null;
    }

    private function getLifestyleAdvice($responses)
    {
        $q76 = $responses[76]['text'] ?? '';
        $q77 = $responses[77]['text'] ?? '';
        $q81 = $responses[81]['text'] ?? '';

        $content = "";

        // Sommeil (Q76)
        if (stripos($q76, 'Moins de 6') !== false || stripos($q76, '6h-7h') !== false) {
            $content .= "😴 PLAN D'ACTION SOMMEIL (7-8H) :\n";
            $content .= "• Étape 1 : Identifier les causes (écrans, café après 14h, stress).\n";
            $content .= "• Étape 2 : Avancer l'heure du coucher de 15 min/semaine.\n";
            $content .= "• Dîner anti-insomnie : Soupe de légumes verts tiède + poisson grillé + patate douce (150g).\n\n";
        }

        // Apnée (Q77)
        if (stripos($q77, 'Oui') !== false) {
            $content .= "🌬️ STRATÉGIE ANTI-APNÉE :\n";
            $content .= "• Dîner ultra-léger AVANT 18H : Soupe claire + poisson vapeur + salade (PAS de féculents le soir).\n";
            $content .= "• Position : Dormir sur le côté, surélever la tête de lit de 15-20 cm. Arrêt alcool complet.\n\n";
        }

        // Sédentarité (Q81)
        if (stripos($q81, '8h') !== false) {
            $content .= "💺 SÉDENTARITÉ EXTRÊME (PLAN DE TRANSFORMATION) :\n";
            $content .= "• Règle d'or : JAMAIS plus de 50 min assis continu. Réglez une alarme chaque heure.\n";
            $content .= "• Bureautique active : Téléphonez debout, utilisez les escaliers, marchez aux pauses.\n\n";
        } elseif (stripos($q81, '6h-8h') !== false) {
            $content .= "💺 SÉDENTARITÉ : Position assise prolongée. Levez-vous toutes les 30 min. 45-60 min d'activité modérée quotidienne obligatoire pour compenser.\n\n";
        }

        return $content ? ['titre' => "Mode de Vie", 'contenu' => $content] : null;
    }

    private function getPhysicalActivityAdvice($user, $responses)
    {
        $q84 = $responses[84]['text'] ?? '';
        $q81 = $responses[81]['text'] ?? '';
        $q82 = $responses[82]['text'] ?? '';
        $q80 = $responses[80]['text'] ?? '';
        
        $activityLevel = $user->niveau_d_activite_physique;
        $age = $user->age ?? 30;

        // Tanaka Formula: FCmax = 208 - 0.7 * age
        $fcMax = round(208 - (0.7 * $age));
        
        $content = "🏃 ACTIVITÉ PHYSIQUE ET POSTURES (WWW.txt) :\n\n";
        $content .= "🎯 VOS ZONES DE FRÉQUENCE CARDIAQUE (Tanaka) :\n";
        $content .= "• FC Maximale : $fcMax bpm.\n";
        $content .= "• Zone 1 (50-60%) : " . round($fcMax*0.5) . "-" . round($fcMax*0.6) . " bpm (Échauffement/Récupération).\n";
        $content .= "• Zone 2 (60-70%) : " . round($fcMax*0.6) . "-" . round($fcMax*0.7) . " bpm (Brûle-graisses/Endurance).\n";
        $content .= "• Zone 3 (70-80%) : " . round($fcMax*0.7) . "-" . round($fcMax*0.8) . " bpm (Amélioration cardio).\n";
        $content .= "• Échelle RPE (1-10) : Visez un effort de 4-6 (essoufflement léger, peut parler).\n\n";

        if ($activityLevel === 'Sédentaire') {
            $content .= "📅 PROGRAMME 'ZÉRO VERS ACTIF' (12 SEMAINES) :\n";
            $content .= "• Semaines 1-4 : 3x15 min de marche lente (Zone 1).\n";
            $content .= "• Semaines 5-8 : 3x30 min de marche rapide (Zone 2).\n";
            $content .= "• Semaines 9-12 : 4x40 min (Alterner Zone 2 et 3).\n\n";
        }

        $content .= "🥣 NUTRITION SPORTIVE BÉNINOISE :\n";
        $content .= "• Pré-effort (2h avant) : 1 banane douce ou bouillie de mil légère (sans sucre ajouté).\n";
        $content .= "• Post-effort (30-60 min) : 1 œuf bouilli ou 20g de noix de cajou nature + 300ml d'eau.\n\n";

        if (!empty($q82) && stripos($q82, 'Aucun') === false) {
            $content .= "• Vos Loisirs ($q82) : Intégrez-les comme séances de plaisir le week-end.\n";
        }

        return ['titre' => "Activité Physique", 'contenu' => $content];
    }

    private function getWeightManagementHistoryAdvice($responses)
    {
        $q79 = $responses[79]['text'] ?? ''; // Tentatives de perte de poids
        $q801 = $responses[80]['text'] ?? ''; // Méthode utilisée (Question type texte)

        $content = "⚖️ GESTION DU POIDS :\n";

        if (stripos($q79, 'Plusieurs') !== false || stripos($q79, 'Oui') !== false) {
            $content .= "• Historique : Le fameux 'effet yoyo' est souvent dû à des régimes trop restrictifs. Notre approche se veut durable.\n";
        }
        
        if (!empty($q801)) {
            $content .= "• Analyse de vos méthodes passées ($q801) : Nous allons corriger les erreurs de répartition des macronutriments pour stabiliser votre métabolisme.\n";
        }

        return ['titre' => "Aide et Gestion du Poids", 'contenu' => $content];
    }

    private function getBehavioralAdvicePlan($responses)
    {
        $q85 = $responses[85]['text'] ?? '';
        $q86 = $responses[86]['text'] ?? '';

        $content = "🧠 COMPORTEMENT ET SATIÉTÉ (WWW.txt) :\n\n";

        if (stripos($q85, 'Oui') !== false) {
            $content .= "⚠️ TECHNIQUE HALT (Faim Émotionnelle) :\n";
            $content .= "• Avant de manger hors repas, posez-vous la question : AI-JE FAIM ?\n";
            $content .= "• H (Hungry) : Faim réelle ? A (Angry) : En colère ? L (Lonely) : Seul(e) ? T (Tired) : Fatigué(e) ?\n";
            $content .= "• Si AL ou T : Remplacez la nourriture par 10 min de marche, appel à un ami ou repos.\n\n";
        }

        if (stripos($q86, 'Oui') !== false) {
            $content .= "🍽️ REDÉCOUVERTE DE LA FAIM (PLAN 4 SEMAINES) :\n";
            $content .= "• Semaine 1 : Posez vos couverts entre chaque bouchée. Mâchez 20 fois.\n";
            $content .= "• Semaine 2 : Évaluez votre faim (1 à 10) avant et après chaque repas.\n";
            $content .= "• Semaine 3-4 : Arrêtez-vous dès la première impression de satiété (Règle d'or).\n\n";
        }

        return $content ? ['titre' => "Gestion des Comportements", 'contenu' => $content] : null;
    }

    private function getPsychologicalAdvice($responses)
    {
        $q87 = $responses[87]['text'] ?? '';
        $q88 = $responses[88]['text'] ?? '';
        
        $content = "🤝 MOTIVATION ET PSYCHOLOGIE :\n\n";
        
        if (stripos($q87, 'Oui') !== false) {
            $content .= "🧐 ANALYSE DE VOS RÉGIMES PASSÉS :\n";
            if (preg_match('/Restrictif|Hypocalorique|Privation/i', $q88)) {
                $content .= "• Régimes restrictifs : Ils ralentissent votre métabolisme. Ici, nous misons sur la qualité.\n";
            }
            if (preg_match('/Keto|Cétogène|Sans sucre/i', $q88)) {
                $content .= "• Keto/Sans sucre : Difficiles à tenir socialement. Notre guide réintègre les bons glucides béninois.\n";
            }
            if (preg_match('/Jeûne|Intermittent|IF|Omad/i', $q88)) {
                $content .= "• Jeûne intermittent : Peut causer des compulsions le soir. Le fractionnement 3 repas + 2 collations est préférable ici.\n";
            }
        }

        $content .= "\n💪 VOTRE FORCE : Vos tentatives passées ne sont pas des échecs, mais des apprentissages. Cette fois est la bonne car elle est progressive et adaptée à VOTRE culture.\n";

        return ['titre' => "Accompagnement Psychologique", 'contenu' => $content];
    }

    private function getTransversalAdvice()
    {
        $content = "🌟 RÉSUMÉ ET PRINCIPES TRANSVERSAUX (WWW.txt) :\n\n";
        $content .= "✅ À PRIVILÉGIER : Igname, Patate douce, Riz complet, Fonio, Poisson frais, Avocat, Noix de cajou nature.\n";
        $content .= "❌ À LIMITER : Fritures (Aloco, beignets), Cubes bouillon (Jumbo), Pain blanc, Boissons gazeuses.\n";
        $content .= "🧂 RÉDUCTION DU SEL : Utilisez Ail, Oignon, Gingembre, Citron et Herbes pour le goût.\n\n";
        
        $content .= "⚖️ NOTICES RÉGLEMENTAIRES :\n";
        $content .= "• Ce programme est un soutien nutritionnel et ne remplace pas une consultation médicale.\n";
        $content .= "• En cas de malaise ou douleur inhabituelle lors de l'activité physique, arrêtez immédiatement et consultez.\n\n";

        $content .= "🌟 MESSAGE D'ENCOURAGEMENT : Vous faites cela pour votre SANTÉ et votre ÉNERGIE. Chaque petit pas compte. Vous méritez de vous sentir bien dans votre corps. Vous êtes CAPABLE de réussir !";

        return ['titre' => "Principes Transversaux", 'contenu' => $content];
    }

    private function getHealthMonitoringAdvice($responses)
    {
        $q83 = $responses[83]['text'] ?? '';
        $q84 = $responses[84]['text'] ?? '';
        
        $content = "📊 SURVEILLANCE SANTÉ :\n";
        
        if (stripos($q83, 'Oui') !== false) {
            $content .= "• Votre fréquence cardiaque au repos (FCR) est de $q84 bpm. ";
            $content .= "Une baisse de la FCR avec le temps est signe d'une meilleure forme cardiovasculaire.\n";
            $content .= "• Monitoring : Mesurez votre FCR le matin au réveil sur 3-5 jours pour une référence stable.\n";
        } else {
            $content .= "• Nous recommandons de mesurer votre FCR (le matin au réveil) pour suivre l'impact de l'activité physique sur votre cœur.\n";
        }

        return ['titre' => "Surveillance Santé", 'contenu' => $content];
    }

    private function generateDynamicFoodGuide($user, $macroGrams, $pathologies)
    {
        $generator = new \App\Services\MenuGenerator($macroGrams, $this->nutritionDataService->getMenus(), $user->niveau_d_activite_physique, 90, $pathologies);
        $result = $generator->generate();

        return [
            'menu_journalier' => $result['menu'],
            'facteurs' => $result['average_factors'],
            'conseils' => "Votre guide alimentaire sur 90 jours a été généré via notre algorithme de précision. Les portions sont ajustées dynamiquement pour votre TDEE et vos objectifs."
        ];
    }
}
