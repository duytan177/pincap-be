<?php

namespace App\Http\Controllers\Admin\ReportReasons;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ReportReasons\AdminReportReasonResource;
use App\Models\ReportReason;
use App\Exceptions\Admin\ReportReasonException;

class RestoreAdminReportReasonController extends Controller
{
    public function __invoke(string $reasonId)
    {
        $reason = ReportReason::withTrashed()->find($reasonId);

        if (!$reason) {
            throw ReportReasonException::reportReasonNotFound();
        }

        if (!$reason->trashed()) {
            throw ReportReasonException::reportReasonNotDeleted();
        }

        $reason->restore();

        return new AdminReportReasonResource($reason->fresh());
    }
}

