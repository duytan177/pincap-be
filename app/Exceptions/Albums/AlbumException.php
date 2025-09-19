<?php

namespace App\Exceptions\Albums;

use App\Exceptions\BaseException;

class AlbumException extends BaseException
{
    public static function forbidden(string $message = 'Forbidden')
    {
        return self::code($message, [], 403);
    }

    public static function notOwner()
    {
        return self::code('You are not the album owner', [], 403);
    }

    public static function deleteFailed()
    {
        return self::code('Deleted album failed', [], 400);
    }
}


