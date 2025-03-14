<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Week extends Model
{
    use HasFactory;
    protected $fillable = [
        'week'
    ];

    public function games()
{
    return $this->hasMany(Game::class);
}
}
