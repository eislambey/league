<?php

namespace App\Repositories;

use App\Enums\FixtureState;
use App\Enums\TournamentState;
use App\Models\Fixture;
use App\Models\Team;
use App\Models\Tournament;
use App\Services\Fixture\Score;

class FixtureRepository
{
    public function create(Team $home, Team $away, Tournament $tournament, int $weekNumber): Fixture
    {
        return Fixture::create([
            'home_team_id' => $home->id,
            'away_team_id' => $away->id,
            'tournament_id' => $tournament->id,
            'week_number' => $weekNumber,
        ]);
    }

    public function applyScore(Fixture $fixture, Score $score): Fixture
    {
        $fixture->home_team_score = $score->home;
        $fixture->away_team_score = $score->away;
        $fixture->state = FixtureState::Finished;

        $homeTeam = $fixture->homeTeam;
        $awayTeam = $fixture->awayTeam;

        $this->applyScoreChanges($homeTeam, $fixture, $awayTeam);
        $this->applyPointChanges($fixture, $homeTeam, $awayTeam);


        $fixture->save();
        $fixture->homeTeam->save();
        $fixture->awayTeam->save();

        $this->applyTournamentChanges($fixture);
        $fixture->tournament->save();

        return $fixture;
    }

    private function applyTournamentChanges(Fixture $fixture): void
    {
        $futureFixtures = $fixture->tournament->fixtures()
            ->where('state', FixtureState::Created)
            ->get();

        if ($futureFixtures->isEmpty()) {
            $fixture->tournament->state = TournamentState::Finished;
            return;
        }

        if ($fixture->tournament->state->isStarted()) {
            return; // already in started state
        }

        $fixture->tournament->state = TournamentState::Started;
    }

    private function applyPointChanges(Fixture $fixture, Team $homeTeam, Team $awayTeam): void
    {
        if ($fixture->isHomeWin()) {
            $homeTeam->points = $homeTeam->points + 3;
            $homeTeam->wins = $homeTeam->wins + 1;
            $awayTeam->losses = $awayTeam->losses + 1;

            return;
        }

        if ($fixture->isAwayWin()) {
            $awayTeam->points = $awayTeam->points + 3;
            $awayTeam->wins = $awayTeam->wins + 1;
            $homeTeam->losses = $homeTeam->losses + 1;

            return;
        }

        // If it's a draw
        $homeTeam->points = $homeTeam->points + 1;
        $homeTeam->draws = $homeTeam->draws + 1;
        $awayTeam->points = $awayTeam->points + 1;
        $awayTeam->draws = $awayTeam->draws + 1;
    }

    private function applyScoreChanges(Team $homeTeam, Fixture $fixture, Team $awayTeam): void
    {
        $homeTeam->goals_scored = $homeTeam->goals_scored + $fixture->home_team_score;
        $homeTeam->goals_conceded = $homeTeam->goals_conceded + $fixture->away_team_score;
        $awayTeam->goals_scored = $awayTeam->goals_scored + $fixture->away_team_score;
        $awayTeam->goals_conceded = $awayTeam->goals_conceded + $fixture->home_team_score;
    }
}
