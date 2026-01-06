<?php

namespace App\Http\Resources\Admin\Albums;

use App\Components\Resources\BaseCollection;

class AdminAlbumCollection extends BaseCollection
{
    public $collects = AdminAlbumResource::class;
}

