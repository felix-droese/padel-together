<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

/** @typescript */
class TSet extends Data
{
    public function __construct(
        public int $first_team,
        public int $second_team,
    ) {}
}
