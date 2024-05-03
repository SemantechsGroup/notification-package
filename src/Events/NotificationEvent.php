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
    public $channel;

    /**
     * Create a new event instance.
     */
    public function __construct($senderId, $receiverId, $channel, $type, $body)
    {
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->type = $type;
        $this->body = $body;
        $this->isRead = 0;
        $this->channel = $channel;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [$this->type . '.' . $this->receiverId];
    }

    public function broadcastAs()
    {
        return $this->type . '.' . $this->receiverId;
    }
}
