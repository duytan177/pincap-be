<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class MarkReadByIdNotificationController extends Controller
{
  public function __invoke(string $id)
  {
    Notification::where('id',  $id)
        ->update(['is_read' => true]);

    return responseWithMessage("Mark read notification successfully");
  }
}
