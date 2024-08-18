<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MediaCreatedEvent
{
    use Dispatchable, SerializesModels;

    public $media;
    /**
     * Create a new event instance.
     */
    public function __construct($media)
    {
        $this->media = $media;
    }
}
