<?php

use App\Http\Controllers\Api\DisplayController;
use Illuminate\Support\Facades\Route;

Route::post('/display', [DisplayController::class, 'load'])->middleware('throttle:60,1');
