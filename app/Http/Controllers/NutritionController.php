<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\ObesityInconsistencyMail;

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


    private function performCalculation(\App\Models\Utilisateur $user)
    {        
        $weight = $user->poids;
        $height = $user->taille; // cm
        $waist = $user->tour_de_taille;
        $hip = $user->tour_de_hanche;
        $neck = $user->tour_du_cou;
        $gender = $user->sexe;
        $age = $user->age;
        $niveauPhysique = $user->niveau_d_activite_physique;

        if (!$weight || !$height || !$gender) {
             return response()->json([
                 'success' => false, 
                 'message' => 'Données utilisateur incomplètes pour le calcul (poids, taille, sexe requis).'
             ], 400);
        }

        // IMC
        $heightM = $height / 100;
        $imc = $weight / ($heightM * $heightM);
        $imc = round($imc, 2);

        // RTH
        $rth = 0;
        if ($hip > 0) {
            $rth = $waist / $hip;
        }
        $rth = round($rth, 2);

        // IMG
        $isMale = (strtoupper($gender) === 'M' || strtoupper($gender) === 'HOMME');

        $img = 0;
        if ($isMale) {
            if (($waist - $neck) > 0) {
                 $img = 86.010 * log10($waist - $neck) - 70.041 * log10($height) + 36.76;
            }
        } else {
             if (($waist + $hip - $neck) > 0) {
                $img = 163.205 * log10($waist + $hip - $neck) - 97.684 * log10($height) - 78.387;
            }
        }
        $img = round($img, 2);

        // BMR
        $bmr = 0;
        if ($isMale) {
            $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) + 5;
        } else {
            $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) - 161;
        }

        $tdee=0;

        if($niveauPhysique === "Sédentaire"){
            $tdee = $bmr * 1.2;
        }elseif($niveauPhysique === "Légèrement actif"){
            $tdee = $bmr * 1.4;
        }elseif($niveauPhysique === "Modérément actif"){
            $tdee = $bmr * 1.6;
        }elseif($niveauPhysique === "Très actif"){
            $tdee = $bmr * 1.7;
        }elseif($niveauPhysique === "Extrêmement actif"){
            $tdee = $bmr * 1.9;
        }

        $isObeseIMC = $imc >= 30;
        $imgThreshold = $isMale ? 25 : 32;
        $isObeseIMG = $img >= $imgThreshold;
        $rthThreshold = $isMale ? 0.90 : 0.85;
        $isHighRTH = $rth > $rthThreshold;

        $calculatedStatus = 'Normal';
        $message = '';

        if ($isObeseIMG) {
            $calculatedStatus = 'Obese';
            $message = "D’après vos mesures, votre pourcentage de masse grasse (IMG) est au-dessus du seuil recommandé. Cela indique que vous êtes ‘obèse’.";
        } elseif ($isObeseIMC && $isHighRTH) {
            $calculatedStatus = 'Obese';
            $message = "Votre IMC et votre répartition de graisse abdominale (RTH) indiquent que vous êtes obèse.";
        } elseif ($isObeseIMC && !$isHighRTH && !$isObeseIMG) {
            $calculatedStatus = 'Normal';
            $message = "Votre IMC est élevé, mais vos mesures de masse grasse et de répartition abdominale sont dans les limites normales. Selon ces mesures, vous n’êtes pas considéré comme obèse.";
        } elseif (!$isObeseIMG && !$isObeseIMC) {
            $calculatedStatus = 'Normal';
            $message = "Vos mesures indiquent que votre masse grasse et votre IMC sont dans les limites normales. Selon ces données, vous n’êtes pas considéré comme obèse.";
        }

        $obesityGrade = 'Normal';
        if ($imc >= 40) {
            $obesityGrade = 'Obésité morbide (Grade 3)';
        } elseif ($imc >= 35) {
            $obesityGrade = 'Obésité sévère (Grade 2)';
        } elseif ($imc >= 30) {
            $obesityGrade = 'Obésité modérée (Grade 1)';
        } else {
            $obesityGrade = 'Normal/Surpoids';
        }

        $declaredType = $user->maladieChronique ? $user->maladieChronique->type : 'Inconnu';
        
        $isConsistent = true;
        $notif = false;

        if ($calculatedStatus === 'Obese') {
             if (stripos($declaredType, 'Obésité') === false && stripos($declaredType, 'Obesite') === false) {
                 $isConsistent = false;
             }
        } else {
             if (stripos($declaredType, 'Obésité') !== false || stripos($declaredType, 'Obesite') !== false) {
                 $isConsistent = false;
             }
        }
        
        if (!$isConsistent) {
             $data = [
                'weight' => $weight,
                'height' => $height,
                'user_name' => $user->nom . ' ' . $user->prenom,
                'user_email' => $user->email,
                'declared_type' => $declaredType
             ];
             
             Mail::to($user->email)->send(new ObesityInconsistencyMail($data['user_name'], $data, $imc, $obesityGrade));
             $notif = true;
        }

        return response()->json([
            'success' => true,
            'user_id' => $user->id,
            'imc' => $imc,
            'rth' => $rth,
            'img' => $img,
            'bmr' => $bmr,
            'tdee' => $tdee,
            'status' => $calculatedStatus,
            'grade_imc' => $obesityGrade,
            'declared_type' => $declaredType,
            'message' => $message,
            'consistent' => $isConsistent,
            'notification' => $notif
        ]);
    }
}
