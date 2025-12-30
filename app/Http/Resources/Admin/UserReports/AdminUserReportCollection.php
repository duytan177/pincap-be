<?php

namespace App\Http\Resources\Admin\UserReports;

use App\Components\Resources\BaseCollection;

class AdminUserReportCollection extends BaseCollection
{
    public $collects = AdminUserReportResource::class;
}

