<?php

namespace App\Http\Controllers\Medias;

use App\Exceptions\MediaException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Medias\DeleteMediaRequest;
use App\Jobs\UpdateMediaEsJob;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;

class DeleteMediaByIdController extends Controller
{
    public function __invoke(DeleteMediaRequest $request)
    {
        $ids = $request->input('ids');
        $currentUserId = Auth::user()->id;

        // Check if user is the owner of all medias
        $medias = Media::whereIn("id", $ids)->get();
        
        if ($medias->isEmpty()) {
            throw MediaException::deleteFaired();
        }

        // Check if all medias belong to the current user
        $notOwnedMedias = $medias->filter(function ($media) use ($currentUserId) {
            return $media->media_owner_id !== $currentUserId;
        });

        if ($notOwnedMedias->isNotEmpty()) {
            throw MediaException::noPermissionToDelete();
        }

        if (Media::whereIn("id", $ids)->delete()) {
            # dispatch job to update ES
            UpdateMediaEsJob::dispatch($ids);

            return responseWithMessage("Deleted medias successfully");
        }

        throw MediaException::deleteFaired();
    }
}
