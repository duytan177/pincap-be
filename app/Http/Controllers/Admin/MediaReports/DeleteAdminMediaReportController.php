<?php

namespace App\Http\Controllers\Admin\MediaReports;

use App\Http\Controllers\Controller;
use App\Models\MediaReport;
use App\Exceptions\Admin\ReportException;

class DeleteAdminMediaReportController extends Controller
{
    public function __invoke(string $reportId)
    {
        $report = MediaReport::withTrashed()->find($reportId);

        if (!$report) {
            throw ReportException::mediaReportNotFound();
        }

        if ($report->trashed()) {
            throw ReportException::reportAlreadyDeleted();
        }

        $report->delete();

        return responseWithMessage("Media report deleted successfully");
    }
}

