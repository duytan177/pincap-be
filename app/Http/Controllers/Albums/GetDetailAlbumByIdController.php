<?php

namespace App\Http\Controllers\Albums;

use App\Http\Controllers\Controller;
use App\Http\Resources\Albums\DetailAlbumResource;
use App\Models\Album;
use Illuminate\Support\Facades\DB;

class GetDetailAlbumByIdController extends Controller
{
    public function __invoke($albumId)
    {
        $albumDetail = Album::with([
            'allUser' => function ($query) {
                $query->withCount('followers')->addSelect(['user_album.invitation_status as status', 'user_album.album_role'])->orderBy("user_album.created_at", "desc")->limit(5);
            },
            'medias' => function ($query) {
                // Eager load thông tin user đã add media vào album
                $query
                    ->leftJoin('users', 'users.id', '=', 'album_media.user_created')
                    ->addSelect(
                        'medias.*',
                        'album_media.user_created as user_created_id',
                        DB::raw("CONCAT(users.first_name, ' ', users.last_name) as name"),
                        'users.avatar as avatar',
                        'users.email as email',
                    );
            }
        ])->findOrFail($albumId);

        return DetailAlbumResource::make($albumDetail);
    }
}
