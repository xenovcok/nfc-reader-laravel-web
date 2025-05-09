<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WrisbandMasterController;

// API endpoint to store NFC tag data
Route::post('/wrisband', [WrisbandMasterController::class, 'store']);
Route::get('/wrisband/next-name', [WrisbandMasterController::class, 'nextName']);
