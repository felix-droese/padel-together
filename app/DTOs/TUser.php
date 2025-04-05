<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

/** @typescript */
class TUser extends Data
{
    public function __construct(
        public ?string $email = null,
    ) {}
}
