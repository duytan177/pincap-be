<?php

namespace App\Http\Controllers\Admin\UserReports;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\UserReports\AdminUserReportResource;
use App\Models\UserReport;
use App\Exceptions\Admin\ReportException;

class RestoreAdminUserReportController extends Controller
{
    public function __invoke(string $reportId)
    {
        $report = UserReport::withTrashed()->find($reportId);

        if (!$report) {
            throw ReportException::userReportNotFound();
        }

        if (!$report->trashed()) {
            throw ReportException::reportNotDeleted();
        }

        $report->restore();

        return new AdminUserReportResource($report->fresh());
    }
}

