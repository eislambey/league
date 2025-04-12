<?php

namespace App\Services\Fixture;

readonly class Score
{
    public function __construct(public int $home, public int $away)
    {
    }

    public function isDraw(): bool
    {
        return $this->home === $this->away;
    }

    public function isHomeWin(): bool
    {
        return $this->home > $this->away;
    }

    public function isAwayWin(): bool
    {
        return $this->home < $this->away;
    }
}
