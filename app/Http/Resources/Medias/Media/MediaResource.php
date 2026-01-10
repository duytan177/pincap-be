<?php

namespace App\Http\Resources\Medias\Media;

use App\Components\Resources\BaseResource;
use App\Services\S3PresignedUrlService;
use App\Traits\SharedTrait;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class MediaResource extends BaseResource
{
    use SharedTrait;
    private static $attributes = [
        'id',
        'media_url',
        'media_name',
        "description",
        "type",
        "safe_search_data",
        "is_policy_violation",
        "media_owner_id",
        "permalink"
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->resource->only(self::$attributes);

        // Convert media_url to presigned URLs
        if (isset($data['media_url'])) {
            $data['media_url'] = S3PresignedUrlService::convert($data['media_url']);
        }

        [$request, $result]= $this->checkReactionOfUserCurrent($request);
        $data += $result;

        return $data;
    }
}
