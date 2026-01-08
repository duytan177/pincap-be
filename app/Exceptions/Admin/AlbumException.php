<?php

namespace App\Exceptions\Admin;

use App\Exceptions\BaseException;

class AlbumException extends BaseException
{
    public static function albumNotFound()
    {
        return self::code("Album not found", [], 404);
    }

    public static function albumNotDeleted()
    {
        return self::code("Album is not deleted", [], 400);
    }

    public static function albumAlreadyDeleted()
    {
        return self::code("Album is already deleted", [], 400);
    }
}

