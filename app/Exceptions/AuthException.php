<?php

namespace App\Exceptions;

use App\Exceptions\BaseException;

class AuthException extends BaseException
{
    public static function invalidCredential()
    {
        return self::code('Email or password is incorrect', [], 401);
    }

    public static function emailNotVerified()
    {
        return self::code("Email has not been verified", [], 403);
    }

    public static function tokenExpired()
    {
        return self::code("Token has expired", [], 401);
    }
}
