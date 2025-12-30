<?php

namespace App\Http\Controllers\Admin\ReportReasons;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReportReasons\CreateAdminReportReasonRequest;
use App\Http\Resources\Admin\ReportReasons\AdminReportReasonResource;
use App\Models\ReportReason;

class CreateAdminReportReasonController extends Controller
{
    public function __invoke(CreateAdminReportReasonRequest $request)
    {
        $data = $request->validated();
        $reason = ReportReason::create($data);

        return new AdminReportReasonResource($reason);
    }
}

