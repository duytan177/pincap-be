<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HasPaginateOrAll
{
    /**
     * Paginate if request has per_page and page, otherwise get all.
     */
    public function scopePaginateOrAll(Builder $query, Request|array|null $request = null)
    {
        // Support passing array or Request
        $perPage = null;
        $page = null;

        if ($request instanceof Request) {
            $perPage = $request->input('per_page');
            $page = $request->input('page');
        } elseif (is_array($request)) {
            $perPage = $request['per_page'] ?? null;
            $page = $request['page'] ?? null;
        }

        // If both per_page and page are provided → paginate
        if ($perPage && $page) {
            return $query->paginate($perPage, ['*'], 'page', $page);
        }

        // Otherwise just return all
        return $query->get();
    }
}
