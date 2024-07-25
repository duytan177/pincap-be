<?php

namespace App\Http\Controllers\Medias;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;

class DeleteMediaByIdController extends Controller
{
    public function __invoke(Request $request)
    {
        $ids = $request->input('ids');

        Media::whereIn("id", $ids)->delete();

        return responseWithMessage("Deleted medias successfully");
    }
}
