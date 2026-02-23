<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Panel;
use App\Models\Screen;
use App\Services\DisplayEncryption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DisplayController extends Controller
{
    public function load(Request $request): JsonResponse
    {
        $token = $request->input('token');

        if (! $token) {
            return response()->json(['error' => 'Token required'], 403);
        }

        $payload = DisplayEncryption::validateToken($token);

        if (! $payload) {
            return response()->json(['error' => 'Invalid or expired token'], 403);
        }

        $data = match ($payload['type'] ?? '') {
            'panel'  => $this->panelData($payload['rid']),
            'screen' => $this->screenData((int) $payload['rid']),
            default  => null,
        };

        if ($data === null) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json(DisplayEncryption::encrypt($data, $token));
    }

    private function panelData(string $hash): ?array
    {
        $panel = Panel::where('hash', $hash)->first();

        if (! $panel) {
            return null;
        }

        return [
            'id'            => $panel->id,
            'hash'          => $panel->hash,
            'title'         => $panel->title,
            'status'        => $panel->status,
            'show_controls' => $panel->show_controls,
            'links'         => $panel->links()
                ->where('status', true)
                ->get(['id', 'title', 'url', 'duration_time', 'display_number'])
                ->toArray(),
        ];
    }

    private function screenData(int $id): ?array
    {
        $screen = Screen::with('panel')->find($id);

        if (! $screen) {
            return null;
        }

        $panel = $screen->panel;

        return [
            'id'         => $screen->id,
            'title'      => $screen->title,
            'status'     => $screen->status,
            'panel_hash' => $panel?->hash,
            'panel'      => $panel ? [
                'id'     => $panel->id,
                'title'  => $panel->title,
                'hash'   => $panel->hash,
                'status' => $panel->status,
            ] : null,
        ];
    }
}
