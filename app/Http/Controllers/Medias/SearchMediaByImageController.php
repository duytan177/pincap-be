<?php

namespace App\Http\Controllers\Medias;

use App\Enums\Album_Media\Privacy;
use App\Http\Controllers\Controller;
use App\Http\Resources\Medias\Media\MediaCollection;
use App\Models\Media;
use App\Services\MediaIntegrateService;
use Illuminate\Http\Request;

class SearchMediaByImageController extends Controller
{
    protected MediaIntegrateService $mediaIntegrateService;

    public function __construct(MediaIntegrateService $mediaIntegrateService)
    {
        $this->mediaIntegrateService = $mediaIntegrateService;
    }

    public function __invoke(Request $request)
    {
        $file = $request->file("image");

        if (!$file) {
            return response()->json([
                "error" => true,
                "message" => "Missing image file"
            ], 422);
        }

        $userId = $request->user()->id ?? null;

        // Pagination parameters
        $page = (int) $request->input("page", 1);
        $perPage = (int) $request->input("per_page", 10);
        $from = ($page - 1) * $perPage;

        // Call MediaIntegrateService to search media by image
        $result = $this->mediaIntegrateService->searchByImage($userId, $file, $from, $perPage);

        if ($result['error']) {
            return response()->json([
                "error" => true,
                "message" => $result['message'],
                "detail" => $result['detail']
            ], 500);
        }

        $data = $result['data'];
        $mediaIds = $data["media_ids"] ?? [];
        $total = $data["total"] ?? 0;

        if (empty($mediaIds)) {
            return response()->json([
                "data" => [],
                "current_page" => $page,
                "last_page" => 0,
                "per_page" => $perPage,
                "total" => $total
            ]);
        }

        // Query Media by IDs returned from Python API
        $medias = Media::whereIn("id", $mediaIds)->where("privacy", Privacy::PUBLIC)->get();

        return response()->json([
            "data" => MediaCollection::make($medias),
            "current_page" => $page,
            "last_page" => ceil($total / $perPage),
            "per_page" => $perPage,
            "total" => $total
        ]);
    }
}
