<?php

namespace App\Http\Controllers\Medias\Reactions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medias\Reactions\ReplyCommentRequest;
use App\Models\Reply;
use App\Traits\AWSS3Trait;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReplyCommentController extends Controller
{
    use AWSS3Trait;

    public function __invoke(ReplyCommentRequest $request)
    {
        $data = $request->validated();
        $data["user_id"] = JWTAuth::user()->getAttribute("id");

        if (isset($data["image"])) {
            $data["image_url"] = $this->uploadToS3($data["image"], self::REPLY);
        }

        Reply::create($data);

        return responseWithMessage("Reply successfully");
    }
}
