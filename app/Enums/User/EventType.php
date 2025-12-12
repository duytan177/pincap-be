<?php

namespace App\Enums\User;

use BenSampo\Enum\Enum;

/**

 */
final class EventType extends Enum
{
    const VIEW = "view";
    const LIKE = "like";
    const COMMENT = "comment";
    const SAVE = "save";
    const SEARCH = "search";
    /**
     * Get a description for each event type
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::VIEW => 'User viewed content',
            self::LIKE => 'User liked content',
            self::COMMENT => 'User commented on content',
            self::SAVE => 'User saved content',
            self::SEARCH => 'User performed a search',
            default => 'Unknown event type',
        };
    }

    /**
     * Check if the value is a valid event type
     */
    public static function isValidValue($value): bool
    {
        return in_array($value, self::getValues(), true);
    }
}
