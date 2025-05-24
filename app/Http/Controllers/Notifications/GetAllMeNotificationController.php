<?php

namespace App\Http\Controllers\Notifications;

use App\Enums\Notifications\NotificationType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Notifications\NotificationCollection;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetAllMeNotificationController extends Controller
{
    public function __invoke(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $page = $request->input('page', 1);

        $notificationType = $request->input('notification_type');
        if ($notificationType && !NotificationType::hasValue($notificationType)) {
            return response()->json(['error' => 'Invalid notification type'], 422);
        }

        $params = array_filter([
            'is_read' => $request->input('is_read'),
            'notification_type' => $notificationType,
        ], function ($value) {
            return !is_null($value);
        });

        $notificationQuery = Auth::user()->notifications()->with('sender');

        $notifications = Notification::getList($notificationQuery, $params)->paginate($perPage, ['*'], 'page', $page);

        return new NotificationCollection($notifications);
    }
}
