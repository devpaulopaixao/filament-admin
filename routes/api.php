<?php

use App\Http\Controllers\Api\PanelController;
use App\Http\Controllers\Api\ScreenController;
use Illuminate\Support\Facades\Route;

Route::get('/panels/{hash}', [PanelController::class, 'show']);
Route::get('/screens/{id}', [ScreenController::class, 'show']);
