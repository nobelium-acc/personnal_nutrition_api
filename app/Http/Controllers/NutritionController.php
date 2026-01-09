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

        // Restriction to ID 1 (Obésité modérée) as requested
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

        // Extract Advanced Info from Reponses (Specific to MC ID 1)
        $objective = 'Perte de poids';
        $deficitValue = 0;
        $weightLossRange = 'Inconnu';
        $pathologies = ['diabetes' => false, 'hypertension' => false, 'cardio' => false];
        $responsesMap = [];

        foreach ($user->reponse as $rep) {
            $qid = $rep->question_id;
            $text = $rep->description ?: '';
            $answerId = $rep->question_possible_answer_id;
            
            // If answer is linked to a possible_answer, use that value instead
            if ($answerId) {
                $possibleAnswer = DB::table('question_possible_answers')->where('id', $answerId)->first();
                if ($possibleAnswer) $text = $possibleAnswer->value;
            }

            $responsesMap[$qid] = [
                'text' => $text,
                'id' => $answerId
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
                case 66: // Antécédents
                    // Logic handled below in Q67/Q70
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
        $macros = $this->calculateMacrosEnhanced($user->niveau_d_activite_physique, $deficitValue, $isWeightLoss, $isFitness, $apportCalorique, $weightLossRange, $pathologies);

        // NEW: Generate Intervention Plan and Food Guide
        $planIntervention = $this->generateNutritionInterventionPlan($user, $metrics, $responsesMap, $pathologies);
        $guideData = $this->generateDynamicFoodGuide($user, $macros['grammes']);

        return response()->json([
            'success' => true,
            'user_profile' => [
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'sexe' => $isMale ? 'Homme' : 'Femme',
                'objectif' => $objective,
                'pathologies_detectees' => array_keys(array_filter($pathologies))
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
            'menu_journalier' => $guideData['menu_30_jours'],
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
        $isObeseIMG = $img >= $imgThreshold;
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

    private function calculateMacrosEnhanced($activity, $deficit, $isWeightLoss, $isFitness, $apportCalorique, $weightLossRange, $pathologies)
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

        return array_filter($sections);
    }

    private function getAntecedentsAdvice($metrics, $responses, $pathologies)
    {
        $hasDiabetes = $pathologies['diabetes'];
        $rth = $metrics['rth'];
        $threshold = $metrics['rth_threshold'];
        $gender = $metrics['is_male'] ? 'homme' : 'femme';

        $title = "Antécédents et Risques Métaboliques";
        $content = "";

        if ($hasDiabetes) {
            $content .= "🎯 DIABÈTE DE TYPE 2 DÉTECTÉ : \n";
            $content .= "• Privilégier les glucides à index glycémique bas (IG bas).\n";
            $content .= "• Fractionner les repas pour éviter les gros volumes.\n";
            $content .= "• Surveiller strictement les portions de féculents.\n";
            $content .= "• Augmenter la part de légumes verts pour les fibres.\n\n";

            if ($rth > $threshold) {
                $content .= "⚠️ CONSTAT CAPITAL : Votre RTH de " . $rth . " (seuil " . $threshold . " pour un " . $gender . ") confirme un risque accru lié à la répartition des graisses abdominales, ce qui corrobore vos antécédents de diabète de type 2. Il est impératif de suivre nos conseils pour combattre efficacement cette obésité.";
            }
        } else {
            $content .= "✅ CONSEILS PRÉVENTIFS : \n";
            $content .= "• Maintenir une alimentation équilibrée.\n";
            $content .= "• Surveiller les portions pour une perte de poids progressive.\n";
            $content .= "• Privilégier les aliments peu transformés et rester actif physiquement.";
        }

        return ['titre' => $title, 'contenu' => $content];
    }

    private function getMedicationAdvice($responses)
    {
        $q70 = $responses[70]['text'] ?? '';
        if (stripos($q70, 'Aucun') !== false || empty($q70)) return null;

        $content = "💊 ADAPTATIONS LIÉES À VOS MÉDICAMENTS :\n";

        if (stripos($q70, 'glycémie') !== false || stripos($q70, 'Metformine') !== false) {
            $content .= "• Metformine : Prendre pendant ou après le repas pour éviter les nausées. Éviter les repas trop gras.\n";
        }
        if (stripos($q70, 'Insuline') !== false) {
            $content .= "• Insuline : Respecter des horaires fixes. Toujours avoir une collation 'hypo' (sucre, jus) à portée de main.\n";
        }
        if (stripos($q70, 'poids') !== false || stripos($q70, 'Orlistat') !== false) {
            $content .= "• Orlistat : Limiter les graisses à 15g/repas pour éviter les selles grasses. Envisager des multivitamines le soir.\n";
        }
        if (stripos($q70, 'tension') !== false) {
            $content .= "• Tension : Régime hyposodé (limiter le sel). Attention aux aliments riches en potassium si vous prenez des IEC/ARA2.\n";
        }
        if (stripos($q70, 'cholestérol') !== false) {
            $content .= "• Statines : Éviter le pamplemousse. Réduire les graisses saturées (viandes grasses, friture).\n";
        }

        return ['titre' => "Gestion des Médicaments", 'contenu' => $content];
    }

    private function getDietaryHabitsAdvice($responses)
    {
        $q72 = $responses[72]['text'] ?? '';
        $q73 = $responses[73]['text'] ?? '';
        $q75 = $responses[75]['text'] ?? '';

        $content = "";

        // Fruits & Légumes
        if (stripos($q72, '1-2') !== false) {
            $content .= "🥦 FRUITS & LÉGUMES : Quantité insuffisante. Visez progressivement 5 portions/jour. Commencez par ajouter un fruit aux collations.\n";
        } elseif (stripos($q72, '5') !== false) {
            $content .= "🥦 FRUITS & LÉGUMES : Excellent ! Continuez à varier les couleurs pour maximiser les antioxydants.\n";
        }

        // Grignotage
        if (stripos($q73, 'Oui') !== false) {
            $content .= "🍿 GRIGNOTAGE : Identifiez les déclencheurs (stress, ennui). Remplacez les beignets ou sodas par des arachides grillées nature (30g) ou un fruit.\n";
        }

        // Boissons
        if (stripos($q75, 'Tous les jours') !== false) {
            $content .= "🥤 BOISSONS : Priorité absolue ! Réduisez les sodas et l'alcool. Remplacez par de l'eau citronnée, du Bissap maison non sucré ou du Kinkeliba.\n";
        }

        return $content ? ['titre' => "Habitudes Alimentaires", 'contenu' => $content] : null;
    }

    private function getLifestyleAdvice($responses)
    {
        $q76 = $responses[76]['text'] ?? '';
        $q77 = $responses[77]['text'] ?? '';
        $q81 = $responses[81]['text'] ?? '';

        $content = "";

        if (stripos($q76, 'Moins de 6') !== false) {
            $content .= "😴 SOMMEIL : Le manque de sommeil favorise la faim et le stockage des graisses (cortisol). Visez 7-8h pour stabiliser votre glycémie.\n";
        }

        if (stripos($q77, 'Oui') !== false) {
            $content .= "🌬️ APNÉE DU SOMMEIL : Très liée à l'obésité. Dîner ultra-léger (soupe + poisson) au moins 4-5h avant le coucher peut grandement vous soulager.\n";
        }

        if (stripos($q81, '8h') !== false || stripos($q81, '6h-8h') !== false) {
            $content .= "💺 SÉDENTARITÉ : Position assise prolongée. Levez-vous 5 min toutes les heures. La sédentarité est le 'nouveau tabagisme'.\n";
        }

        return $content ? ['titre' => "Mode de Vie", 'contenu' => $content] : null;
    }

    private function getPhysicalActivityAdvice($user, $responses)
    {
        $q84 = $responses[84]['text'] ?? ''; // Type d'activité (Question non mappée précédemment mais présente dans l2.txt)
        $q81 = $responses[81]['text'] ?? ''; // Sédentarité
        $activityLevel = $user->niveau_d_activite_physique;

        $content = "🏃 ACTIVITÉ PHYSIQUE ET POSTURES :\n";
        
        if ($activityLevel === 'Sédentaire') {
            $content .= "• Objectif : Briser la sédentarité. Visez 30 min de marche quotidienne.\n";
            $content .= "• Posture : Si vous travaillez assis, utilisez un réhausseur d'écran pour garder le dos droit.\n";
        } else {
            $content .= "• Bravo pour votre niveau : " . $activityLevel . ". Continuez ainsi !\n";
        }

        if (stripos($q81, '8h') !== false) {
            $content .= "• Vigilance : Rester assis plus de 8h/jour augmente les risques cardiovasculaires. Faites des étirements toutes les 2h.\n";
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

        $content = "";

        if (stripos($q85, 'Oui') !== false) {
            $content .= "🧠 ALIMENTATION ÉMOTIONNELLE : Vous mangez par stress ou ennui. Avant de manger, buvez un grand verre d'eau et attendez 10 min pour évaluer la vraie faim.\n";
        }

        if (stripos($q86, 'Oui') !== false) {
            $content .= "🍽️ SATIÉTÉ : Vous finissez votre assiette sans faim. Apprenez à laisser des restes. Votre corps n'est pas une poubelle, mieux vaut jeter que surcharger votre métabolisme.\n";
        }

        return $content ? ['titre' => "Comportements et Psychologie", 'contenu' => $content] : null;
    }

    private function generateDynamicFoodGuide($user, $macroGrams)
    {
        $activity = $user->niveau_d_activite_physique;
        $allMenus = $this->nutritionDataService->getMenus();
        $base = $allMenus[$activity] ?? $allMenus['Sédentaire'];

        // Determine snack count from l3.txt
        $snackCount = 0;
        switch ($activity) {
            case 'Sédentaire': $snackCount = rand(0, 1); break;
            case 'Légèrement actif': $snackCount = 1; break;
            case 'Modérément actif': $snackCount = rand(1, 2); break;
            case 'Très actif': $snackCount = 2; break;
            case 'Extrêmement actif': $snackCount = 2; break;
        }

        $menu30Jours = [];
        $globalFactors = ['g' => 0, 'p' => 0, 'l' => 0];
        $countDays = 30;

        for ($i = 1; $i <= $countDays; $i++) {
            $dayKey = ($i % 2 === 0) ? 'jour2' : 'jour1';
            
            // Randomly select Options for each meal
            $dailyOptions = [
                'Petit-déjeuner' => (rand(0, 1) ? 'Option A' : 'Option B'),
                'Déjeuner' => (rand(0, 1) ? 'Option A' : 'Option B'),
                'Dîner' => (rand(0, 1) ? 'Option A' : 'Option B')
            ];

            $dailyMeals = [];
            foreach ($dailyOptions as $moment => $opt) {
                $dailyMeals[$moment] = $base[$dayKey][$opt][$moment];
            }

            // Add snacks if needed
            $snacks = [];
            if ($snackCount > 0 && isset($base['collations'])) {
                $availableSnacks = $base['collations'];
                $selectedSnacks = array_rand($availableSnacks, min($snackCount, count($availableSnacks)));
                if (!is_array($selectedSnacks)) $selectedSnacks = [$selectedSnacks];
                
                foreach ($selectedSnacks as $index) {
                    $snacks[] = $availableSnacks[$index];
                }
            }

            $scalingResult = $this->scaleMealOption($dailyMeals, $macroGrams, $snacks);
            
            $menu30Jours[] = [
                'jour' => $i,
                'type' => ucfirst($dayKey),
                'repas' => $scalingResult['adjusted'],
                'collations' => $scalingResult['snacks']
            ];

            // Accrue factors for average
            $globalFactors['g'] += $scalingResult['factors']['glucides'];
            $globalFactors['p'] += $scalingResult['factors']['proteines'];
            $globalFactors['l'] += $scalingResult['factors']['lipides'];
        }

        return [
            'menu_30_jours' => $menu30Jours,
            'facteurs' => [
                'glucides' => round($globalFactors['g'] / $countDays, 3),
                'proteines' => round($globalFactors['p'] / $countDays, 3),
                'lipides' => round($globalFactors['l'] / $countDays, 3),
                'final' => round(($globalFactors['g'] + $globalFactors['p'] + $globalFactors['l']) / ($countDays * 3), 3)
            ],
            'conseils' => "Les portions (en grammes) ont été calculées selon vos besoins spécifiques sur 30 jours. Priorité : respectez les portions de glucides."
        ];
    }


    private function scaleMealOption($mealsByMoment, $targetGrams, $snacks = [])
    {
        $adjusted = [];
        
        // Collation calculation
        $snackG = 0; $snackP = 0; $snackL = 0;
        $snacksAdjusted = [];
        foreach ($snacks as $s) {
            $snackG += $s['g'];
            $snackP += $s['p'];
            $snackL += $s['l'];
            $snacksAdjusted[] = [
                'nom' => $s['nom'],
                'portion' => 'Portion standard',
                'details' => "Macros: {$s['p']}g P, {$s['g']}g G, {$s['l']}g L"
            ];
        }

        // Target for main meals (Priority Logic: subtract snacks first)
        $mealTargetG = max(0, $targetGrams['glucides'] - $snackG);
        $mealTargetP = max(0, $targetGrams['proteines'] - $snackP);
        $mealTargetL = max(0, $targetGrams['lipides'] - $snackL);

        // Calculate base totals for main meals
        $baseTotals = ['g' => 0, 'p' => 0, 'l' => 0];
        foreach ($mealsByMoment as $moment => $items) {
            foreach ($items as $item) {
                $baseTotals['g'] += $item['g'];
                $baseTotals['p'] += $item['p'];
                $baseTotals['l'] += $item['l'];
            }
        }

        // Factors based on Priority (l3.txt)
        $factorG = $mealTargetG / max(1, $baseTotals['g']);
        $factorP = $mealTargetP / max(1, $baseTotals['p']);
        $factorL = $mealTargetL / max(1, $baseTotals['l']);

        // Constraints: ideal 0.5 - 1.5, max 1.8
        $factorG = max(0.4, min(1.8, $factorG));
        $factorP = max(0.4, min(1.8, $factorP));
        $factorL = max(0.4, min(1.8, $factorL));

        foreach ($mealsByMoment as $moment => $items) {
            $itemsAdjusted = [];
            foreach ($items as $item) {
                // Determine which macro dominates this ingredient
                $isCarb = ($item['g'] > 15 && $item['g'] > $item['p'] * 1.5);
                $isProt = ($item['p'] > 5 && $item['p'] > $item['g']);
                
                $f = $factorL;
                if ($isCarb) $f = $factorG;
                elseif ($isProt) $f = $factorP;

                $newPortion = round($item['base'] * $f);
                $itemsAdjusted[] = [
                    'nom' => $item['nom'],
                    'portion_recommandee' => $newPortion . ' g',
                    'details' => "Macros: ".round($item['p']*$f,1)."g P, ".round($item['g']*$f,1)."g G, ".round($item['l']*$f,1)."g L"
                ];
            }
            $adjusted[$moment] = $itemsAdjusted;
        }

        return [
            'adjusted' => $adjusted,
            'snacks' => $snacksAdjusted,
            'factors' => [
                'glucides' => $factorG,
                'proteines' => $factorP,
                'lipides' => $factorL
            ]
        ];
    }
}
