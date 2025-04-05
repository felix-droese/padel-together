<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

/** @typescript */
class TGameResult extends Data
{
    public function __construct(
        public int $id,
        public int $game_id,
        /** @var array<int, TSet> */
        public array $sets,
    ) {}
}
