<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Services\TournamentService;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(TournamentService $tournamentService): Response
    {
        $paginator = $tournamentService->paginate()
            ->through(function (Tournament $tournament) {
                return [
                    "id" => $tournament->id,
                    "name" => $tournament->name,
                    "state" => $tournament->state,
                    "created_at_human" => $tournament->created_at->diffForHumans(),
                    "created_at" => $tournament->created_at->format('Y-m-d H:i:s'),
                    "winner" => $tournament->winner,
                ];
            });

        return Inertia::render('Home', [
            'tournaments' => $paginator,
        ]);
    }
}
