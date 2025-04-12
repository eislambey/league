<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $id
 * @property int $tournament_id
 * @property string $name
 * @property int $power
 * @property int $wins
 * @property int $losses
 * @property int $draws
 * @property int $points
 * @property int $goals_scored
 * @property int $goals_conceded
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $goal_diff
 * @property-read \App\Models\Tournament|null $tournament
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereDraws($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereGoalsConceded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereGoalsScored($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereLosses($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team wherePower($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereTournamentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereWins($value)
 * @mixin \Eloquent
 */
class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        "name", "tournament_id", "power", "wins", "losses", "draws", "points", "goals_scored", "goals_conceded",
    ];

    protected $casts = [
        "wins" => "integer",
        "losses" => "integer",
        "draws" => "integer",
        "points" => "integer",
        "goals_scored" => "integer",
        "goals_conceded" => "integer",
    ];

    protected $appends = [
        "goal_diff", "fixtures_played",
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function goalDiff(): Attribute
    {
        return new Attribute(
            get: fn () => $this->goals_scored - $this->goals_conceded,
        );
    }

    public function fixturesPlayed(): Attribute
    {
        return new Attribute(
            get: fn () => $this->wins + $this->losses + $this->draws,
        );
    }
}
