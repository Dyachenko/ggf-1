<?php

namespace App\Transformers;

use App\Match;
use Illuminate\Support\Collection;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\TransformerAbstract;

class StandingsTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [];

    public function transform($pair)
    {
        return [
            'id' => $pair['id'],
            'tournament' => $pair['tournamentId'],
            'round' => $pair['round'],
            'homeTeam' => $pair['homeTeamId'],
            'homeTeamName' => $pair['homeTeamName'],
            'awayTeam' => $pair['awayTeamId'],
            'awayTeamName' => $pair['awayTeamName'],
            'matches' => $pair['matches']
        ];
    }
}