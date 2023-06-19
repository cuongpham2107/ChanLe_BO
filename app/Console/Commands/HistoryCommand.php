<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Player;
use App\History;

class HistoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'history:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $player = Player::whereIn('id',[19,14,13,12,11,2])->where('status','unlock')->get();
        list($day,$month,$year,$hour,$min,$sec) = explode("/",date('d/m/Y/H/i/s'));
        $mins = intval($min) + 1;
        if($mins <  10)
        {
            $mins = '0'.$mins;
        }
        $time_now = $year.''.$month.''.$day.''.$hour.''.$mins;
        foreach ($player as $key => $value) {
            $auto_command = rand(5,10);
            for ($i=0; $i < $auto_command ; $i++) { 
                $results = new History();
                $results->player_id = $value->id;
                $results->bet_id = $time_now;
                $results->price = rand(5,10) * 100000;
                $results->price_start = $value->money;
                $value->money = $value->money - $results->price;
                $value->save();
                if(rand(1,2) == 1){
                    $results->command_bet = 'up';
                }
                else{
                    $results->command_bet = 'down';
                }
                $results->status = 'success';
                $results->updated_at = null;
                $results->period = date('Y-m-d H:i:s');
                $results->save();
            }
        }
    }
}