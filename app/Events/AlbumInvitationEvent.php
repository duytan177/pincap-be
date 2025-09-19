<?php

namespace App\Events;

use App\Models\Album;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AlbumInvitationEvent
{
    use Dispatchable, SerializesModels;

    public $album;
    public $inviter;
    public $inviteeId;

    /**
     * Create a new event instance.
     */
    public function __construct($album, $inviter, string $inviteeId)
    {
        $this->album = $album;
        $this->inviter = $inviter;
        $this->inviteeId = $inviteeId;
    }
}


