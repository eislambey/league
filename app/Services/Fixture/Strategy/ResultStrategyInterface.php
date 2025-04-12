<?php

namespace App\Services\Fixture\Strategy;

use App\Models\Fixture;
use App\Services\Fixture\Score;

interface ResultStrategyInterface
{
    public function getScore(Fixture $fixture): Score;
}
