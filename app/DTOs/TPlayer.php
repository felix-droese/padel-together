<?php

namespace App\DTOs;

use App\Models\Player;
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

    public static function fromPlayer(Player $player): self
    {
        return new self(
            $player->id,
            $player->first_name,
            $player->last_name,
            TUser::fromUser($player->user) ?? null,
        );
    }
}
