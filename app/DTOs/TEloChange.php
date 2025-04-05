<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

/** @typescript */
class TEloChange extends Data
{
    public function __construct(
        public int $id,
        public int $game_id,
        public int $player_id,
        public int $change,
    ) {}
}
