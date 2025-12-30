<?php

namespace App\Http\Controllers\Admin\UserReports;

use App\Http\Controllers\Controller;
use App\Models\UserReport;
use App\Exceptions\Admin\ReportException;

class DeleteAdminUserReportController extends Controller
{
    public function __invoke(string $reportId)
    {
        $report = UserReport::withTrashed()->find($reportId);

        if (!$report) {
            throw ReportException::userReportNotFound();
        }

        if ($report->trashed()) {
            throw ReportException::reportAlreadyDeleted();
        }

        $report->delete();

        return responseWithMessage("User report deleted successfully");
    }
}

