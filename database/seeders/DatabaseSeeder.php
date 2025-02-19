<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\UserRoleEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MaladieChroniqueSeeder::class,
            // AdministrateurSeeder::class,
            QuestionsSeeder::class,
        ]);
        
        DB::table('utilisateurs')->insert([
            'nom' => 'Admin',
            'prenom' => "Nutrition",
            'age' => 20,
            'sexe' => "M",
            'poids' => '110.2',
            'taille' => '110.2',
            'mot_de_passe' => Hash::make("AdminNutrition123@"),
            'tour_de_taille' => 100,
            'tour_de_hanche' => 100,
            'tour_du_cou' => 100,
            'niveau_d_activite_physique' => 2,
            'email' => 'adminnutrition@gmail.com',
            'role' => UserRoleEnum::Admin,
        ]);
    }
}
