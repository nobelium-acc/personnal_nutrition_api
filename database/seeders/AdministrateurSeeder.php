<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdministrateurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('administrateurs')->insert([
            'identifiant' => 'ecko229',
            'mot_de_passe' => Hash::make('eneck1998'),
        ]);
    }
}

