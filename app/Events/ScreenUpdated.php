<?php

namespace App\Events;

use App\Models\Screen;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScreenUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Screen $screen)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('screen.' . $this->screen->id);
    }

    public function broadcastWith(): array
    {
        return [
            'id'         => $this->screen->id,
            'title'      => $this->screen->title,
            'status'     => $this->screen->status,
            'panel_hash' => $this->screen->panel?->hash,
        ];
    }

    public function broadcastIf(): bool
    {
        return config('broadcasting.default') === 'reverb';
    }

    public function broadcastAs(): string
    {
        return 'ScreenUpdated';
    }
}
