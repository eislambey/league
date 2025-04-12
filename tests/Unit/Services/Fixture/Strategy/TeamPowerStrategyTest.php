<?php

namespace Tests\Unit\Services\Fixture\Strategy;

use App\Models\Fixture;
use App\Models\Team;
use App\Models\Tournament;
use App\Services\Fixture\Score;
use App\Services\Fixture\Strategy\TeamPowerStrategy;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TeamPowerStrategyTest extends TestCase
{
    private TeamPowerStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new TeamPowerStrategy();
    }

    #[Test]
    public function it_should_return_a_score(): void
    {
        // Arrange
        $fixture = $this->createFixture(50, 50);

        // Act
        $score = $this->strategy->getScore($fixture);

        // Assert
        $this->assertInstanceOf(Score::class, $score);
        $this->assertIsInt($score->home);
        $this->assertIsInt($score->away);
        $this->assertGreaterThanOrEqual(0, $score->home);
        $this->assertGreaterThanOrEqual(0, $score->away);
    }

    #[Test]
    public function it_should_stronger_team_scores_more_an_average() : void
    {
        // Arrange
        $fixture = $this->createFixture(80, 20);
        $totalHomeGoals = 0;
        $totalAwayGoals = 0;
        $iterations = 100;

        // Act
        for ($i = 0; $i < $iterations; $i++) {
            $score = $this->strategy->getScore($fixture);
            $totalHomeGoals += $score->home;
            $totalAwayGoals += $score->away;
        }

        // Assert
        $this->assertGreaterThan($totalAwayGoals, $totalHomeGoals);
    }

    #[Test]
    public function it_should_wins_more_than_away_team() : void
    {
        // Arrange
        $fixture = $this->createFixture(50, 50);
        $totalHomeTeamWins = 0;
        $totalAwayTeamWins = 0;
        $totalHomeGoals = 0;
        $totalAwayGoals = 0;
        $iterations = 1000; // Increase iterations for better statistical significance

        // Act
        for ($i = 0; $i < $iterations; $i++) {
            $score = $this->strategy->getScore($fixture);
            $totalHomeGoals += $score->home;
            $totalAwayGoals += $score->away;
            if ($score->home > $score->away) {
                $totalHomeTeamWins++;
            } elseif ($score->away > $score->home) {
                $totalAwayTeamWins++;
            }
        }

        // Assert
        $this->assertGreaterThan($totalAwayTeamWins, $totalHomeTeamWins);

    }

    private function createFixture(int $homePower, int $awayPower): Fixture
    {
        $homeTeam = new Team([
            'name' => 'Barcelona',
            'power' => $homePower,
        ]);
        $awayTeam = new Team([
            'name' => 'Liverpool',
            'power' => $awayPower,
        ]);

        $tournament = new Tournament(['name' => "UCL"]);

        $fixture = new Fixture();
        $fixture->setRelation('tournament', $tournament);
        $fixture->setRelation('homeTeam', $homeTeam);
        $fixture->setRelation('awayTeam', $awayTeam);

        return $fixture;
    }
}
