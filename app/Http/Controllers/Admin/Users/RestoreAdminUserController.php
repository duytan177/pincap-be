<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Users\AdminUserResource;
use App\Models\User;
use App\Exceptions\Admin\AdminException;

class RestoreAdminUserController extends Controller
{
    public function __invoke(string $userId)
    {
        $user = User::withoutGlobalScopes()
            ->withTrashed()
            ->find($userId);

        if (!$user) {
            throw AdminException::userNotFound();
        }

        // Check if user is not deleted
        if (!$user->trashed()) {
            throw AdminException::userNotDeleted();
        }

        $user->restore();

        return new AdminUserResource($user->fresh());
    }
}

