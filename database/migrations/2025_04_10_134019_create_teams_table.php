<?php

use App\Models\Tournament;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Tournament::class);
            $table->string('name');
            $table->unsignedTinyInteger("power");
            $table->unsignedTinyInteger("wins")->default(0);
            $table->unsignedTinyInteger("losses")->default(0);
            $table->unsignedTinyInteger("draws")->default(0);
            $table->unsignedTinyInteger("points")->default(0);
            $table->smallInteger("goals_scored")->default(0);
            $table->smallInteger("goals_conceded")->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
