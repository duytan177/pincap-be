<?php

namespace App\Http\Controllers\Medias;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medias\DeleteMediaRequest;
use App\Models\Media;

class DeleteMediaByIdController extends Controller
{
    public function __invoke(DeleteMediaRequest $request)
    {
        $ids = $request->input('ids');

        Media::whereIn("id", $ids)->delete();

        return responseWithMessage("Deleted medias successfully");
    }
}
