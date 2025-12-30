<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Exceptions\Admin\AdminException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ForceDeleteAdminUserController extends Controller
{
    public function __invoke(string $userId)
    {
        $user = User::withoutGlobalScopes()
            ->withTrashed()
            ->find($userId);

        if (!$user) {
            throw AdminException::userNotFound();
        }

        // Check if trying to delete root account
        if ($user->getAttribute('email') === 'admin@gmail.com') {
            throw AdminException::cannotModifyRootAccount();
        }

        // Check if trying to delete self
        $currentUser = JWTAuth::user();
        if ($currentUser && $currentUser->getAttribute('id') === $userId) {
            throw AdminException::cannotDeleteSelf();
        }

        $user->forceDelete();

        return responseWithMessage("User permanently deleted successfully");
    }
}

