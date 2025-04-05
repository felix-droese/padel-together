<?php

namespace App\DTOs;

use App\Models\User;
use Spatie\LaravelData\Data;

/** @typescript */
class TUser extends Data
{
    public function __construct(
        public int $id,
        public ?string $email,
    ) {}

    public static function fromUser(User $user): self
    {
        return new self($user->id, $user->email);
    }
}
