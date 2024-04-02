<?php

namespace Sementechs\Notification\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $senderId;
    public $receiverId;
    public $type;
    public $body;
    public $isRead;

    /**
     * Create a new event instance.
     */
    public function __construct($senderId, $receiverId, $type, $body)
    {
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->type = $type;
        $this->body = $body;
        $this->isRead = 0;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [env('APP_NAME') . '.' . $this->receiverId];
    }

    public function broadcastAs()
    {
        return env('APP_NAME') . '.' . $this->receiverId;
    }
}
