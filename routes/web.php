<?php

use App\Http\Controllers\ScreenDisplayController;
use App\Services\DisplayEncryption;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/painel/{hash}', function (string $hash) {
    $token   = DisplayEncryption::generateToken('panel', $hash);
    $pageKey = DisplayEncryption::getPageKey($token);

    return view('painel.display', compact('hash', 'token', 'pageKey'));
});

Route::get('/tela/{id}', [ScreenDisplayController::class, 'show']);
