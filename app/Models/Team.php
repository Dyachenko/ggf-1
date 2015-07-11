<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model {

	protected $table = 'teams';

	protected $fillable = ['name','logoPath'];

	public $timestamps = true;

	public function teamMembers()
	{
		return $this->hasMany('TeamMember');
	}

	public function tournamentTeams()
	{
		return $this->hasMany('TournamentTeam');
	}

}