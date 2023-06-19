<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \TCG\Voyager\Facades\Voyager::addAction(\App\Actions\PopupAction::class);
        \TCG\Voyager\Facades\Voyager::addAction(\App\Actions\ActiveCloseDepositAction::class);
        \TCG\Voyager\Facades\Voyager::addAction(\App\Actions\ActiveCloseWithdrawAction::class);
        Paginator::useBootstrap();
    }
}
