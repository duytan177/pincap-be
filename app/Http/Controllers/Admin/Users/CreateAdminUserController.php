<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\CreateAdminUserRequest;
use App\Http\Resources\Admin\Users\AdminUserResource;
use App\Models\User;
use App\Exceptions\Admin\AdminException;
use Illuminate\Support\Facades\Hash;

class CreateAdminUserController extends Controller
{
    public function __invoke(CreateAdminUserRequest $request)
    {
        $data = $request->validated();

        // Check if email already exists
        $existingUser = User::withoutGlobalScopes()
            ->withTrashed()
            ->where('email', $data['email'])
            ->first();

        if ($existingUser) {
            throw AdminException::emailAlreadyExists();
        }

        // Check if phone already exists (if provided)
        if (!empty($data['phone'])) {
            $existingPhone = User::withoutGlobalScopes()
                ->withTrashed()
                ->where('phone', $data['phone'])
                ->first();

            if ($existingPhone) {
                throw AdminException::phoneAlreadyExists();
            }
        }

        // Hash password
        $data['password'] = Hash::make($data['password']);

        // Set default role if not provided
        if (!isset($data['role'])) {
            $data['role'] = \App\Enums\User\Role::USER;
        }

        $user = User::create($data);

        return new AdminUserResource($user);
    }
}

