<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

/** @typescript */
class TTeam extends Data
{
    public function __construct(
        public int $id,
        /** @var TPlayer[] */
        public array $players,
    ) {}
}
