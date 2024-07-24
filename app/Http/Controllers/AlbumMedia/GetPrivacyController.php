<?php

namespace App\Http\Controllers\AlbumMedia;

use App\Enums\Album_Media\Privacy;
use App\Http\Controllers\Controller;

class GetPrivacyController extends Controller
{
    public function __invoke()
    {
        return Privacy::asArray();
    }
}
