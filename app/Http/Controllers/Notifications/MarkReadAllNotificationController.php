<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Http\Resources\Notifications\NotificationCollection;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class MarkReadAllNotificationController extends Controller
{
  public function __invoke()
  {
    Notification::where('receiver_id', Auth::id())
        ->update(['is_read' => true]);

    return responseWithMessage("Mark all read notifications successfully");
  }
}
