<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTournamentRequest;
use App\Models\Tournament;
use App\Services\PredictionService;
use App\Services\TournamentService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TournamentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Home');
    }

    public function show(int $id, TournamentService $tournamentService, PredictionService $predictionService): Response
    {
        $tournament = $tournamentService->find($id);
        $teams = $tournamentService->sortTournamentTeams($tournament);
        $predictions = $predictionService->predict($tournament);

        return Inertia::render('Tournament/Show', [
            "tournament" => $tournament,
            "teams" => $teams,
            "fixtureGroups" => $tournament->fixtures->groupBy("week_number"),
            "predictions" => $predictions,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Tournament/Create');
    }

    public function store(StoreTournamentRequest $request, TournamentService $tournamentService): RedirectResponse
    {
        $tournament = $tournamentService->create(
            $request->input("name"),
            $request->input("teams")
        );

        return redirect()->route("tournaments.show", $tournament->id);
    }

    /**
     * @throws \Throwable
     */
    public function reset(Tournament $tournament, TournamentService $tournamentService): RedirectResponse
    {
        if ($tournament->state->isCreated()) {
            return back()->with([
                "error" => "Cannot reset a tournament that is not started yet.",
            ]);
        }

        $tournamentService->reset($tournament);

        return back();
    }
}
