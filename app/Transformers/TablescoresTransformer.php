<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

/**
 * Class TablescoresTransformer
 * @package App\Transformers
 */
class TablescoresTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * @param $teamRank
     * @return array
     */
    public function transform($teamRank)
    {
        return [
            'id' => $teamRank['teamId'],
            'position' => array_get($teamRank, 'position', 0),
            'team' => $teamRank['teamId'],
            'matches' => array_get($teamRank, 'matches', 0),
            'wins' => array_get($teamRank, 'wins', 0),
            'draws' => array_get($teamRank, 'draws', 0),
            'losts' => array_get($teamRank, 'losts', 0),
            'points' => array_get($teamRank, 'points', 0),
            'goalsScored' => array_get($teamRank, 'goalsScored', 0),
            'goalsAgainsted' => array_get($teamRank, 'goalsAgainsted', 0),
            'goalsDifference' => array_get($teamRank, 'goalsDifference', 0)
        ];
    }
}
