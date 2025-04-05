<?php

namespace App\Services;

use App\Models\Player;

class EloService
{
    private const K_FACTOR = 32; // Standard K-factor for ELO calculations

    private const BASE_RATING = 1500; // Starting ELO rating for new players

    public static function calculateNewRatings(Player $winner1, Player $winner2, Player $loser1, Player $loser2): array
    {
        // Calculate team ratings
        $winnerTeamRating = ($winner1->elo + $winner2->elo) / 2;
        $loserTeamRating = ($loser1->elo + $loser2->elo) / 2;

        // Calculate expected scores
        $winnerExpectedScore = 1 / (1 + 10 ** (($loserTeamRating - $winnerTeamRating) / 400));
        $loserExpectedScore = 1 / (1 + 10 ** (($winnerTeamRating - $loserTeamRating) / 400));

        // Calculate new ratings
        $winner1NewRating = $winner1->elo + self::K_FACTOR * (1 - $winnerExpectedScore);
        $winner2NewRating = $winner2->elo + self::K_FACTOR * (1 - $winnerExpectedScore);
        $loser1NewRating = $loser1->elo + self::K_FACTOR * (0 - $loserExpectedScore);
        $loser2NewRating = $loser2->elo + self::K_FACTOR * (0 - $loserExpectedScore);

        return [
            $winner1->id => round($winner1NewRating),
            $winner2->id => round($winner2NewRating),
            $loser1->id => round($loser1NewRating),
            $loser2->id => round($loser2NewRating),
        ];
    }

    public static function getBaseRating(): int
    {
        return self::BASE_RATING;
    }
}
