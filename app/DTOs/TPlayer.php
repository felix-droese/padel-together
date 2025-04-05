<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

/** @typescript */
class TPlayer extends Data
{
    public function __construct(
        public int $id,
        public string $first_name,
        public string $last_name,
        public ?TUser $user = null,
    ) {}
}
