<?php
namespace App\Models;

use App\Enums\User\EventType;
use Illuminate\Database\Eloquent\Model;

class UserEvent extends Model
{
    protected $fillable = ['user_id', 'event_type', 'media_id', 'metadata', 'processed'];
    protected $casts = [
        'metadata' => 'array',
        'processed' => 'boolean',

    ];
    /**
     * Mutator to ensure 'event_type' is always valid
     */
    public function setEventTypeAttribute($value): void
    {
        if (!EventType::isValidValue($value)) {
            throw new \InvalidArgumentException("Invalid event type: $value");
        }
        $this->attributes['event_type'] = $value;
    }

    /**
     * Accessor to retrieve the event_type description
     */
    public function getEventTypeDescriptionAttribute(): string
    {
        return EventType::getDescription($this->event_type);
    }
}
