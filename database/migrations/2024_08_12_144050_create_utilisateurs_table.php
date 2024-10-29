<?php

// database/migrations/2024_08_12_000000_create_utilisateurs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;



class CreateUtilisateursTable extends Migration
{
    public function up()
    {
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->integer('age');
            $table->string('sexe');
            $table->integer('poids');
            $table->integer('taille');
            $table->string('email')->unique();
            $table->string('mot_de_passe');
            $table->float('tour_de_taille');
            $table->float('tour_de_hanche');
            $table->float('tour_du_cou');
            $table->string('niveau_d_activite_physique');
            $table->foreignId('maladie_chronique_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('utilisateurs');
    }
}
