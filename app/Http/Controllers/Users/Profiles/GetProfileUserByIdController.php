<?php

namespace App\Http\Controllers\Users\Profiles;

use App\Http\Controllers\Controller;
use App\Http\Resources\Users\Profiles\ProfileResource;
use App\Models\User;

class GetProfileUserByIdController extends Controller
{
    public function __invoke($id)
    {
        $user = User::findOrFail($id);
        return ProfileResource::make($user);
    }
}
