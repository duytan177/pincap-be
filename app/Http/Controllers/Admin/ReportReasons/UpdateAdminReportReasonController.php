<?php

namespace App\Http\Controllers\Admin\ReportReasons;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReportReasons\UpdateAdminReportReasonRequest;
use App\Http\Resources\Admin\ReportReasons\AdminReportReasonResource;
use App\Models\ReportReason;
use App\Exceptions\Admin\ReportReasonException;

class UpdateAdminReportReasonController extends Controller
{
    public function __invoke(UpdateAdminReportReasonRequest $request, string $reasonId)
    {
        $reason = ReportReason::withTrashed()->find($reasonId);

        if (!$reason) {
            throw ReportReasonException::reportReasonNotFound();
        }

        $data = $request->validated();
        $reason->update($data);

        return new AdminReportReasonResource($reason->fresh());
    }
}

