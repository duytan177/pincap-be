<?php

namespace App\Http\Controllers\Albums;

use App\Enums\Album_Media\AlbumRole;
use App\Enums\Album_Media\InvitationStatus;
use App\Enums\Album_Media\Privacy;
use App\Http\Controllers\Controller;
use App\Http\Resources\Users\Information\UserInfoCollection;
use App\Models\Album;
use App\Models\User;
use App\Traits\OrderableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetUserInAlbumController extends Controller
{
    use OrderableTrait;

    public function __invoke(Request $request, string $albumId)
    {
        $userId = Auth::id();

        // Ensure current user has permission to view this album
        Album::findOrFailWithPermission($albumId, $userId, AlbumRole::getValues(), [InvitationStatus::ACCEPTED]);

        $query = $request->input(key: 'query');
        $search = [
            "first_name" => $query,
            "last_name" => $query,
            "email" => $query,
        ];

        $order = $this->getAttributeOrder($request->input('order_key'), $request->input('order_type'));
        $users = User::getList($search, $order)->withCount("followers")
            ->join('user_album', function ($join) use ($albumId) {
                $join->on('user_album.user_id', '=', 'users.id')
                    ->where('user_album.album_id', $albumId)
                    ->where('user_album.invitation_status', InvitationStatus::ACCEPTED)
                    ->whereNull('user_album.deleted_at');
            })
            ->addSelect([
                'user_album.invitation_status as status',
                'user_album.album_role as album_role',
            ]);

        $users = $users->paginateOrAll($request);

        return UserInfoCollection::make($users);
    }
}
