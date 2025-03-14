<?php

namespace App\Services;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class TeamService
{
    /**
     * Fetch all teams.
     */
    public function getTeams(): Collection
    {
        return Team::all();
    }

    /**
     * Create a new team.
     */
    public function createTeam(array $data): Team
    {
        return Team::create($data);
    }
}
