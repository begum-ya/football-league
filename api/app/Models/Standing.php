<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Standing extends Model
{ 
    use HasFactory;
    protected $fillable = [   'team_id',
    'points',
    'won',
    'lose',
    'draw',
    'goal_difference','goals_scored','goals_conceded'];

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
