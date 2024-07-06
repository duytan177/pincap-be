<?php

namespace App\Components\Resources;

use Illuminate\Http\Resources\Json\PaginatedResourceResponse as BasePaginatedResourceResponse;

class PaginatedResourceResponse extends BasePaginatedResourceResponse
{
    /**
     * Add the pagination information to the response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function paginationInformation($request)
    {
        /** @var \Illuminate\Pagination\LengthAwarePaginator */
        $paginated = $this->resource->resource;

        return [
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'per_page' => (int) $paginated->perPage(),
            'total' => $paginated->total(),
        ];
    }
}
