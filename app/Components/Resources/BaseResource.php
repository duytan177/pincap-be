<?php

namespace App\Components\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Components\Resources\ResourceResponse;

/** @SuppressWarnings(PHPMD.NumberOfChildren) */
class BaseResource extends JsonResource
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        return (new ResourceResponse($this))->toResponse($request);
    }

    /**
     * wrapResponse
     */
    protected function wrapResponse(array $result)
    {
        return ['data' => $result];
    }

    /**
     * successWithMessage
     */
    protected function successWithMessage()
    {
        return $this->resource;
    }
}
