<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserFollowedEvent
{
    use Dispatchable, SerializesModels;

    public $followeeId;
    public $follower;
    /**
     * Create a new event instance.
     */
    public function __construct($followeeId, $follower)
    {
        $this->followeeId = $followeeId;
        $this->follower = $follower;
    }
}
