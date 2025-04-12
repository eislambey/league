<?php

namespace App\Models\Traits;

use App\Enums\TournamentState;

trait TournamentStateTrait
{
    public function setFinished(): void
    {
        $this->state = TournamentState::Finished;
    }

    public function setStarted(): void
    {
        $this->state = TournamentState::Started;
    }

    public function setCreated(): void
    {
        $this->state = TournamentState::Created;
    }
}
