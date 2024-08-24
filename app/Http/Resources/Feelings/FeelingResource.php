<?php

namespace App\Http\Resources\Feelings;

use App\Components\Resources\BaseResource;
use Illuminate\Http\Request;

class FeelingResource extends BaseResource
{
    private static $attributes = [
        'id',
        'feeling_type',
        "icon_url",
    ];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->resource->only(self::$attributes);
        if (isset($this->resource->total)) {
            $data["total"] = $this->resource->total;
        }
        return $data;
    }
}
