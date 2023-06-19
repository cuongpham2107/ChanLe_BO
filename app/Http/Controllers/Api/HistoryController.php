<?php

namespace App\Http\Controllers\Api;

use App\History;
use App\Player;
use App\Control;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HistoryController extends Controller
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
        $results = History::with('player')->where('player_id',$player_id)->orderBy('created_at','DESC')->paginate($per_page);
        if($results->count() > 0){
            return response([
                'error_code' => 0,
                'results' =>$results,
            ],200);
        }
        else{
            return response([
                'error_code' => 1,
                'message' =>'Chưa có lịch sử giao dịch',
            ],200);
        }
    }
    public function get_all()
    {
      
        $results = History::orderBy('created_at','DESC')->where('status','success')->get();
        return response([
            'error_code' => 0,
            'results' =>$results ?? [],
        ],200);
      
    }
    public function new_history(Request $request)
    {
        // dd(1);
        list($day,$month,$year,$hour,$min,$sec) = explode("/",date('d/m/Y/H/i/s'));
        $time_now = $year.''.$month.''.$day.''.$hour.''.$min;
        // dd($time_now);
        $player_id = auth()->user()->id;
        if($request->bet_id){
            $results = History::orderBy('created_at','DESC')
            ->where('bet_id', $request->bet_id)
            ->where('player_id', $player_id)
            ->where('status', '!=' , 'success')
            ->get();
        }
        else{
            $results = History::orderBy('created_at','DESC')
            ->where('bet_id', $time_now)
            ->where('player_id', $player_id)
            ->where('status', '!=' , 'success')
            ->get();
        }

        if($results->count() > 0){
            $total_play = $results->sum('price');
            $total_win = 0;
            $total_loss = 0;
            foreach ($results as $key => $value) {
                    if($value->status == 'win'){
                        $total_win += $value->total_price;  
                    
                    }
                    elseif($value->status == 'loss'){
                        $total_loss += $value->total_price;
                    }
            }
            return response([
                'error_code' => 0,
                'results' =>$results,
                'total_play' => number_format($total_play),
                'total_win'=>number_format($total_win),
                'total_loss'=> number_format($total_loss),
                'sum'=> number_format($total_play + $total_win + $total_loss)
            ],200);
        }
        else{
            return response([
                'error_code' => 1,
                'message' =>'Chưa có lịch sử giao dịch phiên này',
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
            'bet_id' => 'required',
            'price' => 'required',
            'command_bet'=> 'required',
        ],
        [
            'price.required'=>'Số tiền đặt lệnh phải có',
            'command_bet.required'=> 'Lệnh đặt phải có',
        ]
        );
        $player = Player::where('id',auth()->user()->id)->where('status','unlock')->first();
        list($day,$month,$year,$hour,$min,$sec) = explode("/",date('d/m/Y/H/i/s'));
        $time_now = $year.''.$month.''.$day.''.$hour.''.$min+1;
        Log::info($request, ['Time'=>(int)$time_now, 'Player' => auth()->user()->id]);
        if($request->bet_id == (int)$time_now){
            if($player && $player->money >= $fields['price']){
                $results = new History();
                $results->player_id = auth()->user()->id;
                $results->bet_id = $fields['bet_id'];
                $results->price = $fields['price'];
                $results->price_start = $player->money;
                $player->money = $player->money - $fields['price'];
                $player->save();
                $results->command_bet = $fields['command_bet'];
                $results->status = 'success';
                $results->updated_at = null;
                $results->period = date('Y-m-d H:i:s');
                $results->save();
                Log::info('Đặt lệnh thành công', ['Time'=>(int)$time_now, 'Player' => auth()->user()->id]);
                return response([
                    'error_code' => 0,
                    'results' =>$results,
                    'player'=> $player,
                    'message' =>'Đặt lệnh thành công',
                ],200);
            }
            else{
                Log::info('Số dư không đủ', ['Time'=>(int)$time_now, 'Player' => auth()->user()->id]);
                return response([
                    'error_code' => 1,
                    'message' =>'Số dư trong tài khoản của bạn không đủ',
                ],200);
            }
        }
        else{
            Log::info('Sai phiên', ['Time'=>(int)$time_now, 'Player' => auth()->user()->id]);
            return response([
                'error_code' => 1,
                'message' =>'Phiên bạn đánh đã quá thời gian',
            ],200);
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
        $player_id = auth()->user()->id;
        $results = History::where('id',$id)->where('player_id',$player_id)->first();
        if($results){
            return response([
                'error_code' => 0,
                'results' =>$results,
            ],200);
        }
        else{
            return response([
                'error_code' => 1,
                'message' =>'Chưa có lịch sử giao dịch',
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
       
        $fields = $request->validate([
            'point_end'=> 'required',
        ]);
        Log::info('Log History',['request' => $request, 'ID' => $id]);
        $player = Player::where('id',auth()->user()->id)->where('status','unlock')->first();
        
        $results =  History::where('bet_id',$id)->whereBetween('created_at', [now()->subMinutes(10), now()])->first();
       
        if($results != null && $results->point_end == null){
            if($results->point_start > $fields['point_end'] && $results->command_bet == 'down'){
                $results->status = 'win';
                $results->total_price = $results->price;
                $player->money = $player->money + (2 * $results->price);
                $results->point_end = $fields['point_end'];
                $player->save();
                $results->save();
                return response([
                    'error_code' => 0,
                    'results' =>$results,
                    'player'  =>$player
                ],200);
            }
            elseif($results->point_start > $fields['point_end'] && $results->command_bet == 'up'){
                $results->status = 'loss';
                $results->total_price = $results->price;
                $results->point_end = $fields['point_end'];
                $player->save();
                $results->save();
                return response([
                    'error_code' => 0,
                    'results' =>$results,
                    'player'  =>$player
                ],200);
            }
            elseif($results->point_start < $fields['point_end'] && $results->command_bet == 'down'){
                $results->status = 'loss';
                $results->total_price = $results->price;
                $results->point_end = $fields['point_end'];
                $player->save();
                $results->save();
                return response([
                    'error_code' => 0,
                    'results' =>$results,
                    'player'  =>$player
                ],200);
            }
            elseif($results->point_start < $fields['point_end'] && $results->command_bet == 'up'){
                $results->status = 'win';
                $results->total_price = $results->price;
                $player->money = $player->money + (2 * $results->price);
                $results->point_end = $fields['point_end'];
                $player->save();
                $results->save();
                return response([
                    'error_code' => 0,
                    'results' =>$results,
                    'player'  =>$player
                ],200);
            }
            elseif($results->point_start == $fields['point_end']){
                $results->status = 'restore';
                $results->total_price = 0;
                $player->money = $player->money + $results->price;
                $results->point_end = $fields['point_end'];
                $player->save();
                $results->save();
                return response([
                    'error_code' => 0,
                    'results' =>$results,
                    'player'  =>$player
                ],200);
            }
            else{
                return response([
                    'error_code' => 1,
                    'message' =>'Giao dịch không thành công',
                ],200);
            }
        }
        else{
            return response([
                'error_code' => 1,
                'message' =>'Không tồn tại phiên giao dịch hoặc phiên giao dịch đã kết thúc',
            ],200);
        }
        
        
    }
    public function update_history(Request $request)
    {
        //Update mới
        $fields = $request->validate([
            'eventTime' => 'required',
            'command' => 'required',
        ],
        [
            'eventTime.required'=>'Kỳ chơi phải có ',
            'command.required'=>'Giá trị phải có',
        ]);
        $control = Control::where('period',$fields['eventTime'])->first();
        $control->status = 'active';
        $control->save();
        
        // $request->whenHas(['eventTime','command'], function (string $input) {
        //     // ...
        // });
        // Log::channel('slack')->debug($request);
        $histories = History::where('bet_id',$control->period)
        ->where('status','success')
        ->get();
        // Thực hiện update giá trị của các bản ghi history
        $histories->each(function ($history) use ($request)  {
            // Logic tính toán giá trị cược
            // dd($history->command_bet == $request->command);
            $price = $history->price * 0.9;
            // $price_end = $history->price + $price;
            // $player = Player::find($history->player_id);
            if($history->command_bet == $control->command){
               
                $history->status = 'win';
                $history->total_price = $price;
                // $history->price_end =  $player->money;
            }
            else{
                $history->status = 'loss';
                $history->total_price = -$history->price;
                // $history->price_end =  $history->price_start;
            }
            $history->save();
        });
        // Lấy danh sách các player tương ứng cần update theo mảng id
        $playerIds = $histories->pluck('player_id')->unique();
        $players = Player::whereIn('id', $playerIds)->get();
        // dd($playerIds,$players);
        // Thực hiện update số tiền của các player
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
       
       
        Log::channel('slack')->debug($request->ip());
        return response([
            'error_code' => 0,
            'results' =>$control
        ],200);
       
           
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $results = History::find($id);
        if($results){
            $results->delete();
            return response([
                'error_code' => 0,
                'message' => 'Xoá lịch sử thành công'
            ],200);
        }
        else{
            return response([
                'error_code' => 1,
                'message' => 'Xoá lịch sử thất bại'
            ],200);
        }
    }
    
}