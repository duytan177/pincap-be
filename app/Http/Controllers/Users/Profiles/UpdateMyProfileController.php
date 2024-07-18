<?php

namespace App\Http\Controllers\Users\Profiles;

use App\Exceptions\Users\ProfileException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Profiles\UpdateMyProfileRequest;
use App\Models\User;
use App\Traits\S3UploadTrait;
use Tymon\JWTAuth\Facades\JWTAuth;

class UpdateMyProfileController extends Controller
{
    use S3UploadTrait;

    const AVATAR = 'avatar';
    const BACKGROUND = 'background';
    public function __invoke(UpdateMyProfileRequest $request)
    {
        $requestDataUser = $request->validated();
        $user = JWTAuth::user();

        $emailCheck = User::where("email", $requestDataUser["email"])->whereNot("id", $user->getAttribute("id"))->exists();
        if ($emailCheck) {
            throw ProfileException::emailIsExisted();
        }
        $phoneCheck = User::Where("phone", $requestDataUser["phone"])->whereNot("id", $user->getAttribute("id"))->exists();

        if ($phoneCheck) {
            throw ProfileException::phoneIsExisted();
        }

        if (isset($requestDataUser[self::AVATAR])) {
            $requestDataUser[self::AVATAR] = $this->UploadToS3($requestDataUser[self::AVATAR], self::AVATAR);
            // $this->deleteFromS3($user->getAttribute(self::AVATAR));
        }

        if (isset($requestDataUser[self::BACKGROUND])) {
            $requestDataUser[self::BACKGROUND] = $this->UploadToS3($requestDataUser[self::BACKGROUND], self::BACKGROUND);
            // $this->deleteFromS3($user->getAttribute(self::BACKGROUND));
        }

        $user->update($requestDataUser);

        return responseWithMessage("Update profile user successfully");
    }
}
