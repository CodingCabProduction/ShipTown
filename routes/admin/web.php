<?php

/*
|--------------------------------------------------------------------------
| Admin Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

Route::view('settings', 'settings')->name('settings');

Route::prefix('settings')->group(function () {
    Route::view('general', 'settings/general')->name('settings.general');
    Route::view('modules', 'settings/modules')->name('settings.modules');
    Route::view('order-statuses', 'settings/order-statuses')->name('settings.order_statuses');
    Route::view('printnode', 'settings/printnode')->name('settings.printnode');
    Route::view('rmsapi', 'settings/rmsapi')->name('settings.rmsapi');
    Route::view('dpd-ireland', 'settings/dpd-ireland')->name('settings.dpd-ireland');
    Route::view('api2cart', 'settings/api2cart')->name('settings.api2cart');
    Route::view('api', 'settings/api')->name('settings.api');
    Route::view('users', 'users')->name('settings.users');
});

Route::prefix('tools')->group(function () {
    // github.com/rap2hpoutre/laravel-log-viewer
    Route::get('log-viewer', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')
        ->name('tools.log-viewer');

    // github.com/romanzipp/Laravel-Queue-Monitor
    Route::group(['prefix' => 'queue-monitor'], function () {
        Route::queueMonitor();
    });
});