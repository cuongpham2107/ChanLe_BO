<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Player;

class Deposit extends Model
{
    public function player()
    {
        return $this->belongsTo(Player::class,'player_id','id');
    }
}
