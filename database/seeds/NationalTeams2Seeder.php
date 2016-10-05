<?php

use Illuminate\Database\Seeder;

class NationalTeams2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $league = \App\League::firstOrNew([
            'name' => 'National teams',
            'logoPath' => 'leagues-logo/national-teams.png'
        ]);
        $league->save();

        if (1 > \App\Team::where(['name' => 'Austria'])->count()) {
            DB::table('teams')->insert([
                'leagueId' => $league->id,
                'name' => 'Austria',
                'logoPath' => 'teams-logo/austria.png',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ]);
        }

        if (1 > \App\Team::where(['name' => 'Denmark'])->count()) {
            DB::table('teams')->insert([
                'leagueId' => $league->id,
                'name' => 'Denmark',
                'logoPath' => 'teams-logo/denmark.png',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ]);
        }

        if (1 > \App\Team::where(['name' => 'Greece'])->count()) {
            DB::table('teams')->insert([
                'leagueId' => $league->id,
                'name' => 'Greece',
                'logoPath' => 'teams-logo/greece.png',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ]);
        }

        if (1 > \App\Team::where(['name' => 'Portugal'])->count()) {
            DB::table('teams')->insert([
                'leagueId' => $league->id,
                'name' => 'Portugal',
                'logoPath' => 'teams-logo/portugal.png',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ]);
        }

        if (1 > \App\Team::where(['name' => 'Switzerland'])->count()) {
            DB::table('teams')->insert([
                'leagueId' => $league->id,
                'name' => 'Switzerland',
                'logoPath' => 'teams-logo/switzerland.png',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now()
            ]);
        }

    }
}
