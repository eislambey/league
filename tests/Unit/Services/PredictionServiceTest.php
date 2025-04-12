<?php

namespace Tests\Unit\Services;

use App\Enums\FixtureState;
use App\Models\Fixture;
use App\Models\Team;
use App\Models\Tournament;
use App\Services\PredictionService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PredictionServiceTest extends TestCase
{
    private PredictionService $predictionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->predictionService = new PredictionService();
    }

    #[Test]
    public function it_returns_the_winner_when_no_weeks_left(): void
    {
        // Arrange
        $tournament = Tournament::factory()
            ->make();
        $tournament->setRelation("teams", Team::factory(4)->make());
        $tournament->setRelation("fixtures", collect()); // acts as no fixtures left

        $tournament->teams[0]->points = 10;
        $tournament->teams[1]->points = 7;
        $tournament->teams[2]->points = 12; // winner
        $tournament->teams[3]->points = 4;

        $expectedProbabilities = [
            $tournament->teams[0]->name => 0,
            $tournament->teams[1]->name => 0,
            $tournament->teams[2]->name => 100,
            $tournament->teams[3]->name => 0,
        ];

        // Act
        $predictions = $this->predictionService->predict($tournament);

        // Assert
        $this->assertCount($tournament->teams->count(), $predictions);
        $this->assertSame(100.0, $predictions[0]->probability, 'First item is winner and must have 100% probability');

        foreach ($predictions as $prediction) {
            $this->assertEquals($expectedProbabilities[$prediction->team->name], $prediction->probability);
        }
    }

    #[Test]
    public function it_should_returns_zero_when_not_played_half_of_fixtures(): void
    {
        // Arrange
        $tournament = Tournament::factory()
            ->make();
        $tournament->setRelation("teams", Team::factory(4)->make());
        $week1 = Fixture::factory(2)->make([
            "state" => FixtureState::Finished,
            "week_number" => 1,
        ]);
        $week2 = Fixture::factory(2)->make([
            "state" => FixtureState::Finished,
            "week_number" => 2,
        ]); // two weeks played
        $restOfFixtures = Fixture::factory(8)->make([
            "state" => FixtureState::Created,
            "week_number" => 3,
        ]); // four weeks left
        $fixtures = $week1->push(...$week2, ...$restOfFixtures);

        $tournament->setRelation("fixtures", $fixtures);

        // Act
        $predictions = $this->predictionService->predict($tournament);

        // Assert
        $this->assertCount($tournament->teams->count(), $predictions);
        foreach ($predictions as $prediction) {
            $this->assertEquals(0, $prediction->probability, 'ATM all probabilities should be 0%');
        }
    }

    #[Test]
    public function it_should_returns_probabilities(): void
    {
        // Arrange
        $tournament = Tournament::factory()
            ->make();
        $week1 = Fixture::factory(2)->make([
            "state" => FixtureState::Finished,
            "week_number" => 1,
        ]);
        $week2 = Fixture::factory(2)->make([
            "state" => FixtureState::Finished,
            "week_number" => 2,
        ]);
        $week3 = Fixture::factory(2)->make([
            "state" => FixtureState::Finished,
            "week_number" => 3,
        ]);
        $week4 = Fixture::factory(2)->make([
            "state" => FixtureState::Finished,
            "week_number" => 4,
        ]); // four weeks played
        $restOfFixtures = Fixture::factory(2)->make([
            "state" => FixtureState::Created,
            "week_number" => 3,
        ]); // two weeks left
        $fixtures = $week1->push(...$week2, ...$week3, ...$week4, ...$restOfFixtures);

        $teams = Team::factory(4)->make();
        $teams[0]->name = "Barcelona";
        $teams[0]->points = 10; //  %57.32
        $teams[0]->wins = 3;
        $teams[0]->draws = 1;
        $teams[0]->losses = 0;

        $teams[1]->name = "Liverpool";
        $teams[1]->points = 7;  //  %42.68
        $teams[1]->wins = 2;
        $teams[1]->draws = 1;
        $teams[1]->losses = 1;

        $teams[2]->name = "PSG";
        $teams[2]->points = 3;
        $teams[2]->wins = 1;
        $teams[2]->draws = 0;
        $teams[2]->losses = 3;

        $teams[3]->name = "Juventus";
        $teams[3]->points = 2;
        $teams[3]->wins = 0;
        $teams[3]->draws = 2;
        $teams[3]->losses = 2;

        $tournament->setRelation("teams",  $teams);
        $tournament->setRelation("fixtures", $fixtures);

        $expectedProbabilities = [
            $teams[0]->name => 57.32,
            $teams[1]->name => 42.68,
            $teams[2]->name => 0.0,
            $teams[3]->name => 0.0,
        ];

        // Act
        $predictions = $this->predictionService->predict($tournament);

        // Assert
        $this->assertCount($tournament->teams->count(), $predictions);
        foreach ($predictions as $prediction) {
            $this->assertSame(
                $expectedProbabilities[$prediction->team->name],
                $prediction->probability,
                "Expected {$prediction->team->name} to have probability of {$expectedProbabilities[$prediction->team->name]}",
            );
        }
    }

}
