<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\ObesityInconsistencyMail;
use App\Mail\RthWarningMail;
use App\Mail\ImgInconsistencyMail;

use Illuminate\Support\Facades\DB;

class NutritionController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/nutrition/calculate",
     *     summary="Calculer les indicateurs nutritionnels (IMC, RTH, IMG, BMR, TDEE)",
     *     description="Effectue les calculs nutritionnels basés sur les données stockées de l'utilisateur (poids, taille, tours, etc.). Compare également le résultat avec le type d'obésité déclaré par l'utilisateur (via sa maladie chronique) et envoie un email en cas d'incohérence.",
     *     tags={"Nutrition"},
     *     security={{"bearerAuth":{}}},
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
     *     security={{"bearerAuth":{}}},
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
        $userId = $request->user_id ?? auth()->id();

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'User ID required'], 400);
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
        $deficit = 0;
        $weightLossRange = 'Inconnu';
        $pathologies = ['diabetes' => false, 'hypertension' => false, 'cardio' => false];

        foreach ($user->reponse as $rep) {
            $qid = $rep->question_id;
            $text = $rep->description ?: '';
            
            // If answer is linked to a possible_answer, use that value instead
            if ($rep->question_possible_answer_id) {
                $possibleAnswer = DB::table('question_possible_answers')->where('id', $rep->question_possible_answer_id)->first();
                if ($possibleAnswer) $text = $possibleAnswer->value;
            }

            switch ($qid) {
                case 89: // Objectif principal
                    $objective = $text;
                    break;
                case 90: // Kg à perdre
                    $weightLossRange = $text;
                    break;
                case 91: // Niveau de changement (Déficit)
                    if (preg_match('/(\d+)\s*kcal/i', $text, $matches)) {
                        $deficit = intval($matches[1]);
                    }
                    break;
                case 66: // Antécédents
                    if (stripos($text, 'Oui') !== false) {
                        // Logic handled below in Q67/Q70
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
        $apportCalorique = $tdee - $deficit;
        
        if ($isFitness) {
            // Fitness logic: Apport = TDEE - (TDEE × % déficit)
            // TDEE < 2000 -> 5%, 2000-3000 -> 4%, > 3000 -> 3%
            $pct = ($tdee < 2000) ? 0.05 : (($tdee <= 3000) ? 0.04 : 0.03);
            $calcDeficit = round($tdee * $pct);
            
            // Limit deficit to 300 kcal for fitness
            $deficit = min(300, $calcDeficit);
            $apportCalorique = $tdee - $deficit;
        }

        // Safety Check (1200 kcal for female, 1500 kcal for male)
        $minThreshold = $isMale ? 1500 : 1200;
        $lowCalNotification = false;
        
        if ($apportCalorique < $minThreshold) {
            $warningData = [
                'gender' => $isMale ? 'Homme' : 'Femme',
                'objectif' => $objective,
                'tdee' => round($tdee),
                'deficit' => $deficit,
                'apport' => round($apportCalorique)
            ];
            Mail::to($user->email)->send(new \App\Mail\LowCalorieWarningMail($user->nom . ' ' . $user->prenom, $warningData, $minThreshold));
            $lowCalNotification = true;
        }

        // Macronutrients Distribution
        $macros = $this->calculateMacrosEnhanced($user->niveau_d_activite_physique, $deficit, $isWeightLoss, $isFitness, $apportCalorique, $weightLossRange, $pathologies);

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
            'deficit_calorique' => $deficit,
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
            ]
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

        return ['bmr' => $bmr, 'tdee' => $tdee, 'is_male' => $isMale, 'height' => $height, 'weight' => $weight];
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
            // General balanced macros for Fitness
            $pProt = 30; $pGlu = 40; $pLip = 30;
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
}
