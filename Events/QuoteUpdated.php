<?php

namespace App\Events;

use App\DTO\Quote;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuoteUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Quote $info;

    /**
     * @param Quote $info
     */
    public function __construct(Quote $info)
    {
        $this->info = $info;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('quotes');
    }

    public function broadcastWith(): array
    {
        return [
            'info' => $this->info,
        ];
    }

    public function broadcastAs(): string
    {
        return 'default';
    }
}
