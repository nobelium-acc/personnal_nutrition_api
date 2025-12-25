<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\ObesityInconsistencyMail;
use App\Mail\RthWarningMail;
use App\Mail\ImgInconsistencyMail;

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

        // BMR/TDEE Calculation (reuse logic or private method)
        $metrics = $this->calculateBasicMetrics($user);
        if ($metrics instanceof \Illuminate\Http\JsonResponse) return $metrics;

        $tdee = $metrics['tdee'];
        $bmr = $metrics['bmr'];
        $isMale = $metrics['is_male'];

        // Extract Advanced Info from Reponses
        $objective = 'Perte de poids';
        $deficit = 0;
        $weightLossRange = 'Inconnu';

        foreach ($user->reponse as $rep) {
            $desc = $rep->question->description ?? '';
            if (stripos($desc, 'Quel est votre objectif principal') !== false) {
                $objective = $rep->description ?: $objective;
            } elseif (stripos($desc, 'quel niveau de changement êtes-vous prêt à suivre') !== false) {
                if (preg_match('/\((\d+)\s*kcal\)/i', $rep->description, $matches)) {
                    $deficit = intval($matches[1]);
                }
            } elseif (stripos($desc, 'Combien de kg voulez-vous perdre') !== false) {
                $weightLossRange = $rep->description ?: $weightLossRange;
            }
        }

        $isWeightLoss = (stripos($objective, 'Perte de poids') !== false);
        $isFitness = (stripos($objective, 'Améliorer votre forme physique') !== false);

        // Apport Calorique with Fitness Logic
        $apportCalorique = $tdee - $deficit;
        if ($isFitness) {
            $pct = ($tdee < 2000) ? 0.05 : (($tdee <= 3000) ? 0.04 : 0.03);
            $calcDeficit = $tdee * $pct;
            $deficit = round(min(300, max($deficit, $calcDeficit)));
            $apportCalorique = $tdee - $deficit;
        }

        // Safety Check
        $minThreshold = $isMale ? 1500 : 1200;
        $lowCalNotification = false;
        if ($apportCalorique < $minThreshold) {
            $warningData = [
                'gender' => $isMale ? 'Homme' : 'Femme',
                'objectif' => $objective,
                'tdee' => $tdee,
                'deficit' => $deficit,
                'apport' => $apportCalorique
            ];
            Mail::to($user->email)->send(new \App\Mail\LowCalorieWarningMail($user->nom . ' ' . $user->prenom, $warningData, $minThreshold));
            $lowCalNotification = true;
        }

        $declaredType = $user->maladieChronique ? $user->maladieChronique->type : 'Inconnu';
        $macros = $this->calculateMacros($declaredType, $user->niveau_d_activite_physique, $deficit, $isWeightLoss, $isFitness, $apportCalorique, $weightLossRange);

        return response()->json([
            'success' => true,
            'tdee' => $tdee,
            'apport_calorique' => $apportCalorique,
            'deficit_calorique' => $deficit,
            'macronutriments' => $macros,
            'low_calorie_notification' => $lowCalNotification
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

    private function calculateMacros($declaredType, $niveauPhysique, $deficit, $isWeightLoss, $isFitness, $apportCalorique, $weightLossRange = 'Inconnu')
    {
        // Default Distribution (General Case / Fallback)
        $pProt = 30; $pGlu = 40; $pLip = 30;

        // Pathologies Check
        $hasDiabetes = (stripos($declaredType, 'Diabète') !== false);
        $hasHypertension = (stripos($declaredType, 'Hypertension') !== false || stripos($declaredType, 'Tension') !== false);
        $hasCardio = (stripos($declaredType, 'Cardiovasculaire') !== false || stripos($declaredType, 'Coeur') !== false);
        $noPathology = !$hasDiabetes && !$hasHypertension && !$hasCardio;

        // Activity Level Mapping for Switch
        $act = $niveauPhysique; 

        if ($isWeightLoss) {
            // WEIGHT LOSS LOGIC

            // Determine Deficit Category (<5kg, 5-10kg, >10kg based on questionnaire answer or deficit value)
            $range = 'low'; // < 5kg
            if (stripos($weightLossRange, 'Plus de 10 kg') !== false) {
                $range = 'high';
            } elseif (stripos($weightLossRange, '5 - 10 kg') !== false) {
                $range = 'mid';
            } elseif (stripos($weightLossRange, 'Moins de 5 kg') !== false) {
                $range = 'low';
            } else {
                // Fallback to deficit value if range question not answered
                if ($deficit >= 700) { $range = 'high'; }
                elseif ($deficit >= 500) { $range = 'mid'; }
            }

            if ($hasDiabetes) {
                // TABLES 9, 12, 15
                if ($range === 'low') { // Table 9
                    if ($act === "Sédentaire") { $pProt=30; $pGlu=35; $pLip=35; }
                    elseif ($act === "Légèrement actif") { $pProt=30; $pGlu=40; $pLip=30; }
                    elseif ($act === "Modérément actif") { $pProt=25; $pGlu=45; $pLip=30; }
                    elseif ($act === "Très actif") { $pProt=25; $pGlu=48; $pLip=27; }
                    elseif ($act === "Extrêmement actif") { $pProt=25; $pGlu=50; $pLip=25; }
                } elseif ($range === 'mid') { // Table 12
                    if ($act === "Sédentaire") { $pProt=30; $pGlu=35; $pLip=35; } 
                     elseif ($act === "Légèrement actif") { $pProt=30; $pGlu=40; $pLip=30; }
                     elseif ($act === "Modérément actif") { $pProt=25; $pGlu=45; $pLip=30; }
                     elseif ($act === "Très actif") { $pProt=25; $pGlu=48; $pLip=27; }
                     elseif ($act === "Extrêmement actif") { $pProt=25; $pGlu=50; $pLip=25; }
                } elseif ($range === 'high') { // Table 15
                     if ($act === "Sédentaire") { $pProt=30; $pGlu=35; $pLip=35; }
                     elseif ($act === "Légèrement actif") { $pProt=28; $pGlu=40; $pLip=32; }
                     elseif ($act === "Modérément actif") { $pProt=25; $pGlu=43; $pLip=32; }
                     elseif ($act === "Très actif") { $pProt=25; $pGlu=48; $pLip=27; }
                     elseif ($act === "Extrêmement actif") { $pProt=25; $pGlu=50; $pLip=25; }
                }
            } elseif ($hasHypertension) {
                // TABLES 10, 13, 16
                if ($range === 'low') { // Table 10
                    if ($act === "Sédentaire") { $pProt=30; $pGlu=40; $pLip=30; }
                    elseif ($act === "Légèrement actif") { $pProt=28; $pGlu=44; $pLip=28; }
                    elseif ($act === "Modérément actif") { $pProt=25; $pGlu=45; $pLip=30; }
                    elseif ($act === "Très actif") { $pProt=25; $pGlu=48; $pLip=27; }
                    elseif ($act === "Extrêmement actif") { $pProt=25; $pGlu=50; $pLip=25; }
                } elseif ($range === 'mid') { // Table 13
                    if ($act === "Sédentaire") { $pProt=29; $pGlu=41; $pLip=30; }
                    elseif ($act === "Légèrement actif") { $pProt=29; $pGlu=44; $pLip=30; }
                    elseif ($act === "Modérément actif") { $pProt=25; $pGlu=45; $pLip=30; }
                    elseif ($act === "Très actif") { $pProt=25; $pGlu=48; $pLip=27; }
                    elseif ($act === "Extrêmement actif") { $pProt=25; $pGlu=50; $pLip=25; }
                } elseif ($range === 'high') { // Table 16
                    if ($act === "Sédentaire") { $pProt=30; $pGlu=40; $pLip=30; }
                    elseif ($act === "Légèrement actif") { $pProt=28; $pGlu=44; $pLip=28; }
                    elseif ($act === "Modérément actif") { $pProt=25; $pGlu=45; $pLip=30; }
                    elseif ($act === "Très actif") { $pProt=25; $pGlu=48; $pLip=27; }
                    elseif ($act === "Extrêmement actif") { $pProt=25; $pGlu=50; $pLip=25; }
                }
            } elseif ($hasCardio) {
                 // TABLES 11, 14, 17
                 if ($range === 'low') { // Table 11
                    if ($act === "Sédentaire") { $pProt=30; $pGlu=40; $pLip=30; }
                    elseif ($act === "Légèrement actif") { $pProt=30; $pGlu=44; $pLip=26; }
                    elseif ($act === "Modérément actif") { $pProt=25; $pGlu=45; $pLip=30; }
                    elseif ($act === "Très actif") { $pProt=25; $pGlu=48; $pLip=27; }
                    elseif ($act === "Extrêmement actif") { $pProt=25; $pGlu=50; $pLip=25; }
                 } elseif ($range === 'mid') { // Table 14
                    if ($act === "Sédentaire") { $pProt=30; $pGlu=41; $pLip=29; }
                    elseif ($act === "Légèrement actif") { $pProt=28; $pGlu=45; $pLip=27; }
                    elseif ($act === "Modérément actif") { $pProt=25; $pGlu=45; $pLip=30; }
                    elseif ($act === "Très actif") { $pProt=25; $pGlu=48; $pLip=27; }
                    elseif ($act === "Extrêmement actif") { $pProt=25; $pGlu=50; $pLip=25; }
                 } elseif ($range === 'high') { // Table 17
                    if ($act === "Sédentaire") { $pProt=30; $pGlu=40; $pLip=30; }
                    elseif ($act === "Légèrement actif") { $pProt=28; $pGlu=45; $pLip=27; }
                    elseif ($act === "Modérément actif") { $pProt=25; $pGlu=45; $pLip=30; }
                    elseif ($act === "Très actif") { $pProt=25; $pGlu=48; $pLip=27; }
                    elseif ($act === "Extrêmement actif") { $pProt=25; $pGlu=50; $pLip=25; }
                 }
            } else {
                // NO PATHOLOGY (Tables 21, 22, 23)
                 if ($range === 'low') { // Table 21
                    if ($act === "Sédentaire") { $pProt=30; $pGlu=35; $pLip=35; }
                    elseif ($act === "Légèrement actif") { $pProt=30; $pGlu=40; $pLip=30; }
                    elseif ($act === "Modérément actif") { $pProt=27; $pGlu=45; $pLip=28; }
                    elseif ($act === "Très actif") { $pProt=25; $pGlu=50; $pLip=25; }
                    elseif ($act === "Extrêmement actif") { $pProt=25; $pGlu=55; $pLip=20; }
                 } elseif ($range === 'mid') { // Table 22
                    if ($act === "Sédentaire") { $pProt=30; $pGlu=35; $pLip=35; }
                    elseif ($act === "Légèrement actif") { $pProt=30; $pGlu=40; $pLip=30; }
                    elseif ($act === "Modérément actif") { $pProt=26; $pGlu=45; $pLip=29; }
                    elseif ($act === "Très actif") { $pProt=25; $pGlu=50; $pLip=25; }
                    elseif ($act === "Extrêmement actif") { $pProt=25; $pGlu=55; $pLip=20; }
                 } elseif ($range === 'high') { // Table 23
                    if ($act === "Sédentaire") { $pProt=30; $pGlu=35; $pLip=35; }
                    elseif ($act === "Légèrement actif") { $pProt=30; $pGlu=40; $pLip=30; }
                    elseif ($act === "Modérément actif") { $pProt=27; $pGlu=45; $pLip=28; }
                    elseif ($act === "Très actif") { $pProt=25; $pGlu=50; $pLip=25; }
                    elseif ($act === "Extrêmement actif") { $pProt=25; $pGlu=55; $pLip=20; }
                 }
            }

        } elseif ($isFitness) {
             // FITNESS LOGIC
             // Prompt says: "Apport = TDEE - (TDEE * % deficit)". 
             // Without specific macro table for Fitness in provided test.txt, we default to balanced.
             $pProt = 30; $pGlu = 40; $pLip = 30;
        }

        // Normalize (ensure sum is 100)
        $total = $pProt + $pGlu + $pLip;
        if ($total != 100) {
            // Adjust Proportional or fix Lipids
            $pLip = 100 - ($pProt + $pGlu);
        }

        // Convert to Grams
        // Glu 4, Prot 4, Lip 9
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
