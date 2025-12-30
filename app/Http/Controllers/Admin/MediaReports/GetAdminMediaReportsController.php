<?php

namespace App\Http\Controllers\Admin\MediaReports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MediaReports\GetAdminMediaReportsRequest;
use App\Http\Resources\Admin\MediaReports\AdminMediaReportCollection;
use App\Models\MediaReport;
use App\Traits\OrderableTrait;
use App\Enums\Album_Media\StateReport;

class GetAdminMediaReportsController extends Controller
{
    use OrderableTrait;

    public function __invoke(GetAdminMediaReportsRequest $request)
    {
        $query = MediaReport::withTrashed()
            ->with([
                'userReport:id,first_name,last_name,email,avatar',
                'reportMedia:id,media_name,media_url',
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

        // Filter by media_id
        if ($request->filled('media_id')) {
            $query->where('media_id', $request->input('media_id'));
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

        return new AdminMediaReportCollection($reports);
    }
}

