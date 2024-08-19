<?php

namespace App\Listeners;

use App\Enums\Notifications\NotificationType;
use App\Events\NotificationEvent;
use App\Events\UserFollowedEvent;
use App\Http\Resources\Users\Information\SenderResource;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Ramsey\Uuid\Uuid;

class UserFollowedListener implements ShouldQueue
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
    public function handle(UserFollowedEvent $event): void
    {
        $followerId = $event->followerId;
        $followeeId = $event->followeeId;
        $followee = User::find($followeeId);
        $link = "link";
        $notificationType = NotificationType::USER_FOLLOWED;


        $notifications = [
            "title" => "test",
            "content" => "content",
            "link" => "link",
            "sender_id" => $followeeId,
            "receiver_id" => $followerId,
            "notification_type" => $notificationType
        ];

        event(new NotificationEvent(
            Uuid::uuid4()->toString(),
            "test",
            "ghello",
            $link,
            SenderResource::make($followee),
            [$followerId],
            NotificationType::getKey($notificationType)
        ));

        Notification::create($notifications);
    }
}
