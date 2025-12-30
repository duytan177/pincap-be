<?php

namespace App\Http\Controllers\Admin\MediaReports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MediaReports\UpdateAdminMediaReportRequest;
use App\Http\Resources\Admin\MediaReports\AdminMediaReportResource;
use App\Models\MediaReport;
use App\Exceptions\Admin\ReportException;

class UpdateAdminMediaReportController extends Controller
{
    public function __invoke(UpdateAdminMediaReportRequest $request, string $reportId)
    {
        $report = MediaReport::withTrashed()
            ->with([
                'userReport:id,first_name,last_name,email,avatar',
                'reportMedia:id,media_name,media_url',
                'reasonReport:id,title,description'
            ])
            ->find($reportId);

        if (!$report) {
            throw ReportException::mediaReportNotFound();
        }

        $data = $request->validated();
        $report->update($data);

        return new AdminMediaReportResource($report->fresh());
    }
}

