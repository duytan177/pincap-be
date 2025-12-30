<?php

namespace App\Http\Controllers\Admin\ReportReasons;

use App\Http\Controllers\Controller;
use App\Models\ReportReason;
use App\Exceptions\Admin\ReportReasonException;

class DeleteAdminReportReasonController extends Controller
{
    public function __invoke(string $reasonId)
    {
        $reason = ReportReason::withTrashed()->find($reasonId);

        if (!$reason) {
            throw ReportReasonException::reportReasonNotFound();
        }

        if ($reason->trashed()) {
            throw ReportReasonException::reportReasonAlreadyDeleted();
        }

        // Check if reason is in use
        $mediaReportsCount = $reason->mediaReports()->count();
        $userReportsCount = $reason->userReports()->count();

        if ($mediaReportsCount > 0 || $userReportsCount > 0) {
            throw ReportReasonException::reportReasonInUse();
        }

        $reason->delete();

        return responseWithMessage("Report reason deleted successfully");
    }
}

