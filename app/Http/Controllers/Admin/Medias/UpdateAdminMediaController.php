<?php

namespace App\Http\Controllers\Admin\Medias;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Medias\UpdateAdminMediaRequest;
use App\Http\Resources\Admin\Medias\AdminMediaResource;
use App\Models\Media;
use App\Exceptions\Admin\MediaException;
use App\Services\ElasticsearchService;
use Illuminate\Support\Facades\Log;

class UpdateAdminMediaController extends Controller
{
    public function __invoke(UpdateAdminMediaRequest $request, string $mediaId)
    {
        $media = Media::withoutGlobalScopes()
            ->withTrashed()
            ->with(['userOwner:id,first_name,last_name,email,avatar', 'albums:id,album_name,description'])
            ->find($mediaId);

        if (!$media) {
            throw MediaException::mediaNotFound();
        }

        // Only allow updating media_name, description, privacy, and is_policy_violation
        $mediaData = $request->validated();
        
        // Update only allowed fields
        if (isset($mediaData['media_name'])) {
            $media->media_name = $mediaData['media_name'];
        }
        
        if (isset($mediaData['description'])) {
            $media->description = $mediaData['description'];
        }
        
        if (isset($mediaData['privacy'])) {
            $media->privacy = $mediaData['privacy'];
        }
        
        if (isset($mediaData['is_policy_violation'])) {
            $media->is_policy_violation = $mediaData['is_policy_violation'];
            
            // If is_policy_violation is false, set safe_search_data to null
            if ($mediaData['is_policy_violation'] === false) {
                $media->safe_search_data = null;
            }
        }
        
        $media->save();

        // Update Elasticsearch document
        try {
            $es = ElasticsearchService::getInstance();
            $index = config('services.elasticsearch.index');
            $updateData = [
                'updated_at' => now()->toDateTimeString(),
            ];
            
            if (isset($mediaData['media_name'])) {
                $updateData['media_name'] = $mediaData['media_name'];
            }
            
            if (isset($mediaData['description'])) {
                $updateData['description'] = $mediaData['description'];
            }
            
            if (isset($mediaData['privacy'])) {
                $updateData['privacy'] = $mediaData['privacy'];
            }
            
            if (isset($mediaData['is_policy_violation'])) {
                $updateData['is_policy_violation'] = $mediaData['is_policy_violation'];
                
                // If is_policy_violation is false, set safe_search_data to null in ES
                if ($mediaData['is_policy_violation'] === false) {
                    $updateData['safe_search_data'] = null;
                }
            }
            
            $es->updateDocument($index, $mediaId, $updateData);
        } catch (\Exception $e) {
            // Log error but don't fail the request if ES update fails
            Log::error("Failed to update media in Elasticsearch: " . $e->getMessage(), [
                'media_id' => $mediaId,
                'index' => config('services.elasticsearch.index')
            ]);
        }

        // Refresh counts
        $media->loadCount(['reactions', 'comments', 'albums']);

        return new AdminMediaResource($media->fresh());
    }
}

