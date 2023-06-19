<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class ActionLogs extends Model
{
    protected $fillable = [
        'player_id',
        'admin_id',
    ];
    public function player()
    {
        return $this->belongsTo(Player::class,'player_id','id');
    }
}