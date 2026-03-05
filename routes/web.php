<?php

use App\Http\Controllers\ScreenDisplayController;
use App\Models\Panel;
use App\Services\DisplayEncryption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/painel/{hash}', function (string $hash) {
    $panel = Panel::where('hash', $hash)->firstOrFail();

    if ($panel->blocked) {
        $fingerprint = session("panel_pwd_{$hash}");
        $valid       = $fingerprint && substr($panel->password, -10) === $fingerprint;

        if (! $valid) {
            session()->forget(["panel_pwd_{$hash}"]);
            return view('painel.password', compact('hash'));
        }
    }

    $token   = DisplayEncryption::generateToken('panel', $hash);
    $pageKey = DisplayEncryption::getPageKey($token);

    return view('painel.display', compact('hash', 'token', 'pageKey'));
});

Route::post('/painel/{hash}/unlock', function (string $hash, Request $request) {
    $panel = Panel::where('hash', $hash)->firstOrFail();

    if (! $panel->blocked) {
        return redirect("/painel/{$hash}");
    }

    if (Hash::check($request->input('password', ''), $panel->password)) {
        session(["panel_pwd_{$hash}" => substr($panel->password, -10)]);
        return redirect("/painel/{$hash}");
    }

    return back()->withErrors(['password' => 'Senha incorreta.']);
});

Route::get('/tela/{id}', [ScreenDisplayController::class, 'show']);
