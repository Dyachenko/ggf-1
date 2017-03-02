<?php

namespace App\Tests\Functional\Http\API;

use App\League;
use App\Match;
use App\Member;
use App\Team;
use App\Tournament;
use App\TournamentTeam;
use App\Tests\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateMatchTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @param $request
     * @param $response
     * @param $attributesToCheck
     *
     * @dataProvider matchesScore
     */
    public function testUpdateMatchScore($request, $response, $attributesToCheck)
    {
        $member = factory(Member::class)->create();

        Auth::login($member);

        /**
         * @var $tournament Tournament
         * @var $league League
         * @var $homeTeam Team
         * @var $awayTeam Team
         * @var $homeTournamentTeam TournamentTeam
         * @var $awayTournamentTeam TournamentTeam
         * @var $match Match
         */

        $member = factory(Member::class)->create();

        $tournament = factory(Tournament::class)->create([
            'owner' => $member->id,
        ]);

        $league = factory(League::class)->create();

        $homeTeam = factory(Team::class)->create([
            'leagueId' => $league->id
        ]);

        $awayTeam = factory(Team::class)->create([
            'leagueId' => $league->id
        ]);

        $homeTournamentTeam = factory(TournamentTeam::class)->create([
            'teamId' => $homeTeam->id,
            'tournamentId' => $tournament->id
        ]);

        $awayTournamentTeam = factory(TournamentTeam::class)->create([
            'teamId' => $awayTeam->id,
            'tournamentId' => $tournament->id
        ]);

        $match = factory(Match::class)->create([
            'tournamentId' => $tournament->id,
            'homeTournamentTeamId' => $homeTournamentTeam->id,
            'awayTournamentTeamId' => $awayTournamentTeam->id
        ]);

        $this->put(
            '/api/v1/matches/' . $match->id,
            [
                'match' => array_get($request, 'match', [])
            ],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'HTTP_CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json'
            ]
        )
            ->assertStatus($response['status']);

        if (!empty($attributesToCheck)) {
            /**
             * @var $updatedRow Match
             */
            $updatedRow = Match::findOrFail($match->id);

            foreach ($attributesToCheck as $attribute => $value) {
                $this->assertEquals($value, $updatedRow->getAttribute($attribute));
            }
        }
    }

    public function matchesScore()
    {
        return [
            'successMatchUpdate' => [
                'request' => [
                    'match' => [
                        'homeScore' => 4,
                        'awayScore' => 2,
                        'status' => Match::STATUS_FINISHED
                    ]
                ],
                'response' => [
                    'status' => Response::HTTP_OK
                ],
                'attributesToCheck' => []
            ],
            'successMatchUpdateWithNotStartedStatus' => [
                'request' => [
                    'match' => [
                        'homeScore' => 2,
                        'awayScore' => 2,
                        'status' => Match::STATUS_NOT_STARTED
                    ]
                ],
                'response' => [
                    'status' => Response::HTTP_OK
                ],
                'attributesToCheck' => [
                    'homeScore' => 2,
                    'awayScore' => 2,
                    'status' => Match::STATUS_NOT_STARTED,
                    'resultType' => Match::RESULT_TYPE_DRAW
                ]
            ],
            'successMatchUpdateWithStartedStatus' => [
                'request' => [
                    'match' => [
                        'homeScore' => 1,
                        'awayScore' => 1,
                        'status' => Match::STATUS_STARTED
                    ]
                ],
                'response' => [
                    'status' => Response::HTTP_OK
                ],
                'attributesToCheck' => [
                    'homeScore' => 1,
                    'awayScore' => 1,
                    'status' => Match::STATUS_STARTED,
                    'resultType' => Match::RESULT_TYPE_DRAW
                ]
            ],
            'successMatchUpdateWithHomeWinResultType' => [
                'request' => [
                    'match' => [
                        'homeScore' => 4,
                        'awayScore' => 2,
                        'status' => Match::STATUS_FINISHED
                    ]
                ],
                'response' => [
                    'status' => Response::HTTP_OK
                ],
                'attributesToCheck' => [
                    'resultType' => Match::RESULT_TYPE_HOME_WIN
                ]
            ],
            'successMatchUpdateWithAwayWinResultType' => [
                'request' => [
                    'match' => [
                        'homeScore' => 2,
                        'awayScore' => 4,
                        'status' => Match::STATUS_FINISHED
                    ]
                ],
                'response' => [
                    'status' => Response::HTTP_OK
                ],
                'attributesToCheck' => [
                    'resultType' => Match::RESULT_TYPE_AWAY_WIN
                ]
            ],
            'successMatchUpdateWithDrawResultType' => [
                'request' => [
                    'match' => [
                        'homeScore' => 2,
                        'awayScore' => 2,
                        'status' => Match::STATUS_FINISHED
                    ]
                ],
                'response' => [
                    'status' => Response::HTTP_OK
                ],
                'attributesToCheck' => [
                    'resultType' => Match::RESULT_TYPE_DRAW
                ]
            ],
            'invalidStatusProperty' => [
                'request' => [
                    'match' => [
                        'homeScore' => 0,
                        'awayScore' => 0,
                        'status' => 'invalid_string'
                    ]
                ],
                'response' => [
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY
                ],
                'attributesToCheck' => []
            ],
            'missedProperties' => [
                'request' => [],
                'response' => [
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY
                ],
                'attributesToCheck' => []
            ],
            'successMatchUpdateForLeagueTournamentType' => [
                'request' => [
                    'tournament' => [
                        'type' => Tournament::TYPE_LEAGUE
                    ],
                    'match' => [
                        'round' => 1,
                        'homeScore' => 0,
                        'awayScore' => 2,
                        'status' => Match::STATUS_FINISHED
                    ]
                ],
                'response' => [
                    'status' => Response::HTTP_OK
                ],
                'attributesToCheck' => []
            ],
            'successMatchUpdateForKnockOutTournamentType' => [
                'request' => [
                    'tournament' => [
                        'type' => Tournament::TYPE_KNOCK_OUT
                    ],
                    'match' => [
                        'round' => 1,
                        'homeScore' => 2,
                        'awayScore' => 1,
                        'status' => Match::STATUS_FINISHED
                    ]
                ],
                'response' => [
                    'status' => Response::HTTP_OK
                ],
                'attributesToCheck' => []
            ],
        ];
    }

    public function testFailedMatchScoreUpdateByGuest()
    {
        /**
         * @var $tournament Tournament
         * @var $league League
         * @var $homeTeam Team
         * @var $awayTeam Team
         * @var $homeTournamentTeam TournamentTeam
         * @var $awayTournamentTeam TournamentTeam
         * @var $match Match
         */
        $member = factory(Member::class)->create();

        $tournament = factory(Tournament::class)->create([
            'owner' => $member->id,
        ]);

        $league = factory(League::class)->create();

        $homeTeam = factory(Team::class)->create([
            'leagueId' => $league->id
        ]);

        $awayTeam = factory(Team::class)->create([
            'leagueId' => $league->id
        ]);

        $homeTournamentTeam = factory(TournamentTeam::class)->create([
            'teamId' => $homeTeam->id,
            'tournamentId' => $tournament->id
        ]);

        $awayTournamentTeam = factory(TournamentTeam::class)->create([
            'teamId' => $awayTeam->id,
            'tournamentId' => $tournament->id
        ]);

        $match = factory(Match::class)->create([
            'tournamentId' => $tournament->id,
            'homeTournamentTeamId' => $homeTournamentTeam->id,
            'awayTournamentTeamId' => $awayTournamentTeam->id
        ]);

        $this->put(
            '/api/v1/matches/' . $match->id,
            [
                'match' => [
                    'homeScore' => 1,
                    'awayScore' => 2,
                    'status' => Match::STATUS_FINISHED
                ]
            ],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'HTTP_CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json'
            ]
        )
            ->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertEquals(Match::findOrFail($match->id)->status, Match::STATUS_NOT_STARTED);
    }

    public function testMatchUpdateFailForUpdateOldRoundOfKnockOutTournamentType()
    {
        $member = factory(Member::class)->create();

        Auth::login($member);

        /**
         * @var $tournament Tournament
         * @var $league League
         */

        $tournament = factory(Tournament::class)->create([
            'owner' => $member->id,
            'type' => Tournament::TYPE_KNOCK_OUT
        ]);

        $league = factory(League::class)->create();

        factory(Team::class, 4)->create([
            'leagueId' => $league->id
        ])
            ->each(function ($team, $key) use ($tournament) {
                $tournament->tournamentTeams()->create([
                    'teamId' => $team->id,
                    'tournamentId' => $tournament->id,
                ]);
            });

        $tournament->status = Tournament::STATUS_STARTED;
        $tournament->save();

        $this->assertEquals($tournament->matches->count(), 4);

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

        // next round matches should be generated
        $this->assertEquals($tournament->getCurrentRound(), 2);

        // get finished match from previous round
        $oldRoundMatch = $tournament->matches()->where(['round' => 1])->get()->first();

        $this->assertInstanceOf(Match::class, $oldRoundMatch);

        $this->put(
            '/api/v1/matches/' . $oldRoundMatch->id,
            [
                'match' => [
                    'homeScore' => 1,
                    'awayScore' => 2,
                    'status' => Match::STATUS_FINISHED
                ]
            ],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'HTTP_CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json'
            ]
        )
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }


    /**
     * @param $teams
     * @param $matches
     * @param $request
     * @param $response
     *
     * @dataProvider updateWithResults
     */
    public function testMatchUpdateWithMatchResults($teams, $matches, $request, $response)
    {
        $member = factory(Member::class)->create();

        Auth::login($member);

        /**
         * @var $tournament Tournament
         * @var $league League
         */
        $tournament = factory(Tournament::class)->create([
            'owner' => $member->id,
            'type' => Tournament::TYPE_KNOCK_OUT
        ]);

        $league = factory(League::class)->create();

        $tournamentTeams = new Collection();

        foreach ($teams as $teamName) {
            $team = factory(Team::class)->create([
                'leagueId' => $league->id,
                'name' => $teamName
            ]);

            $tournamentTeam = $tournament->tournamentTeams()->create([
                'teamId' => $team->id,
                'tournamentId' => $tournament->id,
            ]);

            $tournamentTeams->push([
                'name' => $teamName,
                'id' => $tournamentTeam->id
            ]);
        }

        $this->expectsEvents(\App\Events\Tournament\TournamentWasStarted::class);

        $tournament->status = Tournament::STATUS_STARTED;
        $tournament->save();

        $this->assertEquals(0, $tournament->matches()->get()->count());

        foreach ($matches as $match) {

            $homeTeam = $tournamentTeams->where('name', $match['homeTournamentTeam'])->first();
            $awayTeam = $tournamentTeams->where('name', $match['awayTournamentTeam'])->first();

            unset($match['homeTournamentTeam']);
            unset($match['awayTournamentTeam']);

            $tournament->matches()->create(
                array_merge(
                    [
                        'tournamentId' => $tournament->id,
                        'homeTournamentTeamId' => array_get($homeTeam, 'id'),
                        'awayTournamentTeamId' => array_get($awayTeam, 'id'),
                    ],
                    $match
                )
            );
        }

        $tournamentMatches = $tournament->matches()->get();

        $this->assertEquals(count($matches), $tournamentMatches->count());

        // find match by data from request variable
        $match = $tournamentMatches->filter(function ($match) use ($tournamentTeams, $request) {
            $homeTeam = $tournamentTeams->where('name', array_get($request, 'match.homeTournamentTeam'))->first();
            $awayTeam = $tournamentTeams->where('name', array_get($request, 'match.awayTournamentTeam'))->first();

            return ($match->homeTournamentTeamId === array_get($homeTeam, 'id'))
            && ($match->awayTournamentTeamId === array_get($awayTeam, 'id'))
            && ($match->round === array_get($request, 'match.round'));
        })->values()->first();

        $this->assertInstanceOf(Match::class, $match);

        $this->put(
            '/api/v1/matches/' . $match->id,
            [
                'match' => [
                    'homeScore' => array_get($request, 'match.homeScore'),
                    'awayScore' => array_get($request, 'match.awayScore'),
                    'status' => Match::STATUS_FINISHED
                ]
            ],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'HTTP_CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json'
            ]
        )->assertStatus($response['status']);
    }

    public function updateWithResults()
    {
        return [
            'successUpdate' => [
                'teams' => ['A', 'B', 'C', 'D'],
                'matches' => [
                    [
                        'round' => 1,
                        'gameType' => Match::GAME_TYPE_QUALIFY,
                        'resultType' => Match::RESULT_TYPE_HOME_WIN,
                        'status' => Match::STATUS_FINISHED,
                        'homeTournamentTeam' => 'A',
                        'awayTournamentTeam' => 'B',
                        'homeScore' => 2,
                        'awayScore' => 0,
                        'homePenaltyScore' => 0,
                        'awayPenaltyScore' => 0
                    ],
                    [
                        'round' => 1,
                        'gameType' => Match::GAME_TYPE_QUALIFY,
                        'resultType' => Match::RESULT_TYPE_DRAW,
                        'status' => Match::STATUS_FINISHED,
                        'homeTournamentTeam' => 'B',
                        'awayTournamentTeam' => 'A',
                        'homeScore' => 1,
                        'awayScore' => 1,
                        'homePenaltyScore' => 0,
                        'awayPenaltyScore' => 0
                    ],
                    [
                        'round' => 1,
                        'gameType' => Match::GAME_TYPE_QUALIFY,
                        'resultType' => Match::RESULT_TYPE_HOME_WIN,
                        'status' => Match::STATUS_FINISHED,
                        'homeTournamentTeam' => 'C',
                        'awayTournamentTeam' => 'D',
                        'homeScore' => 2,
                        'awayScore' => 0,
                        'homePenaltyScore' => 0,
                        'awayPenaltyScore' => 0
                    ],
                    [
                        'round' => 1,
                        'gameType' => Match::GAME_TYPE_QUALIFY,
                        'resultType' => Match::RESULT_TYPE_UNKNOWN,
                        'status' => Match::STATUS_NOT_STARTED,
                        'homeTournamentTeam' => 'D',
                        'awayTournamentTeam' => 'C',
                        'homeScore' => 0,
                        'awayScore' => 0,
                        'homePenaltyScore' => 0,
                        'awayPenaltyScore' => 0
                    ]
                ],
                'request' => [
                    'match' => [
                        'round' => 1,
                        'homeTournamentTeam' => 'D',
                        'awayTournamentTeam' => 'C',
                        'homeScore' => 0,
                        'awayScore' => 3
                    ]
                ],
                'response' => [
                    'status' => Response::HTTP_OK,
                    'rounds' => [
                        2 => [
                            'teams' => ['A', 'D']
                        ]
                    ]
                ]
            ],
            'failToUpdateMatchWithoutWinnerInPair' => [
                'teams' => ['A', 'B', 'C', 'D'],
                'matches' => [
                    [
                        'round' => 1,
                        'gameType' => Match::GAME_TYPE_QUALIFY,
                        'resultType' => Match::RESULT_TYPE_HOME_WIN,
                        'status' => Match::STATUS_FINISHED,
                        'homeTournamentTeam' => 'A',
                        'awayTournamentTeam' => 'B',
                        'homeScore' => 2,
                        'awayScore' => 0,
                        'homePenaltyScore' => 0,
                        'awayPenaltyScore' => 0
                    ],
                    [
                        'round' => 1,
                        'gameType' => Match::GAME_TYPE_QUALIFY,
                        'resultType' => Match::RESULT_TYPE_DRAW,
                        'status' => Match::STATUS_FINISHED,
                        'homeTournamentTeam' => 'B',
                        'awayTournamentTeam' => 'A',
                        'homeScore' => 1,
                        'awayScore' => 1,
                        'homePenaltyScore' => 0,
                        'awayPenaltyScore' => 0
                    ],
                    [
                        'round' => 1,
                        'gameType' => Match::GAME_TYPE_QUALIFY,
                        'resultType' => Match::RESULT_TYPE_HOME_WIN,
                        'status' => Match::STATUS_FINISHED,
                        'homeTournamentTeam' => 'C',
                        'awayTournamentTeam' => 'D',
                        'homeScore' => 2,
                        'awayScore' => 0,
                        'homePenaltyScore' => 0,
                        'awayPenaltyScore' => 0
                    ],
                    [
                        'round' => 1,
                        'gameType' => Match::GAME_TYPE_QUALIFY,
                        'resultType' => Match::RESULT_TYPE_UNKNOWN,
                        'status' => Match::STATUS_NOT_STARTED,
                        'homeTournamentTeam' => 'D',
                        'awayTournamentTeam' => 'C',
                        'homeScore' => 0,
                        'awayScore' => 0,
                        'homePenaltyScore' => 0,
                        'awayPenaltyScore' => 0
                    ]
                ],
                'request' => [
                    'match' => [
                        'round' => 1,
                        'homeTournamentTeam' => 'D',
                        'awayTournamentTeam' => 'C',
                        'homeScore' => 2,
                        'awayScore' => 0
                    ]
                ],
                'response' => [
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'rounds' => []
                ]
            ]
        ];
    }
}
