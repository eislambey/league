<?php

namespace App\Enums;

enum TournamentState: string
{
    case Created = 'created';
    case Started = 'started';
    case Finished = 'finished';

    public function isCreated(): bool
    {
        return $this->is(self::Created);
    }

    public function isStarted(): bool
    {
        return $this->is(self::Started);
    }

    public function isFinished(): bool
    {
        return $this->is(self::Finished);
    }

    public function is(self $state): bool
    {
        return $this === $state;
    }
}
