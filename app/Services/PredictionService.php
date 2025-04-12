<?php

namespace App\Services;

use App\Models\Team;
use App\Models\Tournament;

class PredictionService
{
    /**
     * @return array<\App\Services\Prediction>
     */
    public function predict(Tournament $tournament): array
    {
        $weeksLeft = $tournament->weeksLeft();
        $weeksCount = $tournament->weeksCount();

        // If no weeks left, the winner is determined by points and goal difference
        if ($weeksLeft === 0) {
            return $this->getFinalPredictions($tournament);
        }

        // Rest of the existing prediction logic for ongoing tournaments
        if ($weeksLeft > $weeksCount / 2) {
            return array_map(
                fn(Team $team) => new Prediction($team, 0),
                $tournament->teams->all(),
            );
        }

        $teams = $tournament->teams->sortByDesc('points');
        $leader = $teams->first();
        $predictions = [];

        foreach ($teams as $team) {
            $probability = $this->calculateProbability(
                $team,
                $leader,
                $weeksLeft,
            );

            $predictions[] = new Prediction($team, $probability);
        }

        // Normalize probabilities to ensure sum is 100%
        $total = array_sum(array_column($predictions, 'probability'));
        if ($total > 0) {
            foreach ($predictions as $key => $prediction) {
                $predictions[$key] = new Prediction(
                    $prediction->team,
                    round(($prediction->probability / $total) * 100, 2),
                );
            }
        }

        return $predictions;
    }

    private function calculateProbability(
        Team $team,
        Team $leader,
        int $weeksLeft,
    ): float {
        $maxTeamPoints = $team->points + ($weeksLeft * 3);
        $maxLeaderPoints = $leader->points + (($weeksLeft - ($leader->fixturesPlayed - $team->fixturesPlayed)) * 3);

        // If team can't mathematically surpass leader's current points
        if ($maxTeamPoints < $leader->points) {
            return 0;
        }

        // Base probability weights
        $remainingMatchesWeight = 0.6;
        $currentFormWeight = 0.4;

        // Probability based on potential maximum points
        $pointsRatio = $maxTeamPoints / max($maxLeaderPoints, 1);
        $maxPointsProbability = $pointsRatio * 100;

        // Current form probability (considering matches played)
        $formProbability = ($team->points / max($team->fixturesPlayed * 3, 1)) * 100;

        $probability = ($maxPointsProbability * $remainingMatchesWeight) +
            ($formProbability * $currentFormWeight);

        // Adjust for goal difference as a tiebreaker
        $goalDiffFactor = 1 + ($team->goal_diff / 20);
        $probability *= max(0.8, min(1.2, $goalDiffFactor));

        return max(0, min(100, $probability));
    }

    /**
     * @param \App\Models\Tournament $tournament
     * @return \App\Services\Prediction[]
     */
    private function getFinalPredictions(Tournament $tournament): array
    {
        $teams = $tournament->teams->sortBy([
            ["points", "desc"],
            ["goal_diff", "desc"],
        ]);

        $leader = $teams->first();

        $predictions = [new Prediction($leader, 100)];

        foreach ($teams->slice(1) as $team) {
            $predictions[] = new Prediction($team, 0);
        }

        return $predictions;
    }
}
