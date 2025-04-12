<?php

namespace App\Services;

use App\Models\Team;

final readonly class Prediction
{
    public function __construct(public ?Team $team = null, public ?float $probability = null)
    {
    }
}
