<?php

// database/migrations/2024_08_12_000005_create_plan_nutritionnels_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanNutritionnelsTable extends Migration
{
    public function up()
    {
        Schema::create('plan_nutritionnels', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->foreignId('utilisateur_id')->constrained()->onDelete('cascade'); // Ajoutez cette ligne
            $table->timestamps();
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('plan_nutritionnels');
    }
}

