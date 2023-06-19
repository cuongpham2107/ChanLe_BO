<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;

class ConfigController extends Controller
{
   public function show_config()
   {
        $ctk = setting('config.config_ctk');
        $stk =  setting('config.config_stk');
        $name_bank = setting('config.name_bank');
        $link_telegram =  setting('config.link_telegram');
        $qrcode = Voyager::image(setting('config.qr_code'));
        $logo_url = Voyager::image((json_decode(setting('admin.logo')))[0]->download_link);
        $title_website=  setting('admin.title_website');
        $response = [
            'error_code' => 0,
            'results' => [
                'ctk' => $ctk,
                'stk' => $stk,
                'name_bank'=>$name_bank,
                'qrcode' => $qrcode,
                'link_telegram' => $link_telegram,
                'logo_url' =>  $logo_url,
                'title_website'=> $title_website
            ]
            
        ];
        return response($response,200);
   }
}
