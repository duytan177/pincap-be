<?php

namespace App\Exceptions\Admin;

use App\Exceptions\BaseException;

class ReportException extends BaseException
{
    public static function reportNotFound()
    {
        return self::code("Report not found", [], 404);
    }

    public static function reportAlreadyDeleted()
    {
        return self::code("Report is already deleted", [], 400);
    }

    public static function reportNotDeleted()
    {
        return self::code("Report is not deleted", [], 400);
    }

    public static function userReportNotFound()
    {
        return self::code("User report not found", [], 404);
    }

    public static function mediaReportNotFound()
    {
        return self::code("Media report not found", [], 404);
    }
}

