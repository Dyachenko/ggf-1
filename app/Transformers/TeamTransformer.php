<?php

namespace App\Transformers;

use App\Team;
use League\Fractal\TransformerAbstract;

/**
 * Class TeamTransformer
 * @package App\Transformers
 */
class TeamTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * @param Team $team
     * @return array
     */
    public function transform(Team $team)
    {
        return [
            'id' => $team->id,
            'leagueId' => $team->leagueId,
            'name' => $team->name,
            'logoPath' => $team->logoPath,
            'updated_at' => $team->updated_at->format('F d, Y')
        ];
    }
}
