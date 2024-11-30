<?php

namespace App\Http\Resources\Medias\Media;

use App\Components\Resources\BaseResource;
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
        "type"
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->resource->only(self::$attributes);

        [$request, $result]= $this->checkReactionOfUserCurrent($request);
        $data += $result;

        return $data;
    }
}
