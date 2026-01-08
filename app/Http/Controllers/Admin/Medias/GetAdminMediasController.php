<?php

namespace App\Http\Controllers\Admin\Medias;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Medias\GetAdminMediasRequest;
use App\Http\Resources\Admin\Medias\AdminMediaCollection;
use App\Models\Media;
use App\Traits\OrderableTrait;
use Illuminate\Database\Eloquent\Builder;

class GetAdminMediasController extends Controller
{
    use OrderableTrait;

    public function __invoke(GetAdminMediasRequest $request)
    {
        $query = Media::withoutGlobalScopes()
            ->withTrashed()
            ->with(['userOwner:id,first_name,last_name,email,avatar', 'albums:id,album_name,description']);

        // Search by media_name
        if ($request->filled('media_name')) {
            $query->where('media_name', 'like', '%' . $request->input('media_name') . '%');
        }

        // Search by description
        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->input('description') . '%');
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Filter by privacy
        if ($request->filled('privacy')) {
            $query->where('privacy', $request->input('privacy'));
        }

        // Filter by is_created
        if ($request->has('is_created')) {
            $query->where('is_created', $request->boolean('is_created'));
        }

        // Filter by is_comment
        if ($request->has('is_comment')) {
            $query->where('is_comment', $request->boolean('is_comment'));
        }

        // Filter by is_policy_violation - check safe_search_data for POSSIBLE values
        if ($request->has('is_policy_violation')) {
            $isViolation = $request->boolean('is_policy_violation');
            $query->where('is_policy_violation', $isViolation);
            
            if ($isViolation) {
                // Find media where at least one field (racy, adult, medical, violence) has value "POSSIBLE"
                // Check each field in all array elements
                $query->orWhere(function ($q) {
                    $q->whereRaw("JSON_CONTAINS(JSON_EXTRACT(safe_search_data, '$[*].racy'), '\"POSSIBLE\"')")
                        ->orWhereRaw("JSON_CONTAINS(JSON_EXTRACT(safe_search_data, '$[*].adult'), '\"POSSIBLE\"')")
                        ->orWhereRaw("JSON_CONTAINS(JSON_EXTRACT(safe_search_data, '$[*].medical'), '\"POSSIBLE\"')")
                        ->orWhereRaw("JSON_CONTAINS(JSON_EXTRACT(safe_search_data, '$[*].violence'), '\"POSSIBLE\"')");
                });
            }
        }

        // Filter by media_owner_id
        if ($request->filled('media_owner_id')) {
            $query->where('media_owner_id', $request->input('media_owner_id'));
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

        // Get with counts
        $query->withCount('reactions')
            ->withCount('comments')
            ->withCount('albums');

        // Apply ordering
        $order = $this->getAttributeOrder($request->input('order_key'), $request->input('order_type'));
        if ($order) {
            $query = $this->scopeApplyOrder($query, $order);
        } else {
            // Default order by created_at desc
            $query->orderBy('created_at', 'desc');
        }

        $medias = $query->paginateOrAll($request);

        return new AdminMediaCollection($medias);
    }
}

