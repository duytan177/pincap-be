<?php

namespace App\Http\Controllers\Admin\MediaReports;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\MediaReports\AdminMediaReportResource;
use App\Models\MediaReport;
use App\Exceptions\Admin\ReportException;

class RestoreAdminMediaReportController extends Controller
{
    public function __invoke(string $reportId)
    {
        $report = MediaReport::withTrashed()->find($reportId);

        if (!$report) {
            throw ReportException::mediaReportNotFound();
        }

        if (!$report->trashed()) {
            throw ReportException::reportNotDeleted();
        }

        $report->restore();

        return new AdminMediaReportResource($report->fresh());
    }
}

