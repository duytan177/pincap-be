<?php

namespace App\Exceptions\Admin;

use App\Exceptions\BaseException;

class AdminException extends BaseException
{
    public static function userNotFound()
    {
        return self::code("User not found", [], 404);
    }

    public static function userAlreadyDeleted()
    {
        return self::code("User is already deleted", [], 400);
    }

    public static function userNotDeleted()
    {
        return self::code("User is not deleted", [], 400);
    }

    public static function cannotDeleteSelf()
    {
        return self::code("Cannot delete yourself", [], 400);
    }

    public static function emailAlreadyExists()
    {
        return self::code("Email already exists", [], 400);
    }

    public static function phoneAlreadyExists()
    {
        return self::code("Phone already exists", [], 400);
    }

    public static function cannotModifyRootAccount()
    {
        return self::code("Cannot modify root account (admin@gmail.com)", [], 403);
    }
}

