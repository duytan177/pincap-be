<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $id;
    public $title;
    public $content;
    public $link;
    public $sender;
    public $receiverIds;
    public $notificationType;


    public function __construct($id, $title, $content, $link, $sender, $receiverIds, $notificationType)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->link = $link;
        $this->sender = $sender;
        $this->receiverIds = $receiverIds;
        $this->notificationType = $notificationType;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [];
        foreach ($this->receiverIds as $receiverId) {
            $channels[] = new PrivateChannel('notifications-' . $receiverId);
        }
        return $channels;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'link' => $this->link,
            'sender' => $this->sender,
            'notificationType' => $this->notificationType,
        ];
    }

    // This method is used to specify the event name when broadcasting.
    // It is optional, but can be useful for clarity.
    public function broadcastAs()
    {
        return 'notifications';
    }
}
