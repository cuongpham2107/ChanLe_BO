<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\DepositController;
use App\Http\Controllers\Admin\WithdrawController;
use App\Http\Controllers\Admin\ControlController;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('clear-cache', function () {
	\Artisan::call('config:cache');
	\Artisan::call('cache:clear');
  \Artisan::call('view:clear');
  \Artisan::call('route:clear');
//    Artisan::call('cms:publish:assets');
   Artisan::call('storage:link');
	return 'xong';
});

Route::group(['prefix' => '/'], function () {
  Voyager::routes();
  Route::put('/players/update-money/{id}',[PlayerController::class,'update_money'])->name('voyager.players.update_money');
  Route::put('/deposits/update-status/{id}',[DepositController::class,'update_status'])->name('voyager.deposits.update_status');
  Route::put('/withdraw/update-status/{id}',[WithdrawController::class,'update_status'])->name('voyager.withdraw.update_status');
  Route::post('/controls/update-command/{id}',[ControlController::class,'update_cammand'])->name('voyager.controls.update_cammand');
  Route::post('/controls/add-command',[ControlController::class,'add_command'])->name('voyager.controls.add_command');
});
// Route::get('/', function () {
//     return view('welcome');
// });
Route::any('/{any}', function () {
  return view('welcome');
})->where('any', '.*');



