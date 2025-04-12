<?php

namespace Tests\Unit\Services\Fixture;

use App\Enums\FixtureState;
use App\Models\Fixture;
use App\Models\Team;
use App\Models\Tournament;
use App\Repositories\FixtureRepository;
use App\Services\Fixture\FixturePlayer;
use App\Services\Fixture\Score;
use App\Services\Fixture\Strategy\ResultStrategyInterface;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FixturePlayerTest extends TestCase
{

    #[Test]
    public function it_should_play_a_fixture(): void
    {
        // Arrange
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollBack')->never();

        $fixture = Fixture::factory()->make();
        $home = Team::factory()->make();
        $away = Team::factory()->make();
        $tournament = Tournament::factory()
            ->has(Fixture::factory()->count(12)->state([
                "state" => FixtureState::Created,
            ]))
            ->make();
        $fixture->setRelation("homeTeam", $home);
        $fixture->setRelation("awayTeam", $away);
        $fixture->setRelation("tournament", $tournament);

        $returnFixture = clone $fixture;
        $returnFixture->state = FixtureState::Finished;
        $returnFixture->home_team_score = 2;
        $returnFixture->away_team_score = 0;

        $resultStrategy = $this->createMock(ResultStrategyInterface::class);
        $fixtureRepository = $this->createMock(FixtureRepository::class);
        $score = new Score(2, 0);
        $resultStrategy->expects($this->once())
            ->method('getScore')
            ->willReturn($score);

        $fixtureRepository->expects($this->once())
            ->method('applyScore')
            ->with($fixture, $score)
            ->willReturn($returnFixture);

        $player = new FixturePlayer($resultStrategy, $fixtureRepository);

        // Act
        $result = $player->playFixture($fixture);

        // Assert
        $this->assertSame(FixtureState::Finished, $result->state);
        $this->assertTrue($result->isHomeWin());
        $this->assertFalse($result->isDraw());
        $this->assertFalse($result->isAwayWin());
        $this->assertSame(2, $result->home_team_score);
        $this->assertSame(0, $result->away_team_score);
    }

    #[Test]
    public function it_should_play_by_week(): void
    {
        // Arrange
        DB::shouldReceive('beginTransaction')->times(2);
        DB::shouldReceive('commit')->times(2);
        DB::shouldReceive('rollBack')->never();

        $tournament = Tournament::factory()->make();

        $fixture1 = Fixture::factory()->make(['week_number' => 1]);
        $fixture2 = Fixture::factory()->make(['week_number' => 1]);
        $fixture3 = Fixture::factory()->make(['week_number' => 2]);

        $home1 = Team::factory()->make();
        $away1 = Team::factory()->make();
        $home2 = Team::factory()->make();
        $away2 = Team::factory()->make();

        $fixture1->setRelation('homeTeam', $home1);
        $fixture1->setRelation('awayTeam', $away1);
        $fixture2->setRelation('homeTeam', $home2);
        $fixture2->setRelation('awayTeam', $away2);

        $tournament->setRelation('fixtures', collect([$fixture1, $fixture2, $fixture3]));

        $resultStrategy = $this->createMock(ResultStrategyInterface::class);
        $fixtureRepository = $this->createMock(FixtureRepository::class);

        $returnFixture1 = clone $fixture1;
        $returnFixture1->state = FixtureState::Finished;
        $returnFixture1->home_team_score = 2;
        $returnFixture1->away_team_score = 0;

        $returnFixture2 = clone $fixture2;
        $returnFixture2->state = FixtureState::Finished;
        $returnFixture2->home_team_score = 1;
        $returnFixture2->away_team_score = 1;

        // Configure mocks
        $score1 = new Score(2, 0);
        $score2 = new Score(1, 1);

        $resultStrategy->expects($this->exactly(2))
            ->method('getScore')
            ->willReturnOnConsecutiveCalls($score1, $score2);

        $fixtureRepository->expects($this->exactly(2))
            ->method('applyScore')
            ->willReturnOnConsecutiveCalls($returnFixture1, $returnFixture2);

        $player = new FixturePlayer($resultStrategy, $fixtureRepository);

        // Act
        $player->playTournamentWeek($tournament, 1);
    }
}
