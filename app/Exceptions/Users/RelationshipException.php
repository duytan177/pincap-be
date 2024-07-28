<?php

namespace App\Exceptions\Users;

use App\Exceptions\BaseException;

class RelationshipException extends BaseException
{
    public static function unFollowOrBlock($status)
    {
        return self::code("Un".strtolower($status)." faired", [], 400);
    }
}
