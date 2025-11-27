<?php

namespace App\Http\Resources\Medias\Media;

use App\Components\Resources\BaseResource;
use App\Traits\SharedTrait;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class MyReactedMediaResource extends BaseResource
{
    use SharedTrait;
    private static $attributes = [
        'id',
        'media_url',
        'media_name',
        "description",
        "feeling_id",
        "feeling_type",
        "type",
        "safe_search_data",
        "is_policy_violation",
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

        return $data;
    }
}
