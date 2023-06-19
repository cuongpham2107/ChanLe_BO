<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Player;

class Withdraw extends Model
{
    public function player()
    {
        return $this->belongsTo(Player::class,'player_id','id');
    }
}
