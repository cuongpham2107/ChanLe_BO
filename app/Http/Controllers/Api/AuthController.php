<?php

namespace App\Http\Controllers\Api;

use App\ActionLogs;
use App\Http\Controllers\Controller;
use App\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $fields = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        //check email
        $user = Player::where('username',$fields['username'])->first();

        //check password
        if(!$user || !Hash::check($fields['password'],$user->password)){
            return response([
                'message' => 'Bad creds',
            ],401);
        }
        if($user->status == 'lock' || $user->status == 'banned'){
            return response([
                'message' => 'Tài khoản của bạn đã bị khoá hoặc bị cấm',
            ],400);
        }
        $token = $user->createToken('mytoken_player')->plainTextToken;
        $results = [
            'player' => $user,
            'token' => $token
        ];
        
        $logs = new ActionLogs();
        $logs->player_id = $user->id;
        $logs->type ='login';
        $logs->ip = $request->ip;
        $logs->place = $request->place;
        $logs->save();
        return response($results,200);

    }
    public function logout(Request $request)
    {
        // dd(auth()->user()->tokens());
        auth()->user()->tokens()->delete();
        Session:flush();
        return [
            'message' => "Logged out"
        ];
    }
    public function register(Request $request)
    {
        $fields = $request->validate([
            'username' => 'required|string|unique:players',
            'password' => 'required|string',
        ]);
        $player = Player::create([
            'nickname' => $request->nickname,
            'username' => $fields['username'],
            'phone' => $request->phone,
            'ref_number' => $request->ref_number,
            'status' => 'unlock',
            'email' => $request->email,
            'password' => bcrypt($fields['password']),
            'money' => 0
        ]);
        $token = $player->createToken('mytoken_player')->plainTextToken;
        
        $logs = new ActionLogs();
        $logs->player_id = $player->id;
        $logs->type ='register';
        $logs->ip = $request->ip;
        $logs->place = $request->place;
        $logs->save();
        return response([
            'results' => $player,
            'token'=>$token
        ],200);
    }
    public function forgot_password(Request $request)
    {
        $fields = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        $user = Player::where('username',$fields['username'])->first();
        $user->password =  bcrypt($fields['password']);
        $user->save();
        
        $logs = new ActionLogs();
        $logs->player_id = $player->id;
        $logs->type ='forgotPass';
        $logs->ip = $request->ip;
        $logs->place = $request->place;
        $logs->save();
        return response([
            'results' => $user,
        ],200);

    }
    
}