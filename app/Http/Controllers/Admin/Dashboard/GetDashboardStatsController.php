<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Media;
use App\Models\Album;

class GetDashboardStatsController extends Controller
{
    public function __invoke()
    {
        $totalUsers = User::withoutGlobalScopes()->whereNull('deleted_at')->count();
        $totalMedia = Media::withoutGlobalScopes()->whereNull('deleted_at')->count();
        $totalAlbums = Album::withoutGlobalScopes()->whereNull('deleted_at')->count();

        return response()->json([
            'data' => [
                'total_users' => $totalUsers,
                'total_media' => $totalMedia,
                'total_albums' => $totalAlbums,
            ]
        ]);
    }
}

