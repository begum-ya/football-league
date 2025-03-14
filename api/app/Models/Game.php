<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Game extends Model
{
    use HasFactory;
    protected $fillable = ['home_team_id', 'away_team_id', 'home_score', 'away_score', 'week_id','played'];

    public function homeTeam()
{
    return $this->belongsTo(Team::class, 'home_team_id');
}

public function awayTeam()
{
    return $this->belongsTo(Team::class, 'away_team_id');
}
}
