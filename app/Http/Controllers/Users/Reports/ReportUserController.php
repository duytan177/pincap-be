<?php

namespace App\Http\Controllers\Users\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\ReportUserRequest;
use App\Models\UserReport;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReportUserController extends Controller
{
    public function __invoke(ReportUserRequest $request)
    {
        $data = $request->validated();
        $data["user_report_id"] = JWTAuth::user()->getAttribute("id");
        UserReport::create($data);
        return responseWithMessage("Report user successfully");
    }
}
