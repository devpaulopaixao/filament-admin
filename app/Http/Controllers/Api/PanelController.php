<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Panel;
use Illuminate\Http\JsonResponse;

class PanelController extends Controller
{
    public function show(string $hash): JsonResponse
    {
        $panel = Panel::where('hash', $hash)->firstOrFail();

        return response()->json([
            'id'           => $panel->id,
            'hash'         => $panel->hash,
            'title'        => $panel->title,
            'status'       => $panel->status,
            'show_controls' => $panel->show_controls,
            'links'        => $panel->links()
                ->where('status', true)
                ->get(['id', 'title', 'url', 'duration_time', 'display_number']),
        ]);
    }
}
