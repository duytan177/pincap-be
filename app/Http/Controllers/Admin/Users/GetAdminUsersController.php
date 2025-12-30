<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\GetAdminUsersRequest;
use App\Http\Resources\Admin\Users\AdminUserCollection;
use App\Models\User;
use App\Traits\OrderableTrait;
use Illuminate\Database\Eloquent\Builder;

class GetAdminUsersController extends Controller
{
    use OrderableTrait;

    public function __invoke(GetAdminUsersRequest $request)
    {
        $query = User::withoutGlobalScopes()
            ->withTrashed();

        // Search by first_name
        if ($request->filled('first_name')) {
            $query->where('first_name', 'like', '%' . $request->input('first_name') . '%');
        }

        // Search by last_name
        if ($request->filled('last_name')) {
            $query->where('last_name', 'like', '%' . $request->input('last_name') . '%');
        }

        // Search by email
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->input('email') . '%');
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        // Filter by deleted_at
        if ($request->filled('deleted_at')) {
            if ($request->input('deleted_at') === 'null') {
                $query->whereNull('deleted_at');
            } else {
                $query->whereNotNull('deleted_at');
            }
        }

        // Apply ordering
        $order = $this->getAttributeOrder($request->input('order_key'), $request->input('order_type'));
        if ($order) {
            $query = $this->scopeApplyOrder($query, $order);
        } else {
            // Default order by created_at desc
            $query->orderBy('created_at', 'desc');
        }

        $users = $query->paginateOrAll($request);

        return new AdminUserCollection($users);
    }
}

