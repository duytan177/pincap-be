<?php

namespace App\Listeners;

use App\Enums\Notifications\NotificationType;
use App\Events\MediaCreatedEvent;
use App\Events\NotificationEvent;
use App\Http\Resources\Users\Information\SenderResource;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Ramsey\Uuid\Uuid;

class MediaCreatedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MediaCreatedEvent $event): void
    {
        $media = $event->media;
        $userId = $media->media_owner_id;

        $user = User::with('followers')->find($userId);
        $followerIds = $user->followers->pluck("id")->toArray();
        $notificationType = NotificationType::MEDIA_CREATED;
        $now = Carbon::now();
        $link = config("frontend.paths.media_detail") . '/' . $media->getAttribute("id");

        $notifications = [];

        foreach ($followerIds as $followerId) {
            $notifications[] = [
                "id" => Uuid::uuid4()->toString(),
                "title" => $media->getAttribute("media_name"),
                "content" => $media->getAttribute("description"),
                "link" => $link,
                "sender_id" => $userId,
                "receiver_id" => $followerId,
                "notification_type" => $notificationType,
                "created_at" => $now
            ];
        }

        event(new NotificationEvent(
            $notifications[0]["id"],
            $media->getAttribute("media_name"),
            $media->getAttribute("description"),
            $link,
            SenderResource::make($user),
            $followerIds,
            NotificationType::getKey($notificationType)
        ));

        Notification::insert($notifications);
    }
}
