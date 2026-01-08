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
        $totalUsers = User::withoutGlobalScopes()->count();
        $totalMedia = Media::withoutGlobalScopes()->count();
        
        // Count media with policy violation: is_policy_violation = true AND at least one field (racy, adult, medical, violence) has value "POSSIBLE"
        $totalMediaPolicyViolation = Media::withoutGlobalScopes()
            ->where('is_policy_violation', true)
            ->where(function ($q) {
                $q->whereRaw("JSON_CONTAINS(JSON_EXTRACT(safe_search_data, '$[*].racy'), '\"POSSIBLE\"')")
                    ->orWhereRaw("JSON_CONTAINS(JSON_EXTRACT(safe_search_data, '$[*].adult'), '\"POSSIBLE\"')")
                    ->orWhereRaw("JSON_CONTAINS(JSON_EXTRACT(safe_search_data, '$[*].medical'), '\"POSSIBLE\"')")
                    ->orWhereRaw("JSON_CONTAINS(JSON_EXTRACT(safe_search_data, '$[*].violence'), '\"POSSIBLE\"')");
            })
            ->count();
        
        $totalAlbums = Album::withoutGlobalScopes()->count();

        return response()->json([
            'data' => [
                'total_users' => $totalUsers,
                'total_media' => $totalMedia,
                'total_media_policy_violation' => $totalMediaPolicyViolation,
                'total_albums' => $totalAlbums,
            ]
        ]);
    }
}

