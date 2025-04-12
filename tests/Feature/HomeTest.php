<?php

namespace Tests\Feature;

use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_should_render_home_page_in_order()
    {
        $tournament = Tournament::factory()->create();
        $tournament2 = Tournament::factory()->create();

        $this->get('/')
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Home')
                ->has('tournaments.data', 2)
                ->where('tournaments.data.0.id', $tournament2->id) // latest tournament first
                ->where('tournaments.data.1.id', $tournament->id)
            );
    }
}
