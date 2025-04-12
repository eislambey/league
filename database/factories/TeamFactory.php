<?php

namespace Database\Factories;

use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $names = [
            'Barcelona', 'Real Madrid', 'Manchester United', 'Liverpool', 'Bayern Munich', 'Paris Saint-Germain',
            'Juventus', 'AC Milan', 'Inter Milan', 'Chelsea', 'Arsenal', 'Manchester City', 'Tottenham Hotspur',
            'Borussia Dortmund', 'Atletico Madrid', 'FC Porto', 'Benfica', 'Ajax', 'Celtic', 'Rangers', 'Stuttgart',
        ];

        return [
            'name' => fake()->unique()->randomElement($names),
            'tournament_id' => function () {
                return Tournament::factory()->make()->id;
            },
            'points' => 0,
            'wins' => 0,
            'draws' => 0,
            'losses' => 0,
            'goals_conceded' => 0,
            'goals_scored' => 0,
        ];
    }
}
