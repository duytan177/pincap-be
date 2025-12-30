<?php

namespace App\Http\Resources\Admin\Users;

use App\Components\Resources\BaseCollection;

class AdminUserCollection extends BaseCollection
{
    public $collects = AdminUserResource::class;
}

