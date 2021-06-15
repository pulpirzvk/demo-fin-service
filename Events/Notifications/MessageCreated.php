<?php

namespace App\Events\Notifications;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $message;

    public ?User $user;

    /**
     * Create a new event instance.
     *
     * @param string $message
     * @param User|null $user
     */
    public function __construct(string $message, ?User $user)
    {
        $this->message = $message;

        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return $this->user
            ? new PrivateChannel('user.' . $this->user->id)
            : new Channel('users');
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
        ];
    }

    public function broadcastAs()
    {
        return 'message';
    }
}
