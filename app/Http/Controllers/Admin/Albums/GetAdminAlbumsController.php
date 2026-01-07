<?php

namespace App\Http\Controllers\Admin\Albums;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Albums\GetAdminAlbumsRequest;
use App\Http\Resources\Admin\Albums\AdminAlbumCollection;
use App\Models\Album;
use App\Traits\OrderableTrait;
use Illuminate\Database\Eloquent\Builder;

class GetAdminAlbumsController extends Controller
{
    use OrderableTrait;

    public function __invoke(GetAdminAlbumsRequest $request)
    {
        $query = Album::withoutGlobalScopes()
            ->withTrashed()
            ->with(['userOwner:id,first_name,last_name,email,avatar']);

        // Search by album_name
        if ($request->filled('album_name')) {
            $query->where('album_name', 'like', '%' . $request->input('album_name') . '%');
        }

        // Search by description
        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->input('description') . '%');
        }

        // Filter by privacy
        if ($request->filled('privacy')) {
            $query->where('privacy', $request->input('privacy'));
        }

        // Filter by user_id
        if ($request->filled('user_id')) {
            $query->whereHas('userOwner', function (Builder $q) use ($request) {
                $q->where('users.id', $request->input('user_id'));
            });
        }

        // Search by relationship - user
        if ($request->filled('user_search')) {
            $userSearch = $request->input('user_search');
            $query->whereHas('userOwner', function (Builder $q) use ($userSearch) {
                $q->where('email', 'like', '%' . $userSearch . '%')
                    ->orWhere('first_name', 'like', '%' . $userSearch . '%')
                    ->orWhere('last_name', 'like', '%' . $userSearch . '%');
            });
        }

        // Filter by deleted_at
        if ($request->filled('deleted_at')) {
            if ($request->input('deleted_at') === 'null') {
                $query->whereNull('deleted_at');
            } else {
                $query->whereNotNull('deleted_at');
            }
        }

        // Get with medias count
        $query->withCount('medias');

        // Apply ordering
        $order = $this->getAttributeOrder($request->input('order_key'), $request->input('order_type'));
        if ($order) {
            $query = $this->scopeApplyOrder($query, $order);
        } else {
            // Default order by created_at desc
            $query->orderBy('created_at', 'desc');
        }

        $albums = $query->paginateOrAll($request);

        return new AdminAlbumCollection($albums);
    }
}

