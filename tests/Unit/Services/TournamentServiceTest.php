<?php

namespace Tests\Unit\Services;

use App\Enums\TournamentState;
use App\Models\Team;
use App\Models\Tournament;
use App\Repositories\TournamentRepository;
use App\Services\Fixture\FixtureGenerator;
use App\Services\TournamentService;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TournamentServiceTest extends TestCase
{
    #[Test]
    public function it_should_paginate_tournaments(): void
    {
        // Arrange
        $tournamentRepository = $this->createMock(TournamentRepository::class);
        $fixtureGenerator = $this->createMock(FixtureGenerator::class);
        $fixtureGenerator->expects($this->never())
            ->method('generate');

        $paginator = $this->createMock(Paginator::class);

        $perPage = 10;
        $sortBy = "id";
        $sortDirection = "desc";

        $paginator->expects($this->atLeastOnce())
            ->method("items")
            ->willReturn(array_fill(0, $perPage, []));

        $paginator->expects($this->atLeastOnce())
            ->method("perPage")
            ->willReturn($perPage);

        $tournamentRepository->expects($this->once())
            ->method("paginate")
            ->with($perPage, $sortBy, $sortDirection)
            ->willReturn($paginator);

        $service = new TournamentService($tournamentRepository, $fixtureGenerator);

        // Act
        $result = $service->paginate($perPage, $sortBy, $sortDirection);

        // Assert
        $this->assertSame($perPage, $result->perPage());
        $this->assertSame($perPage, count($result->items()));
    }

    #[Test]
    public function it_should_find_tournament(): void
    {
        // Arrange
        $tournament = Tournament::factory()->make();
        $tournament->id = 1;
        $tournamentRepository = $this->createMock(TournamentRepository::class);
        $fixtureGenerator = $this->createMock(FixtureGenerator::class);

        $fixtureGenerator->expects($this->never())
            ->method('generate');

        $relations = ["teams", "fixtures", "fixtures.homeTeam", "fixtures.awayTeam"];
        $tournamentRepository->expects($this->once())
            ->method("find")
            ->with($tournament->id, $relations)
            ->willReturn($tournament);

        $service = new TournamentService($tournamentRepository, $fixtureGenerator);

        // Act

        $result = $service->find($tournament->id);

        // Assert
        $this->assertInstanceOf(Tournament::class, $result);
        $this->assertSame($tournament->id, $result->id);
        $this->assertSame($tournament->name, $result->name);
    }

    #[Test]
    public function it_should_throws_when_can_not_find_tournament(): void
    {
        $this->expectException(ModelNotFoundException::class);

        // Arrange
        $tournament = Tournament::factory()->make();
        $tournament->id = 1;
        $tournamentRepository = $this->createMock(TournamentRepository::class);
        $fixtureGenerator = $this->createMock(FixtureGenerator::class);

        $fixtureGenerator->expects($this->never())
            ->method('generate');

        $relations = ["teams", "fixtures", "fixtures.homeTeam", "fixtures.awayTeam"];
        $exception = new ModelNotFoundException("Model not found");
        $tournamentRepository->expects($this->once())
            ->method("find")
            ->with($tournament->id, $relations)
            ->willThrowException($exception);

        $service = new TournamentService($tournamentRepository, $fixtureGenerator);

        // Act
        $service->find($tournament->id);
    }

    #[Test]
    public function it_should_create_tournament(): void
    {
        // Arrange
        $name = "Champions League";
        $tournament = Tournament::factory()->make([
            "name" => $name,
        ]);
        $tournament->id = 1;

        $teams = [
            ["name" => "Barcelona", "power" => 88],
            ["name" => "Benfica", "power" => 75],
            ["name" => "Liverpool", "power" => 90],
            ["name" => "PSG", "power" => 88],
        ];
        $repository = $this->createMock(TournamentRepository::class);
        $repository->expects($this->once())
            ->method("create")
            ->with($name, $teams)
            ->willReturn($tournament);

        $fixtureGenerator = $this->createMock(FixtureGenerator::class);
        $fixtureGenerator->expects($this->once())
            ->method('generate')
            ->with($tournament);

        $service = new TournamentService($repository, $fixtureGenerator);
        // Act
        $result = $service->create($tournament->name, $teams);

        // Assert

        $this->assertSame($tournament->id, $result->id);
        $this->assertSame($name, $result->name);
        $this->assertSame($tournament->state, $result->state);
    }

    #[Test]
    public function it_should_reset_tournament(): void
    {
        // Arrange
        DB::shouldReceive("beginTransaction")->once()->andReturn(null);
        DB::shouldReceive("commit")->once()->andReturn(null);
        DB::shouldReceive("rollback")->never()->andReturn(null);

        $tournament = Tournament::factory()->make();
        $tournament->id = 1;
        $tournament->state = TournamentState::Finished;

        $returnTournament = clone $tournament;
        $returnTournament->state = TournamentState::Created;

        $repository = $this->createMock(TournamentRepository::class);
        $repository->expects($this->once())
            ->method("reset")
            ->with($tournament)
            ->willReturn($returnTournament);

        $fixtureGenerator = $this->createMock(FixtureGenerator::class);
        $fixtureGenerator->expects($this->once())
            ->method('generate')
            ->with($returnTournament);

        $service = new TournamentService($repository, $fixtureGenerator);

        // Act
        $result = $service->reset($tournament);

        // Assert
        $this->assertSame($returnTournament->id, $result->id);
        $this->assertSame($returnTournament->name, $result->name);
        $this->assertEquals(TournamentState::Created, $result->state);
    }

    #[Test]
    public function it_should_not_reset_tournament_when_exception_occurs(): void
    {
        // Assert
        $this->expectException(ModelNotFoundException::class);

        // Arrange
        DB::shouldReceive("beginTransaction")->once()->andReturn(null);
        DB::shouldReceive("commit")->never()->andReturn(null);
        DB::shouldReceive("rollback")->once()->andReturn(null);

        $tournament = Tournament::factory()->make();
        $tournament->id = 1;
        $tournament->state = TournamentState::Finished;

        $repository = $this->createMock(TournamentRepository::class);
        $repository->expects($this->once())
            ->method("reset")
            ->with($tournament)
            ->willThrowException(new ModelNotFoundException());

        $fixtureGenerator = $this->createMock(FixtureGenerator::class);

        $service = new TournamentService($repository, $fixtureGenerator);

        // Act
        $service->reset($tournament);
    }

    #[Test]
    public function it_should_sorts_teams_correctly(): void
    {
        // Arrange
        $team1 = Team::factory()->make([
            "points" => 9,
            "goal_diff" => 3,
        ]);
        $team2 = Team::factory()->make([
            "points" => 12,
            "goal_diff" => 5,
        ]);
        $team3 = Team::factory()->make([
            "points" => 4,
            "goal_diff" => -1,
        ]);
        $team4 = Team::factory()->make([
            "points" => 2,
            "goal_diff" => -4,
        ]);
        $tournament = Tournament::factory()->make();
        $tournament->setRelation("teams", collect([
            $team1,
            $team2,
            $team3,
            $team4,
        ]));

        $tournamentRepository = $this->createMock(TournamentRepository::class);
        $fixtureGenerator = $this->createMock(FixtureGenerator::class);

        $service = new TournamentService($tournamentRepository, $fixtureGenerator);

        // Act
        $result = $service->sortTournamentTeams($tournament);

        // Assert
        $this->assertCount(4, $result);
        $this->assertSame($team2->name, $result[0]['name']);
        $this->assertSame($team1->name, $result[1]['name']);
        $this->assertSame($team3->name, $result[2]['name']);
        $this->assertSame($team4->name, $result[3]['name']);
    }
}
