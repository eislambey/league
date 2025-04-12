<?php

use App\Http\Controllers\FixtureController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TournamentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, "index"])->name('home');

Route::resource('tournaments', TournamentController::class)
    ->only(['index', 'show', 'create', 'store']);

Route::post("/tournaments/{tournament}/week/{week}/play", [FixtureController::class, "play"])
    ->name("fixtures.play")
    ->where([
        "tournament" => "[0-9]+",
        "week" => "[0-9]+"
    ]);

Route::post("/tournaments/{tournament}/play", [FixtureController::class, "playAll"])
    ->name("fixtures.playAll");

Route::post("/tournaments/{tournament}/reset", [TournamentController::class, "reset"])
    ->name("tournament.reset");
