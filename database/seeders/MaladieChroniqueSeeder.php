<?php


// database/seeders/MaladieChroniqueSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Enums\TypeMaladieChronique ;

class MaladieChroniqueSeeder extends Seeder
{
    public function run()
    {
        $maladies = [
            [
                'nom' => 'Obésité',
                'types' => [
                    TypeMaladieChronique::OBESITE_MODEREE->value,
                    TypeMaladieChronique::OBESITE_SEVERE->value,
                    TypeMaladieChronique::OBESITE_MORBIDE->value,
                ]
            ],
            
        ];

        foreach ($maladies as $maladie) {
            foreach ($maladie['types'] as $type) {
                DB::table('maladie_chroniques')->insert([
                    'nom' => $maladie['nom'],
                    'type' => $type
                ]);
            }
        }
    }
}

