<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class DeleteNotificationByIdController extends Controller
{
    public function __invoke(string $id)
    {
        $notification = Notification::where('id', $id)
            ->where('receiver_id', Auth::id())
            ->first();

        if (!$notification) {
            return response()->json([
                'message' => 'Notification not found',
            ], 404);
        }

        $notification->delete();

        return responseWithMessage("Delete notification successfully");
    }
}
