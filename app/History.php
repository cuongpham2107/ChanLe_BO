<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Player;

class History extends Model
{ 
    protected $fillable = [
        'player_id',
        'price',
        'command_bet',
        'point_start',
        'status',
        'period',
        'point_end',
        'bet_id'
        
    ];
    public function player()
    {
        return $this->belongsTo(Player::class,'player_id','id');
    }
}
