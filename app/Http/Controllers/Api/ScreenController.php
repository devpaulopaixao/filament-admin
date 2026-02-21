<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Screen;
use Illuminate\Http\JsonResponse;

class ScreenController extends Controller
{
    public function show(int $id): JsonResponse
    {
        $screen = Screen::with('panel')->findOrFail($id);

        $panel = $screen->panel;

        return response()->json([
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
        ]);
    }
}
