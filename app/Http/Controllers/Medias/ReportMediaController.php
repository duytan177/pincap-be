<?php

namespace App\Http\Controllers\Medias;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medias\ReportMediaRequest;
use App\Models\MediaReport;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReportMediaController extends Controller
{
    public function __invoke(ReportMediaRequest $request)
    {
        $data = $request->validated();
        $data["user_id"] = JWTAuth::user()->getAttribute("id");
        MediaReport::create($data);
        return responseWithMessage("Report media successfully");
    }
}
