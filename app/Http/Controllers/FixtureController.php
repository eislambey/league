<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Services\Fixture\FixturePlayer;
use Illuminate\Http\RedirectResponse;

class FixtureController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function play(Tournament $tournament, int $week, FixturePlayer $fixturePlayer): RedirectResponse
    {
        $fixturePlayer->playTournamentWeek($tournament, $week);

        return redirect()->back();
    }

    /**
     * @throws \Throwable
     */
    public function playAll(Tournament $tournament, FixturePlayer $fixturePlayer): RedirectResponse
    {
        $fixturePlayer->playTournament($tournament);

        return redirect()->back();
    }
}
