<?php

namespace App\Exceptions\Users;

use App\Exceptions\BaseException;

class ProfileException extends BaseException
{
    public static function emailIsExisted()
    {
        return self::code("email is existed", [], 400);
    }

    public static function phoneIsExisted()
    {
        return self::code("phone is existed", [], 400);
    }
}
