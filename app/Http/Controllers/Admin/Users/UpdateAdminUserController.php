<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\UpdateAdminUserRequest;
use App\Http\Resources\Admin\Users\AdminUserResource;
use App\Models\User;
use App\Exceptions\Admin\AdminException;
use Illuminate\Support\Facades\Hash;

class UpdateAdminUserController extends Controller
{
    public function __invoke(UpdateAdminUserRequest $request, string $userId)
    {
        $user = User::withoutGlobalScopes()
            ->withTrashed()
            ->find($userId);

        if (!$user) {
            throw AdminException::userNotFound();
        }

        // Check if trying to modify root account
        if ($user->getAttribute('email') === 'admin@gmail.com') {
            throw AdminException::cannotModifyRootAccount();
        }

        $data = $request->validated();

        // Check if email already exists (excluding current user)
        if (isset($data['email']) && $data['email'] !== $user->getAttribute('email')) {
            $existingUser = User::withoutGlobalScopes()
                ->withTrashed()
                ->where('email', $data['email'])
                ->where('id', '!=', $userId)
                ->first();

            if ($existingUser) {
                throw AdminException::emailAlreadyExists();
            }
        }

        // Check if phone already exists (if provided and different)
        if (!empty($data['phone']) && $data['phone'] !== $user->getAttribute('phone')) {
            $existingPhone = User::withoutGlobalScopes()
                ->withTrashed()
                ->where('phone', $data['phone'])
                ->where('id', '!=', $userId)
                ->first();

            if ($existingPhone) {
                throw AdminException::phoneAlreadyExists();
            }
        }

        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return new AdminUserResource($user->fresh());
    }
}

