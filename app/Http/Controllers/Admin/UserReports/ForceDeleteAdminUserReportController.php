<?php

namespace App\Http\Controllers\Admin\UserReports;

use App\Http\Controllers\Controller;
use App\Models\UserReport;
use App\Exceptions\Admin\ReportException;

class ForceDeleteAdminUserReportController extends Controller
{
    public function __invoke(string $reportId)
    {
        $report = UserReport::withTrashed()->find($reportId);

        if (!$report) {
            throw ReportException::userReportNotFound();
        }

        $report->forceDelete();

        return responseWithMessage("User report permanently deleted successfully");
    }
}

