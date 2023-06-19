<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Player;
use Validator;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        
    }
    public function show_player()
    {
        $results = Player::with('deposit','withdraw','history')->where('id',auth()->user()->id)->first();
        if($results){
            return response([
                'error_code' => 0,
                'results' => $results
            ],200);
        }
        else{
            return response([
                'error_code' => 1,
                'message' => 'Không có dữ liệu người dùng'
            ],200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }
    public function update_player(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nickname' => 'required|string',
            'username' => 'required|string',
            'phone' => 'required|string',
            
        ],
        [
            'nickname.required'=>'Nickname phải có',
            'username.required'=> 'Tên đăng nhập phải có',
            'phone.required'=> 'Số điện thoại phải có',
        ]);
        if($validator->fails()){
            return response()->json(
                [
                    'error_code' => 1,
                    'message' => $validator->errors()

            ],200);
        }
        $player = Player::find(auth()->user()->id);
        if($player){
            $pass = $request->password;
            $player->nickname = $request->nickname;
            $player->username = $request->username;
            $player->phone = $request->phone;
            $player->email = $request->email;
            $player->password =  $pass ? bcrypt($pass) :  $player->password;
            $player->ref_number =  $request->ref_number ?? $player->ref_number;
            $player->save();
            return response([
                'error_code' => 0,
                'results' => $player
            ],200);
        }
        else{
            return response([
                'error_code' => 1,
                'message' => 'Tài khoản không tồn tại'
            ],200);
        }
       
    }
    public function updateBank(Request $request)
    {
        $fields = Validator::make($request->all(),[
            'account_name_bank' => 'required|string',
            'account_number' => 'required|string',
            'bank' => 'required',
        ],
        [
            'account_name_bank.required'=>'Tên chủ tài khoản phải có',
            'account_number.required'=>'Số tài khoản phải có',
            'bank.required'=>'Tên ngân hàng phải có',
        ]);
        if($fields->fails()){
            return response()->json(
                [
                    'error_code' => 1,
                    'message' => $fields->errors()
            ],200);
        }
        $player = Player::find(auth()->user()->id);
        if($player){
            $player->account_name_bank = $request->account_name_bank;
            $player->account_number = $request->account_number;
            $player->bank = $request->bank;
            $cccd_up  = $request->file('cccd_up');
            $cccd_down  = $request->file('cccd_down');
            // dd( $request->all(), $cccd_up ,$cccd_down);
            $validator = Validator::make($request->all(),[
                'cccd_up' => 'required',
                'cccd_down' => 'required',
            ],
            [
                'cccd_up.required'=>'Ảnh CCCD mặt trước phải có',
                'cccd_down.required'=>'Ảnh CCCD mặt trước phải có',
            ]);
            if($validator->fails()){
                return response()->json(
                    [
                        'error_code' => 1,
                        'message' => "Ảnh CCCD mặt trước HOẶC mặt sau phải có"

                ],200);
            }
            $cccd_up_path = $cccd_up->store('public/players');
            $cccd_down_path = $cccd_down->store('public/players');
            $path_cccd_up = substr($cccd_up_path,7);
            $path_cccd_down = substr($cccd_down_path,7);
            $player->cccd_up = $path_cccd_up;
            $player->cccd_down = $path_cccd_down;
            
            $player->save();
            return response([
                'error_code' => 0,
                'results' => $player
            ],200);
        }
        else{
            return response([
                'error_code' => 1,
                'message' => 'Tài khoản không tồn tại'
            ],200);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
