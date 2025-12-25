<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->float('tdee')->nullable();
            $table->boolean('img_notification')->default(false);
            $table->boolean('imc_notification')->default(false);
            $table->boolean('rth_notification')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->dropColumn(['tdee', 'img_notification', 'imc_notification', 'rth_notification']);
        });
    }
};
