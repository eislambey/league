<?php

namespace App\Services;

use App\Models\Tournament;
use App\Repositories\TournamentRepository;
use App\Services\Fixture\FixtureGenerator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

readonly class TournamentService
{
    public function __construct(
        private TournamentRepository $tournamentRepository,
        private FixtureGenerator $fixtureGenerator,
    ) {
    }

    public function paginate(int $perPage = 10, string $sortBy = "id", string $sortDirection = "desc"): Paginator
    {
        return $this->tournamentRepository->paginate($perPage, $sortBy, $sortDirection);
    }

    public function find(int $id): Tournament
    {
        $relations = ["teams", "fixtures", "fixtures.homeTeam", "fixtures.awayTeam"];

        return $this->tournamentRepository->find($id, $relations);
    }

    /**
     * @param string $name
     * @param array<array<string, string>> $teams
     * @return \App\Models\Tournament
     */
    public function create(string $name, array $teams): Tournament
    {
        $tournament = $this->tournamentRepository->create($name, $teams);
        $this->fixtureGenerator->generate($tournament);

        return $tournament;
    }

    public function sortTournamentTeams(Tournament $tournament): array
    {
        return $tournament->teams
            ->sortBy([
                ["points", "desc"],
                ["goal_diff", "desc"],
            ])
            ->values()
            ->toArray();
    }

    /**
     * @throws \Throwable
     */
    public function reset(Tournament $tournament): Tournament
    {
        DB::beginTransaction();
        try {
            $tournament = $this->tournamentRepository->reset($tournament);

            $this->fixtureGenerator->generate($tournament);

            DB::commit();

            return $tournament;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
