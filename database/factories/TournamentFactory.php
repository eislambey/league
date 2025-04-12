<?php

namespace Database\Factories;

use App\Enums\TournamentState;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tournament>
 */
class TournamentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $names = [
            "UEFA Champions League", "UEFA Europa League", "FIFA World Cup", "Copa America", "African Cup of Nations",
            "Asian Cup", "CONCACAF Gold Cup", "OFC Nations Cup",
        ];


        return [
            "name" => fake()->randomElement($names) . " " . fake()->year(),
            "state" => TournamentState::Created,
        ];
    }
}
