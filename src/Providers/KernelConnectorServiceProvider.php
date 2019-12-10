<?php

namespace Zdrojowa\KernelConnector\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class KernelConnectorServiceProvider extends ServiceProvider
{


    public function boot() {
        Route::post('/zdrojowa/kernel/sync/', '\Zdrojowa\KernelConnector\Http\Controllers\KernelSynchronizationController@sync')->name('kernel.sync');
    }
}
