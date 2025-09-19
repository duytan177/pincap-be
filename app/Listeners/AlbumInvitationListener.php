<?php

namespace App\Listeners;

use App\Enums\Notifications\NotificationType;
use App\Events\AlbumInvitationEvent;
use App\Events\NotificationEvent;
use App\Http\Resources\Users\Information\SenderResource;
use App\Models\Notification;
use Ramsey\Uuid\Uuid;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AlbumInvitationListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(AlbumInvitationEvent $event): void
    {
        $album = $event->album;
        $inviter = $event->inviter;
        $inviteeId = $event->inviteeId;

        $title = "Album invitation";
        $content = "You've been invited to join album: " . $album->getAttribute("album_name");
        $link = '/albums/' . $album->getAttribute('id');
        $notificationType = NotificationType::ALBUM_INVITATION;
        $notificationId = Uuid::uuid4()->toString();
        Notification::create([
            'id' => $notificationId,
            'title' => $title,
            'content' => $content,
            'link' => $link,
            'sender_id' => $inviter->getAttribute('id'),
            'receiver_id' => $inviteeId,
            'notification_type' => $notificationType,
        ]);

        event(new NotificationEvent(
            $notificationId,
            $title,
            $content,
            $link,
            SenderResource::make($inviter),
            [$inviteeId],
            NotificationType::getKey($notificationType)
        ));
    }
}


