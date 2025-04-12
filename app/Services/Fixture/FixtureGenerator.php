<?php

namespace App\Services\Fixture;

use App\Models\Tournament;
use App\Repositories\FixtureRepository;

readonly class FixtureGenerator
{
    public function __construct(private FixtureRepository $fixtureRepository)
    {
    }

    public function generate(Tournament $tournament): array
    {
        $fixtures = [];

        $weekCount = ($tournament->teams->count() - 1) * 2;
        $teams = $tournament->teams->shuffle();

        for ($i = 0; $i < $weekCount; $i++) {
            for ($j = 0; $j < $teams->count() / 2; $j++) {
                $home = $teams[$j];
                $away = $teams[$teams->count() - 1 - $j];
                $weekNumber = $i + 1;
                $fixtures[] = $this->fixtureRepository->create($home, $away, $tournament, $weekNumber);
            }

            $teams->push($teams->shift());
        }

        return $fixtures;
    }
}
