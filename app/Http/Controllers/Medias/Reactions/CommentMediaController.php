<?php

namespace App\Http\Controllers\Medias\Reactions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medias\Reactions\CommentMediaRequest;
use App\Models\Comment;
use App\Traits\AWSS3Trait;
use Tymon\JWTAuth\Facades\JWTAuth;

class CommentMediaController extends Controller
{
    use AWSS3Trait;

    public function __invoke(CommentMediaRequest $request)
    {
        $data = $request->validated();
        $data["user_id"] = JWTAuth::user()->getAttribute("id");

        if (isset($data["image"])) {
            $data["image_url"] = $this->uploadToS3($data["image"], self::COMMENT);
        }

        Comment::create($data);

        return responseWithMessage("Commented successfully");
    }
}
