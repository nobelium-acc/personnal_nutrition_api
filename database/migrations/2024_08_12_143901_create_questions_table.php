<?php

// database/migrations/2024_08_12_000004_create_questions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('texte_question');
            $table->foreignId('maladie_chronique_id')->constrained()->onDelete('cascade');
            $table->boolean('has_possible_answers')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
