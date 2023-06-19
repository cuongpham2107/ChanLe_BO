<?php

if(!function_exists('prepare_bill')){
    function prepare_bill($player_id,$desc, $admin_id = null)
    {
        $data = new \App\Log();
        $data->admin_id = $admin_id;
        $data->player_id = $player_id;
        $data->desc = $desc;
        $data->save();
    }
}


?>