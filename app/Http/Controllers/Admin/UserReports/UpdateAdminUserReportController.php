<?php

namespace App\Http\Controllers\Admin\UserReports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserReports\UpdateAdminUserReportRequest;
use App\Http\Resources\Admin\UserReports\AdminUserReportResource;
use App\Models\UserReport;
use App\Exceptions\Admin\ReportException;

class UpdateAdminUserReportController extends Controller
{
    public function __invoke(UpdateAdminUserReportRequest $request, string $reportId)
    {
        $report = UserReport::withTrashed()
            ->with([
                'user:id,first_name,last_name,email,avatar',
                'reporter:id,first_name,last_name,email,avatar',
                'reasonReport:id,title,description'
            ])
            ->find($reportId);

        if (!$report) {
            throw ReportException::userReportNotFound();
        }

        $data = $request->validated();
        $report->update($data);

        return new AdminUserReportResource($report->fresh());
    }
}

