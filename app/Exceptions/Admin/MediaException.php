<?php

namespace App\Exceptions\Admin;

use App\Exceptions\BaseException;

class MediaException extends BaseException
{
    public static function mediaNotFound()
    {
        return self::code("Media not found", [], 404);
    }

    public static function mediaNotDeleted()
    {
        return self::code("Media is not deleted", [], 400);
    }
}

