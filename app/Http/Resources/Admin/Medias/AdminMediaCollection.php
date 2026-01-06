<?php

namespace App\Http\Resources\Admin\Medias;

use App\Components\Resources\BaseCollection;

class AdminMediaCollection extends BaseCollection
{
    public $collects = AdminMediaResource::class;
}

