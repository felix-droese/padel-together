<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

/** @typescript */
class TGamePayment extends Data
{
    public function __construct(
        public int $id,
        public int $game_id,
        public int $player_id,
        public int $amount_in_cent,
        public ?string $paypal_payment_id,
        public string $status,
        public TPlayer $player,
    ) {}
}
