<?php

namespace App;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Deposit;
use App\Withdraw;
use App\History;


class Player extends Model
{
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nickname',
        'username',
        'password',
        'phone',
        'email',
        'account_number',
        'bank',
        'cccd_up',
        'cccd_down',
        'status',
        'ref_number'
        
    ];
    public function deposit()
    {
        return $this->hasMany(Deposit::class,'player_id','id');
    }
    public function withdraw()
    {
        return $this->hasMany(Withdraw::class,'player_id','id');
    }
    public function history()
    {
        return $this->hasMany(History::class,'player_id','id');
    }
}
 