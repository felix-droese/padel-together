<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

/** @typescript */
class TLocation extends Data
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}
}
