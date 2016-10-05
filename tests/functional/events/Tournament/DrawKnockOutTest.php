<?php

namespace App\Tests\Unit\Events\Tournament;

use App\Match;
use App\Tournament;
use App\TournamentTeam;
use App\Events\TournamentWasStarted;

use App\Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Log;
use Laracasts\TestDummy\Factory;

class DrawKnockOutTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /**
     * @param $teamsAmount
     * @param $matchesAmount
     *
     * @dataProvider firstRoundDraw
     */
    public function testKnockOutDrawWithDifferrentTeamsAmount($teamsAmount, $matchesAmount, $expectedException = null)
    {
        /**
         * @var $tournament Tournament
         */
        $tournament = Factory::create('App\Tournament', [
            'type' => Tournament::TYPE_KNOCK_OUT
        ]);

        /**
         * @var $tournament Tournament
         */
        $league = Factory::create('App\League');

        Factory::times($teamsAmount)->create('App\MTeam', ['leagueId' => $league->id])
            ->each(function($team, $key) use ($tournament) {
                $tournament->tournamentTeams()->create([
                    'teamId' => $team->id,
                    'tournamentId' => $tournament->id,
                ]);
            });

        if ($expectedException) {
            $this->setExpectedException($expectedException);
        }

        $tournament->status = Tournament::STATUS_STARTED;
        $tournament->save();

        $this->assertTrue($tournament instanceof Tournament);
        // verify total matches amount
        $this->assertEquals($matchesAmount, $tournament->matches()->getResults()->count());
    }

    /**
     * @param $teamsAmount
     * @param $nextRoundMatchesCount
     * @param $currentRound
     *
     * @dataProvider nextRoundDraw
     */
    public function testKnockOutNextRoundDraw($teamsAmount, $nextRoundMatchesCount, $currentRound)
    {
        /**
         * @var $tournament Tournament
         */
        $tournament = Factory::create('App\Tournament', [
            'type' => Tournament::TYPE_KNOCK_OUT
        ]);

        /**
         * @var $tournament Tournament
         */
        $league = Factory::create('App\League');

        Factory::times($teamsAmount)->create('App\Team', ['leagueId' => $league->id])
            ->each(function($team, $key) use ($tournament) {
                $tournament->tournamentTeams()->create([
                    'teamId' => $team->id,
                    'tournamentId' => $tournament->id
                ]);
            });

        // generate first round matches
        $tournament->status = Tournament::STATUS_STARTED;
        $tournament->save();

        // update results of first round matches
        foreach ($tournament->matches as $match) {
            /**
             * @var Match $match
             */

            // set first team as winner
            if ($match->homeTournamentTeamId > $match->awayTournamentTeamId) {
                $match->homeScore = 1;
                $match->awayScore = 0;
            } else {
                $match->homeScore = 0;
                $match->awayScore = 1;
            }

            $match->status = Match::STATUS_FINISHED;
            $match->save();
        }

        $this->assertEquals($currentRound, $tournament->getCurrentRound(), 'Current round is not equal.');

        // new matches of the next round should be generated
        $this->assertEquals(
            $nextRoundMatchesCount,
            $tournament->matches()->where(['status' => Match::STATUS_NOT_STARTED])->get()->count()
        );
    }

    public function firstRoundDraw()
    {
        return [
            [
                'teamsAmount' => 3,
                'matchesCount' => 2,
                'expectedException' => \UnexpectedValueException::class
            ],
            [
                'teamsAmount' => 2,
                'matchesCount' => 2
            ],
            [
                'teamsAmount' => 4,
                'matchesCount' => 4
            ]
        ];
    }

    public function nextRoundDraw()
    {
        return [
            [
                'teamsAmount' => 2,
                'nextRoundMatchesCount' => 0,
                'currentRound' => 1
            ],
            [
                'teamsAmount' => 4,
                'nextRoundMatchesCount' => 1,
                'currentRound' => 2
            ]
        ];
    }
}
