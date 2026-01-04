<?php

namespace App\Http\Controllers\Medias;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medias\GenerateMediaMetadataRequest;
use App\Services\MediaIntegrateService;
use Illuminate\Http\JsonResponse;

class GenerateMediaMetadataController extends Controller
{
    protected MediaIntegrateService $mediaIntegrateService;

    public function __construct(MediaIntegrateService $mediaIntegrateService)
    {
        $this->mediaIntegrateService = $mediaIntegrateService;
    }

    /**
     * Generate metadata for media (title, description, tags)
     *
     * @param GenerateMediaMetadataRequest $request
     * @return JsonResponse
     */
    public function __invoke(GenerateMediaMetadataRequest $request): JsonResponse
    {
        $mediaId = $request->input('media_id');

        // Call MediaIntegrateService to generate metadata
        $result = $this->mediaIntegrateService->generateMetadata($mediaId);

        if ($result['error']) {
            return response()->json([
                "error" => true,
                "message" => $result['message'] ?? "Failed to generate metadata",
                "detail" => $result['detail'] ?? null
            ], 500);
        }

        // Return the response from the Python service directly
        return response()->json($result['data']);
    }
}

