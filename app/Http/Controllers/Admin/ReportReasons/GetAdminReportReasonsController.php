<?php

namespace App\Http\Controllers\Admin\ReportReasons;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReportReasons\GetAdminReportReasonsRequest;
use App\Http\Resources\Admin\ReportReasons\AdminReportReasonCollection;
use App\Models\ReportReason;
use App\Traits\OrderableTrait;

class GetAdminReportReasonsController extends Controller
{
    use OrderableTrait;

    public function __invoke(GetAdminReportReasonsRequest $request)
    {
        $query = ReportReason::withTrashed();

        // Search by title
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }

        // Filter by deleted_at
        if ($request->filled('deleted_at')) {
            if ($request->input('deleted_at') === 'null') {
                $query->whereNull('deleted_at');
            } else {
                $query->whereNotNull('deleted_at');
            }
        }

        // Apply ordering
        $order = $this->getAttributeOrder($request->input('order_key'), $request->input('order_type'));
        if ($order) {
            $query = $this->scopeApplyOrder($query, $order);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $reasons = $query->paginateOrAll($request);

        return new AdminReportReasonCollection($reasons);
    }
}

