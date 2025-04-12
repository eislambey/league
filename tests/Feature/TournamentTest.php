<?php

namespace Tests\Feature;

use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TournamentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_should_render_create_tournament_page()
    {
        $this->get('/tournaments/create')
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Tournament/Create')
            );
    }

    #[Test]
    public function it_should_returns_requested_tournament()
    {
        $tournament = Tournament::factory()->create();

        $this->get('/tournaments/'. $tournament->id)
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Tournament/Show')
                ->has('tournament')
                ->where('tournament.id', $tournament->id)
                ->where('tournament.name', $tournament->name)
                ->where('tournament.state', $tournament->state)
            );
    }

    #[Test]
    public function it_should_create_new_tournament()
    {
        $name = 'UCL';
        $teams = [
            ['name' => 'Barcelona', 'power' => 90],
            ['name' => 'Real Madrid', 'power' => 90],
            ['name' => 'Bayern Munich', 'power' => 90],
            ['name' => 'Manchester City', 'power' => 90],
        ];
        $data = [
            'name' => $name,
            'teams' => $teams,
        ];

        $this->post(route('tournaments.store'), $data)
            ->assertRedirect(route('tournaments.show', 1));
    }
}
