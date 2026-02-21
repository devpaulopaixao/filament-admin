<?php

namespace App\Events;

use App\Models\Panel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PanelUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Panel $panel)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('panel.' . $this->panel->hash);
    }

    public function broadcastWith(): array
    {
        return [
            'id'            => $this->panel->id,
            'hash'          => $this->panel->hash,
            'title'         => $this->panel->title,
            'status'        => $this->panel->status,
            'show_controls' => $this->panel->show_controls,
            'links'         => $this->panel->links()
                ->where('status', true)
                ->get(['id', 'title', 'url', 'duration_time', 'display_number'])
                ->toArray(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'PanelUpdated';
    }
}
