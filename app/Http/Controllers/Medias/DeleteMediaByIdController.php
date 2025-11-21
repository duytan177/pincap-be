<?php

namespace App\Http\Controllers\Medias;

use App\Exceptions\MediaException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Medias\DeleteMediaRequest;
use App\Jobs\UpdateMediaEsJob;
use App\Models\Media;

class DeleteMediaByIdController extends Controller
{
    public function __invoke(DeleteMediaRequest $request)
    {
        $ids = $request->input('ids');

        if (Media::whereIn("id", $ids)->delete()) {
            # dispatch job to update ES
            UpdateMediaEsJob::dispatch($ids);

            return responseWithMessage("Deleted medias successfully");
        }

        throw MediaException::deleteFaired();
    }
}
