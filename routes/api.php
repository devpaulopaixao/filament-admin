<?php

use App\Http\Controllers\Api\PanelController;
use Illuminate\Support\Facades\Route;

Route::get('/panels/{hash}', [PanelController::class, 'show']);
