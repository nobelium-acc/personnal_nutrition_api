<?php

namespace Tests\Feature;

use App\Models\Utilisateur;
use App\Models\MaladieChronique;
use App\Models\Reponse;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Mail\LowCalorieWarningMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class NutritionRecommendationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup Obesity Modérée
        // Using direct DB insert to ensure IDs are exactly what we expect if needed
        DB::table('maladie_chroniques')->insert([
            'id' => 1,
            'nom' => 'Obésité',
            'type' => 'obésité modérée',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Setup Questions
        $questions = [
            ['id' => 89, 'texte_question' => 'Objectif principal', 'maladie_chronique_id' => 1],
            ['id' => 90, 'texte_question' => 'Poids à perdre', 'maladie_chronique_id' => 1],
            ['id' => 91, 'texte_question' => 'Déficit', 'maladie_chronique_id' => 1],
            ['id' => 66, 'texte_question' => 'Antécédents', 'maladie_chronique_id' => 1],
            ['id' => 67, 'texte_question' => 'Détails antécédents', 'maladie_chronique_id' => 1],
            ['id' => 70, 'texte_question' => 'Médicaments', 'maladie_chronique_id' => 1],
        ];

        foreach ($questions as $q) {
            DB::table('questions')->insert(array_merge($q, ['created_at' => now(), 'updated_at' => now()]));
        }
    }

    private function createUtilisateur(array $data = [])
    {
        return Utilisateur::create(array_merge([
            'nom' => 'Test',
            'prenom' => 'User',
            'age' => 30,
            'sexe' => 'M',
            'poids' => 80,
            'taille' => 180,
            'email' => 'test@example.com',
            'mot_de_passe' => Hash::make('password'),
            'tour_de_taille' => 90,
            'tour_de_hanche' => 100,
            'tour_du_cou' => 40,
            'niveau_d_activite_physique' => 'Sédentaire',
            'maladie_chronique_id' => 1
        ], $data));
    }

    public function test_recommendation_requires_user_id()
    {
        $response = $this->postJson('/api/nutrition/recommendation');
        $response->assertStatus(400);
    }

    public function test_recommendation_restricted_to_obesity_moderee()
    {
        DB::table('maladie_chroniques')->insert([
            'id' => 2,
            'nom' => 'Obésité',
            'type' => 'obésité sévère',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user = $this->createUtilisateur(['maladie_chronique_id' => 2, 'email' => 'other@example.com']);

        $response = $this->postJson('/api/nutrition/recommendation', ['user_id' => $user->id]);
        $response->assertStatus(403);
    }

    public function test_weight_loss_calculation_with_diabetes()
    {
        Mail::fake();

        $user = $this->createUtilisateur([
            'poids' => 100,
            'taille' => 180,
            'age' => 40,
            'sexe' => 'Homme',
            'niveau_d_activite_physique' => 'Sédentaire'
        ]);

        Reponse::create(['utilisateur_id' => $user->id, 'question_id' => 89, 'description' => 'Perte de poids']);
        Reponse::create(['utilisateur_id' => $user->id, 'question_id' => 90, 'description' => 'Moins de 5 kg']);
        Reponse::create(['utilisateur_id' => $user->id, 'question_id' => 91, 'description' => '500 kcal']);
        Reponse::create(['utilisateur_id' => $user->id, 'question_id' => 66, 'description' => 'Oui']);
        Reponse::create(['utilisateur_id' => $user->id, 'question_id' => 67, 'description' => 'diabète']);

        $response = $this->postJson('/api/nutrition/recommendation', ['user_id' => $user->id]);

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('user_profile.pathologies_detectees.0', 'diabetes')
                 ->assertJsonPath('macronutriments.distribution.proteines_percent', 30);
    }

    public function test_fitness_objective_with_3_5_percent_deficit_cap()
    {
        Mail::fake();

        $user = $this->createUtilisateur([
            'poids' => 100,
            'taille' => 180,
            'age' => 40,
            'sexe' => 'Homme',
            'niveau_d_activite_physique' => 'Légèrement actif'
        ]);

        Reponse::create(['utilisateur_id' => $user->id, 'question_id' => 89, 'description' => 'Améliorer votre forme physique']);

        $response = $this->postJson('/api/nutrition/recommendation', ['user_id' => $user->id]);

        $response->assertStatus(200);
        $data = $response->json();
        
        $tdee = $data['tdee'];
        // Fitness deficit: >3000: 3%, 2000-3000: 4%, <2000: 5%
        // BMR = 10*100 + 6.25*180 - 5*40 + 5 = 1000 + 1125 - 200 + 5 = 1930
        // TDEE = 1930 * 1.4 = 2702
        // 4% of 2702 = 108.08 -> 108
        $expectedDeficit = min(300, round($tdee * 0.04)); 
        $this->assertEquals($expectedDeficit, $data['deficit_calorique']);
    }

    public function test_low_calorie_warning_email()
    {
        Mail::fake();

        $user = $this->createUtilisateur([
            'poids' => 60,
            'taille' => 160,
            'age' => 50,
            'sexe' => 'Femme',
            'niveau_d_activite_physique' => 'Sédentaire'
        ]);
        
        Reponse::create(['utilisateur_id' => $user->id, 'question_id' => 89, 'description' => 'Perte de poids']);
        Reponse::create(['utilisateur_id' => $user->id, 'question_id' => 91, 'description' => '500 kcal']); 

        $response = $this->postJson('/api/nutrition/recommendation', ['user_id' => $user->id]);

        $response->assertStatus(200)
                 ->assertJsonPath('low_calorie_notification', true);

        Mail::assertSent(LowCalorieWarningMail::class);
    }
}
