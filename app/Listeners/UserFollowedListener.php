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
        $follower = $event->follower;
        $followerId = $follower->getAttribute("id");
        $followeeId = $event->followeeId;
        $followerName = $follower->getAttribute('first_name').' '.$follower->getAttribute('last_name');
        $title = "A user is following you";
        $content = "$followerName has started following you. Check out their profile!";
        $link = config("frontend.paths.user_detail") . '/' . $followerId;
        $notificationType = NotificationType::USER_FOLLOWED;
        $notificationId = Uuid::uuid4()->toString();

        $notifications = [
            'id' => $notificationId,
            "title" => $title,
            "content" => $content,
            "link" => $link,
            "sender_id" => $followerId,
            "receiver_id" => $followeeId,
            "notification_type" => $notificationType
        ];

        event(new NotificationEvent(
            $notificationId,
            $title,
            $content,
            $link,
            SenderResource::make($follower),
            [$followeeId],
            NotificationType::getKey($notificationType)
        ));

        Notification::create($notifications);
    }
}
