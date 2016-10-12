<?php

namespace App\Listeners\Match;

use App\Events\MatchWasFinished;
use App\Match;

/**
 * Class UpdateResultType
 * @package App\Listeners\Match
 */
class UpdateResultType
{
    /**
     * Handle the event.
     *
     * @param  MatchWasFinished $event
     * @return void
     */
    public function handle(MatchWasFinished $event)
    {
        $homeScore = $event->match->homeScore;
        $awayScore = $event->match->awayScore;

        $resultType = Match::RESULT_TYPE_DRAW;

        if ($homeScore > $awayScore) {
            $resultType = Match::RESULT_TYPE_HOME_WIN;
        }

        if ($homeScore < $awayScore) {
            $resultType = Match::RESULT_TYPE_AWAY_WIN;
        }

        $event->match->setAttribute('resultType', $resultType);
    }
}
