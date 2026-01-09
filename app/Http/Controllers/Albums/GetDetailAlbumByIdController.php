<?php

namespace App\Http\Controllers\Albums;

use App\Enums\Album_Media\InvitationStatus;
use App\Enums\Album_Media\Privacy;
use App\Http\Controllers\Controller;
use App\Http\Resources\Albums\DetailAlbumResource;
use App\Models\Album;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GetDetailAlbumByIdController extends Controller
{
    public function __invoke($albumId)
    {
        $userId = Auth::user()?->id;
        
        // Check if user is in the album
        $isUserInAlbum = false;
        if ($userId) {
            $isUserInAlbum = DB::table('user_album')
                ->where('album_id', $albumId)
                ->where('user_id', $userId)
                ->where('invitation_status', InvitationStatus::ACCEPTED)
                ->whereNull('deleted_at')
                ->exists();
        }

        $albumDetail = Album::with([
            'allUser' => function ($query) {
                $query->withCount('followers')->addSelect(['user_album.invitation_status as status', 'user_album.album_role'])->orderBy("user_album.created_at", "desc")->limit(5);
            },
            'medias' => function ($query) use ($userId, $isUserInAlbum) {
                // Eager load thông tin user đã add media vào album
                $query
                    ->leftJoin('users', 'users.id', '=', 'album_media.added_by_user_id')
                    ->addSelect(
                        'medias.*',
                        'album_media.added_by_user_id as added_by_user_id',
                        DB::raw("CONCAT(users.first_name, ' ', users.last_name) as name"),
                        'users.avatar as avatar',
                        'users.email as email',
                    );
                
                // Filter out private medias if user is not in the album
                if (!$isUserInAlbum) {
                    $query->where('medias.privacy', Privacy::PUBLIC);
                }
            }
        ])->findOrFail($albumId);

        return DetailAlbumResource::make($albumDetail);
    }
}
