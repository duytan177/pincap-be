<?php

namespace App\Http\Controllers\Users\Reports;

use App\Http\Controllers\Controller;
use App\Http\Resources\Users\Reports\ReportReasonCollection;
use App\Models\ReportReason;

class GetListReportReasonController extends Controller
{
    public function __invoke()
    {
        return ReportReasonCollection::make(ReportReason::all());
    }
}
