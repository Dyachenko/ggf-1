<?php

namespace App\Observers;

use App\Events\Tournament\TournamentWasReset;
use App\Events\Tournament\TournamentWasStarted;
use App\TournamentTeam;
use Illuminate\Support\Facades\Log;

class TournamentTeamObserver
{
    public function saving(TournamentTeam $model)
    {
        
    }
}