<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserFollowedEvent
{
    use Dispatchable, SerializesModels;

    public $followeeId;
    public $followerId;
    /**
     * Create a new event instance.
     */
    public function __construct($followeeId, $followerId)
    {
        $this->followeeId = $followeeId;
        $this->followerId = $followerId;
    }
}
