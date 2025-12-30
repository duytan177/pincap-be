<?php

namespace App\Http\Controllers\Admin\UserReports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserReports\GetAdminUserReportsRequest;
use App\Http\Resources\Admin\UserReports\AdminUserReportCollection;
use App\Models\UserReport;
use App\Traits\OrderableTrait;
use App\Enums\Album_Media\StateReport;

class GetAdminUserReportsController extends Controller
{
    use OrderableTrait;

    public function __invoke(GetAdminUserReportsRequest $request)
    {
        $query = UserReport::withTrashed()
            ->with([
                'user:id,first_name,last_name,email,avatar',
                'reporter:id,first_name,last_name,email,avatar',
                'reasonReport:id,title,description'
            ]);

        // Filter by report_state
        if ($request->filled('report_state')) {
            $query->where('report_state', $request->input('report_state'));
        }

        // Filter by user_id
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Filter by user_report_id
        if ($request->filled('user_report_id')) {
            $query->where('user_report_id', $request->input('user_report_id'));
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

        $reports = $query->paginateOrAll($request);

        return new AdminUserReportCollection($reports);
    }
}

