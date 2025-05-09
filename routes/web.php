<?php

use App\Http\Controllers\WrisbandMasterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('wrisband');
});

Route::post('/wrisband', [WrisbandMasterController::class, 'store']);

