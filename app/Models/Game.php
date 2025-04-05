<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Game extends Model
{
    /** @use HasFactory<\Database\Factories\GameFactory> */
    use HasFactory;

    protected $guarded = [];

    public function firstTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'first_team_id');
    }

    public function secondTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'second_team_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function result(): HasOne
    {
        return $this->hasOne(GameResult::class);
    }
}
