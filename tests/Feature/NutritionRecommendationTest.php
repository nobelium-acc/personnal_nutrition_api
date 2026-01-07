<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Utilisateur;
use App\Models\Reponse;
use App\Models\MaladieChronique;
use App\Models\Question;
use App\Models\QuestionPossibleAnswer;
use Illuminate\Support\Facades\DB;

class NutritionRecommendationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed necessary data
        DB::table('maladie_chroniques')->insert([
            ['id' => 1, 'type' => 'Obésité modérée', 'nom' => 'Obésité modérée'],
            ['id' => 2, 'type' => 'Obésité sévère', 'nom' => 'Obésité sévère'],
        ]);

        $questions = [
            ['id' => 89, 'libelle' => 'Objectif principal'],
            ['id' => 90, 'libelle' => 'Kg à perdre'],
            ['id' => 91, 'libelle' => 'Niveau de changement'],
            ['id' => 66, 'libelle' => 'Antécédents'],
            ['id' => 67, 'libelle' => 'Quel antécédent ?'],
            ['id' => 70, 'libelle' => 'Médicaments'],
            ['id' => 72, 'libelle' => 'Portions fruits/légumes'],
            ['id' => 73, 'libelle' => 'Grignotage'],
            ['id' => 75, 'libelle' => 'Boissons'],
            ['id' => 76, 'libelle' => 'Sommeil'],
            ['id' => 81, 'libelle' => 'Temps assis'],
            ['id' => 85, 'libelle' => 'Stress'],
            ['id' => 86, 'libelle' => 'Finir assiette'],
        ];
        DB::table('questions')->insert($questions);

        DB::table('question_possible_answers')->insert([
            ['id' => 500, 'question_id' => 89, 'value' => 'Perdre du poids'],
            ['id' => 501, 'question_id' => 90, 'value' => 'Plus de 10 kg'],
            ['id' => 502, 'question_id' => 91, 'value' => 'Intense (-1000 kcal)'],
            ['id' => 503, 'question_id' => 72, 'value' => '1-2 portions'],
            ['id' => 504, 'question_id' => 73, 'value' => 'Oui'],
            ['id' => 505, 'question_id' => 75, 'value' => 'Tous les jours'],
            ['id' => 506, 'question_id' => 76, 'value' => 'Moins de 6 heures'],
        ]);
    }

    public function test_recommendation_output_structure_and_logic()
    {
        $user = Utilisateur::create([
            'nom' => 'Doe',
            'prenom' => 'John',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'sexe' => 'Homme',
            'age' => 35,
            'poids' => 110,
            'taille' => 180,
            'tour_de_taille' => 110,
            'tour_de_hanche' => 100,
            'tour_du_cou' => 45,
            'niveau_d_activite_physique' => 'Sédentaire',
            'maladie_chronique_id' => 1,
        ]);

        // Mocking responses
        $responses = [
            ['utilisateur_id' => $user->id, 'question_id' => 89, 'question_possible_answer_id' => 500], // Perdre du poids
            ['utilisateur_id' => $user->id, 'question_id' => 90, 'question_possible_answer_id' => 501], // > 10kg
            ['utilisateur_id' => $user->id, 'question_id' => 91, 'question_possible_answer_id' => 502], // -1000 kcal
            ['utilisateur_id' => $user->id, 'question_id' => 67, 'description' => 'diabète de type 2'],
            ['utilisateur_id' => $user->id, 'question_id' => 70, 'description' => 'Metformine'],
            ['utilisateur_id' => $user->id, 'question_id' => 72, 'question_possible_answer_id' => 503], // 1-2 portions
            ['utilisateur_id' => $user->id, 'question_id' => 73, 'question_possible_answer_id' => 504], // Oui
            ['utilisateur_id' => $user->id, 'question_id' => 75, 'question_possible_answer_id' => 505], // Tous les jours
            ['utilisateur_id' => $user->id, 'question_id' => 76, 'question_possible_answer_id' => 506], // < 6h
            ['utilisateur_id' => $user->id, 'question_id' => 85, 'description' => 'Oui'],
            ['utilisateur_id' => $user->id, 'question_id' => 86, 'description' => 'Oui'],
        ];
        DB::table('reponses')->insert($responses);

        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/nutrition/recommendation');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'user_profile',
                     'tdee',
                     'apport_calorique',
                     'macronutriments' => [
                         'distribution',
                         'grammes'
                     ],
                     'plan_intervention',
                     'menu_journalier',
                     'conseils_personnalises'
                 ]);

        // Check if plan intervention is not empty
        $this->assertNotEmpty($response->json('plan_intervention'));
        
        // Assert specific advice presence (based on mocked responses)
        $planText = json_encode($response->json('plan_intervention'), JSON_UNESCAPED_UNICODE);
        $this->assertStringContainsString('DIABÈTE DE TYPE 2 DÉTECTÉ', $planText);
        $this->assertStringContainsString('Metformine', $planText);
        $this->assertStringContainsString('FRUITS & LÉGUMES', $planText);
        $this->assertStringContainsString('SOMMEIL', $planText);
        $this->assertStringContainsString('CONSTAT CAPITAL', $planText); // RTH 110/100 = 1.1 > 0.9

        // Check menu scaling (at least one item present)
        $this->assertNotEmpty($response->json('menu_journalier'));
    }
}
