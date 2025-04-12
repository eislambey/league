<?php

namespace App\Models;

use App\Enums\FixtureState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $id
 * @property int $home_team_id
 * @property int $away_team_id
 * @property int $tournament_id
 * @property int $week_number
 * @property int|null $home_team_score
 * @property int|null $away_team_score
 * @property FixtureState $state
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Team $awayTeam
 * @property-read \App\Models\Team $homeTeam
 * @property-read \App\Models\Tournament $tournament
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fixture newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fixture newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fixture query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fixture whereAwayTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fixture whereAwayTeamScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fixture whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fixture whereHomeTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fixture whereHomeTeamScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fixture whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fixture whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fixture whereTournamentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fixture whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fixture whereWeekNumber($value)
 * @mixin \Eloquent
 */
class Fixture extends Model
{
    use HasFactory;

    protected $fillable = [
        'home_team_id', 'away_team_id', 'tournament_id', 'state', 'week_number', 'home_team_score', 'away_team_score',
    ];

    protected $casts = [
        'state' => FixtureState::class,
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function isDraw(): bool
    {
        return $this->home_team_score === $this->away_team_score;
    }

    public function isHomeWin(): bool
    {
        return $this->home_team_score > $this->away_team_score;
    }

    public function isAwayWin(): bool
    {
        return $this->home_team_score < $this->away_team_score;
    }

    public function hasWinner(): bool
    {
        return $this->home_team_score !== $this->away_team_score;
    }

    public function getWinner(): ?Team
    {
        if ($this->isHomeWin()) {
            return $this->homeTeam;
        }

        if ($this->isAwayWin()) {
            return $this->awayTeam;
        }

        return null;
    }
}
