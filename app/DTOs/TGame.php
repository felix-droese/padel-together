<?php

namespace App\DTOs;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

/** @typescript */
class TGame extends Data
{
    public function __construct(
        public int $id,
        public int $first_team_id,
        public ?int $second_team_id,
        public string $date,
        public int $location_id,
        public TTeam $first_team,
        public ?TTeam $second_team,
        public ?TGameResult $result,
        public ?TTeam $winning_team,
        public ?int $price_in_cent,

        /** @param Collection<int, TEloChange> $elo_changes */
        public Collection $elo_changes,

        /** @param Collection<int, TGamePayment> $payments */
        public Collection $payments,
    ) {}
}
