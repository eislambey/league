<?php

namespace Tests\Unit\Services\Fixture;

use App\Services\Fixture\Score;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ScoreTest extends TestCase
{
    #[Test]
    #[DataProvider("homeWinScoreProvider")]
    public function it_should_returns_home_win(int $home, int $away): void
    {
        $score = new Score($home, $away);
        $this->assertTrue($score->isHomeWin());
        $this->assertFalse($score->isAwayWin());
        $this->assertFalse($score->isDraw());
    }

    public static function homeWinScoreProvider(): array
    {
        return [
            'simple win' => [2, 1],
            'big win' => [5, 0],
            'close win' => [3, 2],
        ];
    }

    #[Test]
    #[DataProvider("awayWinScoreProvider")]
    public function it_should_returns_away_win(int $home, int $away): void
    {
        $score = new Score($home, $away);
        $this->assertTrue($score->isAwayWin());
        $this->assertFalse($score->isHomeWin());
        $this->assertFalse($score->isDraw());
    }

    public static function awayWinScoreProvider(): array
    {
        return [
            'simple win' => [1, 2],
            'big win' => [0, 5],
            'close win' => [2, 3],
        ];
    }

    #[Test]
    #[DataProvider("drawScoreProvider")]
    public function it_should_returns_draw(int $score): void
    {
        $score = new Score($score, $score);
        $this->assertTrue($score->isDraw());
        $this->assertFalse($score->isHomeWin());
        $this->assertFalse($score->isAwayWin());
    }

    public static function drawScoreProvider(): array
    {
        return [
            'scoreless draw' => [0],
            'low scoring draw' => [1],
            'high scoring draw' => [3],
        ];
    }
}
