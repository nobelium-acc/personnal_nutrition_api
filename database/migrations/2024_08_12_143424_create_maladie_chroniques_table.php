<?php

// database/migrations/2024_08_12_000003_create_maladie_chroniques_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaladieChroniquesTable extends Migration
{
    public function up()
    {
        Schema::create('maladie_chroniques', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('maladie_chroniques');
    }
}

