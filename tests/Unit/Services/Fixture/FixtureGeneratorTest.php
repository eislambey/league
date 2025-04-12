<?php

namespace Tests\Unit\Services\Fixture;

use App\Models\Fixture;
use App\Models\Team;
use App\Models\Tournament;
use App\Repositories\FixtureRepository;
use App\Services\Fixture\FixtureGenerator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FixtureGeneratorTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    #[Test]
    public function it_should_create_fixtures_for_a_tournament()
    {
        $tournament = new Tournament(["name" => "UCL 2024/25"]);
        $team = new Team(["name" => "Barcelona", "power" => 85]);
        $team2 = new Team(["name" => "Liverpool", "power" => 90]);
        $team3 = new Team(["name" => "Juventus", "power" => 75]);
        $team4 = new Team(["name" => "PSG", "power" => 85]);
        $teams = collect([$team, $team2, $team3, $team4]);
        $tournament->setRelation("teams", $teams);

        $fixtureRepository = $this->createMock(FixtureRepository::class);
        $weekCount = ($tournament->teams->count() - 1) * 2;
        $expectedFixtureCount = $tournament->teams->count() / 2 * $weekCount;
        $returns = [];
        for ($i = 0; $i < $expectedFixtureCount; $i++) {
            $returns[] = new Fixture([
                "week_number" => $i + 1,
            ]);
        }

        $fixtureRepository->expects($this->exactly($expectedFixtureCount))
            ->method('create')
            ->with($this->isInstanceOf(Team::class), $this->isInstanceOf(Team::class), $tournament, $this->isInt())
            ->willReturn(...$returns);

        $fixtureGenerator = new FixtureGenerator($fixtureRepository);
        $generatedFixtures = $fixtureGenerator->generate($tournament);

        $this->assertCount($expectedFixtureCount, $generatedFixtures);
        foreach ($generatedFixtures as $index => $fixture) {
            $this->assertInstanceOf(Fixture::class, $fixture);
            $this->assertEquals($index + 1, $fixture->week_number);
        }
    }
}
