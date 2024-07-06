<?php

namespace App\Components\Resources;

use Illuminate\Http\Resources\Json\ResourceResponse as BaseResourceResponse;
use Illuminate\Support\Collection;

class ResourceResponse extends BaseResourceResponse
{
    /**
     * Wrap the given data if necessary.
     *
     * @param  array|\Illuminate\Support\Collection  $data
     * @param  array  $with
     * @param  array  $additional
     * @return array
     */
    protected function wrap($data, $with = [], $additional = [])
    {
        if ($data instanceof Collection) {
            $data = $data->all();
        }

        return array_merge_recursive($data, $with, $additional);
    }
}
