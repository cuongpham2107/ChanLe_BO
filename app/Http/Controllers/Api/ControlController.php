<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Control;
use DateTime;
use App\History;
use App\Player;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Lock;

class ControlController extends Controller
{

    private $isProcessing = 0; // tạo cờ

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $results = Control::where('status','active')->orderBy('created_at','DESC')->get();
        if($results){
            return response([
                'error_code' => 0,
                'results'=>$results,
            ],200);
        }else{
            return response([
                'error_code' => 1,
                'message' => 'Không có bản ghi nào',
            ],200);
        }
    }
    public function show_new()
    {
        $results = Control::where('status','active')->where('created_at',date('Y-m-d H:i'))->select('period','command','type')->first();
        if($results){
            return response([
                'error_code' => 0,
                'results'=>$results,
            ],200);
        }else{
            return response([
                'error_code' => 1,
                'message' => 'Không có bản ghi nào',
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

/*
    public function store(Request $request)
    {
        Log::info($request);
        $fields = $request->validate([
            'eventTime' => 'required',
            'close' => 'required',
        ],
        [
            'eventTime.required'=>'Kỳ chơi phải có ',
            'close.required'=>'Giá trị phải có',
        ]);
        $check = Control::where('period',$fields['eventTime'])->where('status','close')->first();
        if($check == null){
            $results = new Control();
            $results->period = $fields['eventTime'];
            $results->status = 'active';
            $results->value = $fields['close'];
            $results->description = $request->description;
            $results->type = "Auto Binance";
            $data = Control::where('period',(string)((int)$fields['eventTime']-1))->first();
            if($data){
                if($data->value > $fields['close']){
                    $results->command = 'down';
                }
                elseif($data->value < $fields['close']){
                    $results->command = 'up';
                }
                else{
                    if(rand(1,2) == 1){
                        $results->command = 'up';
                    }
                    else{
                        $results->command = 'down';
                    }
                }

            }else{
                if(rand(1,2) == 1){
                    $results->command = 'up';
                }
                else{
                    $results->command = 'down';
                }
            }
            $results->save();
            $check_update = false;
            if($check_update == false){
                $check_update = true;
                $histories = History::where('bet_id',$results->period)
                ->where('status','success')
                ->lockForUpdate()
                ->get();
                $histories->each(function ($history) use ($results)  {
                    $price = $history->price * 0.9;
                    if($history->command_bet == $results->command){
                        $history->status = 'win';
                        $history->total_price = $price;
                    }
                    else{
                        $history->status = 'loss';
                        $history->total_price = -$history->price;
                    }
                    $history->save();
                });

                $playerIds = $histories->pluck('player_id')->unique();
                $players = Player::whereIn('id', $playerIds)->lockForUpdate()->get();
                $players->each(function ($player) use ($histories ) {
                    $money = $histories->where('player_id', $player->id)->where('status','win')->sum('price');
                    $total_win = $histories->where('player_id', $player->id)->where('status','win')->sum('total_price');
                    $player->money += $money + $total_win;

                    $player->save();
                    prepare_bill(
                        $player->id,
                        "<p>*Lịch sử giao dịch</p>
                        <p>- Tổng tiền đặt: ". number_format($histories->where('player_id', $player->id)->where('status','!=','success')->sum('price')) ."</p>
                        <p>- Tổng tiền thắng/ thua: ". number_format($total_win) ."</p>
                        <p>- Số tiền còn lại: ". number_format($player->money) ."</p>"
                        ,null);
                });
                //Log::channel('slack')->info('results', ['ip' => $request->ip()]);
                Log::info('result $check null',['ip' => $request->ip()]);
            }
            return response([
                'error_code' => 0,
                'results' =>$results
            ],200);
        }
        else{
            $check->status = 'active';
            $check->save();
            $histories = History::where('bet_id',$check->period)
            ->where('status','success')
            ->lockForUpdate()
            ->get();
            $check_update = false;
            if($check_update == false){
                $check_update = true;
                $histories->each(function ($history) use ($check)  {
                    $price = $history->price * 0.9;
                    if($history->command_bet == $check->command){

                        $history->status = 'win';
                        $history->total_price = $price;
                    }
                    else{
                        $history->status = 'loss';
                        $history->total_price = -$history->price;
                    }
                    $history->save();
                });
                $playerIds = $histories->pluck('player_id')->unique();
                $players = Player::whereIn('id', $playerIds)->lockForUpdate()->get();
                $players->each(function ($player) use ($histories ) {
                    $money = $histories->where('player_id', $player->id)->where('status','win')->sum('price');
                    $total_win = $histories->where('player_id', $player->id)->where('status','win')->sum('total_price');
                    $player->money += $money + $total_win;
                    $player->save();
                    prepare_bill(
                        $player->id,
                        "<p>*Lịch sử giao dịch</p>
                        <p>- Tổng tiền đặt: ". number_format($histories->where('player_id', $player->id)->where('status','!=','success')->sum('price')) ."</p>
                        <p>- Tổng tiền thắng/ thua: ". number_format($total_win) ."</p>
                        <p>- Số tiền còn lại: ". number_format($player->money) ."</p>"
                        ,null);
                });
                //Log::channel('slack')->info('check', ['ip' => $request->ip()]);
                Log::info('result $check not null',['ip' => $request->ip()]);
            }
            return response([
                'error_code' => 0,
                'results' => $check
            ],200);
        }
    }
*/
public function calculatePayment($check, $histories)
{

    $histories->each(function ($history) use ($check) {
        $price = $history->price * 0.9;
        if ($history->command_bet == $check->command) {
            $history->status = 'win';
            $history->total_price = $price;
        } else {
            $history->status = 'loss';
            $history->total_price = -$history->price;
        }
        $history->paid += 1; // Đánh dấu giao dịch đã được thanh toán (giá trị 1)
        $history->save();
    });

    $playerIds = $histories->pluck('player_id')->unique();
    $players = Player::whereIn('id', $playerIds)->get();
    $players->each(function ($player) use ($histories) {
        $start_money = $player->money;
        $money_win = $histories->where('player_id', $player->id)->where('status', 'win')->sum('price');
        $money_loss = $histories->where('player_id', $player->id)->where('status', 'loss')->sum('price');
        $total_win = $histories->where('player_id', $player->id)->where('status', 'win')->sum('total_price');
        $player->money += $total_win+$money_win;
        $player->save();
        prepare_bill(
            $player->id,
            "<p>*Lịch sử giao dịch</p>
            <p>- Tổng tiền đặt: " . number_format($histories->where('player_id', $player->id)->where('status', '!=', 'success')->sum('price')) . "</p>
            <p>- Tổng tiền thắng/ thua: " . number_format($total_win) . "</p>
            <p>- Số tiền còn lại: " . number_format($player->money) . "</p>",
            null
        );
        Log::info('Log money', ['ID' => $player->id, 'End Money' => number_format($player->money),'Money win' => number_format($money_win), 'Total Win' => number_format($total_win), 'Money Loss' => number_format($money_loss), 'Start Monney' => number_format($start_money)]);
    });
}


     public function store(Request $request)
     {

         $fields = $request->validate([
             'eventTime' => 'required',
             'close' => 'required',
             'token' => 'required',
         ], [
             'eventTime.required' => 'Kỳ chơi phải có',
             'close.required' => 'Giá trị phải có',
             'token.required' => 'Bị cấm không được post',
         ]);
	     if($fields['token'] != '5Q7maWtn3312xavvk')
		 {
		     
            //Log::info($request);
			 //Log::info('Bị hack');
			 return response([
				 'error_code' => 0,
				 'results' => 'Fuck you!!'
			 ], 200);
		 }
		 
         Log::info($request);
		 $check = Control::where('period', $fields['eventTime'])->where('status', 'close')->first();

         if ($check == null) {
             $check = Control::where('period', $fields['eventTime'])->where('status', 'active')->first();
             if($check)
             {
                 Log::info('Duplicate', ['ip' => $request->ip(), 'line' => __LINE__, 'time' => date("Y-m-d H:i:s.").gettimeofday()["usec"]]);
                 return response([
                     'error_code' => 0,
                     'results' => $check
                 ], 200);
             }
             //Log::info('result $check null', ['ip' => $request->ip(), 'line' => __LINE__, 'time' => date("Y-m-d H:i:s.").gettimeofday()["usec"]]);

             $results = new Control();
             $results->period = $fields['eventTime'];
             $results->status = 'active';
             $results->value = $fields['close'];
             $results->description = $request->description;//  .' A - '. date("Y-m-d H:i:s.").gettimeofday()["usec"]
             $results->type = "Auto Binance";

             $data = Control::where('period', (string)((int)$fields['eventTime'] - 1))->first();

             if ($data) {
                 if ($data->value > $fields['close']) {
                     $results->command = 'down';
                 } elseif ($data->value < $fields['close']) {
                     $results->command = 'up';
                 } else {
                     $results->command = rand(1, 2) == 1 ? 'up' : 'down';
                 }
             } else {
                 $results->command = rand(1, 2) == 1 ? 'up' : 'down';
             }
             $check = Control::where('period', $fields['eventTime'])->where('status', 'active')->first();
             if($check)
             {
                 Log::info('Duplicate', ['ip' => $request->ip(), 'line' => __LINE__, 'time' => date("Y-m-d H:i:s.").gettimeofday()["usec"]]);
                 return response([
                     'error_code' => 0,
                     'results' => $check
                 ], 200);
             }else{
                 //Log::info('$results->save()', ['ip' => $request->ip(), 'line' => __LINE__, 'time' => date("Y-m-d H:i:s.").gettimeofday()["usec"]]);

                 if($results->save()){

                 //Log::info('Saved', ['ip' => $request->ip(), 'line' => __LINE__, 'time' => date("Y-m-d H:i:s.").gettimeofday()["usec"]]);
                 if ($results->exists) {
                     //Log::info('Update', ['ip' => $request->ip(), 'line' => __LINE__, 'time' => date("Y-m-d H:i:s.").gettimeofday()["usec"]]);
                     $results->description = $results->description .' UPDATE';
                     $results->save();
                 }
                 else{
                     //Log::info('Insert', ['ip' => $request->ip(), 'line' => __LINE__, 'time' => date("Y-m-d H:i:s.").gettimeofday()["usec"]]);
                     $results->description = $results->description .' Insert';
                     $results->save();
                 }
                     if ($results->wasRecentlyCreated) {
                         $histories = History::where('bet_id', $results->period)
                             ->where('status', 'success')
                             ->where('paid', 0)
                             ->get();

                         $this->calculatePayment($results, $histories);

                         return response([
                             'error_code' => 0,
                             'results' => $results
                         ], 200);
                     }
                 }else{
                 Log::info('Cant save', ['ip' => $request->ip(), 'line' => __LINE__]);
                     return response([
                         'error_code' => 0,
                         'results' => $results
                     ], 200);
             }



             }


         } else {

             $check2 = Control::where('period', $fields['eventTime'])->where('status', 'active')->first();
             if($check2)
             {
                 Log::info('Duplicate B', ['ip' => $request->ip(), 'line' => __LINE__]);
                 return response([
                     'error_code' => 0,
                     'results' => $check
                 ], 200);
             }

             Log::info('result $check not null', ['ip' => $request->ip(), 'line' => __LINE__, 'time' => date("Y-m-d H:i:s.").gettimeofday()["usec"]]);

             $check->status = 'active';
             $check->description = $check->description .' B - '. date("Y-m-d H:i:s.").gettimeofday()["usec"];
             $check->save();

             $histories = History::where('bet_id', $check->period)
                 ->where('status', 'success')
                 ->where('paid', 0)
                 ->get();


             $this->calculatePayment($check, $histories);
             return response([
                 'error_code' => 0,
                 'results' => $check
             ], 200);
         }
     }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $results = Control::where('id',$id)->first();
        if($results){
            return response([
                'error_code' => 0,
                'results'=>$results,
            ],200);
        }else{
            return response([
                'error_code' => 1,
                'message' => 'Không có bản ghi nào',
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