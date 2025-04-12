<?php

namespace App\Services\Fixture\Strategy;

use App\Models\Fixture;
use App\Services\Fixture\Score;

class TeamPowerStrategy implements ResultStrategyInterface
{
    private const HUGE_POWER_GAP = .3; // 30% power gap considered huge
    private const HOME_TEAM_BOOST = .15; // 15% boost for the home team

    public function getScore(Fixture $fixture): Score
    {
        $homeTeamPower = $fixture->homeTeam->power / 100;
        $awayTeamPower = $fixture->awayTeam->power / 100;
        $powerGap = abs($homeTeamPower - $awayTeamPower);

        // Apply home team boost.
        $effectiveHomePower = $homeTeamPower + self::HOME_TEAM_BOOST;
        $totalEffective = $effectiveHomePower + $awayTeamPower;

        // Define a base goal rate and multiplier.
        $base = 1.0;
        $factor = 1.5;

        // Calculate expected goals using team power proportions.
        $lambdaHome = $base + ($effectiveHomePower / $totalEffective) * $factor;
        $lambdaAway = $base + ($awayTeamPower / $totalEffective) * $factor;

        // Adjust lambdas if the power gap is huge.
        if ($powerGap > self::HUGE_POWER_GAP) {
            if ($homeTeamPower > $awayTeamPower) {
                $lambdaHome += 0.5;
                $lambdaAway = max($lambdaAway - 0.2, 0.1);
            } else {
                $lambdaAway += 0.5;
                $lambdaHome = max($lambdaHome - 0.2, 0.1);
            }
        }

        // Sample the goals using the Poisson distribution.
        $homeGoals = $this->samplePoisson($lambdaHome);
        $awayGoals = $this->samplePoisson($lambdaAway);

        return new Score($homeGoals, $awayGoals);
    }

    private function samplePoisson(float $lambda): int
    {
        $l = exp(-$lambda);
        $k = 0;
        $p = 1.0;

        while ($p > $l) {
            $k++;
            $p *= mt_rand() / mt_getrandmax();
        }

        return $k - 1;
    }
}
