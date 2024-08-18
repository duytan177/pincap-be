<?php declare(strict_types=1);

namespace App\Enums\Notifications;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class NotificationType extends Enum
{
    const MEDIA_CREATED = "media_created";
    const ALBUM_INVITATION = "album_invitation";
    const USER_FOLLOWED = "user_followed";
    const MEDIA_REACTION = "media_reaction";
    const COMMENT_REACTION = "comment_reaction";
    const COMMENT = "comment";
    const COMMENT_REPLY = "reply";
    const MEDIA_ADD_GROUP_ALBUM = "media_add_group_album";
}
