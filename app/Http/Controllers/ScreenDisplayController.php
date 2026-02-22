<?php

namespace App\Http\Controllers;

use App\Models\Screen;
use App\Models\ScreenAccessLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScreenDisplayController extends Controller
{
    public function show(int $id, Request $request): View
    {
        $screen = Screen::findOrFail($id);

        $userAgent   = $request->userAgent() ?? '';
        $deviceType  = $this->detectDevice($userAgent);

        // Garante apenas 1 registo por dia, por IP, por dispositivo
        ScreenAccessLog::firstOrCreate(
            [
                'screen_id'   => $screen->id,
                'ip_address'  => $request->ip(),
                'device_type' => $deviceType,
                'logged_date' => today(),
            ],
            [
                'user_agent' => $userAgent,
            ]
        );

        return view('tela.display', ['id' => $id]);
    }

    private function detectDevice(string $ua): string
    {
        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobile))/i', $ua)) {
            return 'tablet';
        }

        if (preg_match('/Mobile|Android|iPhone|iPod|BlackBerry|Windows Phone|Opera Mini/i', $ua)) {
            return 'mobile';
        }

        return 'desktop';
    }
}