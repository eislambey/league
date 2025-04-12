<?php

namespace App\Services\Fixture;

use App\Enums\FixtureState;
use App\Models\Fixture;
use App\Models\Tournament;
use App\Repositories\FixtureRepository;
use App\Services\Fixture\Strategy\ResultStrategyInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

readonly class FixturePlayer
{
    public function __construct(
        private ResultStrategyInterface $resultStrategy,
        private FixtureRepository $fixtureRepository,
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function playTournamentWeek(Tournament $tournament, int $week): void
    {
        $tournament->fixtures
            ->where('week_number', $week)
            ->each(function (Fixture $fixture) {
                $this->playFixture($fixture);
            });
    }

    /**
     * @throws \Throwable
     */
    public function playFixture(Fixture $fixture): Fixture
    {
        DB::beginTransaction();
        try {
            $score = $this->resultStrategy->getScore($fixture);
            $fixture = $this->fixtureRepository->applyScore($fixture, $score);

            DB::commit();
            Log::info('Fixture played', [
                'fixture_id' => $fixture->id,
                'has_winner' => $fixture->hasWinner(),
                'winner_id' => $fixture->getWinner()?->id,
            ]);

            return $fixture;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws \Throwable
     */
    public function playTournament(Tournament $tournament): Tournament
    {
        $tournament->fixtures()
            ->where("state", FixtureState::Created)
            ->each(function (Fixture $fixture) {
                $this->playFixture($fixture);
            });

        return $tournament;
    }
}
