<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Withdraw;
use App\Player;
class WithdrawController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $per_page = $request->input('per_page',20);
        $player_id = auth()->user()->id;
        $results = Withdraw::with('player')->where('player_id',$player_id)->orderBy('created_at','DESC')->paginate($per_page);
        if($results->count() > 0){
            return response([
                'error_code' => 0,
                'results' =>$results,
            ],200);
        }
        else{
            return response([
                'error_code' => 1,
                'message' =>'Chưa có lịch sử rút tiền nào',
            ],200);
        }
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
        $fields = $request->validate([
            'withdraw' => 'required'
        ],
        [
            'withdraw.required'=>'Số tiền rút phải có',
        ]);
        $id = auth()->user()->id;
        $player = Player::where('id', $id)->where('status','unlock')->first();
        if($player){
            $results = new Withdraw();
            $results->player_id =  $id;
            $results->withdraw = $fields['withdraw'];
            $results->account_number = $player->account_number;
            $results->bank = $player->bank;
            $results->account_name_bank = $player->account_name_bank;
            $results->status = 'waiting';
            if($player->money < $fields['withdraw']){
                return response([
                    'error_code' => 1,
                    'message' =>'Số dư trong tài khoản không đủ',
                ],200);
            }
            else{
                $money = $player->money - $fields['withdraw'];
                $player->money = $money;
                $player->save();
                $results->save();
                return response([
                    'error_code' => 0,
                    'results' =>$results,
                    'player'=>$player
                ],200);
            }
        }
        return response([
            'error_code' => 1,
            'message' =>'Không tồn tại người chơi, tài khoản đã bị khoá  hoặc bị khoá',
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
