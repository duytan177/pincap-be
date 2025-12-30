<?php

namespace App\Http\Controllers\Admin\ReportReasons;

use App\Http\Controllers\Controller;
use App\Models\ReportReason;
use App\Exceptions\Admin\ReportReasonException;

class ForceDeleteAdminReportReasonController extends Controller
{
    public function __invoke(string $reasonId)
    {
        $reason = ReportReason::withTrashed()->find($reasonId);

        if (!$reason) {
            throw ReportReasonException::reportReasonNotFound();
        }

        $reason->forceDelete();

        return responseWithMessage("Report reason permanently deleted successfully");
    }
}

