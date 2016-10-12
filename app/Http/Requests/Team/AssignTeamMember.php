<?php

namespace App\Http\Requests\Team;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class AssignTeamMember
 * @package App\Http\Requests\Team
 */
class AssignTeamMember extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'teamMember.teamId' => 'required||exists:tournament_teams,id',
            'teamMember.memberId' => 'required|exists:members,id'
        ];
    }
}
