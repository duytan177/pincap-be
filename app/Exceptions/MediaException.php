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
        return self::code("You cannot remove medias that you did not add to this album or owner album", [], 403);
    }
}
