<?php

namespace App\Repositories;

use App\Enums\TournamentState;
use App\Models\Tournament;
use Illuminate\Contracts\Pagination\Paginator;

class TournamentRepository
{
    public function find(int $id, array $with = []): Tournament
    {
        return Tournament::with($with)->findOrFail($id);
    }

    public function create(string $name, array $teams = []): Tournament
    {
        $tournament = Tournament::create([
            'name' => $name,
        ]);

        foreach ($teams as $team) {
            $tournament->teams()->create([
                'name' => $team['name'],
                'power' => $team['power'],
            ]);
        }

        return $tournament;
    }

    public function update(string|Tournament $tournament, array $attributes): Tournament
    {
        if (is_string($tournament)) {
            $tournament = $this->find($tournament);
        }

        $tournament->update($attributes);

        return $tournament;
    }

    public function deleteFixtures(Tournament $tournament): Tournament
    {
        $tournament->fixtures()->delete();

        return $tournament;
    }

    public function resetTeams(Tournament $tournament): void
    {
        $tournament->teams()->update([
            "wins" => 0,
            "draws" => 0,
            "losses" => 0,
            "points" => 0,
            "goals_scored" => 0,
            "goals_conceded" => 0,
        ]);
    }

    public function reset(Tournament $tournament): Tournament
    {
        $this->deleteFixtures($tournament);
        $this->resetTeams($tournament);
        $this->update($tournament, [
            "state" => TournamentState::Created,
        ]);

        return $tournament;
    }

    public function paginate(int $perPage, string $sortBy, string $sortDirection): Paginator
    {
        return Tournament::orderBy($sortBy, $sortDirection)
            ->with("winner")
            ->simplePaginate($perPage);
    }
}
