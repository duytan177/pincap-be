<?php

namespace App\Http\Controllers\Medias;

use App\Enums\Album_Media\InvitationStatus;
use App\Enums\Album_Media\Privacy;
use App\Exceptions\MediaException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Medias\MediaDetail\MediaDetailResource;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetDetailMediaByIdController extends Controller
{
    public function __invoke(Request $request, $mediaId)
    {
        $userId = Auth::user()->id;
        $query = Media::withCount(["reactionUser", "feelings"])
            ->with([
                'comments.userComment',
                "feelings",
                "comments.feelings",
                "comments.allFeelings",
                "comments.replies",
                "reactions" => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }
            ]);

        if (filter_var($request->input('tag_flg'), FILTER_VALIDATE_BOOLEAN)) {
            $query->with(['tags']);
        }

        $media = $query->findOrFail($mediaId);

        // Check permission for private media
        if ($media->getRawOriginal('privacy') === Privacy::PRIVATE) {
            // Allow if user is the owner
            if ($media->media_owner_id === $userId) {
                return MediaDetailResource::make($media);
            }

            // Check if media is in an album where user is owner or member
            $hasAccess = $media->albums()
                ->whereHas('allUser', function ($q) use ($userId) {
                    $q->where('users.id', $userId)
                        ->where('user_album.invitation_status', InvitationStatus::ACCEPTED)
                        ->whereNull('user_album.deleted_at');
                })
                ->exists();

            if (!$hasAccess) {
                throw MediaException::noPermission();
            }
        }

        return MediaDetailResource::make($media);
    }
}
