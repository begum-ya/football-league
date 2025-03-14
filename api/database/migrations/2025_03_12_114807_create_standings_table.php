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
        Schema::create('standings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->integer('won')->default(0);
            $table->integer('lose')->default(0);
            $table->integer('draw')->default(0);
            $table->integer('points')->default(0);
            $table->integer('goal_difference')->default(0);
            $table->integer('goals_scored')->default(0);
            $table->integer('goals_conceded')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standings');
    }
};
