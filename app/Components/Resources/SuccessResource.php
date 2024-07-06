<?php

namespace App\Components\Resources;

use App\Components\Resources\BaseResource;

class SuccessResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray($request)
    {
        return $this->successWithMessage();
    }
}
