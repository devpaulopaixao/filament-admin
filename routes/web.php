<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/painel/{hash}', function (string $hash) {
    return view('painel.display', ['hash' => $hash]);
});
