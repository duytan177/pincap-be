<?php

namespace App\Http\Controllers\Admin\MediaReports;

use App\Http\Controllers\Controller;
use App\Models\MediaReport;
use App\Exceptions\Admin\ReportException;

class ForceDeleteAdminMediaReportController extends Controller
{
    public function __invoke(string $reportId)
    {
        $report = MediaReport::withTrashed()->find($reportId);

        if (!$report) {
            throw ReportException::mediaReportNotFound();
        }

        $report->forceDelete();

        return responseWithMessage("Media report permanently deleted successfully");
    }
}

