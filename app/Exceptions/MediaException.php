<?php

namespace App\Exceptions;

use App\Exceptions\BaseException;

class MediaException extends BaseException
{
    public static function deleteFaired()
    {
        return self::code("Deleted media faired", [], 400);
    }

    public static function cannotUpdateTagsCreated()
    {
        return self::code("Cannot update tags when media is created", [], 400);
    }

    public static function cannotRemoveMediasNotCreatedByUser()
    {
        return self::code("You cannot remove medias from this album. Only album owner or users with EDIT role can remove medias", [], 403);
    }

    public static function mediaPolicyViolation()
    {
        return self::code("Media violates policy and not create media", [], 403);
    }

    public static function noPermission()
    {
        return self::code("You do not have permission to view this media", [], 403);
    }

    public static function noPermissionToDelete()
    {
        return self::code("You can only delete medias that you created", [], 403);
    }
}
