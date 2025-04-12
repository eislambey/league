<?php

namespace App\Enums;

enum FixtureState: string
{
    case Created = 'created';
    case Cancelled = 'cancelled';
    case Postponed = 'postponed';
    case Finished = 'finished';

    public function isCreated(): bool
    {
        return $this->is(self::Created);
    }

    public function isFinished(): bool
    {
        return $this->is(self::Finished);
    }

    public function isCancelled(): bool
    {
        return $this->is(self::Cancelled);
    }

    public function isPostponed(): bool
    {
        return $this->is(self::Postponed);
    }

    public function is(self $state): bool
    {
        return $this === $state;
    }
}
