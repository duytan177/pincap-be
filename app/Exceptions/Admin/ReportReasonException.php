<?php

namespace App\Exceptions\Admin;

use App\Exceptions\BaseException;

class ReportReasonException extends BaseException
{
    public static function reportReasonNotFound()
    {
        return self::code("Report reason not found", [], 404);
    }

    public static function reportReasonAlreadyDeleted()
    {
        return self::code("Report reason is already deleted", [], 400);
    }

    public static function reportReasonNotDeleted()
    {
        return self::code("Report reason is not deleted", [], 400);
    }

    public static function reportReasonInUse()
    {
        return self::code("Report reason is in use and cannot be deleted", [], 400);
    }
}

