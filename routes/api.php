<?php


use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\Api\ConfigController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\HistoryController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\WithdrawController;
use App\Http\Controllers\Api\ControlController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/login',[AuthController::class,'login']);
Route::post('/register',[AuthController::class,'register']);


Route::get('/show-config',[ConfigController::class,'show_config']);

Route::get('/control/data',[ControlController::class,'show_new']);
Route::get('/command-bet',[HistoryController::class,'get_all']);

Route::prefix('money')->group(function(){
    Route::post('/control',[ControlController::class,'store']);
    // Route::post('/command-bet',[HistoryController::class,'update_history']);
});

// Route::post('/resfesh-token',[AuthController::class,'resfesh_token']);

Route::group(['middleware'=>['auth:sanctum']],function(){
        Route::post('/logout',[AuthController::class,'logout']);
        Route::post('/forgot-password',[AuthController::class,'forgot_password']);
      
        Route::prefix('deposit')->group(function(){
            Route::get('/',[DepositController::class,'index']);
            Route::post('/',[DepositController::class,'store']);
        });
        Route::prefix('withdraw')->group(function(){
            Route::get('/',[WithdrawController::class,'index']);
            Route::post('/',[WithdrawController::class,'store']);
        });
        Route::prefix('history')->group(function(){
            Route::post('/command-bet',[HistoryController::class,'store']);
            Route::get('/command-bet/{id}',[HistoryController::class,'show']);
            Route::get('/command-bet',[HistoryController::class,'index']);
            Route::post('/command-bet/{id}',[HistoryController::class,'update']);
            Route::delete('/command-bet/{id}',[HistoryController::class,'destroy']);
            Route::get('/new-history',[HistoryController::class,'new_history']);
            
        });
        Route::prefix('player')->group(function(){
            Route::get('/',[PlayerController::class,'show_player']);
            Route::post('/',[PlayerController::class,'update_player']);
            Route::post('/add-bank',[PlayerController::class,'updateBank']);
        });
        Route::prefix('control')->group(function(){
            Route::get('/',[ControlController::class,'index']);
            Route::get('/{id}',[ControlController::class,'show']);
           
        });
        Route::prefix('bank')->group(function(){
            Route::get('/',[BankController::class,'index']);
            Route::get('/{id}',[BankController::class,'show']);
        });


});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
