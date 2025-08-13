<?php

use App\Http\Controllers\HookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Log::debug('input', (new Illuminate\Http\Request)->all());

Route::group(['prefix' => 'hooks',], function () {

    Route::post('stage/lead', [HookController::class, 'leads']);

    Route::post('stage/sale', [HookController::class, 'sales']);

    Route::post('telegram', 'TelegramController@handle');
});




