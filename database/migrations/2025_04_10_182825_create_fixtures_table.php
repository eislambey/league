<?php

use App\Enums\FixtureState;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixtures', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Team::class, "home_team_id")->constrained("teams")->cascadeOnDelete();
            $table->foreignIdFor(Team::class, "away_team_id")->constrained("teams")->cascadeOnDelete();
            $table->foreignIdFor(Tournament::class)->constrained("tournaments")->cascadeOnDelete();
            $table->unsignedTinyInteger("week_number");
            $table->unsignedSmallInteger("home_team_score")->nullable();
            $table->unsignedSmallInteger("away_team_score")->nullable();
            $table->enum("state", Arr::pluck(FixtureState::cases(), "value"))->default(FixtureState::Created);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixtures');
    }
};
