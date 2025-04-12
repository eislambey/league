<?php

namespace App\Models;

use App\Enums\FixtureState;
use App\Enums\TournamentState;
use App\Models\Traits\TournamentStateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property TournamentState $state
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Fixture> $fixtures
 * @property-read int|null $fixtures_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Team> $teams
 * @property-read int|null $teams_count
 * @property-read \App\Models\Team|null $winner
 * @method static \Database\Factories\TournamentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tournament whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Tournament extends Model
{
    use HasFactory;
    use TournamentStateTrait;

    protected $fillable = ["name", "state"];

    protected $casts = [
        "state" => TournamentState::class,
    ];

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class);
    }

    public function winner(): HasOne
    {
        return $this->hasOne(Team::class)
            ->orderBy("points", "desc")
            ->orderByRaw("goals_scored - goals_conceded DESC");
    }

    public function weeksLeft(): int
    {
        return $this->weeksCount() - ceil($this->fixtures->where("state", "=", FixtureState::Finished)->count() / 2);
    }

    public function weeksCount(): int|float
    {
        return $this->fixtures->count() / 2;
    }
}
